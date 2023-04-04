<?php
    include_once('../domain/userService.php');
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get user id
        if (isset($_GET['id'])) {
            $id = htmlspecialchars($_GET['id']);
            $jsonDataId = getId($id);
            echo $jsonDataId;
        } else {
        // Get user
            $jsonData = get();
            echo $jsonData;
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Crear users
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
    }elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        // Actualizar users
        $id = htmlspecialchars($_GET['id']);
        $request_body = file_get_contents('php://input');
        $resultado = edit($id, $request_body);
        if (isset($resultado["error"])) {
            echo json_encode(array("error" => $resultado["error"]));
        } else {
            echo json_encode(array("id" => $resultado["id"]));
        }
    }elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Elimina un usuario por su id
        $id = htmlspecialchars($_GET['id']);
        $resultado = delete($id);
        echo $resultado;
        if (isset($resultado["error"])) {
            echo json_encode(array("error" => $resultado["error"]));
        } else {
            echo json_encode(array("id" => $resultado["id"]));
        }
    }else {
        // Si la petición no es ni GET ni POST, devolver un mensaje de error
        http_response_code(405);
        echo json_encode(array("error" => "Método no permitido"));
    }
?>
