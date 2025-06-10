<?php
    class Admin {
        private $pdo;

        public function __construct(Database $db) {
            $this->pdo = $db->getPDO();
        }

        public function getAllSendedOrders() {
            $stmt = $this->pdo->prepare("SELECT * FROM `order` WHERE state = 'O' ORDER BY created_at");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function setOrderAsDone($orderid) {
            $stmt = $this->pdo->prepare("   UPDATE `order`
                                            SET `state` = 'S'
                                            WHERE id = :order_id");
            $stmt->bindParam(':order_id', $orderid, PDO::PARAM_INT);
            return $stmt->execute();
        }
 
        public function getOrderPhotos($orderid) {
            $stmt = $this->pdo->prepare("   SELECT photo.id AS photo_id, file_name, copies, photo_size.width AS size_width_in_mm, photo_size.height AS size_height_in_mm, photo_type.name AS paper_type, photo_type.price_of_1x1_mm*photo_size.width*photo_size.height*copies AS price
                                            FROM photo
                                            JOIN photo_type ON photo_type.id = photo.photo_type_id
                                            JOIN photo_size ON photo_size.id = photo.photo_size_id
                                            WHERE order_id = :order_id");
            $stmt->bindParam(':order_id', $orderid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        public function getOrderDetails($orderid) {
            $stmt = $this->pdo->prepare("SELECT * FROM `order` JOIN user ON `order`.user_id = user.id WHERE `order`.id = :order_id");
            $stmt->bindParam(':order_id', $orderid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
?>