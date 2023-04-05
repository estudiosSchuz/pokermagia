<?php
require_once('../../../core/logger.php');


//http://localhost/api-pokermagia/modules/users/infraestructure/userController.php?role=administrators
function get($conexion, $role = null) {
    //Se comprueba si se proporcionó el rol correcto
    if ($role !=="administrators") {
        http_response_code(400);
        exit(json_encode(array("error" => "El rol $role proporcionado no es válido.")));
    }
    //Se realiza la consulta para obtener todos los usuarios
    $sql = "SELECT * FROM users";
    $statement = $conexion->prepare($sql);
    $statement->execute();
    $resultado = $statement->fetchAll(PDO::FETCH_ASSOC);
    //Se devuelve el resultado como un objeto JSON
    echo json_encode($resultado);
    exit();
};


//http://localhost/api-pokermagia/modules/users/infraestructure/userController.php?id=21
function getId($conexion, $id) {
    //Verifica si el ID proporcionado es un número entero mayor que cero, y si no lo es, devuelve un error HTTP 400.
    if (!is_numeric($id) || $id <= 0) {
        http_response_code(400);
        exit(json_encode(array("error" => "El ID $id debe ser un número entero mayor que cero")));
    }

    //Si el ID es válido, realiza una consulta en la tabla "users" de la base de datos para obtener los datos del usuario con el ID proporcionado.
    $sql = "SELECT * FROM users WHERE id = :id";
    $statement = $conexion->prepare($sql);
    $statement->bindValue(":id", $id, PDO::PARAM_INT);

    if (!$statement->execute()) {
        http_response_code(500);
        exit(json_encode(array("error" => "Error al ejecutar la consulta.")));
    }

    //Si la consulta no es exitosa o no se encuentra un usuario con el ID proporcionado, devuelve un error HTTP 500 o 404, respectivamente.
    $resultado = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$resultado) {
        http_response_code(404);
        exit(json_encode(array("error" => "El usuario con ID $id no existe.")));
    }

    //Si la consulta es exitosa y devuelve datos, los devuelve en formato JSON.
    echo json_encode($resultado);
    exit();
};


//http://localhost/api-pokermagia/modules/users/infraestructure/userController.php
//Body tipo form-data
function create($conexion, $email, $name, $last_name, $password, $role, $avatar) {
    //Validación de datos
    if (empty(trim($email)) || empty(trim($name)) || empty(trim($last_name)) || empty(trim($password)) || empty(trim($role))) {
        http_response_code(400);
        exit(json_encode(array("error" => "Debe proporcionar todos los campos obligatorios: email, name, last_name, password, role.")));
    }
    //Validación del formato de correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        exit(json_encode(array("error" => "El correo electrónico proporcionado no tiene un formato válido.")));
    }
    //Verificar si el correo electrónico ya está registrado
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

    //Validacion name
    if (!preg_match('/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/', $name) || !preg_match('/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/', $last_name )) {
        http_response_code(400);
        exit(json_encode(array("error" => "El campo 'name' solo puede contener letras, espacios en blanco y caracteres especiales.")));
    }
    
    //Validacion role
    if ($role !== 'administrators' && $role !== 'teachers' && $role !== 'players') {
        http_response_code(400);
        exit(json_encode(array("error" => "El campo 'role' no es valido.")));
    }
  
    //Validacion password
    if (strlen($password) < 8 || !preg_match('/[a-zA-Z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[^\w\s]/', $password)) {
        http_response_code(400);
        exit(json_encode(array("error" => "La contraseña debe tener al menos 8 caracteres y contener al menos una letra, un número y un carácter especial (como @#$%).")));
    }

    //Validacion avatar
    if (!filter_var($avatar, FILTER_VALIDATE_URL) || !preg_match('/^(http|https):\/\/(.*)$/', $avatar)) {
        http_response_code(400);
        exit(json_encode(array("error" => "La URL no es válida")));
    }

    //Insertar el nuevo usuario
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

    //retorna un array con el id del nuevo usuario creado.
    $idNuevoUsuario = $conexion->lastInsertId();  
    return array("id" => $idNuevoUsuario);
}


//http://localhost/api-pokermagia/modules/users/infraestructure/userController.php?id=22
//Body tipo raw-json
function edit($conexion, $id, $request_body) {

    //Comprobar si el ID no está vacío
    if (empty($id)) {
        http_response_code(400);
        exit(json_encode(array("error" => "El ID $id no puede ir vacio")));
    }
    //Comprobar si el ID es un número mayor que cero
    if (!is_numeric($id) || $id <= 0) {
        http_response_code(400);
        exit(json_encode(array("error" => "El ID $id debe ser un número entero mayor que cero")));
    }

    //Verificar si el id de usuario existe en la base de datos antes de editarlo
    $sql = "SELECT id FROM users WHERE id = :id";
    $statement = $conexion->prepare($sql);
    $statement->bindValue(":id", $id, PDO::PARAM_INT);
    if (!$statement->execute()) {
        http_response_code(500);
        exit(json_encode(array("error" => "Error al ejecutar la consulta.")));
    }
    $resultado = $statement->fetch(PDO::FETCH_ASSOC);

    //Si el usuario no existe, devuelve un error 404
    if (!$resultado) {
        http_response_code(404);
        exit(json_encode(array("error" => "El usuario con ID $id no existe.")));
    }
    //Convertir el cuerpo de la solicitud en un objeto JSON
    $request_data = json_decode($request_body, true);
    $update_fields = [];
    //Recorrer todos los campos enviados en la solicitud
    foreach($request_data as $field => $value) {
        // Comprobar si el valor del campo no está vacío ni nulo
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
                if ($value !== 'administrators' && $value !== 'teachers' && $value !== 'players') {
                    http_response_code(400);
                    exit(json_encode(array("error" => "El campo 'role' no es valido.")));
                }
            }else if ($field == "password") {
                if (strlen($value) < 8 || !preg_match('/[a-zA-Z]/', $value) || !preg_match('/\d/', $value) || !preg_match('/[^\w\s]/', $value)) {
                    http_response_code(400);
                    exit(json_encode(array("error" => "La contraseña debe tener al menos 8 caracteres y contener al menos una letra, un número y un carácter especial (como @#$%).")));
                }
            }else if ($field == "avatar") {
                if (!filter_var($value, FILTER_VALIDATE_URL) || !preg_match('/^(http|https):\/\/(.*)$/', $value)) {
                    http_response_code(400);
                    exit(json_encode(array("error" => "La URL no es válida")));
                }
            }         
            $update_fields[] = $field . ' = :' . $field;
        }
    }
    //Se une los campos a actualizar en una cadena separada por comas
    $update_query = implode(', ', $update_fields);
    //Si la cadena está vacía, no hay campos para actualizar, por lo que se devuelve un error
    if (empty($update_query)) {
        return array("error" => "No se proporcionaron campos a actualizar.");
    }
    //Se construye la consulta de actualización de la base de datos
    $sql = "UPDATE users SET $update_query WHERE id = :id";
    //Se prepara la consulta para su ejecución
    $statement = $conexion->prepare($sql);
    //Se asigna el valor del ID como un parámetro de la consulta
    $statement->bindValue(":id", $id, PDO::PARAM_INT);
    //Se recorren los campos de la solicitud de actualización
    foreach($request_data as $field => $value) {
        //Si el valor del campo no está vacío, se asigna como parámetro de la consulta
        if (!empty(trim($value))) { 
            $statement->bindValue(":$field", $value);
        }
    }
    //Se ejecuta la consulta y se comprueba si se produjo un error
    if (!$statement->execute()) {
    //Se establece el código de respuesta HTTP en 500 (Error interno del servidor)
        http_response_code(500);
        exit(json_encode(array("error" => "Error al crear el usuario.")));
    }
    //Si la consulta se ejecutó correctamente, se devuelve un array con el ID del usuario actualizado y los campos que se actualizaron
    return array("id" => $id, "updated_fields" => array_keys($request_data));
}


//http://localhost/api-pokermagia/modules/users/infraestructure/userController.php?id=21&role=administrators
function delete($conexion,$id, $role = null) {
    //Verificar que el ID sea un número entero mayor que cero
    if (!is_numeric($id) || $id <= 0) {
        http_response_code(400);
        exit(json_encode(array("error" => "El ID $id debe ser un número entero mayor que cero")));
    }
    //Verificar que el usuario que solicita la eliminación tenga rol de administrador
    if ($role !=="administrators") {
        http_response_code(400);
        exit(json_encode(array("error" => "Solo un usuario con rol de administrador puede borrar usuarios.")));
    }
    //Preparar la consulta SQL para eliminar al usuario con el ID proporcionado
    $sql = "DELETE FROM users WHERE id = :id";
    $statement = $conexion->prepare($sql);
    $statement->bindValue(":id", $id, PDO::PARAM_INT);

    //Ejecutar la consulta SQL
    if (!$statement->execute()) {
        http_response_code(500);
        exit(json_encode(array("error" => "Error al ejecutar la consulta.")));
    }
    //Verificar que se haya eliminado al menos un registro
    if ($statement->rowCount() == 0) {
        http_response_code(404);
        exit(json_encode(array("error" => "El usuario con ID $id no existe.")));
    }
    //Devolver el ID del usuario eliminado
    return array("id" => $id);
}