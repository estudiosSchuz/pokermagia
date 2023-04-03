<?php
require_once('../../../core/db.php');
//http://localhost/api-pokermagia/core/logger.php

$conexionDB = ConexionDB::getInstance('localhost', 'root', '', 'pokermagia');
$conexion = $conexionDB->conectar();

function get() {
    global $conexion;
    $sql = "SELECT * FROM users";
    $statement = $conexion->prepare($sql);
    $statement->execute();
    $resultado = $statement->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($resultado);
    exit();
};

function getId($id) {
    global $conexion;
    $sql = "SELECT * FROM users WHERE id = :id";
    $statement = $conexion->prepare($sql);
    $statement->bindParam(":id", $id);
    $statement->execute();
    $resultado = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$resultado) {
        http_response_code(404);
        exit();
    }
    echo json_encode($resultado);
    exit();
};

function create($email, $name, $last_name, $password, $role, $avatar) {
    global $conexion;
    /*
     // Validar los datos
     if (!validarEmail($email)) {
        // Si el email no es válido, devolver un error
        return array("error" => "El email ingresado no es válido");
    }
    
    if (!validarNombre($name)) {
        // Si el nombre no es válido, devolver un error
        return array("error" => "El nombre ingresado no es válido");
    }
    
    // Y así sucesivamente para cada campo...
    */
    
    $sql = "INSERT INTO users (email, name, last_name, password, role, avatar) VALUES (:email, :name, :last_name, :password, :role, :avatar)";
    $statement = $conexion->prepare($sql);
    $statement->bindParam(":email", $email);
    $statement->bindParam(":name", $name);
    $statement->bindParam(":last_name", $last_name);
    $statement->bindParam(":password", $password);
    $statement->bindParam(":role", $role);
    $statement->bindParam(":avatar", $avatar);
    $statement->execute();
    $idNuevoUsuario = $conexion->lastInsertId();  
    return array("id" => $idNuevoUsuario);
}


function edit($id, $email) {
    global $conexion;
    var_dump("Todo ok");
    //$sql = "UPDATE users SET email = :email, name = :name, last_name = :last_name, password = :password, role = :role, avatar = :avatar WHERE id = :id";
    $sql = "UPDATE usuarios SET email = :email WHERE id = :id";
    $statement = $conexion->prepare($sql);
    $statement->bindParam(":email", $email);
    $statement->bindParam(":id", $id);
    $statement->execute();
}


/*
$statement->bindParam(":name", $name);
    $statement->bindParam(":last_name", $last_name);
    $statement->bindParam(":password", $password);
    $statement->bindParam(":role", $role);
    $statement->bindParam(":avatar", $avatar);







function delete($email, $name, $last_name, $password, $role, $avatar) {
    global $conexion;
    $sql = "DELETE FROM users WHERE id = :id";
    $statement = $conexion->prepare($sql);
    $statement->bindParam(":id", $id);
    $statement->execute();
}



function validateId($id) {
    // Validar que el id sea un entero mayor a 0
    if (!filter_var($id, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1)))) {
        return false;
    }
    return true;
}

function validateEmail($email) {
    // Validar que el email tenga un formato válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}

function validateName($name) {
    // Validar que el nombre no esté vacío y contenga solo letras y espacios
    if (empty(trim($name)) || !preg_match('/^[a-zA-Z\s]+$/', $name)) {
        return false;
    }
    return true;
}

function validateLastName($last_name) {
    // Validar que el apellido no esté vacío y contenga solo letras y espacios
    if (empty(trim($last_name)) || !preg_match('/^[a-zA-Z\s]+$/', $last_name)) {
        return false;
    }
    return true;
}

function validatePassword($password) {
    // Validar que la contraseña tenga al menos 8 caracteres y contenga letras, números y caracteres especiales
    if (strlen($password) < 8 || !preg_match('/[a-zA-Z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[^\w\s]/', $password)) {
        return false;
    }
    return true;
}

function validateRole($role) {
    // Validar que el rol sea 'admin' o 'user'
    if ($role !== 'admin' && $role !== 'user') {
        return false;
    }
    return true;
}





function createUser($email, $name, $last_name, $password, $role) {
    if (!validateEmail($email) || !validateName($name) || !validateLastName($last_name) || !validatePassword($password) || !validateRole($role)) {
        // Si algún campo no es válido, retornar un error
        return array('error' => 'Datos inválidos');
    }

    // Código para crear el usuario en la base de datos
    // ...
}




function validarEmail($email) {
    // Aquí iría la lógica para validar el email
    // Por ejemplo, podrías usar la función filter_var
    // para validar el formato del email
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validarNombre($nombre) {
    // Aquí iría la lógica para validar el nombre
    // Por ejemplo, podrías verificar que el nombre
    // tenga una longitud mínima y máxima
    return strlen($nombre) >= 2 && strlen($nombre) <= 50;
}

// Y así sucesivamente para cada campo a validar...


Luego, en la función create(), podrías usar estas funciones de validación para verificar que los datos ingresados por el usuario sean válidos antes de insertarlos en la base de datos.
function create($email, $name, $last_name, $password, $role, $avatar) {
    global $conexion;
    
    // Validar los datos
    if (!validarEmail($email)) {
        // Si el email no es válido, devolver un error
        return array("error" => "El email ingresado no es válido");
    }
    
    if (!validarNombre($name)) {
        // Si el nombre no es válido, devolver un error
        return array("error" => "El nombre ingresado no es válido");
    }
    
    // Y así sucesivamente para cada campo...
    
    // Si todos los datos son válidos, insertarlos en la base de datos
    $sql = "INSERT INTO usuarios (email, name, last_name, password, role, avatar) VALUES (:email, :name, :last_name, :password, :role, :avatar)";
    $statement = $conexion->prepare($sql);
    $statement->bindParam(":email", $email);
    $statement->bindParam(":name", $name);
    $statement->bindParam(":last_name", $last_name);
    $statement->bindParam(":password", $password);
    $statement->bindParam(":role", $role);
    $statement->bindParam(":avatar", $avatar);
    $statement->execute();
    $idNuevoUsuario = $conexion->lastInsertId();   
    
    // Devolver el id del nuevo usuario creado
    return array("id" => $idNuevoUsuario);
}
*/