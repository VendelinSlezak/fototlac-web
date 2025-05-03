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

        public function getUserId($email) {
            $stmt = $this->pdo->prepare("SELECT id FROM user WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getUserOrders($userid) {
            $stmt = $this->pdo->prepare("SELECT * FROM `order` WHERE user_id = :user_id AND state != 'S' ORDER BY created_at");
            $stmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getUserOrdersCount($userid) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) AS count FROM `order` WHERE user_id = :user_id AND state != 'S'");
            $stmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        }

        public function createNewOrder($userid) {
            $stmt = $this->pdo->prepare("INSERT INTO `order` (state, user_id) VALUES ('N', :user_id)");
            $stmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
            return $stmt->execute();
        }

        public function haveUserIdOrderId($userid, $orderid) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `order` WHERE user_id = :user_id AND id = :order_id");
            $stmt->bindParam(':order_id', $orderid, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
            $stmt->execute();
            $order_info = $stmt->fetchColumn();

            if($order_info == 1) {
                return true; // user ma tuto objednavku
            }
            else {
                return false; // user nema tuto objednavku
            }
        }

        public function deleteOrder($userid, $orderid) {
            if($this->haveUserIdOrderId($userid, $orderid) == false) {
                return false;
            }

            // vymazeme vsetky fotky
            $stmt = $this->pdo->prepare("SELECT id FROM photo WHERE order_id = :order_id");
            $stmt->bindParam(':order_id', $orderid, PDO::PARAM_INT);
            $stmt->execute();
            $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($photos as $photo) {
                $this->deletePhoto($userid, $orderid, $photo["id"]);
            }

            // vymazeme objednavku
            $stmt = $this->pdo->prepare("DELETE FROM `order` WHERE id = :order_id AND user_id = :user_id");
            $stmt->bindParam(':order_id', $orderid, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
            return $stmt->execute();
        }

        public function getOrderPhotos($userid, $orderid) {
            if($this->haveUserIdOrderId($userid, $orderid) == false) {
                return false;
            }

            $stmt = $this->pdo->prepare("   SELECT photo.id AS photo_id, file_name, copies, size_width_in_mm, size_height_in_mm, photo_type.name AS paper_type, photo_type.price_of_1x1_mm*size_width_in_mm*size_height_in_mm*copies AS price
                                            FROM photo
                                            JOIN photo_type ON photo_type.id = photo.photo_type_id
                                            WHERE order_id = :order_id");
            $stmt->bindParam(':order_id', $orderid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        public function getPhotoTypes() {
            $stmt = $this->pdo->prepare("SELECT * FROM photo_type");
            $stmt->execute();
            return $stmt->fetchAll();
        }

        public function createNewPhoto($orderid, $photo_type_id, $file_name, $copies, $width, $height) {
            $stmt = $this->pdo->prepare("INSERT INTO photo (
                `file_name`, 
                `copies`, 
                `order_id`, 
                `size_width_in_mm`, 
                `size_height_in_mm`, 
                `photo_type_id`
            ) VALUES (
                :file_name, 
                :copies, 
                :order_id, 
                :size_width_in_mm, 
                :size_height_in_mm, 
                :photo_type_id
            )");
            $stmt->bindParam(':file_name', $file_name);
            $stmt->bindParam(':copies', $copies, PDO::PARAM_INT);
            $stmt->bindParam(':order_id', $orderid, PDO::PARAM_INT);
            $stmt->bindParam(':size_width_in_mm', $width, PDO::PARAM_INT);
            $stmt->bindParam(':size_height_in_mm', $height, PDO::PARAM_INT);
            $stmt->bindParam(':photo_type_id', $photo_type_id, PDO::PARAM_INT);
            return $stmt->execute();
        }

        public function deletePhoto($userid, $orderid, $photoid) {
            if($this->haveUserIdOrderId($userid, $orderid) == false) {
                return false;
            }

            // ziskame cestu ku fotke
            $stmt = $this->pdo->prepare("SELECT file_name FROM photo WHERE id = :photo_id");
            $stmt->bindParam(':photo_id', $photoid, PDO::PARAM_INT);
            $stmt->execute();
            $photo_info = $stmt->fetch();
            $photo_file_name = "photos/" . $photo_info["file_name"];

            // vymazeme fotku z disku
            if (file_exists($photo_file_name)) {
                unlink($photo_file_name);
            }

            // vymazeme data o fotke
            $stmt = $this->pdo->prepare("DELETE FROM photo WHERE id = :photo_id");
            $stmt->bindParam(':photo_id', $photoid, PDO::PARAM_INT);
            return $stmt->execute();
        }

        public function getPhotoInfo($userid, $orderid, $photoid) {
            if($this->haveUserIdOrderId($userid, $orderid) == false) {
                return false;
            }

            $stmt = $this->pdo->prepare("SELECT * FROM photo WHERE id = :photo_id");
            $stmt->bindParam(':photo_id', $photoid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        }

        public function editPhotoInfo($userid, $orderid, $photoid, $copies, $width, $height, $photo_type_id) {
            if($this->haveUserIdOrderId($userid, $orderid) == false) {
                return false;
            }

            $stmt = $this->pdo->prepare("UPDATE photo 
                                         SET copies = :copies, 
                                             size_width_in_mm = :width, 
                                             size_height_in_mm = :height, 
                                             photo_type_id = :photo_type_id
                                         WHERE id = :photo_id");
            $stmt->bindParam(':photo_id', $photoid, PDO::PARAM_INT);
            $stmt->bindParam(':copies', $copies, PDO::PARAM_INT);
            $stmt->bindParam(':width', $width, PDO::PARAM_INT);
            $stmt->bindParam(':height', $height, PDO::PARAM_INT);
            $stmt->bindParam(':photo_type_id', $photo_type_id, PDO::PARAM_INT);
            return $stmt->execute();
        }
    }
?>