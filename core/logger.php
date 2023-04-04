<?php
require_once('db.php');
//http://localhost/api-pokermagia/core/logger.php

$conexionDB = ConexionDB::getInstance('localhost', 'root', '', 'pokermagia');
$conexion = $conexionDB->conectar();