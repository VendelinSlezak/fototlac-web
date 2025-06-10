<?php
    class Registration {
        private $pdo;

        public function __construct(Database $db) {
            $this->pdo = $db->getPDO();
        }

        public function error($str) {
            echo '<div class="alert alert-danger" role="alert">Chyba: ' . $str . '</div>';
        }
 
        public function addUser($email, $password) {
            // validacia udajov - predchadzanie utokom
            if(strlen($email) > 45) {
                throw new InvalidArgumentException('Email je príliš dlhý, maximálna dĺžka je 45 znakov');
            }
            if(strlen($password) > 72) {
                throw new InvalidArgumentException('Heslo je príliš dlhé, maximálna dĺžka je 72 znakov');
            }
            if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
                throw new InvalidArgumentException('Neplatný email');
            }

            // kontrola duplicity
            $stmt = $this->pdo->prepare("SELECT COUNT(*) AS count FROM user WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $count_of_users = $stmt->fetchColumn();
            if($count_of_users != 0) {
                throw new InvalidArgumentException('Užívateľ s týmto emailom už existuje');
            }

            // pridanie uzivatela do databazy
            $stmt = $this->pdo->prepare("INSERT INTO user (email, password) VALUES (:email, :password)");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
            if($stmt->execute() == false) {
                throw new PDOException('Zlyhanie databázy');
            }
            else {
                return true;
            }
        }
    }
?>