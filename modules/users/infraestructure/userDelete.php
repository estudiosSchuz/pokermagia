<?php
    include_once('../domain/userService.php');

    $id = htmlspecialchars($_GET["id"]);
    $resp = delete($id);
    echo json_encode($resp);