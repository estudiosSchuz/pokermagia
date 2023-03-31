<?php

    include_once('../domain/userService.php');
    // TODO: validar el request
    $id = htmlspecialchars($_GET["id"]);
    $arr = get($id);
    echo $arr;
?>