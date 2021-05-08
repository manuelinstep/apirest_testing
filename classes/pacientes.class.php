<?php 
    require_once("connection/connect.php");
    require_once("respuestas.class.php");
    
    class pacientes extends connect{

        private $table = "pacientes"; 
        //La pagina nos indica de que a que registros se van a mostrar 
        public function listaPacientes($pagina = 1){
            $inicio = 0; //Para saber por que registro comenzar
            $cantidad = 100; //registros para mostrar
            if($pagina > 1){
                $inicio = ($cantidad *($pagina - 1)) +1;
                $cantidad = $cantidad * $pagina;
            }

            $query = "SELECT PacienteId,Nombre,DNI,Telefono,Correo FROM " . $this->table . " limit $inicio,$cantidad";
            $datos = parent::obtenerDatos($query);
            return ($datos);
        }

        public function obtenerPaciente($id){
            $query = "SELECT * FROM " . $this->table . " WHERE PacienteId = '$id'";
            return parent::obtenerDatos($query);
        }

        public function post($json){
            
        }
    }
?>