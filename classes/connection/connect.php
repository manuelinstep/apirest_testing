<?php 

class connect {

    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    private $conexion;

    //Esta función inicializa la clase en base a los datos guardados en config
    function __construct(){
        $listadatos = $this->datosConexion();
        foreach ($listadatos as $key => $value) {
            $this->server = $value['server'];
            $this->user = $value['user'];
            $this->password = $value['password'];
            $this->database = $value['database'];
            $this->port = $value['port'];
        }

        $this->conexion = new mysqli($this->server,$this->user,$this->password,$this->database,$this->port);
        if($this->conexion->connect_errno){
            echo "algo salio mal con la conexión";
            die();
        }
    }

    //Esta función obtiene todos los datos necesarios para conectar a la DB
    private function datosConexion(){
        $direccion = dirname(__FILE__);
        $jsondata = file_get_contents($direccion . "/" . "config");
        return json_decode($jsondata,true);
    }

    //Esta función convierte todos los datos recibidos a UTF8
    private function convertirUTF8($array){
        array_walk_recursive($array,function(&$item,$key){
            if(!mb_detect_encoding($item,'utf-8',true)){
                $item = utf8_encode($item);
            }
        });
        return $array;
    }
    
    //Esta función obtiene datos de la DB
    public function obtenerDatos($sqlstr){
        //Se utiliza la instancia de la variable conexión que es una instancia de la clase mysqli
        $results = $this->conexion->query($sqlstr);

        $resultArray = array();
        foreach ($results as $key) {
            $resultArray[] = $key;
        }
        return $this->convertirUTF8($resultArray);
    }

    //Esta función es para querys generales y nos indica cuantos rows han sido afectados
    public function nonQuery($sqlstr){
        $results = $this->conexion->query($sqlstr);
        return $this->conexion->affected_rows;
    }

    //Esta función nos devuelde el ID de lo que se insertó, solo se usará para los INSERT
    public function nonQueryId($sqlstr){
        $results = $this->conexion->query($sqlstr);
        $filas =  $this->conexion->affected_rows;
        if($filas >= 1){
            return $this->conexion->insert_id;
        }else{
            return 0;
        }
    }
}

?>