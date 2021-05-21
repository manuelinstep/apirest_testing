<?php 
    require_once("response.class.php");
    //Ayyy lmao yo no había borrado esto
    /** 
     *
     *METODOS MAS IMPORTANTES POR AGREGAR:
     *-Función AÑADIR ORDEN
     *-Función REPORTAR ORDEN
     *-Función REPORTAR ORDEN MASTER
     *-Función AÑADIR UPGRADE
     *-Función SOLICITUD DE ANULACIÓN
     *-Función SOLICITAR CANCELACION DE UPGRADE 
     *
     *CAMBIOS A ORDENES AGREGADAS/REPORTADAS:
     *-Función COMPROBAR PRE-ORDEN
     *-Función SOLICITAR CAMBIOS
     *-Función CAMBIOS EN ORDENES REPORTADAS
     *
     *Adicionales:
     *-Función AGREGAR SUSCRIPCION --------------
     *-Función REPORTAR SUSCRIPCIÓN --------------
     *-Función CAMBIOS EN SUSCRIPCION --------------
     *-Función EXTENDER SUSBCRIPCION --------------
     *-Función CANCELAR SUSCRIPCION --------------
     *
     *IMPORTANTE IMPLEMENTAR:
     *trans_all_webservices: se registra todo el movimiento de la DB, principalmente los datos que introdujo el usuario
     *y la respuesta que dio el servidor
     *log_consultas: registra todas las consultas que llegan a la DB, referentes a inserts, updates y deletes principalmente
     *
    */
    class quote extends cls_dbtools //Se omite temporalmente la clase Model ya que no representa una necesidad inmediata
    {   
        public function handle($data){
            //Recibimos los datos, empieza el jogo bonito
            $_respuesta = new response;
            
            $datos = json_decode($data,true);
            $request = $datos['request'];
            switch ($request) {
                case 'add_order':
                    /**
                     * Primero debemos verificar que campos se reciben
                     * luego verificar que no estén vacios
                     * luego verificar que sean correctos
                     * y en ese punto, proceder a guardar la orden
                     * 
                     * Token (el de southpark) (string) (Obligatorio)
                     * f salida (string) (Obligatorio)
                     * f llegada (string) (Obligatorio)
                     * referencia (string) (Obligatorio)
                     * id_plan (string) (Obligatorio)
                     * pais_destino (string) (Obligatorio)
                     * pais_origen (string) (Obligatorio)
                     * moneda (string) (Obligatorio)
                     * tasa_cambio (string) (Obligatorio)
                     * pasajeros (string) (Obligatorio)
                     * nacimientos (JSON) (Obligatorio)
                     * upgrade (JSON) 
                     * documentos (JSON) (Obligatorio)
                     * nombres (JSON) (Obligatorio)
                     * apellidos (JSON) (Obligatorio)
                     * telefonos (JSON) (Obligatorio)
                     * correos (JSON) (Obligatorio)
                     * observaciones médicas (JSON) (Obligatorio)
                     * nombre_contacto (string) 
                     * telefono_contacto (string)
                     * email_contacto (string)
                     * consideraciones_generales (string)
                     * emision (string) (Obligatorio)
                     * lenguaje (string) (Obligatorio) 
                     * 
                     * primero verificamos que los campos obligatorios no estan vacios
                     */


                    break;
                
                case 'report_order':
                    # code...
                    break;
                
                case 'report_order_master':
                    # code...
                    break;

                case 'add_upgrade':
                    # code...
                    break;
                
                case 'request_cancellation':
                    # code...
                    break;

                case 'request_upgrade_cancellation':
                    # code...
                    break;

                default:
                    # code...
                    break;
            }
        }
        private function selectDynamic($filters, $table = string, $where = '1', $fields, $querys = '', $die = false, $limit = 6)
        {
            /**
             * Este código viene del anterior webservices
             * Funciona, asi que no debería haber problema implementandolo
             * Lo único que aún no entiendo del todo es como funciona el apartado filters, pero de momento
             * no se usará, buscare un ejemplo en el anterior webservices para cersiorarme de que es lo que sucede
             */
            if (empty($querys)) {
                $fields = !empty($fields) ? implode(',', $fields) : "*";
                $query = "SELECT $fields FROM $table WHERE $where ";
                foreach ($filters as $campo => $value) {
                    if (!empty($campo) && !empty($value)) {
                        $valor   = addslashes($value);
                        $valor   = (is_array($value)) ? implode(',', $value) : "'$valor'";
                        $query  .= " AND $campo IN ($valor) ";
                    }
                }
                $query .= " LIMIT $limit ";
            } else {

                $query = $querys;
            }
            if ($die) {
                die($query);
            }
            //return parent::obtenerDatos($query);
            return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
        }

        private function insertDynamic($data=Array(), $table=null){
            if (empty($table) || count($data) == 0) {
                return false;
            }
            $arrFiels       = [];
            $arrValues      = [];
            $SQL_functions  = [
                'NOW()'
            ];
    
            foreach ($data as $key => $value) {
                $arrFiels[] = '`'. $key .'`';
                if (in_array(strtoupper($value), $SQL_functions)) {
                    $arrValues[] = strtoupper($value);
                } else {
                    $arrValues[] = '\''. $value .'\'';
                }
            }
    
            $query = "INSERT INTO $table (". implode(',', $arrFiels) .") VALUES (". implode(',', $arrValues) .")";
            return $this->_SQL_tool($this->INSERT, __METHOD__,$query);  
        }

        private function updateDynamic($table,$field,$fieldwere,$data,$and){
        
            (!empty($table))    ?: $table;
            (!empty($field))    ?: $field;
            (!empty($fieldwere))?: $fieldwere;
    
            $query="UPDATE $table SET ";
            $cadQuery='';
            foreach ($data as $key => $value) {
                if(!empty($value)){
                    $cadQuery .= $key.'='."'".$value."'".',';
                }
            }
            
            $query .= mb_strrchr($cadQuery,',',true);
            $query .= " WHERE $table.$field = '$fieldwere' ";
            if(!empty($and)){
                $andField=$and['field'];
                $andValue=$and['value'];
                $query .= " AND $andField = '$andValue' ";
            }
            if(!empty($cadQuery)){
            $resp   = $this->_SQL_tool($this->UPDATE, __METHOD__, $query);
            }
            return ($query)?true:false;
        }

        public function checkToken($token){
            $_response = new response;

            $query = "SELECT api_key, ip_remote, id_status, id FROM users WHERE api_key = '$token'";
            $resp = $this->selectDynamic('','','','',$query);
            if($resp){
                return $resp;
            }else{
                return $_response->getError('1005'); // 1005 es el error de "Apikey suministrada es inválida"
            }
        }

        private function checkEmpty($datos){
            /**
             * Este es el momento de brillar del foreach
             */
            foreach ($datos as $field => $value) {
                /**Pasos:
                 * -Revisar que campo está vacio
                 * -Revisar cual sería el error de dicho campo
                 * -Retornar dicho campo
                 * Creo que este proceso ya estaba hecho en el anterior webservices tho
                 */
            }
        }
    }
    
?>