<?php
    class Login {
        private $pdo;

        public function __construct(Database $db) {
            $this->pdo = $db->getPDO();
        }

        public function error($str) {
            echo '<div class="alert alert-danger" role="alert">Chyba: ' . $str . '</div>';
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

        public function isUserAdmin($userid) {
            $stmt = $this->pdo->prepare("
                SELECT *
                FROM user
                WHERE id = :userid
            ");
            $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch();
            if($user['email'] == 'admin@admin.sk') {
                return true;
            }
            else {
                return false;
            }
        }
    }
?>