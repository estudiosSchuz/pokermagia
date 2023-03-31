<?php
    include_once('../domain/userService.php');
    
    $resp = edit($arr);
    echo json_encode($resp);