<?php
//http://localhost/api-pokermagia/core/db.php

class ConexionDB {
    private static $instance = null;
    private $host;
    private $user;
    private $password;
    private $database;
    private $conexion;

    private function __construct($host, $user, $password, $database) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->conexion = null;
    }

    public static function getInstance($host, $user, $password, $database) {
        if (self::$instance == null) {
            self::$instance = new ConexionDB($host, $user, $password, $database);
        }
        return self::$instance;
    }
    
    public function conectar() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8";
            $this->conexion = new PDO($dsn, $this->user, $this->password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error al conectar a la base de datos: " . $e->getMessage());
        }
        return $this->conexion;
    }

    public function desconectar() {
        $this->conexion = null;
    }
}
?>