<?php 
    require_once("response.class.php");
    require_once("quote_general_new.class.php");
    require_once("currencylayer.class.php");
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
            $quoteGeneral 					= new quote_general_new;
            $_respuesta = new response;
            $currencyLayer                  = new currencylayer;
            
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
                    /**
                     * Ahora buscamos:
                     * -Datos relacionados al plan
                     * -Datos relacionados a la agencia
                     */
                    $plan = $datos['id_plan'];
                    $dataPlan			= $this->selectDynamic('', 'plans', "id='$plan'", array("id_plan_categoria", "name", "num_pas"));
                    $datAgency			= $this->datAgency($datos['token']); //Debemos pasar el token de autenticacion
                    $idCategoryPlan 	= $dataPlan[0]['id_plan_categoria'];
                    $namePlan			= $dataPlan[0]['name'];
                    $idAgency			= $datAgency[0]['id_broker'];
                    $isoCountry			= $datAgency[0]['id_country'];
                    $nameAgency			= $datAgency[0]['broker'];
                    $userAgency			= $datAgency[0]['user_id'];
                    $cantPassengerPlan	= $dataPlan[0]['num_pas'];
                    $prefix				= $datAgency[0]['prefijo'];
                    $arrivalTrans       = $this->transformerDate($datos['fecha_llegada']);
                    $departureTrans     = $this->transformerDate($datos['fecha_salida']);
                    $daysByPeople 		= $this->betweenDates($departureTrans, $arrivalTrans);

                    /**
                     * Validamos las fechas de la orden
                     */
                    $validateDateOrder	= $this->validateDateOrder($arrivalTrans, $departureTrans, $isoCountry);
                    if ($validateDateOrder) {
                        return $validateDateOrder;
                    }

                    /**
                     * Validamos el plan
                     */
                    $validatePlans		= $this->validatePlans($plan, $idAgency, $datos['pais_origen'], $datos['pais_destino'], $daysByPeople);
                    if ($validatePlans) {
                        return $validatePlans;
                    }

                    //Obtenemos la edad del pasajero y el pais de la agencia
                    $agesPassenger		= $this->setAges($datos['nacimientos'], $isoCountry); 
                    
                    /**
                     * BirthdayPassenger, en el primer parametro, obtiene un array
                     */
		            $countryAgency		= $this->getCountryAgency($datos['token']);
                    $dataQuoteGeneral	= $quoteGeneral->quotePlanbenefis($idCategoryPlan, $daysByPeople, $countryAgency, $datos['pais_destino'], $datos['pais_origen'], $agesPassenger, $datos['fecha_salida'], $datos['fecha_llegada'], $idAgency, $plan);
                    $validatBenefits	= $this->verifyBenefits($dataQuoteGeneral);
                    if ($validatBenefits) {
                        return $validatBenefits;
                    }

                    $cost							= $dataQuoteGeneral[0]['total_costo'];
                    $price							= $dataQuoteGeneral[0]['total'];
                    $familyPlan						= $dataQuoteGeneral[0]['family_plan'];

                    if ($dataQuoteGeneral[0]['banda'] == "si") {
                        for ($i = 0; $i < $dataQuoteGeneral[0]["total_rangos"]; $i++) {
                            $pricePassenger[] 		= $price / $datos['pasajeros'];
                            $costPassenger[]		= $dataQuoteGeneral[0]["costo_banda$i"];
                        }
                    } else {
                        if ($dataQuoteGeneral[0]['numero_menores'] > 0) {
                            for ($i = 0; $i < $dataQuoteGeneral[0]['numero_menores']; $i++) {
                                $pricePassenger[] 	= $dataQuoteGeneral[0]['valorMenor'];
                                $costPassenger[] 	= $dataQuoteGeneral[0]['costoMenor'];
                            }
                        }
                        if ($dataQuoteGeneral[0]['numero_mayores'] > 0) {
                            for ($i = 0; $i < $dataQuoteGeneral[0]['numero_mayores']; $i++) {
                                $pricePassenger[] 	= $dataQuoteGeneral[0]['valorMayor'];
                                $costPassenger[] 	= $dataQuoteGeneral[0]['costoMayor'];
                            }
                        }
                    }

                    //Pasamos 
                    for ($i = 0; $i < $datos['pasajeros']; $i++) {
                        $birthDayPassengerTrans[]	= $this->transformerDate($datos['nacimientos'][$i]);
                    }

                    $verifiedOrderDuplicate 		= $this->verifiedOrderDuplicate($departureTrans, $arrivalTrans, $datos['pais_origen'], $datos['pais_destino']);

                    if (!empty($verifiedOrderDuplicate)) {

                        $Verified_Beneficiaries		= $this->verifiedBeneficiariesDuplicate($verifiedOrderDuplicate, $datos['documentos'], $birthDayPassengerTrans);
                        if ($Verified_Beneficiaries) {
                            return $Verified_Beneficiaries;
                        }
                    }

                    do{
                    $code = $prefix . '-' . $this->valueRandom(6);
                    $verify = $this->selectDynamic('', 'orders', "codigo='$code'", array("codigo"));
                
                    }while(empty($verify) == 0); //devuelve 1 si esta vacio, 0 si contiene algo

                    $exchangeRate = (empty($datos['tasa_cambio']) || $datos['tasa_cambio'] == 1) ? $this->dataExchangeRate($datos['pais_origen']) : $datos['tasa_cambio'];

                    if (empty($exchangeRate[0]['usd_exchange']) && empty($exchangeRate)) {
                        //$exchangeRate = 1;
            
            
                        $exchangeRate = $currencyLayer->exchangeRate($datos['moneda'], date('Y-m-d'));
                        $adjustedExchangeRate = 1;
                        if ($datos['moneda'] != 'USD') {
                            $tasa_cambio_recibida = $exchangeRates;
                        }
                    }   elseif (empty($exchangeRate[0]['usd_exchange']) && !empty($exchangeRate) && is_numeric($exchangeRate)) {

                        $exchangeRate = $exchangeRate;
            
                        $exchangeRateIlsbsys = $this->dataExchangeRate($datos['pais_origen']);
            
            
                        if ($exchangeRateIlsbsys[0]['usd_exchange']) {
            
                            if ($exchangeRate > $exchangeRateIlsbsys[0]['usd_exchange']) {
                                $difExchange =  $exchangeRate - $exchangeRateIlsbsys[0]['usd_exchange'];
                                $difPorcentajeExchange = ROUND((($difExchange * 100) / $exchangeRateIlsbsys[0]['usd_exchange']), 1);
                                if ($difPorcentajeExchange >= $porcentaje) {
                                    $adjustedExchangeRateMax = 1;
                                    $exchangeRateOur = $exchangeRateIlsbsys[0]['usd_exchange'];
                                }
                            }
                        } else {
            
                            if ($coin != 'USD') {
                                $exchangeRateApi = $currencyLayer->exchangeRate($coin, date('Y-m-d'));
                            }
            
                            if ($exchangeRateApi) {
                                if ($exchangeRate > $exchangeRateApi) {
                                    $difExchange =  $exchangeRate - $exchangeRateApi;
                                    $difPorcentajeExchange = ROUND((($difExchange * 100) / $exchangeRateApi), 1);
                                    if ($difPorcentajeExchange >= $porcentaje) {
                                        $adjustedExchangeRateMax = 1;
                                        $exchangeRateOur = $exchangeRateApi;
                                    }
                                }
                            }
                        }
                    } elseif (!empty($exchangeRate[0]['usd_exchange'])) {
                        $tasa_cambio_recibida = $exchangeRates;
                        $exchangeRate = $exchangeRate[0]['usd_exchange'];
                        $adjustedExchangeRate = 1;
                    } else {
                        return $_respuesta->getError('9011');
                    }

                    $language		= ($datos['lenguaje'] == "spa") ? "es" : "en";

                    if (!empty($datos['upgrades'])) {
                        //Aqui ponemos todos los datos recibidos
                        $data	= [
                            "api"				=> $datos['token'],
                            "upgrades"			=> $datos['upgrades'],
                            "codigo"			=> $code,
                            "plan"				=> $datos['id_plan'],
                            "daybypeople"		=> $daysByPeople,
                            "price"				=> $price,
                            "cost"				=> $cost,
                            "numberPassengers"	=> $datos['pasajeros'],
                            "source"			=> false,
                            'beneficiaries'		=> $datos['documentos'],
                            "precio_vta"		=> $pricePassenger,
                            "precio_cost"		=> $costPassenger
                        ];
            
                        $dataUpgrade			= $this->addUpgrades($data, false);
            
                        if (count($dataUpgrade["id"]) == 0) {
                            return $dataUpgrade;
                        } else {
            
                            $price		= $dataUpgrade["price"];
                            $cost		= $dataUpgrade["cost"];
                            $idUpgrade 	= $dataUpgrade["id"];
                        }
                    }

                    //En este punto, revisamos los guarnin

                    return $dataUpgrade;
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

        /**
         * Funciones que ayudan a traer/modificar datos para las funciones del handler
         */
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

        private function splitNamePassenger($namePassenger)
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

        private function verifyMail($parametros = array())
        {
            if (is_array($parametros)) {
                return array_reduce($parametros, function ($resp, $value) {
                    return $resp = filter_var($value, FILTER_VALIDATE_EMAIL) ? $resp : false;
                }, true);
            } else {
                return filter_var($parametros, FILTER_VALIDATE_EMAIL);
            }
        }

        private function transformerDate($date, $type = 1)
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

        private function datAgency($api)
        {
            $query = "SELECT
            users.id AS user_id,
            broker.broker,
            broker.id_broker,
            users.id_country,
            broker.prefijo,
            users.language_id
            FROM
                users
            INNER JOIN user_associate ON users.id = user_associate.id_user
            INNER JOIN broker ON user_associate.id_associate = broker.id_broker
            WHERE
                users.api_key = '$api'";


            return $this->selectDynamic('', '', '', '', $query);
        }

        public function betweenDates($start, $end, $type)
        {   
            $_respuesta = new response;
            $startdate ? date('Y-m-d') : $startdate;
            switch ($type) {
                case 'years':
                    if (is_array($start) || is_array($end)) {
                        foreach ($end as $value) {
                            $query        = "SELECT timestampdiff(YEAR,'$value', '$start') as year";
                            $response     = $this->selectDynamic('', '', '', '', $query)[0]['year'];
                            return ($response < 0) ? $_respuesta->getError('1062') : false;
                        }
                    } else {
                        $query      = "SELECT timestampdiff(YEAR,'$value', '$start') as year";
                        return      $this->selectDynamic('', '', '', '', $query)[0]['year'];
                    }
                    break;
                default:
                    $query = "SELECT DATEDIFF('$end', '$start') + 1 AS dias";
                    return $this->selectDynamic('', '', '', '', $query)[0]['dias'];
                    break;
            }
        }

        public function validateDateOrder($arrival, $departure, $isoCountry, $today)
        {   
            $_respuesta = new response;
            $this->setTimeZone($isoCountry);
            if (!$today) {
                $today  = date('Y-m-d');
            }
            if ($departure < $today or $arrival < $today) {
                return $_respuesta->getError('2004');
                /* } elseif (!$report_sales) {

                if ($arrival == $departure || $departure > $arrival) {
                    return $_respuesta->getError('3030');
                }*/
            } elseif ($departure > $arrival) {
                return $_respuesta->getError('3030');
            }
        }

        public function setTimeZone($isoCountry)
        {
            $timeZone   = $this->selectDynamic('', 'cities', "iso_country='$isoCountry'", array("Timezone"))[0]['Timezone'];
            $timeZone   = !empty($timeZone) ? $timeZone : 'America/Lima';
            ini_set('date.timezone', $timeZone);
        }

        public function validatePlans($plan, $agency, $origin, $destination, $daysByPeople)
        {
            $arrayValidate = [];
            if (!empty($agency)) {
                $arrayValidate[] = $this->verifyRestrictionPlan($agency, $plan);
            }
            if (!empty($destination)) {
                $arrayValidate[] = $this->verifyRestrictionDestination($destination, $plan);
            }
            if (!empty($origin)) {
                $arrayValidate[] = $this->verifyRestrictionOrigin($origin, $plan);
            }
            if (!empty($daysByPeople)) {
                $arrayValidate[] = $this->verifyDaysPlan($daysByPeople, $plan);
            }
            if (!empty($arrayValidate[0])) {
                return  $arrayValidate[0];
            }
        }

        public function verifyRestrictionPlan($agency, $plan, $languaje, $details = false, $api, $simple)
        {   
            $_respuesta = new response;
            $agency     = (!empty($agency)) ? $agency : $this->datAgency($api)[0]['id_broker'];
            $choicePlan = $this->selectDynamic('', 'broker', "id_broker='$agency'", array("opcion_plan"))[0]['opcion_plan'];

            $query = "SELECT
                plans.id ";
            if ($details) {
                $query .= ", plan_detail.titulo,
                    plan_detail.description,
                    plan_detail.language_id,
                    plan_detail.plan_id,
                    plans.id_plan_categoria,
                    plans.num_pas,
                    plans.min_tiempo,
                    plans.max_tiempo,
                    plans.id_currence,
                    plans.family_plan,
                    plans.min_age,
                    plans.max_age,
                    plans.normal_age,
                    plans.plan_local,
                    plans.modo_plan,
                    plans.original_id ";
            }
            $query .= " FROM
                plans
                INNER JOIN plan_detail ON plans.id = plan_detail.plan_id
                INNER JOIN restriction ON plans.id = restriction.id_plans
            ";
            ($details) ?: $where[] = " plans.id = '$plan'";
            (!$details && (empty($languaje))) ?: $where[] = " plan_detail.language_id = '$languaje'";
            $where[] = " plans.activo = '1' ";
            $where[] = " plans.eliminado = '1' ";
            $where[] = "(
                plans.modo_plan = 'W'
                OR plans.modo_plan = 'T'
            )";
            if ($choicePlan == '1') {
                $where[] =
                    "(
                    restriction.dirigido = 1
                    OR (restriction.dirigido = 2 AND restriction.id_broker = $agency)
                    OR (restriction.dirigido = 6 AND restriction.id_broker = $agency)
                )";
            } else if ($choicePlan == '2') {
                $where[] =
                    "(
                    (restriction.dirigido = 2 AND restriction.id_broker = $agency)
                    OR (restriction.dirigido = 6 AND restriction.id_broker = $agency)
                )";
            }
            $query .= (count($where) > 0 ? " WHERE " . implode(' AND ', $where) : " ");

            $response = $this->selectDynamic('', '', '', '', $query);
            if (!$response) {
                return $_respuesta->getError('1050');
            } elseif ($details) {
                return $response;
            }
        }

        public function verifyRestrictionDestination($destination, $plan)
        {   
            $_respuesta = new response;
            $restrictionTerritory = $this->selectDynamic('', 'restriction', "id_plans='$plan'", array("id_territory_destino"))[0]['id_territory_destino'];

            if ($restrictionTerritory) {

                if ($restrictionTerritory != '0') {
                    $query = "SELECT
                        territory.id_territory
                    FROM
                        restriction
                        INNER JOIN territory ON restriction.id_territory_destino = territory.id_territory
                    WHERE
                        restriction.id_plans = '$plan'
                        AND territory.id_territory = '$destination'";
                    $response = $this->selectDynamic('', '', '', '', $query);

                    if (!$response) {
                        return $_respuesta->getError('1081');
                    }
                }
            }
        }

        public function verifyRestrictionOrigin($origin, $plan)
        {   
            $_respuesta = new response;
            $query = "SELECT
            relaciotn_restriction.iso_country,
            countries.description
            FROM
                relaciotn_restriction
            INNER JOIN restriction ON relaciotn_restriction.id_restric = restriction.id_restric
            INNER JOIN countries ON relaciotn_restriction.iso_country = countries.iso_country
            WHERE
            restriction.id_plans = '$plan'
            AND countries.iso_country = '$origin'";
            $response = $this->selectDynamic('', '', '', '', $query);
            if ($response) {
                return $_respuesta->getError('1091');
            }
        }

        public function verifyDaysPlan($daysByPeople, $plan)
        {
            $_respuesta = new response;
            $daysConfigPlan  = $this->selectDynamic('', 'plans', "id='$plan'", array("min_tiempo", "max_tiempo", "compra_minima"))[0];


            if ($daysByPeople < $daysConfigPlan['min_tiempo']) {

                return  $_respuesta->getError('9195');
            }

            if ($daysByPeople > $daysConfigPlan['max_tiempo']) {

                return $_respuesta->getError('1247');
            }
            if (!empty($daysConfigPlan['compra_minima']) ? $daysByPeople < $daysConfigPlan['compra_minima'] : false) {
                return $_respuesta->getError('1248');
            }
        }

        public function setAges($birthDayPassenger, $isoCountry)
        {

            foreach ($birthDayPassenger as $value) {
                $transformateValue  = $this->transformerDate($value);
                $calculate[]        = $this->calculateAge($transformateValue, $isoCountry);
            }

            return implode(',', $calculate);
        }

        public function calculateAge($birthDayPassenger, $isoCountry)
        {
            $this->setTimeZone($isoCountry);
            $birthDayPassenger  = new DateTime($birthDayPassenger);
            $today              = new DateTime();
            $difference         = $today->diff($birthDayPassenger);
            return $difference->y;
        }

        public function getCountryAgency($api)
        {
            $query = "SELECT
                broker.id_country
            FROM
                users
            Inner Join user_associate ON users.id = user_associate.id_user
            Inner Join broker ON user_associate.id_associate = broker.id_broker
            WHERE
                users.api_key =  '$api'";
            return $this->_SQL_tool($this->SELECT_SINGLE, __METHOD__, $query)['id_country'];
        }

        public function verifyBenefits($dataQuoteGeneral)
        {   
            $_response = new response;
            $benefits    = [
                '1246'    => $dataQuoteGeneral[0]['error_age'],
                '1100'    => $dataQuoteGeneral[0]['error_broker'],
                '5003'    => $dataQuoteGeneral[0]['error_cant_passenger'],
                '1090'    => $dataQuoteGeneral[0]['error_country'],
                '1080'    => $dataQuoteGeneral[0]['error_territory'],
                '1247'    => $dataQuoteGeneral[0]['error_time']
            ];

            $filter = array_filter($benefits, function ($var) {
                return ($var == '0');
            });
            $filter = array_keys($filter);
            return !empty($filter[0]) ? $_response->getError($filter[0]) : false;
        }

        public function verifiedOrderDuplicate($salida = '', $retorno = '', $origen = '', $destino = '')
        {

            $query = "SELECT
                        GROUP_CONCAT(orders.id) ids
                    FROM
                        orders
                    WHERE status IN ('1','9')
                    AND orders.salida   = '$salida'
                    AND orders.retorno  = '$retorno'
                    AND orders.origen   = '$origen'";
            if ($destino == 1 || $destino    == 2 || $destino    == 9) {
                $query .= " AND orders.territory = '$destino'";
            } else {
                $query .= " AND orders.destino = '$destino'";
            }

            return $this->selectDynamic('', '', '', '', $query)[0]['ids'] ?: false;
        }

        public function verifiedBeneficiariesDuplicate($id_orders, $documento, $nacimiento, $error = 6045)
        {
            $_response = new response;
            $documento  = implode("','", $documento);
            $nacimiento = implode("','", $nacimiento);
            $query  = "SELECT id
                FROM  
                    beneficiaries WHERE beneficiaries.id_orden IN ($id_orders)
                AND beneficiaries.documento IN ('$documento')
                AND beneficiaries.nacimiento IN ('$nacimiento')";

            $result = $this->selectDynamic('', '', '', '', $query);
            if ($result) {
                return $_response->getError($error);
            }
        }

        public function valueRandom($length = 12)
        {
            $chr = "0123456789ABCDEFGHIJKML";
            $str = "";
            while (strlen($str) < $length) {
                $str .= substr($chr, mt_rand(0, (strlen($chr))), 1);
            }
            return ($str);
        }

        public function dataExchangeRate($isoCountry)
        {
            $query = "SELECT
            countries.description,
            countries.iso_country,
            countries.currencyname,
            currency.usd_exchange
            FROM
            countries
            INNER JOIN currency ON countries.currencycode = currency.value_iso
            WHERE currency.usd_exchange != '0'
            AND currency.id_status = 1 ";

            if ($isoCountry) {
                $query .= "AND countries.iso_country = '$isoCountry'";
            }
            return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
        }

        public function addUpgrades($data, $source = true)
        {
            /**
             * Buscar como convertir el data upgrades en el objeto que se requiere
             */
            $_response = new response;
            $api      			= $data['api'];
            $code				= $data['codigo'];
            $upgrade			= $data['upgrades'];
            $plan				= $data['plan'];
            $daysByPeople		= $data['daybypeople'];
            $cost				= $data['cost'];
            $price				= $data['price'];
            $numberPassengers	= $data['numberPassengers'];
            $upgradeObj			= $data["upgrades"];
            $bDayBeneficiaries	= $data["beneficiaries"];
            $priceBeneficiaries = $data["precio_vta"];
            $costBeneficiaries 	= $data["precio_cost"];
            $idOrden			= 0;
            $decodeUpg			= (is_object($data['upgrades'])) ? json_encode($data['upgrades']) : false;
            $upgradeObj			= (($decodeUpg)) ? json_decode($decodeUpg, true) : json_decode($data['upgrades'], true);
            $dataUpgrade 		= (array)$upgradeObj["item"];
            $dataUpgrade		= (is_object($data['upgrades']->item)) ? [0 => $dataUpgrade] : $dataUpgrade;
            $countDataUpgrade 	= count($data['upgrades']);
            $idUpgrade	= [];
            $arrUpgrade	= [];
            $procedenciaBack = $data['procedenciaBack'];
            if (!$procedenciaBack) {
                $procedenciaBack = '1';
            }
    
    
    
            $idUpgrade 	 = array_map(function ($value) {
                return $value['id'];
            }, $data['upgrades']);
    
    
            $dataValida				= [
                '6037'	=> !(empty($code) and empty($data['upgrades'])),
                '6020'	=> $api,
                '6023'	=> $code,
                '6039'	=> $countDataUpgrade
            ];
    
            if ($source) {
                $dataOrder			= $this->getOrderData($code);
                $plan 				= $dataOrder['plan'];
                $idOrden			= $dataOrder['id'];
                $status             = $dataOrder['status'];
    
    
                $datAgency 			= $this->datAgency($api);
                $numberPassengers	= $dataOrder['cantidad'];
                $idUser				= $datAgency[0]['user_id'];
                $prefix			    = $datAgency[0]['prefijo'];
                $daysByPeople 		= $this->betweenDates($dataOrder['salida'], $dataOrder['retorno']);
                $price 				= $dataOrder['total'];
                $cost 				= $dataOrder['neto_prov'];
                $isoCountry 		= $dataOrder['id_country'];
                $procedencia 		= $dataOrder['procedencia_funcion'];
    
                $dataValidaUpgra	= [
                    '9137'		=> ($procedenciaBack == '2'  && $status != '9') ? 0 : 1,
                    '6037'		=> count($this->selectDynamic('', 'orders', "codigo='$code'", array("id"))),
                    '9048'		=> !empty($idUpgrade[0]),
                    '6047'		=> !empty($idUpgrade[0]) ? !($this->selectDynamic(['id_raider' => $idUpgrade], 'orders_raider', "id_orden='$idOrden'", array("id"))) : false,
                ];
    
                $dataValida		= $dataValida + $dataValidaUpgra;
    
                $verifyVoucher 	= $this->verifyVoucher($code, $idUser, $isoCountry, 'ADD');
                if ($verifyVoucher) {
                    return $verifyVoucher;
                }
            }
    
            $validatEmpty	= $this->validatEmpty($dataValida);
            if ($validatEmpty) {
                return $validatEmpty;
            }
    
            $arrPricePassengers	= [];
            $arrUpgNotType2		= [];
            $idUpgradesOrden	= [];
    
    
            for ($i = 0; $i < $countDataUpgrade; $i++) {
    
                $id 		= $data['upgrades'][$i];
                $document 	= $data['upgrades'][$i]['documento'];
    
                $typeUpgrade	= $this->valUpgrades($plan, $id);
    
                if (!empty($typeUpgrade)) {
                    if ($typeUpgrade == 2) {
                        if (empty($document)) {
                            return $_response->getError('4006');
                        } else {
                            if (!$source) {
                                $dataUpgradeDocument = array_map(function ($value) {
                                    return $value['documento'];
                                }, $data['documentos']);
    
                                $validateBeneficiaries = array_diff($bDayBeneficiaries, $dataUpgradeDocument);
    
                                if (count($validateBeneficiaries) > 0) {
                                    return $_response->getError('9028');
                                }
    
                                $arrPricePassengers[] = [
                                    'id_raider'		=> $id,
                                    'precio_vta'	=> $priceBeneficiaries[$i],
                                    'precio_cost'	=> $costBeneficiaries[$i],
                                    'id'	=> 0
                                ];
                            } else {
    
                                $pricePassengers = $this->dataBeneficiaries($code, '', $document);
                                if (!empty($pricePassengers['Error_Code'])) {
                                    return $pricePassengers;
                                } else {
                                    $arrPricePassengers[] = $pricePassengers[0] + ['id_raider' => $id];
                                }
                            }
                        }
                    } else {
                        $arrUpgNotType2[] = $id;
                    }
                } else {
                    return $_response->getError('1095');
                }
            }
    
    
    
            $priceUpgrades	= 0;
            $costUpgrades 	= 0;
    
            if (count($arrPricePassengers) > 0) {
    
                foreach ($arrPricePassengers as  $value) {
    
    
    
                    $getPriceUpgrade	= $this->dataUpgrades($plan, 'spa', $price, $daysByPeople, $numberPassengers, $value['id_raider'], $value['precio_vta'], $cost, $value['precio_cost'])[0];
                    //$getCostUpgrade		= $this->dataUpgrades($plan,'spa' ,$cost,$daysByPeople,$numberPassengers,$value['id_raider'],$value['precio_cost'])[0];
    
                    

                    
                    //$addOrderUpgrades []= $this->addOrderUpgrades($idOrden, $value['id_raider'], $getPriceUpgrade['price_upgrade'],$getCostUpgrade['price_upgrade'], 0,$value['id'],$prefix);
                    
                    $addOrderUpgrades[] = $this->addOrderUpgrades($idOrden, $value['id_raider'], $getPriceUpgrade['price_upgrade'], $getPriceUpgrade['costo_upgrade'], 0, $value['id'], $prefix);
                    /*if ($procedencia == 0) {
    
                        $data =	[
                            'precio_vta'	=> $getPriceUpgrade['price_upgrade'] + $value['precio_vta'],
                            //'precio_cost'	=> $getCostUpgrade['price_upgrade'] + $value['precio_cost']
                            'precio_cost'	=> $getPriceUpgrade['costo_upgrade'] + $value['precio_cost']
                        ];
    
                        
                    } else {
                        $data =	[
                            'precio_cost'	=> $getPriceUpgrade['costo_upgrade'] + $value['precio_cost']
                        ];
                    }*/
                    //if ('190.78.69.189' == $_SERVER['REMOTE_ADDR']) {
                    if ($procedencia == 0) {
    
                        $data =	[
                            'total_neto_benefit'	=> $getPriceUpgrade['price_upgrade'] + $value['total_neto_benefit'],
                            'neto_cost'	            => $getPriceUpgrade['costo_upgrade'] + $value['neto_cost']
                        ];
                    } else {
                        $data =	[
                            'neto_cost'	            => $getPriceUpgrade['costo_upgrade'] + $value['neto_cost']
                        ];
                    }
                    //}
    
    
    
    
    
                    if ($source) {
                        $this->updateDynamic('beneficiaries', 'id', $value['id'], $data);
                    }
    
                    $priceUpgrades 	+= $getPriceUpgrade['price_upgrade'];
                    //$costUpgrades 	+= $getCostUpgrade['price_upgrade'];
                    $costUpgrades 	+= $getPriceUpgrade['costo_upgrade'];
                }
            }
    
    
            if (count($arrUpgNotType2) > 0) {
    
    
                $getPriceUpgrade	= $this->dataUpgrades($plan, 'spa', $price, $daysByPeople, $numberPassengers, implode(',', $arrUpgNotType2), '', $cost);
                //$getCostUpgrade		= $this->dataUpgrades($plan,'spa' ,$cost,$daysByPeople,$numberPassengers,implode(',',$arrUpgNotType2));
    
    
    
    
                for ($i = 0; $i < count($getPriceUpgrade); $i++) {
    
    
                    //$addOrderUpgrades []= $this->addOrderUpgrades($idOrden, $getPriceUpgrade[$i]['id_raider'], $getPriceUpgrade[$i]['price_upgrade'],$getCostUpgrade[$i]['price_upgrade'],0,0,$prefix);
                    $addOrderUpgrades[] = $this->addOrderUpgrades($idOrden, $getPriceUpgrade[$i]['id_raider'], $getPriceUpgrade[$i]['price_upgrade'], $getPriceUpgrade[$i]['costo_upgrade'], 0, 0, $prefix);
    
                    $priceUpgrades 	+= $getPriceUpgrade[$i]['price_upgrade'];
                    //$costUpgrades 	+= $getCostUpgrade[$i]['price_upgrade'];
                    $costUpgrades 	+= $getPriceUpgrade[$i]['costo_upgrade'];
                    //nuevo
                    //if ('190.78.69.189' == $_SERVER['REMOTE_ADDR']) {
    
                    if ($source) {
                        $pricePassengers = $this->dataBeneficiaries($code, '', '');
                        if (!empty($pricePassengers['Error_Code'])) {
                            return $pricePassengers;
                        }
                    }
    
                    for ($j = 0; $j < count($pricePassengers); $j++) {
    
    
    
                        if ($procedencia == 0) {
    
                            $data =	[
                                'total_neto_benefit'	=> ($getPriceUpgrade[$i]['price_upgrade'] / $numberPassengers) + $pricePassengers[$j]['total_neto_benefit'],
                                'neto_cost'	            => ($getPriceUpgrade[$i]['costo_upgrade'] / $numberPassengers) + $pricePassengers[$j]['neto_cost']
                            ];
                        } else {
                            $data =	[
                                'neto_cost'	            => ($getPriceUpgrade[$i]['costo_upgrade'] / $numberPassengers) + $pricePassengers[$j]['neto_cost']
                            ];
                        }
    
                        if ($source) {
                            $this->updateDynamic('beneficiaries', 'id', $pricePassengers[$j]['id'], $data);
                        }
                    }
                    //}
                    //nuevo
                }
            }
    
            if ($procedencia == 0) {
    
                $priceNew 	= $price + $priceUpgrades;
                $costNew 	= $cost  + $costUpgrades;
            } else {
    
                $costNew 	= $cost  + $costUpgrades;
            }
    
            $addUpgradeOrder = $this->updateUpgradeOrder($code, $priceNew, $costNew);
    
    
    
            $arrResult = [
                'voucher' 			=> $code,
                'valor_adicional' 	=> $priceUpgrades,
                'upgrades' 			=> $upgrade,
            ];
    
            if (!$source) {
    
                $arrResult	= array_merge($arrResult, ["id" => $addOrderUpgrades, "price" => $priceNew, "cost" => $costNew]);
            }
            return $arrResult;
        }

        public function getOrderData($code)
        {
            $query = "SELECT
                id,
                producto,
                retorno,
                salida,
                territory,
                agencia,
                total,
                neto_prov,
                vendedor,
                tasa_cambio,
                cantidad,
                status,
                procedencia_funcion
            FROM
                orders
            WHERE
                codigo = '$code'";
            return $this->_SQL_tool($this->SELECT_SINGLE, __METHOD__, $query);
        }

        public function verifyVoucher($code, $idUser, $isoCountry, $procedencia = 'ADD', $onlySelect, $skypCancel = true)
        {

            $this->setTimeZone($isoCountry);
            $query = "SELECT
                orders.status,
                orders.salida,
                orders.vendedor,
                orders.procedencia_funcion
            FROM
                orders
            where
                codigo ='$code'";
            $response = $this->_SQL_tool($this->SELECT_SINGLE, __METHOD__, $query);
            $dataValida        = [
                '1020'        => count($response),
                '1021'        => !($response['status'] == 5 && $response['procedencia_funcion'] == 0),
                '9018'      => ($response['vendedor'] == $idUser),
                '9019'      => !($response['procedencia_funcion'] == '0' && $procedencia == 'REPORT'),
                '4001'      => !(strtotime($response['salida']) < strtotime(date('Y-m-d')))
            ];
            $validatEmpty    = $this->validatEmpty($dataValida);
            if (!empty($validatEmpty)) {
                return $validatEmpty;
            }
        }

        public function valUpgrades($plan, $upgrades)
        {
            $query = "SELECT
                    raiders.id_raider,
                    raiders.type_raider
                FROM
                    raiders
                    INNER JOIN plan_raider ON raiders.id_raider = plan_raider.id_raider
                WHERE   
                    plan_raider.id_plan = '$plan' AND
                    raiders.id_raider IN ($upgrades)
                    AND raiders.id_status = 1 ";
            return $this->_SQL_tool($this->SELECT_SINGLE, __METHOD__, $query)['type_raider'];
        }

        public function dataBeneficiaries($idOrden, $status = 1, $document)
        {
            $query = "SELECT               
                beneficiaries.id,
                beneficiaries.id_orden,
                beneficiaries.nombre,
                beneficiaries.apellido,
                beneficiaries.email,
                beneficiaries.telefono,
                beneficiaries.nacimiento,
                beneficiaries.documento,
                beneficiaries.condicion_medica,
                beneficiaries.precio_vta,
                beneficiaries.precio_cost,
                beneficiaries.total_neto_benefit,
                beneficiaries.neto_cost,
                beneficiaries.ben_status,
                IFNULL(beneficiaries.id_rider,'N/A') as raider
            FROM
                beneficiaries
                INNER JOIN orders ON orders.id = beneficiaries.id_orden
            where orders.codigo ='$idOrden'";
            if (!empty($status)) {
                $query .= " AND ben_status = '$status' ";
            }
            if (!empty($document)) {
                $query .= " AND documento IN ('$document') ";
            }
            $response = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
            return ($response) ? $response : $this->getError('9028');
        }

        public function dataUpgrades($plan, $language, $price, $daysByPeople, $numberPassengers, $upgrade, $pricePassengers, $cost, $costPax)
        {
            $query = "SELECT
        raiders.id_raider,
        raiders_detail.name_raider,
        raiders.type_raider,
        raiders.value_raider,
        raiders.cost_raider,
        raiders.rd_calc_type,
    
        CASE
        WHEN raiders.rd_calc_type= 1 THEN 
            IF(
                raiders.type_raider     = 1,
                ROUND(raiders.value_raider,2),
                ROUND(((raiders.value_raider / 100) * '$price'),2)
            )
            WHEN raiders.rd_calc_type   = 4 THEN 
            IF(
            raiders.type_raider  = 1,
            ROUND(raiders.value_raider * '$daysByPeople',2),
            ROUND((raiders.value_raider / 100) * '$price' * '$daysByPeople',2)
            )
            WHEN raiders.rd_calc_type   = 3   THEN 
            IF(
                raiders.type_raider  = 1,
                ROUND(raiders.value_raider * '$numberPassengers',2),
                ROUND((raiders.value_raider / 100)  * '$price',2)
            )
            WHEN raiders.rd_calc_type   = 5 THEN 
            IF(
            raiders.type_raider = 1,
            ROUND((raiders.value_raider * '$daysByPeople' * '$numberPassengers'),2),
            ROUND(((raiders.value_raider / 100) * '$price') * '$daysByPeople'  * '$numberPassengers',2)
            )
            WHEN raiders.rd_calc_type   = 2 THEN 
            IF(
            raiders.type_raider = 1,
            ROUND(raiders.value_raider,2),
            ROUND((raiders.value_raider / 100) * '$pricePassengers',2)
            )
            ELSE 'Precio No disponible'
            END AS price_upgrade,

                        CASE
                                WHEN raiders.rd_calc_type= 1  THEN 
                                    IF(
                                        raiders.type_raider     = 1,
                                        ROUND(raiders.cost_raider,2),
                                        ROUND(((raiders.cost_raider / 100) * '$cost'),2)
                                        
                                    )
                                    
                                WHEN raiders.rd_calc_type   = 2  THEN 
                                    IF(
                                    raiders.type_raider = 1,
                                    ROUND(raiders.cost_raider,2),
                                    ROUND((raiders.cost_raider / 100) * '$costPax',2)
                                    )
                                WHEN raiders.rd_calc_type   = 4   THEN 
                                    IF(
                                    raiders.type_raider  = 1,
                                    ROUND(raiders.cost_raider * '$daysByPeople',2),
                                    ROUND((raiders.cost_raider / 100) * '$cost' * '$daysByPeople',2)
                                    )
                                WHEN raiders.rd_calc_type   = 3   THEN 
                                    IF(
                                    raiders.type_raider  = 1,
                                    ROUND(raiders.cost_raider * '$numberPassengers',2),
                                    ROUND((raiders.cost_raider / 100)  * '$cost',2)
                                    )
                                
                                WHEN raiders.rd_calc_type   = 5  THEN 
                                    IF(
                                    raiders.type_raider = 1,
                                    ROUND((raiders.cost_raider * '$daysByPeople' * '$numberPassengers'),2),
                                    ROUND(((raiders.cost_raider / 100) * '$cost') * '$daysByPeople'  * '$numberPassengers',2)
                                    )
                                ELSE 0
                                END AS costo_upgrade
            FROM raiders
                INNER JOIN raiders_detail ON raiders_detail.id_raider = raiders.id_raider
                INNER JOIN plan_raider ON raiders.id_raider = plan_raider.id_raider
            WHERE
                plan_raider.id_plan = '$plan'
            AND 
                raiders_detail.language_id  = '$language' ";
            if (!empty($upgrade)) {
                $query .= " AND 
                raiders.id_raider IN ($upgrade) ";
            }


            $response   = $this->selectDynamic('', '', '', '', $query);



            if ($response) {
                return $response;
            } else {
                return [
                    "status"   => "No hay resultados",
                    "message"  => "No hay upgrades asociados a éste Plan"
                ];
            }
        }

        public function addOrderUpgrades($idOrden, $idUpgrade, $priceUpgrade, $costUpgrade, $netPriceUpgrade, $idBenefit, $prefix)
        {
            $idBenefit = $idBenefit ?: 0;
            $data       = [
                'id_orden'      => $idOrden,
                'id_raider'     => $idUpgrade,
                'value_raider'  => $priceUpgrade,
                'id_beneft'     => $idBenefit,
                'cost_raider'   => $costUpgrade,
                'neta_raider'   => $netPriceUpgrade
            ];
            return $this->insertDynamic($data, 'orders_raider');
        }

        public function updateUpgradeOrder($codigo_voucher, $total, $totaCost)
        {
            $data   = [
                'total'         => $total,
                'neto_prov'     => $totaCost,
            ];
            return $this->updateDynamic('orders', 'codigo', $codigo_voucher, $data);
        }
    }
?>