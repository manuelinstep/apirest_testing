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
                $password = parent::encrypt($password);
                $datos = $this->obtenerDatosUsuario($usuario);
                if($datos){
                    //Si existe el usuario, creamos el token de autenticación
                    //Verificamos que la contraseña sea correcta
                    //Esta contraseña debe estar encriptada
                    if($password == $datos[0]['Password']){
                        //Verificamos que el usuario este activo
                        if($datos[0]['Estado'] == "Activo"){
                            //Creamos el token
                            $verificar = $this->insertarToken($datos[0]['UsuarioId']);
                            //Revisamos si se guardó
                            if($verificar){

                                $result = $_respuestas->response;
                                $result['result'] = array(
                                    "token" => $verificar   
                                );
                                return $result;
                            }else{
                                return $_respuestas->error_500();
                            }
                        }else{
                            return $_respuestas->error_200("El usuario esta inactivo");    
                        }
                    }else{
                        return $_respuestas->error_200("El password es invalido");    
                    }

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

        //Método para crear e insertar el token
        private function insertarToken($userId){
            $val = true;
            $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
            $date = date("Y-m-d H:i");
            $estado = "Activo";
            $query = "INSERT INTO usuarios_token (UsuarioId,Token,Estado,Fecha) VALUES ('$userId','$token','$estado','$date')";
            $verificar = parent::nonQuery($query);
            if($verificar){
                return $token;
            }else{
                return 0;
            }
        }

    }
?>