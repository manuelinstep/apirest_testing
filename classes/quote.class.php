<?php 
    require_once("response.class.php");
    //Ayyy lmao yo no había borrado esto
    /*

    METODOS MAS IMPORTANTES POR AGREGAR:
    -Función AÑADIR ORDEN
    -Función REPORTAR ORDEN
    -Función REPORTAR ORDEN MASTER
    -Función AÑADIR UPGRADE
    -Función SOLICITUD DE ANULACIÓN
    -Función SOLICITAR CANCELACION DE UPGRADE 
    
    CAMBIOS A ORDENES AGREGADAS/REPORTADAS:
    -Función COMPROBAR PRE-ORDEN
    -Función SOLICITAR CAMBIOS
    -Función CAMBIOS EN ORDENES REPORTADAS

    Adicionales:
    -Función AGREGAR SUSCRIPCION --------------
    -Función REPORTAR SUSCRIPCIÓN --------------
    -Función CAMBIOS EN SUSCRIPCION --------------
    -Función EXTENDER SUSBCRIPCION --------------
    -Función CANCELAR SUSCRIPCION --------------

    IMPORTANTE IMPLEMENTAR:
    *trans_all_webservices: se registra todo el movimiento de la DB, principalmente los datos que introdujo el usuario
    y la respuesta que dio el servidor
    *log_consultas: registra todas las consultas que llegan a la DB, referentes a inserts, updates y deletes principalmente

    */
    class quote extends cls_dbtools //Se omite temporalmente la clase Model ya que no representa una necesidad inmediata
    {   
        public function put($data){
            //Recibimos los datos, empieza el jogo bonito
            $_respuesta = new response;
            
            $datos = json_decode($data,true);
            $request = $datos['request'];
            switch ($request) {
                case 'add_order':
                    # code...
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
        public function selectDynamic($filters, $table = string, $where = '1', $fields, $querys = '', $die = false, $limit = 6)
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

        public function insertDynamic($data=Array(), $table=null){
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

        public function updateDynamic($table,$field,$fieldwere,$data,$and){
        
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
    }
    
?>