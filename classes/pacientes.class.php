<?php 
    require_once("connection/connect.php");
    require_once("respuestas.class.php");
    
    class pacientes extends connect{

        private $table = "pacientes"; 
        private $pacienteid = "";
        private $dni = "";
        private $nombre = "";
        private $direccion = "";
        private $codigoPostal = "";
        private $genero = "";
        private $telefono = "";
        private $fechaNacimiento = "0000-00-00"; 
        private $correo = "";
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
            $_respuestas = new respuestas;
            $datos = json_decode($json,true);

            if(!isset($datos['token'])){
                return $_respuestas->error_401();
            }else{
                $this->token = $datos['token'];
                $arrayToken = $this->buscarToken();
                if($arrayToken){

                     if(!isset($datos['nombre']) || !isset($datos['dni']) || !isset($datos['correo'])){
                         return $_respuestas->error_400();
                     }else{
                         $this->nombre = $datos['nombre'];
                         $this->dni = $datos['dni'];
                         $this->correo = $datos['correo'];
                         if(isset($datos['telefono'])) { $this->telefono = $datos['telefono']; }
                         if(isset($datos['direccion'])) { $this->direccion = $datos['direccion']; }
                         if(isset($datos['codigoPostal'])) { $this->codigoPostal = $datos['codigoPostal']; }
                         if(isset($datos['genero'])) { $this->genero = $datos['genero']; }
                         if(isset($datos['fechaNacimiento'])) { $this->fechaNacimiento = $datos['fechaNacimiento']; }
                         $resp = $this->insertarPaciente();
                         if($resp){
                             $respuesta = $_respuestas->response;
                             $respuesta["result"] = array(
                                 "pacienteId" => $resp
                             );
                             return $respuesta;
                         }else{
                             return $_respuestas->error_500();
                         }
                     }

                }else{
                    return $_respuestas->error_401("El Token que envió es invalido o ha caducado");
                }
            }

        }

        private function insertarPaciente(){
            $query = "INSERT INTO " . $this->table . " (DNI,Nombre,Direccion,CodigoPostal,Telefono,Genero,FechaNacimiento,Correo)
            VALUES 
            ('" . $this->dni . "','" . $this->nombre . "','" . $this->direccion . "','" . $this->codigoPostal . "','" . $this->telefono . "','" . $this->genero . "','" . $this->fechaNacimiento . "','" . $this->correo . "')";
            $resp = parent::nonQueryId($query);
            if($resp){
                return $resp;
            }else{
                return 0;
            }
        }

        public function put($json){
            $_respuestas = new respuestas;
            $datos = json_decode($json,true);

            if(!isset($datos['token'])){
                return $_respuestas->error_401();
            }else{
                $this->token = $datos['token'];
                $arrayToken = $this->buscarToken();
                if($arrayToken){
                    if(!isset($datos['pacienteId'])){
                        return $_respuestas->error_400();
                    }else{
                        $this->pacienteid = $datos['pacienteId'];
                        if(isset($datos['nombre'])) { $this->nombre = $datos['nombre']; }
                        if(isset($datos['dni'])) { $this->dni = $datos['dni']; }
                        if(isset($datos['correo'])) { $this->correo = $datos['correo']; }
                        if(isset($datos['telefono'])) { $this->telefono = $datos['telefono']; }
                        if(isset($datos['direccion'])) { $this->direccion = $datos['direccion']; }
                        if(isset($datos['codigoPostal'])) { $this->codigoPostal = $datos['codigoPostal']; }
                        if(isset($datos['genero'])) { $this->genero = $datos['genero']; }
                        if(isset($datos['fechaNacimiento'])) { $this->fechaNacimiento = $datos['fechaNacimiento']; }
                        $resp = $this->modificarPaciente();
                        if($resp){
                            $respuesta = $_respuestas->response;
                            $respuesta["result"] = array(
                                "pacienteId" => $this->pacienteid
                            );
                            return $respuesta;
                        }else{
                            return $_respuestas->error_500();
                        }
                    }
                }else{
                    return $_respuestas->error_401("El Token que envió es invalido o ha caducado");
                }
            }

        }

        private function modificarPaciente(){
            $query = "UPDATE " . $this->table . " SET DNI ='" . $this->dni . "',Nombre ='" . $this->nombre . "',Direccion='" . $this->direccion . "',CodigoPostal='" . $this->codigoPostal . "',
            Telefono='" . $this->telefono . "',Genero='" . $this->genero . "',FechaNacimiento='" . $this->fechaNacimiento . "',Correo='" . $this->correo . "' WHERE PacienteId='" . $this->pacienteid . "'";
            $resp = parent::nonQuery($query);
            if($resp >= 1){
                return $resp;
            }else{
                return 0;
            }
        }

        public function delete($json){
            $_respuestas = new respuestas;
            $datos = json_decode($json,true);

            if(!isset($datos['token'])){
                return $_respuestas->error_401();
            }else{
                $this->token = $datos['token'];
                $arrayToken = $this->buscarToken();
                if($arrayToken){
                    if(!isset($datos['pacienteId'])){ //Necesitamos el ID de paciente para borrarlo
                        return $_respuestas->error_400();
                    }else{
                        $this->pacienteid = $datos['pacienteId'];
                        
                        $resp = $this->eliminarPaciente();
                        if($resp){
                            $respuesta = $_respuestas->response;
                            $respuesta["result"] = array(
                                "pacienteId" => $this->pacienteid
                            );
                            return $respuesta;
                        }else{
                            return $_respuestas->error_500();
                        }
                    }
                }else{
                    return $_respuestas->error_401("El Token que envió es invalido o ha caducado");
                }
            }
        }

        private function eliminarPaciente(){
            $query = "DELETE FROM " . $this->table . " WHERE PacienteId= '" . $this->pacienteid . "'";
            $resp = parent::nonQuery($query);
            if($resp>=1){ //Nonquery devuelve las filas afectadas, si no ha cambiado nada, devuelve 0
                return $resp;
            }else{
                return 0;
            }
        }

        private function buscarToken(){
            $query = "SELECT TokenId, UsuarioId, Estado FROM usuarios_token" . " WHERE Token= '" . $this->token . "' AND Estado = 'Activo'";
            $resp = parent::obtenerDatos($query);
            if($resp){
                return $resp;
            }else{
                return 0;
            }
        }

        private function actualizarToken($tokenid){
            $date = date("Y-m-d H:i");
            $query = "UPDATE usuarios_token SET Fecha  = '$date' WHERE TokenId = '$tokenid' ";
            $resp = parent::nonQuery($query);
            if($resp >= 1){
                return $resp;
            }else{
                return 0;
            }
        }

    }
?>