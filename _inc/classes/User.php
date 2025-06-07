<?php
    class User {
        private $pdo;

        public function __construct(Database $db) {
            $this->pdo = $db->getPDO();
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

        public function continueIfUserHasOrder($userid, $orderid) {
            if($this->haveUserIdOrderId($userid, $orderid) == false) {
                echo '<div class="alert alert-danger" role="alert">Chyba: Tento užívateľ nevlastní túto objednávku</div>';
                require("partials/footer.php");
                exit;
            }
        }

        public function getUserOrdersCount($userid) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) AS count FROM `order` WHERE user_id = :user_id AND state != 'S'");
            $stmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        }

        public function getUserOrders($userid) {
            $stmt = $this->pdo->prepare("SELECT * FROM `order` WHERE user_id = :user_id AND state != 'S' ORDER BY created_at");
            $stmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        public function createNewOrder($userid) {
            $stmt = $this->pdo->prepare("INSERT INTO `order` (state, user_id) VALUES ('N', :user_id)");
            $stmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
            return $stmt->execute();
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

        public function getPhotoTypes() {
            $stmt = $this->pdo->prepare("SELECT * FROM photo_type");
            $stmt->execute();
            return $stmt->fetchAll();
        }

        public function getPhotoSizes() {
            $stmt = $this->pdo->prepare("SELECT * FROM photo_size");
            $stmt->execute();
            return $stmt->fetchAll();
        }

        public function createNewPhoto($orderid, $photo_type_id, $photo_size_id, $file_name, $copies, $width, $height) {
            $stmt = $this->pdo->prepare("INSERT INTO photo (
                `file_name`, 
                `copies`, 
                `order_id`, 
                `size_width_in_mm`, 
                `size_height_in_mm`, 
                `photo_type_id`,
                `photo_size_id`
            ) VALUES (
                :file_name, 
                :copies, 
                :order_id, 
                :size_width_in_mm, 
                :size_height_in_mm, 
                :photo_type_id,
                :photo_size_id
            )");
            $stmt->bindParam(':file_name', $file_name);
            $stmt->bindParam(':copies', $copies, PDO::PARAM_INT);
            $stmt->bindParam(':order_id', $orderid, PDO::PARAM_INT);
            $stmt->bindParam(':size_width_in_mm', $width, PDO::PARAM_INT);
            $stmt->bindParam(':size_height_in_mm', $height, PDO::PARAM_INT);
            $stmt->bindParam(':photo_type_id', $photo_type_id, PDO::PARAM_INT);
            $stmt->bindParam(':photo_size_id', $photo_size_id, PDO::PARAM_INT);
            return $stmt->execute();
        }

        public function deletePhoto($userid, $orderid, $photoid) {
            if($this->haveUserIdOrderId($userid, $orderid) == false) {
                return false;
            }

            // skontrolujeme ci fotka patri k objednavke
            $stmt = $this->pdo->prepare("SELECT order_id FROM photo WHERE id = :photo_id");
            $stmt->bindParam(':photo_id', $photoid, PDO::PARAM_INT);
            $stmt->execute();
            $photo_order_id = $stmt->fetch();
            if($photo_order_id != $orderid) {
                return false;
            }

            // ziskame cestu ku fotke
            $stmt = $this->pdo->prepare("SELECT file_name FROM photo WHERE id = :photo_id");
            $stmt->bindParam(':photo_id', $photoid, PDO::PARAM_INT);
            $stmt->execute();
            $photo_info = $stmt->fetch();
            $photo_file_name = "photos/" . $photo_info["file_name"];

            // vymazeme fotku z disku
            if(file_exists($photo_file_name)) {
                unlink($photo_file_name);
            }

            // vymazeme data o fotke
            $stmt = $this->pdo->prepare("DELETE FROM photo WHERE id = :photo_id");
            $stmt->bindParam(':photo_id', $photoid, PDO::PARAM_INT);
            return $stmt->execute();
        }
    }
?>