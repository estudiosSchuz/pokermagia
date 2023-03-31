<?php
function get($id) {
    $arr = array(
        array(
            'id' => '1',
            'email' => 'test@gmail.com',
            'name' => 'Ivan',
            'lastName' => 'Calderon',
            'password' => '1234',
            'role' => 'admin',
            'avatar' => 'pokemon',
        ),
    );
    return json_encode($arr);
}


function create() {
    $arr = array(
        array(
            'id' => '1',
            'email' => 'test@gmail.com',
            'name' => 'Ivan',
            'lastName' => 'Calderon',
            'password' => '1234',
            'role' => 'admin',
            'avatar' => 'pokemon',
        ),
        array(
            'id' => '2',
            'email' => 'test@gmail.com',
            'name' => 'Ivan',
            'lastName' => 'Calderon',
            'password' => '1234',
            'role' => 'admin',
            'avatar' => 'pokemon',
        )
    );
    $nuevoDato = json_decode(file_get_contents('php://input'), true);

    if(!empty($nuevoDato)){
        $ultimoId = end($arr)['id'];
        $nuevoDato['id'] = $ultimoId + 1;
        $arr[] = $nuevoDato;
        return $arr;
    }else {
        http_response_code(402);
        echo "Faltan datos";
    }
}

$arr = array(
    array(
        'id' => '1',
        'email' => 'test1@gmail.com',
        'name' => 'Ivan',
        'lastName' => 'Calderon',
        'password' => '1234',
        'role' => 'admin',
        'avatar' => 'pokemon',
    ),
    array(
        'id' => '2',
        'email' => 'test2@gmail.com',
        'name' => 'Ivan',
        'lastName' => 'Calderon',
        'password' => '1234',
        'role' => 'admin',
        'avatar' => 'pokemon',
    ),
    array(
        'id' => '3',
        'email' => 'test3@gmail.com',
        'name' => 'Ivan',
        'lastName' => 'Calderon',
        'password' => '1234',
        'role' => 'admin',
        'avatar' => 'pokemon',
    )
);

function edit($arr){
    $datoActualizado = json_decode(file_get_contents('php://input'), true);
    foreach ($arr as $key => $array){
        if($array['id'] == $datoActualizado['id']){
            $arr[$key]['email'] = $datoActualizado['email'];
            $arr[$key]['name'] = $datoActualizado['name'];
            $arr[$key]['lastName'] = $datoActualizado['lastName'];
            $arr[$key]['password'] = $datoActualizado['password'];
            $arr[$key]['role'] = $datoActualizado['role'];
            $arr[$key]['avatar'] = $datoActualizado['avatar'];
            return $arr;
        }
    }
    return null;
}


function delete($id){
    $arr = array(
        array(
            'id' => '1',
            'email' => 'test1@gmail.com',
            'name' => 'Ivan',
            'lastName' => 'Calderon',
            'password' => '1234',
            'role' => 'admin',
            'avatar' => 'pokemon',
        ),
        array(
            'id' => '2',
            'email' => 'test2@gmail.com',
            'name' => 'Ivan',
            'lastName' => 'Calderon',
            'password' => '1234',
            'role' => 'admin',
            'avatar' => 'pokemon',
        ),
        array(
            'id' => '3',
            'email' => 'test3@gmail.com',
            'name' => 'Ivan',
            'lastName' => 'Calderon',
            'password' => '1234',
            'role' => 'admin',
            'avatar' => 'pokemon',
        )
    );
    foreach ($arr as $key => $array){
        if($array['id'] == $id){
            unset($arr[$key]);
            $arr = array_values($arr);
            return $arr;
        }
    }
    return null;
}