<?php

    include_once('../domain/userService.php');
    // TODO: validar el request
    
    $id = htmlspecialchars($_GET['id']);
    $jsonDataId = getId($id);
    echo $jsonDataId;
    
    /*
    $jsonData = get();
    echo $jsonData;
    */
?>