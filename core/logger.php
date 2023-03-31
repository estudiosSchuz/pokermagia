<?php
require_once('db.php');

$conexionDB = ConexionDB::getInstance('localhost', 'root', '', 'pokermagia');
$conexionDB->conectar();