<?php
    class RequestManager {
        private $userManager;
        private $contents;

        public function __construct(array $contents) {
            $this->userManager = new UserManager();
            $this->contents = $contents;
            $this->respondToRequest($_GET, $_POST);
        }

        private function respondToRequest($get, $post) {
            switch($get["task"]) {
                case "getContent":
                    $this->getContent($get);
                    break;
                case "getUserData":
                    $this->getUserData();
                    break;
                case "logOut":
                    $this->logOut();
                    break;
                case "login":
                    $this->login($post["userOrEmail"], $post["password"]);
                    break;
                case "reg":
                    $this->registrate($post["username"], $post["email"], $post["pwd1"], $post["pwd2"]);
                    break;
                default:
            }
        }

        private function getContent($get) {
            switch($get["keyword"]) {
                case "start":
                    if($this->userManager->isUserLoggedIn()) {
                        $this->echoContentInJSON("mainPage");
                    } else {
                        $this->echoContentInJSON("loginPage");
                    }
                    break;
                case "registrate":
                    if($this->userManager->isUserLoggedIn()) {
                        $this->echoContentInJSON("mainPage");
                    } else {
                        $this->echoContentInJSON("regPage");
                    }
                    break;
                default:
                    echo json_encode(["result" => "error", "errors" => ["requestError"]]);
            }
        }

        private function echoContentInJSON(string $contentName) {
            echo json_encode([
                "result" => "ok",
                "content" => $this->contents[$contentName]["content"],
                "functions" => $this->contents[$contentName]["functions"]
            ]);
        }

        private function getUserData() {
            if($this->userManager->isUserLoggedIn()) {
                echo json_encode([
                    "result" => "ok",
                    "username" => $this->userManager->getUsername(),
                    "email" => $this->userManager->getEmail()
                ]);
            } else {
                echo json_encode(["result" => "error", "errors" => ["NotLoggedIn"]]);
            }
        }

        private function logOut() {
            if($this->userManager->logOut()) {
                echo json_encode(["result" => "ok"]);
            } else {
                echo json_encode(["result" => "error", "errors" => ["LogOutError"]]);
            }
        }

        private function login($userOrEmail, $password) {
            $errors = $this->userManager->login($userOrEmail, $password);
            if(empty($errors)) {
                echo json_encode(["result" => "ok"]);
            } else {
                echo json_encode(["result" => "error", "errors" => $errors]);
            }
        }

        private function registrate($username, $email, $pwd1, $pwd2) {
            $errors = $this->userManager->registrate($username, $email, $pwd1, $pwd2);
            if(empty($errors)) {
                echo json_encode(["result" => "ok"]);
            } else {
                echo json_encode(["result" => "error", "errors" => $errors]);
            }
        }
    }

?>