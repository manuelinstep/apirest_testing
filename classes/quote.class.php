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

                    //Estos son todos los campos obligatorios
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

                    $moneda = $datos['moneda'];

                    $dataValida = [
                        '6029' => $datos['fecha_salida'], 
                        '6030' => $datos['fecha_llegada'],
                        '6032' => $datos['referencia'],
                        '6022' => $datos['id_plan'],
                        '6028' => $datos['pais_destino'],
                        '6027' => $datos['pais_origen'],
                        '6034' => $datos['moneda'],
                        '1022' => $datos['tasa_cambio'],
                        '6026' => $datos['pasajeros'],//Hay que verificar que sea numérico
                        '5005' => $datos['nacimientos'],
                        '4006' => $datos['documentos'],
                        '4005' => $datos['nombres'],
                        '4007' => $datos['apellidos'],
                        '6025' => $datos['telefonos'],
                        '4011' => $datos['correos'],
                        '6021' => $datos['condiciones_med'],
                        '6035' => $datos['emision'],
                        '6021' => $datos['lenguaje'],
                        //Campos de verificaciones varias
                        '4029'	=> (empty($datos['pasajeros']) or $datos['pasajeros'] == 0 or !is_numeric($datos['pasajeros'])) ? 0 : 1,
                        '9012'	=> ($datos['emision'] < 1 || !is_numeric($datos['emision']) || $datos['emision'] > 2) ? 0 : 1,
                        '1022'	=> (!$this->selectDynamic('', 'currency', "value_iso='$moneda'", array("desc_small"))) ? 0 : 1,
                        '2001'	=> $this->checkDates($datos['fecha_salida']),
			            '2002'	=> $this->checkDates($datos['fecha_llegada']),
                        '9059'	=> $this->verifyOrigin($datos['pais_origen']),
                        '1080'	=> ($datos['pais_destino'] == "1" or $datos['pais_destino'] == "2" or $datos['pais_destino'] == "9") ? 1 : 0,
                        '1030'	=> $this->validLanguage($datos['lenguaje']),
                        '4029'	=> (empty($datos['pasajeros']) or $datos['pasajeros'] == 0 or !is_numeric($datos['pasajeros'])) ? 0 : 1,
                        //Verificamos que exista la cantidad requerida
                        '9049'	=> ($this->countData($datos['nombres'], $datos['pasajeros'])) ? 0 : 1,
                        '9053'	=> ($this->countData($datos['apellidos'], $datos['pasajeros'])) ? 0 : 1,
                        '9051'	=> ($this->countData($datos['nacimientos'], $datos['pasajeros'])) ? 0 : 1,
                        '9050'	=> ($this->countData($datos['documentos'], $datos['pasajeros'])) ? 0 : 1,
                        '9052'	=> ($this->countData($datos['correos'], $datos['pasajeros'])) ? 0 : 1,
                        '9054'	=> ($this->countData($datos['telefonos'], $datos['pasajeros'])) ? 0 : 1,
                        '9055'	=> ($this->countData($datos['condiciones_med'], $datos['pasajeros'])) ? 0 : 1
                    ]; //Verificación lista
                    /**
                     * También hay que contar la cantidad con respecto
                     * al numero de pasajeros (campo pasajeros)
                     * verificar que el campo pasajeros sea un numero
                     */

                    $validatEmpty			= $this->validatEmpty($dataValida);
                    if ($validatEmpty) {
                        return $validatEmpty;
                    }  
                    
                    //Verificamos que los datos del pasajero sean validos (VERIFICACIÓN CORRECTA)

                    $validateDataPassenger	= $this->validateDataPassenger($datos['pasajeros'], $datos['nombres'], $datos['apellidos'], $datos['nacimientos'], $datos['documentos'], $datos['correos'], $datos['telefonos'], $datos['condiciones_med']);
                    if ($validateDataPassenger) {
                        return $validateDataPassenger;
                    }

                    $response = 'pasó';
                    return $response;
                    /**
                     * Lo mas probable es que esta sea la función mas larga
                     * pero una vez terminada, es un copiar y pegar
                     * solo hay que fixear un par de cosas luego de eso
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

        private function validatEmpty($parametros)
        {   
            $_response = new response;
            $array_keys =
                array_keys(
                    array_filter($parametros, function ($key) {
                        if (is_array($key)) {
                            foreach ($key as $value) {
                                return empty($value);
                            }
                        } else {
                            return empty($key);
                        }
                    })
                );

            return (!empty($array_keys[0])) ? $_response->getError($array_keys[0]) : false;
            /**
             * Iba a añadir una verificación por si el valor pasado era un array
             * pero ya esta cubierto
             * que excelente servicio
             * 
             * Comprobado, funciona
             */
        }

        private function countData($quantity, $vsquantity)
        {
            return (count($quantity) != $vsquantity) ? true : false;
        }

        private function checkDates($date)
        {
            if (is_array($date)) {
                foreach ($date as $value) {
                    $date   = explode('/', $value);
                    return (checkdate($date[1], $date[0], $date[2]));
                }
            } else {
                $date   = explode('/', $date);
                return (checkdate($date[1], $date[0], $date[2]));
            }
        }

        private function verifyOrigin($origin)
        {
            $response = $this->selectDynamic('', 'countries', "iso_country='$origin'");
            return ($response) ? true : false;
        }

        private function validLanguage($lng)
        {

            $lng   = strtolower($lng);
            $query = "SELECT
                languages.id
            FROM
                `languages`
            WHERE
                languages.active = '1'
            AND languages.lg_id = '$lng'";
            $response   = $this->selectDynamic('', '', '', '', $query);
            return count($response);
        }

        private function validateDataPassenger($quantity, $namePassenger, $lastNamePassenger, $birthDayPassenger, $documentPassenger, $emailPassenger, $phonePassenger, $medicalConditionsPassenger, $skipBirthDay = true, $report_sales)
        {     
            $_respuesta = new response;
            $dataBithDay    = [];
            $dataValidate   = [
                '4005'  => count($namePassenger),
                '4007'  => count($lastNamePassenger),
                '4006'  => count($documentPassenger),
                //'5012'  => count($emailPassenger),
                //'4008'  => count($phonePassenger),
                '5012'  => (empty(count($emailPassenger)) && ($report_sales != 'report_sales')) ? 0 : 1,
                '4008'  => (empty(count($phonePassenger)) && ($report_sales != 'report_sales')) ? 0 : 1,
                //'5006'  => count($medicalConditionsPassenger) //,
                // '4011'    => (!$this->verifyMail($emailPassenger)) ? 0 : 1
            ];

            if ($skipBirthDay) {
                $dataBithDay    = [
                    '5005'  => $birthDayPassenger
                ];
            }
            $dataValidate   = $dataValidate + $dataBithDay;
            $validatEmpty   = $this->validatEmpty($dataValidate);
            if ($validatEmpty) {
                return $validatEmpty;
            }
            

            for ($i = 0; $i < $quantity; $i++) {

                if (empty($namePassenger[$i])) {
                    return $_respuesta->getError('4005');
                }
                if (empty($lastNamePassenger[$i])) {
                    if ($report_sales == 'report_sales') {
                        $arraySplitName = $this->splitNamePassenger($namePassenger[$i]);
                        $lastNamePassenger[$i] = $arraySplitName['apellidos'];
                        $ArraylastNamePassenger[] = $lastNamePassenger[$i];
                        $namePassenger[$i]  =  $arraySplitName['nombres'];
                        $ArraynamePassenger[] = $namePassenger[$i];
                    } else {
                        return $_respuesta->getError('4007');
                    }
                }
                if (empty($documentPassenger[$i])) {
                    return $_respuesta->getError('4006');
                }
                if (empty($phonePassenger[$i])) {

                    if ($report_sales == 'report_sales') {
                        $phonePassenger[$i] = 'NA';
                        $ArrayphonePassenger[] = $phonePassenger[$i];
                    } else {
                        return $_respuesta->getError('6025');
                    }
                }
                /*if (empty($medicalConditionsPassenger[$i])) {
                    return $_respuesta->getError('5006');
                }*/

                if (!preg_match('(^([a-zA-Z ÑñÁ-ú.]{2,60})$)', html_entity_decode($namePassenger[$i], ENT_QUOTES, "UTF-8"))) {

                    return $_respuesta->getError('9032');
                }
                if (!preg_match('(^([a-zA-Z ÑñÁ-ú.]{2,60})$)', html_entity_decode($lastNamePassenger[$i], ENT_QUOTES, "UTF-8"))) {
                    return $_respuesta->getError('9035');
                }

                if (!is_numeric($phonePassenger[$i])) {

                    if ($report_sales != 'report_sales') {
                        return $_respuesta->getError('9034');
                    } else {
                        if ((!preg_match('(^([NA0-9() +/-]{2,40})$)', strtoupper($phonePassenger[$i])))) {
                            return $_respuesta->getError('9198');
                        }
                    }
                }


                if ($report_sales == 'report_sales') {

                    if (empty($emailPassenger[$i])) {
                        $emailPassenger[$i] = 'NA';
                        $ArrayemailPassenger[] = $emailPassenger[$i];
                    } else {

                        if ((strtoupper($emailPassenger[$i]) != 'NA') && (strtoupper($emailPassenger[$i]) != 'N/A')) {
                            if (!$this->verifyMail($emailPassenger[$i])) {
                                return $_respuesta->getError('4011');
                            }
                        }
                    }
                } else {
                    if (!$this->verifyMail($emailPassenger[$i])) {
                        return $_respuesta->getError('4011');
                    }
                }


                $today = date('Y-m-d');
                $birthDayPassengerTrans[$i] = $this->transformerDate($birthDayPassenger[$i]);
                if ($skipBirthDay) {
                    if (!($this->checkDates($birthDayPassenger[$i])) || (strtotime($birthDayPassengerTrans[$i]) > strtotime($today))) {
                        return $_respuesta->getError('1062');
                    }
                }
            }
            if ($report_sales == 'report_sales') {
                $data = [
                    'lastNamePassenger' => $ArraylastNamePassenger,
                    'namePassenger'     => $ArraynamePassenger,
                    'phonePassenger'    => $ArrayphonePassenger,
                    'emailPassenger'    => $ArrayemailPassenger

                ];

                return $data;
            }
        }

        public function splitNamePassenger($namePassenger)
        {
            $tokens = explode(' ', trim($namePassenger));
            $names = array();
            $special_tokens = array('da', 'de', 'del', 'la', 'las', 'los', 'mac', 'mc', 'van', 'von', 'y', 'i', 'san', 'santa', 'jr.');
            $prev = "";
            foreach ($tokens as $token) {
                $_token = strtolower($token);
                if (in_array($_token, $special_tokens)) {
                    $prev .= "$token ";
                } else {
                    $names[] = $prev . $token;
                    $prev = "";
                }
            }

            $num_nombres = count($names);
            $nombres = $apellidos = "";

            switch ($num_nombres) {

                case 1:
                    $nombres   = $names[0];
                    $apellidos = "No indica apellido";

                    break;
                case 2:
                    $nombres    = $names[0];
                    $apellidos  = $names[1];

                    break;
                case 3:
                    $nombres     = $names[0] . ' ' . $names[1];
                    $apellidos   = $names[2];

                    break;
                case 4:
                    $nombres     = $names[0] . ' ' . $names[1];
                    $apellidos   = $names[2] . ' ' . $names[3];

                    break;
                case 5:
                    $nombres     = $names[0] . ' ' . $names[1] . ' ' . $names[2];
                    $apellidos   = $names[3] . ' ' . $names[4];

                    break;
                default:
                    $nombres = $names[0] . ' ' . $names[1] . ' ' . $names[2];
                    unset($names[0]);
                    unset($names[1]);
                    unset($names[2]);
                    $apellidos = implode(' ', $names);


                    break;
            }

            $nombres    = mb_convert_case($nombres, MB_CASE_TITLE, 'UTF-8');
            $apellidos  = mb_convert_case($apellidos, MB_CASE_TITLE, 'UTF-8');


            $data       = [
                'nombres'      => $nombres,
                'apellidos'     => $apellidos
            ];

            return $data;
        }

        public function verifyMail($parametros = array())
        {
            if (is_array($parametros)) {
                return array_reduce($parametros, function ($resp, $value) {
                    return $resp = filter_var($value, FILTER_VALIDATE_EMAIL) ? $resp : false;
                }, true);
            } else {
                return filter_var($parametros, FILTER_VALIDATE_EMAIL);
            }
        }

        public function transformerDate($date, $type = 1)
        {
            if ($type == '1') {
                $date   = str_replace('/', '-', $date);
                $fecha  = DateTime::createFromFormat('d-m-Y', $date);
                return $fecha ? $fecha->format('Y-m-d') : $date;
            } elseif ($type == '2') {
                $fecha  = DateTime::createFromFormat('Y-m-d', $date);
                return $fecha ? $fecha->format('d/m/Y') : $date;
            }
        }
    }

?>