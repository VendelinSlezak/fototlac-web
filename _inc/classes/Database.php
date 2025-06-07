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

        public function getPDO() {
            return $this->pdo;
        }

        public function index() {
            $stmt = $this->pdo->prepare("SELECT * FROM user");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getOrderDetails($orderid) {
            $stmt = $this->pdo->prepare("SELECT * FROM `order` JOIN user ON `order`.user_id = user.id WHERE `order`.id = :order_id");
            $stmt->bindParam(':order_id', $orderid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
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

        public function sendOrder($userid, $orderid, $name, $surname, $country, $city, $postalCode, $street, $houseNumber) {
            if ($this->haveUserIdOrderId($userid, $orderid) == false) {
                return false;
            }

            $stmt = $this->pdo->prepare("
                UPDATE `order` 
                SET name = :name, 
                    surname = :surname, 
                    country = :country, 
                    city = :city, 
                    postal_code = :postalCode, 
                    street = :street, 
                    house_number = :houseNumber,
                    state = 'O'
                WHERE user_id = :userid AND id = :orderid
            ");
            $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            $stmt->bindParam(':orderid', $orderid, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
            $stmt->bindParam(':country', $country, PDO::PARAM_STR);
            $stmt->bindParam(':city', $city, PDO::PARAM_STR);
            $stmt->bindParam(':postalCode', $postalCode, PDO::PARAM_STR);
            $stmt->bindParam(':street', $street, PDO::PARAM_STR);
            $stmt->bindParam(':houseNumber', $houseNumber, PDO::PARAM_STR);
            return $stmt->execute();
        }
    }
?>