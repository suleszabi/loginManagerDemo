<?php

    session_start();
    require_once "./const.php";
    require_once "./contents.php";
    require_once "./class/StrOpColl.php";
    require_once "./class/DBManager.php";
    require_once "./class/UserManager.php";
    require_once "./class/RequestManager.php";

    $requestManager = new RequestManager($contents);

?>