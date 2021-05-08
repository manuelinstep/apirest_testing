<?php 
    require_once("connection/connect.php");
    require_once("respuestas.class.php");
    
    class auth extends connect{

        //Creamos el método login
        public function login($json){
            $_respuestas = new respuestas;
            $datos = json_decode($json,true); //el true convierte el array en asociativo
            //Checkea que existan los campos user y password
            if(!isset($datos['usuario']) || !isset($datos['password'])){
                //Error ya que no existen
                return $_respuestas->error_400();
            }else{
                $usuario = $datos['usuario'];
                $password = $datos['password'];
                //Todo esto se debe documentar correctamente
                $datos = $this->obtenerDatosUsuario($usuario);
                if($datos){
                    //Si existe el usuario, creamos el token de autenticación
                }else{
                    //no existe el usuario
                    return $_respuestas->error_200("El usuario $usuario no existe");
                }
            }
        }

        private function obtenerDatosUsuario($correo){
            $query = "SELECT UsuarioId,Password,Estado FROM usuarios WHERE Usuario = '$correo'";
            $datos = parent::obtenerDatos($query);
            if(isset($datos[0]['UsuarioId'])){
                return $datos;
            }else{
                return 0;
            }
        }

    }
?>