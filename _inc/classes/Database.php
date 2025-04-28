<?php
    class Database {
        private $host = "localhost";
        private $db = "fototlac_db";
        private $user = "root";
        private $pass = "";
        private $charset = "utf8";
        private $pdo;

        public function __construct() {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
            try {
                $this->pdo = new PDO($dsn, $this->user, $this->pass);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $e) {
                die('Connection failed: ' . $e->getMessage());
            }
        }

        public function __destruct() {
            $this->pdo = null;
        }

        public function getConnection() {
            return $this->pdo;
        }

        public function index() {
            $stmt = $this->pdo->prepare("SELECT * FROM user");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getUserCount() {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) AS count FROM user");
            $stmt->execute();
            return $stmt->fetchColumn();
        }

        public function addUser($name, $email, $password) {
            // validacia udajov - predchadzanie utokom
            if(strlen($name) > 45) {
                throw new InvalidArgumentException('Meno je príliš dlhé, maximálna dĺžka je 45 znakov.');
            }
            if(strlen($email) > 45) {
                throw new InvalidArgumentException('Email je príliš dlhý, maximálna dĺžka je 45 znakov.');
            }
            if(strlen($password) > 72) {
                throw new InvalidArgumentException('Heslo je príliš dlhé, maximálna dĺžka je 72 znakov.');
            }
            if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
                throw new InvalidArgumentException('Neplatný email.');
            }

            // kontrola duplicity
            $stmt = $this->pdo->prepare("SELECT COUNT(*) AS count FROM user WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $count_of_users = $stmt->fetchColumn();
            if($count_of_users != 0) {
                throw new InvalidArgumentException('Užívateľ s týmto emailom už existuje.');
            }

            // pridanie uzivatela do databazy
            $stmt = $this->pdo->prepare("INSERT INTO user (name, email, password) VALUES (:name, :email, :password)");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
            return $stmt->execute();
        }

        public function doesUserExist($email, $password) {
            $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user_entry = $stmt->fetch(PDO::FETCH_ASSOC);

            if($user_entry == false) {
                return false;
            }

            if(password_verify($password, $user_entry['password']) == false) {
                return false;
            }

            return true;
        }
    }
?>