<?php
class ConexionDB{
    private static $instance = null;
    private $host;
    private $user;
    private $password;
    private $database;
    private $conexion;

    private function __construct($host, $user, $password, $database){
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->conexion = null;
    }

    public static function getInstance($host, $user, $password, $database){
        if(self::$instance==null){
            self::$instance = new ConexionDB($host, $user, $password, $database);
        }
        return self::$instance;
    }
    
    public function conectar() {
        $this->conexion = mysqli_connect($this->host, $this->user, $this->password,  $this->database);
        if(!$this->conexion){
            die("Error al conectar a la base de datos");
        }else {
            echo "Conexion exitosa";
        }
    }

    public function desconectar() {
        mysqli_close($this->conexion);
    }
    
} 
?>