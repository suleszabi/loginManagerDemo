<?php

    class UserManager {
        private $dbManager;
        private $username;
        private $email;

        public function __construct() {
            $this->dbManager = new DBManager(DBHOST, DBNAME, DBUSER, DBPWD);
        }

        public function getUsername() {
            return $this->username;
        }

        public function getEmail() {
            return $this->email;
        }

        private function checkUsername(string $username) {
            $errors = array();

            if(empty($username)) {
                $errors[] = "UsernameEmpty";
            } else {
                if(!StrOpColl::checkStringLength($username, 5, 12)) {
                    $errors[] = "UsernameLength";
                }
    
                if(StrOpColl::isStringEmail($username)) {
                    $errors[] = "UsernameIsEmail";
                }
            }

            if(empty($errors)) {
                $usernameCountInDb = $this->dbManager->executeQuery(
                    "SELECT COUNT(*) AS qty FROM user WHERE username LIKE ?",
                    [$username],
                    false
                );
                if($usernameCountInDb["qty"] != 0) {
                    $errors[] = "UsernameUsed";
                }
            }

            return $errors;
        } 

        private function checkEmail(string $email) {
            $errors = array();

            if(empty($email)) {
                $errors[] = "EmailEmpty";
            } else {
                if(!StrOpColl::isStringEmail($email)) {
                    $errors[] = "EmailFormat";
                }

                if(!StrOpColl::checkStringLength($email, 6, 40)) {
                    $errors[] = "EmailLength";
                }
            }

            if(empty($errors)) {
                $emailCountInDb = $this->dbManager->executeQuery(
                    "SELECT COUNT(*) AS qty FROM user WHERE email LIKE ?",
                    [$email],
                    false
                );
                if($emailCountInDb["qty"] != 0) {
                    $errors[] = "EmailUsed";
                }
            }

            return $errors;
        } 

        private function checkPassword(string $pwd1, string $pwd2) {
            $errors = array();

            if(empty($pwd1)) {
                $errors[] = "Pwd1Empty";
            } else {
                if(!StrOpColl::checkStringLength($pwd1, 8, 64)) {
                    $errors[] = "PwdLength";
                }
    
                if(StrOpColl::isStringEmail($pwd1)) {
                    $errors[] = "PwdIsEmail";
                }
    
                if(!StrOpColl::isStringContainNumber($pwd1)) {
                    $errors[] = "PwdNumber";
                }
    
                if(!StrOpColl::isStringContainLowercase($pwd1)) {
                    $errors[] = "PwdLowercase";
                }
    
                if(!StrOpColl::isStringContainUppercase($pwd1)) {
                    $errors[] = "PwdUppercase";
                }
            }
            
            if(empty($errors)) {
                if(empty($pwd2)) {
                    $errors[] = "Pwd2Empty";
                } else {
                    if(!StrOpColl::isStringsAreEqual($pwd1, $pwd2)) {
                        $errors[] = "PwdsNotEqual";
                    }
                }
            }

            return $errors;
        } 

        public function registrate(string $username, string $email, string $pwd1, string $pwd2) {
            $errors = array_merge(
                $this->checkUsername($username),
                $this->checkEmail($email),
                $this->checkPassword($pwd1, $pwd2)
            );

            if(empty($errors)) {
                $password_salt = StrOpColl::generateRandomString(10);
                $password_hash = StrOpColl::encryptWithSalt($pwd1, $password_salt);

                $registrateSuccess = $this->dbManager->executeModifierCommand(
                    "INSERT INTO user(username, email, password_hash, password_salt)
                    VALUES (?,?,?,?)",
                    [$username, $email, $password_hash, $password_salt]
                );

                if(!$registrateSuccess) {
                    $errors[] = "RegistrateError";
                }
            }

            return $errors;
        }

        public function login(string $userOrEmail, string $pwd) {
            $errors = array();

            $userData = $this->dbManager->executeQuery(
                "SELECT id, password_hash, password_salt FROM user WHERE username LIKE ? OR email LIKE ?",
                [$userOrEmail, $userOrEmail],
                false
            );

            if(is_array($userData) && !empty($userData)) {

                if(StrOpColl::encryptWithSalt($pwd, $userData["password_salt"]) == $userData["password_hash"]) {
                    $_SESSION["userId"] = $userData["id"];
                } else {
                    $errors[] = "WrongLoginData";
                }

            } else {
                $errors[] = "WrongLoginData";
            }

            return $errors;
        }

        private function setAttributes() {
            $userData = $this->dbManager->executeQuery(
                "SELECT username, email FROM user WHERE id=?",
                [$_SESSION["userId"]],
                false
            );

            $this->username = $userData["username"];
            $this->email = $userData["email"];

            return (
                is_string($this->username) &&
                is_string($this->email) &&
                !empty($this->username) &&
                !empty($this->email)
            );
        }

        public function isUserLoggedIn() {
            $isUserIdValid = false;
            if(isset($_SESSION["userId"])) {
                $userCount = $this->dbManager->executeQuery(
                    "SELECT COUNT(*) AS qty FROM user WHERE id=?",
                    [$_SESSION["userId"]],
                    false
                );

                if($userCount["qty"] == 1) {
                    $isUserIdValid = $this->setAttributes();
                }
            }

            return $isUserIdValid;
        }

        public function logOut() {
            unset($_SESSION["userId"]);
            return !isset($_SESSION["userId"]);
        }
    }

?>