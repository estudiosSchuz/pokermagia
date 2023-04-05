<?php
require_once('../../../core/logger.php');

function get($conexion, $role = null) {
    if ($role !=="admin") {
        http_response_code(400);
        exit(json_encode(array("error" => "El rol $role proporcionado no es válido.")));
    }
    $sql = "SELECT * FROM users";
    $statement = $conexion->prepare($sql);
    $statement->execute();
    $resultado = $statement->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($resultado);
    exit();
};

function getId($conexion, $id) {
    if (!is_numeric($id) || $id <= 0) {
        http_response_code(400);
        exit(json_encode(array("error" => "El ID $id debe ser un número entero mayor que cero")));
    }

    $sql = "SELECT * FROM users WHERE id = :id";
    $statement = $conexion->prepare($sql);
    $statement->bindValue(":id", $id, PDO::PARAM_INT);

    if (!$statement->execute()) {
        http_response_code(500);
        exit(json_encode(array("error" => "Error al ejecutar la consulta.")));
    }

    $resultado = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$resultado) {
        http_response_code(404);
        exit(json_encode(array("error" => "El usuario con ID $id no existe.")));
    }

    echo json_encode($resultado);
    exit();
};

function create($conexion, $email, $name, $last_name, $password, $role, $avatar) {
    // Validación de datos
    if (empty(trim($email)) || empty(trim($name)) || empty(trim($last_name)) || empty(trim($password)) || empty(trim($role))) {
        http_response_code(400);
        exit(json_encode(array("error" => "Debe proporcionar todos los campos obligatorios: email, name, last_name, password, role.")));
    }
    // Validación del formato de correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        exit(json_encode(array("error" => "El correo electrónico proporcionado no tiene un formato válido.")));
    }
    // Verificar si el correo electrónico ya está registrado
    $sql = "SELECT id FROM users WHERE email = :email";
    $statement = $conexion->prepare($sql);
    $statement->bindValue(":email", $email, PDO::PARAM_STR);
    if (!$statement->execute()) {
        http_response_code(500);
        exit(json_encode(array("error" => "Error al buscar el correo electrónico.")));
    }
    if ($statement->rowCount() > 0) {
        http_response_code(400);
        exit(json_encode(array("error" => "El correo electrónico proporcionado ya está registrado.")));
    }

    // Insertar el nuevo usuario
    $sql = "INSERT INTO users (email, name, last_name, password, role, avatar) VALUES (:email, :name, :last_name, :password, :role, :avatar)";
    $statement = $conexion->prepare($sql);
    $statement->bindValue(":email", $email, PDO::PARAM_STR);
    $statement->bindValue(":name", $name, PDO::PARAM_STR);
    $statement->bindValue(":last_name", $last_name, PDO::PARAM_STR);
    $statement->bindValue(":password", $password, PDO::PARAM_STR);
    $statement->bindValue(":role", $role, PDO::PARAM_STR);
    $statement->bindValue(":avatar", $avatar, PDO::PARAM_STR);
    if (!$statement->execute()) {
        http_response_code(500);
        exit(json_encode(array("error" => "Error al crear el usuario.")));
    }
    $idNuevoUsuario = $conexion->lastInsertId();  
    return array("id" => $idNuevoUsuario);
}

function edit($conexion, $id, $request_body) {
    if (empty($id)) {
        http_response_code(400);
        exit(json_encode(array("error" => "El ID $id no puede ir vacio")));
    }
    if (!is_numeric($id) || $id <= 0) {
        http_response_code(400);
        exit(json_encode(array("error" => "El ID $id debe ser un número entero mayor que cero")));
    }
    $request_data = json_decode($request_body, true);
    $update_fields = [];
    
    foreach($request_data as $field => $value) {
        if (!empty(trim($value) || isset($value))) {
            if ($field == "email") {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    http_response_code(400);
                    exit(json_encode(array("error" => "El campo 'email' no contiene una dirección de correo electrónico válida")));
                }
            }else if ($field == "name" || $field == "last_name") {
                if (!preg_match('/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/', $value)) {
                    http_response_code(400);
                    exit(json_encode(array("error" => "El campo 'name' solo puede contener letras, espacios en blanco y caracteres especiales.")));
                }
            }else if ($field == "role") {
                if ($value !== 'admin' && $value !== 'user' && $value !== 'player') {
                    http_response_code(400);
                    exit(json_encode(array("error" => "El campo 'role' no es valido.")));
                }
            }else if ($field == "password") {
                if (strlen($value) < 8 || !preg_match('/[a-zA-Z]/', $value) || !preg_match('/\d/', $value) || !preg_match('/[^\w\s]/', $value)) {
                    http_response_code(400);
                    exit(json_encode(array("error" => "La contraseña debe tener al menos 8 caracteres y contener al menos una letra, un número y un carácter especial (como @#$%).")));
                }
            }           
            $update_fields[] = $field . ' = :' . $field;
        }
    }

    $update_query = implode(', ', $update_fields);
    if (empty($update_query)) {
        return array("error" => "No se proporcionaron campos a actualizar.");
    }
    $sql = "UPDATE users SET $update_query WHERE id = :id";
    $statement = $conexion->prepare($sql);
    $statement->bindValue(":id", $id, PDO::PARAM_INT);
    foreach($request_data as $field => $value) {
        if (!empty(trim($value))) {
            $statement->bindValue(":$field", $value);
        }
    }
    if (!$statement->execute()) {
        http_response_code(500);
        exit(json_encode(array("error" => "Error al crear el usuario.")));
    }
    return array("id" => $id, "updated_fields" => array_keys($request_data));
}

function delete($conexion,$id, $role = null) {
    if (!is_numeric($id) || $id <= 0) {
        http_response_code(400);
        exit(json_encode(array("error" => "El ID $id debe ser un número entero mayor que cero")));
    }
    if ($role !=="admin") {
        http_response_code(400);
        exit(json_encode(array("error" => "Solo un usuario con rol de administrador puede borrar usuarios.")));
    }
    $sql = "DELETE FROM users WHERE id = :id";
    $statement = $conexion->prepare($sql);
    $statement->bindValue(":id", $id, PDO::PARAM_INT);

    if (!$statement->execute()) {
        http_response_code(500);
        exit(json_encode(array("error" => "Error al ejecutar la consulta.")));
    }
    if ($statement->rowCount() == 0) {
        http_response_code(404);
        exit(json_encode(array("error" => "El usuario con ID $id no existe.")));
    }
    return array("id" => $id);
}