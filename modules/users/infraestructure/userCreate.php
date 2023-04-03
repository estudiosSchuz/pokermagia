<?php
    include_once('../domain/userService.php');
    
    //Crear users
    $email = $_POST['email'];
    $name = $_POST['name'];
    $last_name = $_POST['last_name'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $avatar = $_POST['avatar'];
    $resultado = create($email, $name, $last_name, $password, $role, $avatar);
    
    if (isset($resultado["error"])) {
        echo json_encode(array("error" => $resultado["error"]));
    } else {
        echo json_encode(array("id" => $resultado["id"]));
    }