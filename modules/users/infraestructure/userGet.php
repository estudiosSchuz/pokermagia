<?php

    include_once('../domain/userService.php');
    // TODO: validar el request
    //$id = $_GET['id'];
    $id = htmlspecialchars($_GET['id']);
    $jsonData = get($id);
    echo $jsonData;
    
?>