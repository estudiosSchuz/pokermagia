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

function edit($id, $request_body) {
    global $conexion;
    $request_data = json_decode($request_body, true);

    $update_fields = [];
    foreach($request_data as $field => $value) {
        if (!empty(trim($value))) {
            $update_fields[] = $field . ' = :' . $field;
        }
    }
    $update_query = implode(', ', $update_fields);

    if (empty($update_query)) {
        return array("error" => "No se proporcionaron campos a actualizar.");
    }

    $sql = "UPDATE users SET $update_query WHERE id = :id";
    $statement = $conexion->prepare($sql);
    $statement->bindParam(":id", $id);

    foreach($request_data as $field => $value) {
        if (!empty(trim($value))) {
            $statement->bindValue(":$field", $value);
        }
    }

    $statement->execute();
    return array("id" => $id, "updated_fields" => array_keys($request_data));
}

function delete($id) {
    global $conexion;
    $sql = "DELETE FROM users WHERE id = :id";
    $statement = $conexion->prepare($sql);
    $statement->bindParam(":id", $id);
    $statement->execute(); 
    return array("id" => $id);
}