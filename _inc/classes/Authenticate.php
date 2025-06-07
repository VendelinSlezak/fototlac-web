<?php
    class Authenticate {
        private $pdo;

        public function continueIfLoggedIn() {
            if(isset($_SESSION['user_id']) == false) {
                header("Location: login.php");
                exit;
            }
        }

        public function continueIfUserLoggedIn() {
            $this->continueIfLoggedIn();
            if($_SESSION['admin'] == true) {
                header("Location: admin-panel.php");
                exit;
            }
        }

        public function continueIfAdminLoggedIn() {
            $this->continueIfLoggedIn();
            if($_SESSION['admin'] == false) {
                header("Location: panel.php");
                exit;
            }
        }

        public function isUserLoggedIn() {
            if(isset($_SESSION['user_id']) && $_SESSION['admin'] == false) {
                return true;
            }
            else {
                return false;
            }
        }

        public function isAdminLoggedIn() {
            if(isset($_SESSION['user_id']) && $_SESSION['admin'] == true) {
                return true;
            }
            else {
                return false;
            }
        }
    }
?>