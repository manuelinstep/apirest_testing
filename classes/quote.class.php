<?php 
    require_once("response.class.php");
    require_once("quote_general_new.class.php");
    require_once("currencylayer.class.php");
    require_once("api_client_wta.class.php");
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
                    $coin = $datos['moneda'];
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
                    $individualOrder	= $dataPlan[0]['voucher_individual'];
                    $idAgency			= $datAgency[0]['id_broker'];
                    $isoCountry			= $datAgency[0]['id_country'];
                    $nameAgency			= $datAgency[0]['broker'];
                    $userAgency			= $datAgency[0]['user_id'];
                    $cantPassengerPlan	= $dataPlan[0]['num_pas'];
                    $prefix				= (!empty($datAgency[0]['prefijo'])) ? $datAgency[0]['prefijo'] : ''; //Debe buscarse una forma de traerlo por defecto
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
                            $tasa_cambio_recibida = $exchangeRate;
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
                        $tasa_cambio_recibida = $exchangeRate;
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

                    $status = 1;

                    $data	= [
                        'salida'				=> $departureTrans,
                        'retorno'				=> $arrivalTrans,
                        'referencia'			=> $datos['referencia'],
                        'producto'				=> $plan,
                        'destino'				=> $datos['pais_destino'],
                        'origen'				=> strtoupper($datos['pais_origen']),
                        'nombre_contacto'		=> $datos['nombre_contacto'],
                        'telefono_contacto'		=> $datos['telefono_contacto'],
                        'agencia'				=> $idAgency,
                        'nombre_agencia'		=> $nameAgency,
                        'vendedor'				=> $userAgency,
                        'programaplan'			=> $idCategoryPlan,
                        'family_plan'			=> $familyPlan,
                        'fecha'					=> 'now()',
                        'cantidad'				=> $datos['pasajeros'],
                        'status'				=> $status,
                        'origin_ip'				=> $_SERVER['REMOTE_ADDR'],
                        'email_contacto'		=> $datos['email_contacto'],
                        'comentarios'			=> $datos['consideraciones_generales'],
                        'tiempo_x_producto'		=> $daysByPeople,
                        'comentario_medicas'	=> $datos['consideraciones_generales'],
                        'id_emision_type'		=> '2',
                        'validez'				=> '1',
                        'hora'					=> 'now()',
                        'tasa_cambio'			=> $exchangeRate,
                        'alter_cur'				=> $coin,
                        'territory'				=> $datos['pais_destino'],
                        'total_tax'				=> $dataQuoteGeneral[0]['total_tax1'] + $dataQuoteGeneral[0]['total_tax2'],
                        'total_tax_mlc'			=> ($dataQuoteGeneral[0]['total_tax1'] + $dataQuoteGeneral[0]['total_tax2']) * $exchangeRate,
                        'lang'					=> $language,
                        'procedencia_funcion'	=> '0'//,
                        //'prefijo'               => $prefix,
                        //'tasa_cambio_recibida'      => $tasa_cambio_recibida
                    ];
                    //En este punto, revisamos los guarnin

                    //Luego, procedemos a los pasos finales de la función

                    $DataWta = $this->GetId('FT');
                    $OrderId =  $this->getLastIdOrder();
                    $WtaopsId = $DataWta['order'];
                    //$WtaopsBen= $DataWta['beneficiary'];
                    $Id = (($WtaopsId > $OrderId) ? $WtaopsId : $OrderId) + 1;
                    //$beneficiary = (($WtaopsBen > $BeneficiarieId)?$WtaopsBen:$BeneficiarieId)+1;

                    if ($individualOrder == 'Y' && $datos['pasajeros'] > 1) {
                        for ($i = 0; $i < $datos['pasajeros']; $i++) {
                            $data['id'] =  $Id;
                            $data['codigo'] = $code . '-' . $i;
                            $data['total'] 	=  $pricePassenger[$i];
                            $data['neto_prov'] 	=  $costPassenger[$i];
                            $data['total_mlc'] 	=  $pricePassenger[$i] * $exchangeRate;
                            $data['neto_prov_mlc'] 	=  $costPassenger[$i] * $exchangeRate;
                            if ($issue == 4) {
                                $data['status'] = 9;
                            }
                            $idOrden[]	= $this->insertDynamic($data, 'orders');
                            $BeneficiarieId = $this->getLastIdBeneficiarie();
                            $idben[$i] = $BeneficiarieId + $i + 1;

                            $addBeneficiaries[$i]	= $this->addBeneficiares($datos['documentos'][$i], $birthDayPassengerTrans[$i], $datos['nombres'][$i], $datos['apellidos'][$i], $datos['telefonos'][$i], $datos['correos'][$i], $idOrden[$i], '1', $pricePassenger[$i], $costPassenger[$i], $datos['condiciones_med'], $pricePassenger[$i] * $exchangeRate, $costPassenger[$i] * $exchangeRate, 0, 0, $prefix, $idben[$i]);
                            $link[] = LINK_REPORTE_VENTAS . $data['codigo'] . "&selectLanguage=$language&broker_sesion=$idAgency";
                        }
                    } else {
                        $data['id'] =  $Id;
                        $data['codigo'] =  $code;
                        $data['total'] 	=  $price;
                        $data['neto_prov'] 	=  $cost;
                        $data['total_mlc'] 	=  $price * $exchangeRate;
                        $data['neto_prov_mlc'] 	=  $cost * $exchangeRate;
                        if ($issue == 4) {
                            $data['status'] = 9;
                        }
                        $link = LINK_REPORTE_VENTAS . $code . "&selectLanguage=$language&broker_sesion=$idAgency";
                        $idOrden	= $this->insertDynamic($data, 'orders');
                        //$idben = $beneficiary;
                        $BeneficiarieId = $this->getLastIdBeneficiarie();
                        for ($i = 0; $i < $datos['pasajeros']; $i++) {

                            $idben[$i] = $BeneficiarieId + $i + 1;
                            $addBeneficiaries[$i]	= $this->addBeneficiares($datos['documentos'][$i], $birthDayPassengerTrans[$i], $datos['nombres'][$i], $datos['apellidos'][$i], $datos['telefonos'][$i], $datos['correos'][$i], $idOrden, '1', $pricePassenger[$i], $costPassenger[$i], $datos['condiciones_med'], $pricePassenger[$i] * $exchangeRate, $costPassenger[$i] * $exchangeRate, 0, 0, $prefix, $idben[$i]);
                        }
                    }

                    //Pasos finales

                    $issue = '1';

                    if (!empty($addBeneficiaries) && !empty($idOrden)) {
                        if (is_array($idOrden)) {
                            for ($i = 0; $i < count($idOrden); $i++) {
                                $this->addCommission($idAgency, $idCategoryPlan, $price, $idOrden[$i]);
                            }
                        } else {
                            $this->addCommission($idAgency, $idCategoryPlan, $price, $idOrden);
                        }
            
                        if (count($idUpgrade) > 0) {
                            foreach ($idUpgrade as $value) {
                                $this->updateDynamic('orders_raider', 'id', $value, ['id_orden' => $idOrden]);
                            }
                        }
            
                        switch ($issue) {
                            case '1':
            
                                if ($adjustedExchangeRate and $coin != 'USD') {
            
                                    if (!empty($emptyContact)) {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "ruta"			=> $link,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            "El valor de cambio fue ajustado a:" => number_format($exchangeRate, 2),
                                            $contact       => $emptyContact
                                        ];
                                    } else {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "ruta"			=> $link,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            "El valor de cambio fue ajustado a:" => number_format($exchangeRate, 2)
                                        ];
                                    }
                                } elseif ($adjustedExchangeRateMax) {
            
                                    if (!empty($emptyContact)) {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "ruta"			=> $link,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            "La tasa cambiaria reportada " . number_format($exchangeRate, 2) . " , excede con respecto a la tasa de cambio nuestra " . number_format($exchangeRateOur, 2) . " en" => $difPorcentajeExchange . "%",
                                            $contact       => $emptyContact
                                        ];
                                    } else {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "ruta"			=> $link,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            "La tasa cambiaria reportada " . number_format($exchangeRate, 2) . " , excede con respecto a la tasa de cambio nuestra " . number_format($exchangeRateOur, 2) . " en" => $difPorcentajeExchange . "%"
                                        ];
                                    }
                                } else {
                                    if (!empty($emptyContact)) {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "ruta"			=> $link,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            $contact       => $emptyContact
                                        ];
                                    } else {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "ruta"			=> $link,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia']
                                        ];
                                    }
                                }
            
                                //$this->sendOrder($emailPassenger[0], $idOrden, $language, $language);
                                break;
                            case '2':
                                if ($adjustedExchangeRate and $coin != 'USD') {
            
                                    if (!empty($emptyContact)) {
            
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "referencia"	=> $datos['referencia'],
                                            "El valor de cambio fue ajustado a:" => number_format($exchangeRate, 2),
                                            $contact       => $emptyContact
                                        ];
                                    } else {
            
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "referencia"	=> $datos['referencia'],
                                            "El valor de cambio fue ajustado a:" => number_format($exchangeRate, 2)
                                        ];
                                    }
                                } elseif ($adjustedExchangeRateMax) {
            
                                    if (!empty($emptyContact)) {
            
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "referencia"	=> $datos['referencia'],
                                            "La tasa cambiaria reportada " . number_format($exchangeRate, 2) . " , excede con respecto a la tasa de cambio nuestra " . number_format($exchangeRateOur, 2) . " en" => $difPorcentajeExchange . "%",
                                            $contact       => $emptyContact
                                        ];
                                    } else {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "referencia"	=> $datos['referencia'],
                                            "La tasa cambiaria reportada " . number_format($exchangeRate, 2) . " , excede con respecto a la tasa de cambio nuestra " . number_format($exchangeRateOur, 2) . " en" => $difPorcentajeExchange . "%"
                                        ];
                                    }
                                } else {
            
                                    if (!empty($emptyContact)) {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "referencia"	=> $datos['referencia'],
                                            $contact       => $emptyContact
                                        ];
                                    } else {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "referencia"	=> $datos['referencia']
                                        ];
                                    }
                                }
                                break;
                            case '3':
                                if ($adjustedExchangeRate and $coin != 'USD') {
            
                                    if (!empty($emptyContact)) {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            "El valor de cambio fue ajustado a:" => number_format($exchangeRate, 2),
                                            $contact       => $emptyContact
                                        ];
                                    } else {
            
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            "El valor de cambio fue ajustado a:" => number_format($exchangeRate, 2)
                                        ];
                                    }
                                } elseif ($adjustedExchangeRateMax) {
                                    if (!empty($emptyContact)) {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            "La tasa cambiaria reportada " . number_format($exchangeRate, 2) . " , excede con respecto a la tasa de cambio nuestra " . number_format($exchangeRateOur, 2) . " en" => $difPorcentajeExchange . "%",
                                            $contact       => $emptyContact
                                        ];
                                    } else {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            "La tasa cambiaria reportada " . number_format($exchangeRate, 2) . " , excede con respecto a la tasa de cambio nuestra " . number_format($exchangeRateOur, 2) . " en" => $difPorcentajeExchange . "%"
                                        ];
                                    }
                                } else {
                                    if (!empty($emptyContact)) {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            $contact       => $emptyContact
                                        ];
                                    } else {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia']
                                        ];
                                    }
                                }
            
                                //$this->sendOrder($emailPassenger[0], $idOrden, $language, $language);
                                break;
                            default:
                                //$this->sendOrder($emailPassenger[0], $idOrden, $language, $language);
                                if ($adjustedExchangeRate and $coin != 'USD') {
            
                                    if (!empty($emptyContact)) {
            
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "ruta"			=> $link,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            "El valor de cambio fue ajustado a:" => number_format($exchangeRate, 2),
                                            $contact       => $emptyContact
                                        ];
                                    } else {
            
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "ruta"			=> $link,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            "El valor de cambio fue ajustado a:" => number_format($exchangeRate, 2)
                                        ];
                                    }
                                } elseif ($adjustedExchangeRateMax) {
            
                                    if (!empty($emptyContact)) {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "ruta"			=> $link,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            "La tasa cambiaria reportada " . number_format($exchangeRate, 2) . " , excede con respecto a la tasa de cambio nuestra " . number_format($exchangeRateOur, 2) . " en" => $difPorcentajeExchange . "%",
                                            $contact       => $emptyContact
            
                                        ];
                                    } else {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "ruta"			=> $link,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            "La tasa cambiaria reportada " . number_format($exchangeRate, 2) . " , excede con respecto a la tasa de cambio nuestra " . number_format($exchangeRateOur, 2) . " en" => $difPorcentajeExchange . "%"
            
                                        ];
                                    }
                                } else {
            
                                    if (!empty($emptyContact)) {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "ruta"			=> $link,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia'],
                                            $contact       => $emptyContact
                                        ];
                                    } else {
                                        return [
                                            "status"		=> "OK",
                                            "codigo"		=> $code,
                                            "valor"			=> $price,
                                            "ruta"			=> $link,
                                            "documento"		=> implode(",", $datos['documentos']),
                                            "referencia"	=> $datos['referencia']
                                        ];
                                    }
                                }
            
                                break;
                        }
                    }

                    /**
                     * Lo mas probable es que esta sea la función mas larga
                     * pero una vez terminada, es un copiar y pegar
                     * solo hay que fixear un par de cosas luego de eso
                     */
                    break;
                
                case 'report_order':
                    /**
                     * Diferencias
                     * 1- El código lo establece el usuario
                     * 2- No se añaden upgrades mediante este método
                     * 3- no se provee emision
                     * 4- se provee el costo
                     */

                    /**
                     * Anotar aqui los campos que recíbe el método
                     */

                    $moneda = $datos['moneda'];
                    $monto_neto_recibido       	    = trim($datos['costo']);
                    $numberPassengers = $datos['pasajeros'];
                    $price = 0;

                    $dataValida = [
                        '6029' => $datos['fecha_salida'], 
                        '6030' => $datos['fecha_llegada'],
                        '6035' => $datos['codigo'],
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
                        '6021' => $datos['lenguaje'],
                        //Campos de verificaciones varias
                        '4029'	=> (empty($datos['pasajeros']) or $datos['pasajeros'] == 0 or !is_numeric($datos['pasajeros'])) ? 0 : 1,
                        '1022'	=> (!$this->selectDynamic('', 'currency', "value_iso='$moneda'", array("desc_small"))) ? 0 : 1,
                        '2001'	=> $this->checkDates($datos['fecha_salida']),
			            '2002'	=> $this->checkDates($datos['fecha_llegada']),
                        '9059'	=> $this->verifyOrigin($datos['pais_origen']),
                        '1080'	=> ($datos['pais_destino'] == "1" or $datos['pais_destino'] == "2" or $datos['pais_destino'] == "9") ? 1 : 0,
                        '1030'	=> $this->validLanguage($datos['lenguaje']),
                        '4029'	=> (empty($datos['pasajeros']) or $datos['pasajeros'] == 0 or !is_numeric($datos['pasajeros'])) ? 0 : 1,
			            '6054'	=> (!$this->selectDynamic('', 'orders', "codigo='$code'", array("codigo"))) ? 1 : 0,
                        '9023'	=> (!empty($datos['costo'])) ? (is_numeric($datos['costo'])) : true,
                        '9023'	=> (!empty($monto_neto_recibido)) ? (is_numeric($monto_neto_recibido)) : true,
                        '6053'	=> (!empty($price)) ? (is_numeric($price)) : true,
                        //Verificamos que exista la cantidad requerida
                        '9049'	=> ($this->countData($datos['nombres'], $datos['pasajeros'])) ? 0 : 1,
                        '9053'	=> ($this->countData($datos['apellidos'], $datos['pasajeros'])) ? 0 : 1,
                        '9051'	=> ($this->countData($datos['nacimientos'], $datos['pasajeros'])) ? 0 : 1,
                        '9050'	=> ($this->countData($datos['documentos'], $datos['pasajeros'])) ? 0 : 1,
                        '9052'	=> ($this->countData($datos['correos'], $datos['pasajeros'])) ? 0 : 1,
                        '9054'	=> ($this->countData($datos['telefonos'], $datos['pasajeros'])) ? 0 : 1,
                        '9055'	=> ($this->countData($datos['condiciones_med'], $datos['pasajeros'])) ? 0 : 1
                    ]; //Verificación lista
                    $coin = $datos['moneda'];

                    $code = $datos['codigo'];

                    $validatEmpty			= $this->validatEmpty($dataValida);
                    if ($validatEmpty) {
                        return $validatEmpty;
                    }  

                    $validateDataPassenger	= $this->validateDataPassenger($datos['pasajeros'], $datos['nombres'], $datos['apellidos'], $datos['nacimientos'], $datos['documentos'], $datos['correos'], $datos['telefonos'], $datos['condiciones_med']);
                    if ($validateDataPassenger) {
                        return $validateDataPassenger;
                    }

                    $plan = $datos['id_plan'];
                    $dataPlan			= $this->selectDynamic('', 'plans', "id='$plan'", array("id_plan_categoria", "name", "num_pas"));
                    $datAgency			= $this->datAgency($datos['token']); //Debemos pasar el token de autenticacion
                    $idCategoryPlan 	= $dataPlan[0]['id_plan_categoria'];
                    $namePlan			= $dataPlan[0]['name'];
                    $idAgency			= $datAgency[0]['id_broker'];
                    $isoCountry			= $datAgency[0]['id_country'];
                    $nameAgency			= $datAgency[0]['broker'];
                    $userAgency			= $datAgency[0]['user_id'];
                    $prefix				= (!empty($datAgency[0]['prefijo'])) ? $datAgency[0]['prefijo'] : 'FT'; //Debe buscarse una forma de traerlo por defecto
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

                    $validateCorporate = $this->validateCorporate($idCategoryPlan, $datos['referencia'], $code);
                    if ($validateCorporate) {
                        return $validateCorporate;
                    }

                    //Obtenemos la edad del pasajero y el pais de la agencia
                    $agesPassenger		= $this->setAges($datos['nacimientos'], $isoCountry); 
                    
                    /**
                     * BirthdayPassenger, en el primer parametro, obtiene un array
                     */
		            $countryAgency		= $this->getCountryAgency($datos['token']);
                    $dataQuoteGeneral	= $quoteGeneral->quotePlanbenefis($idCategoryPlan, $daysByPeople, $countryAgency, $datos['pais_destino'], $datos['pais_origen'], $agesPassenger, $datos['fecha_salida'], $datos['fecha_llegada'], $idAgency, $plan, '', '', '', '', $price, 1);
                    $validatBenefits	= $this->verifyBenefits($dataQuoteGeneral);
                    if ($validatBenefits) {
                        return $validatBenefits;
                    }

                    $cost							= $dataQuoteGeneral[0]['total_costo'];
                    //$price							= $dataQuoteGeneral[0]['total'];
                    $familyPlan						= $dataQuoteGeneral[0]['family_plan'];

                    if ($dataQuoteGeneral[0]['banda'] == "si") {
                        for ($i = 0; $i < $dataQuoteGeneral[0]["total_rangos"]; $i++) {
                            $pricePassenger[] 		= $price / $datos['pasajeros'];
                            $costPassenger[]		= $dataQuoteGeneral[0]["costo_banda$i"];
                        }
                    } else {
                        if ($dataQuoteGeneral[0]['numero_menores'] > 0) {
                            for ($i = 0; $i < $dataQuoteGeneral[0]['numero_menores']; $i++) {
                                $pricePassenger[] 	= $price / $datos['pasajeros'];
                                $costPassenger[] 	= $dataQuoteGeneral[0]['costoMenor'];
                            }
                        }
                        if ($dataQuoteGeneral[0]['numero_mayores'] > 0) {
                            for ($i = 0; $i < $dataQuoteGeneral[0]['numero_mayores']; $i++) {
                                $pricePassenger[] 	= $price / $datos['pasajeros'];
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

                    //Tasa de cambio
                    $exchangeRate = (empty($datos['tasa_cambio']) || $datos['tasa_cambio'] == 1) ? $this->dataExchangeRate($datos['pais_origen']) : $datos['tasa_cambio'];

                    if (empty($exchangeRate[0]['usd_exchange']) && empty($exchangeRate)) {
                        //$exchangeRate = 1;
            
            
                        $exchangeRate = $currencyLayer->exchangeRate($datos['moneda'], date('Y-m-d'));
                        $adjustedExchangeRate = 1;
                        if ($datos['moneda'] != 'USD') {
                            $tasa_cambio_recibida = $exchangeRate;
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
                        $tasa_cambio_recibida = $exchangeRate;
                        $exchangeRate = $exchangeRate[0]['usd_exchange'];
                        $adjustedExchangeRate = 1;
                    } else {
                        return $_respuesta->getError('9011');
                    }

                    $price         = (empty($datos['costo'])) ? 0 : $datos['costo'];

                    if ($idCategoryPlan == 14) {
                        $invailableDays = $this->selectDynamic('', 'sales_corp', "codigo='$reference'", array("invailable_days"))[0]['invailable_days'];;
                        $coint_days = $daysByPeople * $numberPassengers;
                        $total_days =  $invailableDays - $coint_days;
                        $cost = 0;
                        $price = 0;
                    }

                    $status = '1';

                    $typeuser = $this->getTypeByStatus($data['api']);
                    if($typeuser['user_type']==15){
                        $status = '9';
                    }

                    $data	= [
                        'codigo'                => $code,
                        'salida'				=> $departureTrans,
                        'retorno'				=> $arrivalTrans,
                        'referencia'			=> $datos['referencia'],
                        'producto'				=> $plan,
                        'destino'				=> $datos['pais_destino'],
                        'origen'				=> strtoupper($datos['pais_origen']),
                        'nombre_contacto'		=> $datos['nombre_contacto'],
                        'telefono_contacto'		=> $datos['telefono_contacto'],
                        'agencia'				=> $idAgency,
                        'nombre_agencia'		=> $nameAgency,
                        'vendedor'				=> $userAgency,
                        'programaplan'			=> $idCategoryPlan,
                        'family_plan'			=> $familyPlan,
                        'fecha'					=> 'now()',
                        'cantidad'				=> $datos['pasajeros'],
                        'status'				=> $status,
                        'origin_ip'				=> $_SERVER['REMOTE_ADDR'],
                        'email_contacto'		=> $datos['email_contacto'],
                        'comentarios'			=> $datos['consideraciones_generales'],
                        'total'                 => $price,
                        'tiempo_x_producto'		=> $daysByPeople,
                        'neto_prov'             => $cost,
                        'comentario_medicas'	=> $datos['consideraciones_generales'],
                        'id_emision_type'		=> '2',
                        'validez'				=> '1',
                        'hora'					=> 'now()',
                        'tasa_cambio'			=> $exchangeRate,
                        'alter_cur'				=> $coin,
                        'territory'				=> $datos['pais_destino'],
                        'total_mlc'				=> $price * $exchangeRate,
                        'neto_prov_mlc'			=> $cost * $exchangeRate,
                        'total_tax'				=> $dataQuoteGeneral[0]['total_tax1'] + $dataQuoteGeneral[0]['total_tax2'],
                        'total_tax_mlc'			=> ($dataQuoteGeneral[0]['total_tax1'] + $dataQuoteGeneral[0]['total_tax2']) * $exchangeRate,
                        'lang'					=> $datos['lenguaje'],
                        'procedencia_funcion'	=> '1',
                        'prefijo'               => $prefix,
                        'monto_neto_recibido'       => $monto_neto_recibido,
                        'tasa_cambio_recibida'      => $tasa_cambio_recibida
                    ];

                    $DataWta = $this->GetId($prefix);
                    $OrderId =  $this->getLastIdOrder();
                    $WtaopsId = $DataWta['order'];

                    $Id = (($WtaopsId > $OrderId) ? $WtaopsId : $OrderId) + 1;
                    $data['id'] =  $Id;

                    if (strtolower($generalConsiderations) == '4wbs') {
                        $data['status'] = 9;
                    }

                    $idOrden = $this->insertDynamic($data, 'orders');
                    for ($i = 0; $i < $numberPassengers; $i++) {
                        $BeneficiarieId = $this->getLastIdBeneficiarie();
                        $WtaopsBen = $DataWta['beneficiary'];
                        $beneficiary = (($WtaopsBen > $BeneficiarieId) ? $WtaopsBen : $BeneficiarieId) + 1;
                        $idben[$i] = $beneficiary;
                        $addBeneficiaries[$i]	= $this->addBeneficiares($datos['documentos'][$i], $birthDayPassengerTrans[$i], $datos['nombres'][$i], $datos['apellidos'][$i], $datos['telefonos'][$i], $datos['correos'][$i], $idOrden, '1', $pricePassenger[$i], $costPassenger[$i], $datos['condiciones_med'], $pricePassenger[$i] * $exchangeRate, $costPassenger[$i] * $exchangeRate, 0, 0, $prefix, $idben[$i]);
                    }

                    if (!empty($addBeneficiaries) && !empty($idOrden)) {
                        $this->addCommission($idAgency, $idCategoryPlan, $price, $idOrden);
            
                        if ($idCategoryPlan == 14) {
                            $this->updateDynamic('sales_corp', 'codigo', $reference, ['invailable_days' => $total_days]);
                        }
            
                        if ($adjustedExchangeRate and $coin != 'USD') {
                            if (!empty($emptyContact)) {
                                return ["status" => "OK", "El valor de cambio fue ajustado a:" => number_format($exchangeRate, 2), $contact => $emptyContact];
                            } else {
                                return ["status" => "OK", "El valor de cambio fue ajustado a:" => number_format($exchangeRate, 2)];
                            }
                        } elseif ($adjustedExchangeRateMax) {
                            if (!empty($emptyContact)) {
                                return ["status" => "OK", "La tasa cambiaria reportada " . number_format($exchangeRate, 2) . " , excede con respecto a la tasa de cambio nuestra " . number_format($exchangeRateOur, 2) . " en" => $difPorcentajeExchange . "%", $contact => $emptyContact];
                            } else {
                                return ["status" => "OK", "La tasa cambiaria reportada " . number_format($exchangeRate, 2) . " , excede con respecto a la tasa de cambio nuestra " . number_format($exchangeRateOur, 2) . " en" => $difPorcentajeExchange . "%"];
                            }
                        } else {
                            if (!empty($emptyContact)) {
                                return ["status" => "OK", $contact => $emptyContact];
                            } else {
                                return ["status" => "OK"];
                            }
                        }
                    }

                    //Donde emptycontact se refiere a los warnings

                    break;
                
                case 'report_order_master':
                    //Corregir las diferencias y similitudes con el report order original
                    /**
                     * Report order MASTER
                     * 
                     * Aparte de los campos recibidos por el report order original, recibe el campo:
                     * numero_días
                     */
                    $moneda = $datos['moneda'];
                    $monto_neto_recibido       	    = trim($datos['costo']);
                    
                    $price = 0;

                    $numberDays = $datos['numero_dias'];

                    $dataValida = [
                        '6029' => $datos['fecha_salida'], 
                        '6030' => $datos['fecha_llegada'],
                        '6035' => $datos['codigo'],
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
                        '6021' => $datos['lenguaje'],
                        //Campos de verificaciones varias
                        '4029'	=> (empty($datos['pasajeros']) or $datos['pasajeros'] == 0 or !is_numeric($datos['pasajeros'])) ? 0 : 1,
                        '1022'	=> (!$this->selectDynamic('', 'currency', "value_iso='$moneda'", array("desc_small"))) ? 0 : 1,
                        '2001'	=> $this->checkDates($datos['fecha_salida']),
			            '2002'	=> $this->checkDates($datos['fecha_llegada']),
                        '9059'	=> $this->verifyOrigin($datos['pais_origen']),
                        '1080'	=> ($datos['pais_destino'] == "1" or $datos['pais_destino'] == "2" or $datos['pais_destino'] == "9") ? 1 : 0,
                        '1030'	=> $this->validLanguage($datos['lenguaje']),
                        '4029'	=> (empty($datos['pasajeros']) or $datos['pasajeros'] == 0 or !is_numeric($datos['pasajeros'])) ? 0 : 1,
			            '6054'	=> (!$this->selectDynamic('', 'orders', "codigo='$code'", array("codigo"))) ? 1 : 0,
                        '9023'	=> (!empty($datos['costo'])) ? (is_numeric($datos['costo'])) : true,
                        '9023'	=> (!empty($monto_neto_recibido)) ? (is_numeric($monto_neto_recibido)) : true,
                        '6053'	=> (!empty($price)) ? (is_numeric($price)) : true,
                        '9118'	=> (strlen($numberDays) < 6) ? 1 : 0,
                        '9119'	=> ($numberDays == 0) ? 0 : 1,
                        '9119'	=> (empty($numberDays)) ? 0 : 1,
                        //Verificamos que exista la cantidad requerida
                        '9049'	=> ($this->countData($datos['nombres'], $datos['pasajeros'])) ? 0 : 1,
                        '9053'	=> ($this->countData($datos['apellidos'], $datos['pasajeros'])) ? 0 : 1,
                        '9051'	=> ($this->countData($datos['nacimientos'], $datos['pasajeros'])) ? 0 : 1,
                        '9050'	=> ($this->countData($datos['documentos'], $datos['pasajeros'])) ? 0 : 1,
                        '9052'	=> ($this->countData($datos['correos'], $datos['pasajeros'])) ? 0 : 1,
                        '9054'	=> ($this->countData($datos['telefonos'], $datos['pasajeros'])) ? 0 : 1,
                        '9055'	=> ($this->countData($datos['condiciones_med'], $datos['pasajeros'])) ? 0 : 1
                    ]; //Verificación lista
                    $coin = $datos['moneda'];

                    $code = $datos['codigo'];

                    $validatEmpty			= $this->validatEmpty($dataValida);
                    if ($validatEmpty) {
                        return $validatEmpty;
                    }  

                    $validateDataPassenger	= $this->validateDataPassenger($datos['pasajeros'], $datos['nombres'], $datos['apellidos'], $datos['nacimientos'], $datos['documentos'], $datos['correos'], $datos['telefonos'], $datos['condiciones_med']);
                    if ($validateDataPassenger) {
                        return $validateDataPassenger;
                    }

                    $plan = $datos['id_plan'];
                    $dataPlan			= $this->selectDynamic('', 'plans', "id='$plan'", array("id_plan_categoria", "name", "num_pas"));
                    $datAgency			= $this->datAgency($datos['token']); //Debemos pasar el token de autenticacion
                    $idCategoryPlan 	= $dataPlan[0]['id_plan_categoria'];
                    $namePlan			= $dataPlan[0]['name'];
                    $idAgency			= $datAgency[0]['id_broker'];
                    $isoCountry			= $datAgency[0]['id_country'];
                    $nameAgency			= $datAgency[0]['broker'];
                    $userAgency			= $datAgency[0]['user_id'];
                    $prefix				= (!empty($datAgency[0]['prefijo'])) ? $datAgency[0]['prefijo'] : ''; //Debe buscarse una forma de traerlo por defecto
                    $arrivalTrans       = $this->transformerDate($datos['fecha_llegada']);
                    $departureTrans     = $this->transformerDate($datos['fecha_salida']);
                    $daysByPeople 		= $this->betweenDates($departureTrans, $arrivalTrans);

                    /**
                     * Validamos las fechas de la orden
                     */
                    $validateCategory 	= $this->validateCategory($idCategoryPlan);

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

                    $validateCorporate = $this->validateCorporate($idCategoryPlan, $datos['referencia'], $code);
                    if ($validateCorporate) {
                        return $validateCorporate;
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
                    //$price							= $dataQuoteGeneral[0]['total'];
                    $familyPlan						= $dataQuoteGeneral[0]['family_plan'];

                    if ($dataQuoteGeneral[0]['banda'] == "si") {
                        for ($i = 0; $i < $dataQuoteGeneral[0]["total_rangos"]; $i++) {
                            $pricePassenger[] 		= $price / $datos['pasajeros'];
                            $costPassenger[]		= $dataQuoteGeneral[0]["costo_banda$i"];
                        }
                    } else {
                        if ($dataQuoteGeneral[0]['numero_menores'] > 0) {
                            for ($i = 0; $i < $dataQuoteGeneral[0]['numero_menores']; $i++) {
                                $pricePassenger[] 	= $price / $datos['pasajeros'];
                                $costPassenger[] 	= $dataQuoteGeneral[0]['costoMenor'];
                            }
                        }
                        if ($dataQuoteGeneral[0]['numero_mayores'] > 0) {
                            for ($i = 0; $i < $dataQuoteGeneral[0]['numero_mayores']; $i++) {
                                $pricePassenger[] 	= $price / $datos['pasajeros'];
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

                    //Tasa de cambio
                    $exchangeRate = (empty($datos['tasa_cambio']) || $datos['tasa_cambio'] == 1) ? $this->dataExchangeRate($datos['pais_origen']) : $datos['tasa_cambio'];

                    if (empty($exchangeRate[0]['usd_exchange']) && empty($exchangeRate)) {
                        //$exchangeRate = 1;
            
            
                        $exchangeRate = $currencyLayer->exchangeRate($datos['moneda'], date('Y-m-d'));
                        $adjustedExchangeRate = 1;
                        if ($datos['moneda'] != 'USD') {
                            $tasa_cambio_recibida = $exchangeRate;
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
                        $tasa_cambio_recibida = $exchangeRate;
                        $exchangeRate = $exchangeRate[0]['usd_exchange'];
                        $adjustedExchangeRate = 1;
                    } else {
                        return $_respuesta->getError('9011');
                    }

                    $status = '1';

                    $typeuser = $this->getTypeByStatus($data['api']);
                    if($typeuser['user_type']==15){
                        $status = '9';
                    }

                    $data	= [
                        'codigo'                => $code,
                        'salida'				=> $departureTrans,
                        'retorno'				=> $arrivalTrans,
                        'referencia'			=> $datos['referencia'],
                        'producto'				=> $plan,
                        'destino'				=> $datos['pais_destino'],
                        'origen'				=> strtoupper($datos['pais_origen']),
                        'nombre_contacto'		=> $datos['nombre_contacto'],
                        'telefono_contacto'		=> $datos['telefono_contacto'],
                        'agencia'				=> $idAgency,
                        'nombre_agencia'		=> $nameAgency,
                        'vendedor'				=> $userAgency,
                        'programaplan'			=> $idCategoryPlan,
                        'family_plan'			=> $familyPlan,
                        'fecha'					=> 'now()',
                        'cantidad'				=> $datos['pasajeros'],
                        'status'				=> $status,
                        'origin_ip'				=> $_SERVER['REMOTE_ADDR'],
                        'email_contacto'		=> $datos['email_contacto'],
                        'comentarios'			=> $datos['consideraciones_generales'],
                        'total'                 => $price,
                        'tiempo_x_producto'		=> $daysByPeople,
                        'neto_prov'             => $cost,
                        'comentario_medicas'	=> $datos['consideraciones_generales'],
                        'id_emision_type'		=> '2',
                        'validez'				=> '1',
                        'hora'					=> 'now()',
                        'tasa_cambio'			=> $exchangeRate,
                        'alter_cur'				=> $coin,
                        'territory'				=> $datos['pais_destino'],
                        'total_mlc'				=> $price * $exchangeRate,
                        'neto_prov_mlc'			=> $cost * $exchangeRate,
                        'total_tax'				=> $dataQuoteGeneral[0]['total_tax1'] + $dataQuoteGeneral[0]['total_tax2'],
                        'total_tax_mlc'			=> ($dataQuoteGeneral[0]['total_tax1'] + $dataQuoteGeneral[0]['total_tax2']) * $exchangeRate,
                        'lang'					=> $language,
                        'id_client'                 => $idAgency,
			            'es_emision_corp'           => '1',
                        'procedencia_funcion'	=> '1',
                        'prefijo'               => $prefix,
                        'monto_neto_recibido'       => $monto_neto_recibido,
                        'tasa_cambio_recibida'      => $tasa_cambio_recibida
                    ];

                    $DataWta = $this->GetId($prefix);
                    $OrderId =  $this->getLastIdOrder();
                    $WtaopsId = $DataWta['order'];

                    $Id = (($WtaopsId > $OrderId) ? $WtaopsId : $OrderId) + 1;
                    $data['id'] =  $Id;

                    if (strtolower($generalConsiderations) == '4wbs') {
                        $data['status'] = 9;
                    }

                    $idOrden = $this->insertDynamic($data, 'orders');

                    for ($i = 0; $i < $numberPassengers; $i++) {

                        $BeneficiarieId = $this->getLastIdBeneficiarie();
                        $WtaopsBen = $DataWta['beneficiary'];
                        $beneficiary = (($WtaopsBen > $BeneficiarieId) ? $WtaopsBen : $BeneficiarieId) + 1;
                        $idben[$i] = $beneficiary;
                        $addBeneficiaries[$i]	= $this->addBeneficiares($datos['documentos'][$i], $birthDayPassengerTrans[$i], $datos['nombres'][$i], $datos['apellidos'][$i], $datos['telefonos'][$i], $datos['correos'][$i], $idOrden[$i], '1', $pricePassenger[$i], $costPassenger[$i], $datos['condiciones_med'], $pricePassenger[$i] * $exchangeRate, $costPassenger[$i] * $exchangeRate, 0, 0, $prefix, $idben[$i]);
                    }

                    if (!empty($addBeneficiaries) && !empty($idOrden)) {
                        $this->addCommission($idAgency, $idCategoryPlan, $price, $idOrden);
                    }

                    if (!empty($idOrden)) {
                        $data	= [
                            'codigo'					=> $code,
                            'corp_plan'					=> $plan,
                            'corp_user'					=> $userAgency,
                            'corp_costo'				=> $price,
                            'corp_status'				=> '2',
                            'invailable_days'			=> $numberDays,
                            'corp_date'				    => 'NOW()'
                        ];
                        $this->insertDynamic($data, 'sales_corp');
            
                        if ($adjustedExchangeRate and $coin != 'USD') {
                            return ["status" => "OK", "El valor de cambio fue ajustado a:" =>  number_format($exchangeRate, 2) ,"WARNINGS" => $emptyContact];
                        } elseif ($adjustedExchangeRateMax) {
                            return ["status" => "OK", "La tasa cambiaria reportada " . number_format($exchangeRate, 2) . " , excede con respecto a la tasa de cambio nuestra " . number_format($exchangeRateOur, 2) . " en" => $difPorcentajeExchange . "%","WARNINGS" => $emptyContact];
                        } else {
                            return ["status" => "OK","WARNINGS" => $emptyContact];
                        }
                    }

                    break;

                case 'add_upgrade': 
                    /**
                     * Recibimos:
                     * Código de la orden
                     * Upgrade
                     */
                    $data = [
                        'codigo' => $datos['codigo'],
                        'upgrades' => $datos['upgrade'],
                        'api' => $datos['token']
                    ];

                    return $this->addUpgrades($data,true);

                    break;
                
                case 'request_cancellation':
                    /**
                     * Este código es bastante sencillo
                     * solo hay que revisar que todas las referencias esten bien
                     */
                    
                    $api		= $datos['token'];
                    $code		= $datos['codigo'];
                    $notify		= $datos['notificar'];
                    $procedenciaBack = $datos['procedenciaBack'];
                    if (!$procedenciaBack) {
                        $procedenciaBack = '1';
                    }

                    $dataValida	= [
                        '6037'	=> !(empty($code) and empty($notify)),
                        '6023'	=> $code,
                        '9089'	=> $notify,
                        '4050'	=> !($notify > 2 || $notify < 1)
                    ];

                    $validatEmpty	= $this->validatEmpty($dataValida);
                    if (!empty($validatEmpty)) {
                        return $validatEmpty;
                    }
                
                    $datAgency 	= $this->datAgency($api);
                    //$language	= $this->arrLanguage[$datAgency[0]['language_id']];
                    $idAgency	= $datAgency[0]['id_broker'];
                    $idUser		= $datAgency[0]['user_id'];
                    $isoCountry = $datAgency[0]['id_country'];

                    $verifyVoucher	= $this->verifyVoucher($code, $idUser, $isoCountry, 'ADD');

                    if ($verifyVoucher) {
                        return $verifyVoucher;
                    }

                    $dataStatus			= $this->selectDynamic('', 'orders', "codigo='$code'", array("status"));
                    $statusOrder     	= $dataStatus[0]['status'];
                    $today 	= date('Y-m-d');
                    
                    if ($statusOrder == 1) {

                        if ($procedenciaBack == '2') {
                            return $_respuesta->getError(9137);
                        } else {

                            $data	= [
                                'status'	=>	'5',
                                'f_anulado'	=>	$today
                            ];
                        }
                    } else {
                        return $_respuesta->getError(1021);
                    }
                    
                    $cancelOrder	= $this->updateDynamic('orders', 'codigo', $code, $data);
                    /*
                    * 
                    if ($notify == '2') {
                        $this->sendMailCancel($code, $idAgency, $language);
                    }
                     *
                     * Esta opción no será implementada de momento para agilizar la salida del webservices
                     * 
                    */
                    if ($cancelOrder) {
                        return [
                            "status" => "OK"
                        ];
                    }

                    break;

                case 'request_upgrade_cancellation':
                    /**
                     * WEEEEEEEEEEEE ARE THE CHAMPIONS
                     * MY FRIEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEND
                     */

                    $api			= $datos['token'];
                    $code			= $datos['codigo'];
                    $upgrade		= $datos['upgrade'];
                    $procedenciaBack = $datos['procedenciaBack'];
                    if (!$procedenciaBack) {
                        $procedenciaBack = '1';
                    }
            
            
                    //$idOrden		= $this->selectDynamic(['status'=>'1'],'orders',"codigo='$code'",array("id"))[0]['id'];
                    $idOrden		= $this->selectDynamic('', 'orders', "codigo='$code'", array("id"))[0]['id'];
                    $status		    = $this->selectDynamic('', 'orders', "id='$idOrden'", array("status"))[0]['status'];
                    
                    $data			= [
                        'value_raider',
                        'cost_raider'
                    ];
                    $dataRaider     = $this->selectDynamic(['id_raider' => $upgrade], 'orders_raider', "id_orden='$idOrden'", $data);
            
                    $dataValida		= [
                        '6037'	=> !(empty($code) and empty($upgrade)),
                        '6023'	=> $code,
                        '6039'	=> $upgrade,
                        '1020'	=> $idOrden,
                        '9134'  => ($status != 1 && $status != 9) ? 0 : 1,
                        '6046'	=> count($dataRaider),
                        '9137'	=> ($procedenciaBack == '2'  && $status != '9') ? 0 : 1,
                    ];
            
                    $validatEmpty	= $this->validatEmpty($dataValida);
                    if (!empty($validatEmpty)) {
                        return $validatEmpty;
                    }
                    
                    $dataOrder			= $this->getOrderData($code);
                    $datAgency			= $this->datAgency($api);
            
                    $idOrden			= $dataOrder['id'];
                    $price 				= $dataOrder['total'];
                    $cost	 			= $dataOrder['neto_prov'];
                    $status	 			= $dataOrder['status'];
                    $idUser 			= $datAgency[0]['user_id'];
                    $plan				= $dataOrder['producto'];
                    $priceRaider		= $dataRaider[0]['value_raider'];
                    $costRaider			= $dataRaider[0]['cost_raider'];
                    $idCountry 			= $datAgency[0]['id_country'];
            
            
                    $verifyVoucher 		= $this->verifyVoucher($code, $idUser, $idCountry, 'ADD');
            
                    if ($verifyVoucher) {
                        return $verifyVoucher;
                    }
            
                    $data	= [
                        'total'		=> $price - $priceRaider,
                        'neto_prov'	=> $cost - $costRaider
                    ];
            
                    $updatePriceOrder 	= $this->updateDynamic('orders', 'codigo', $code, $data);
                    $deleteUpgradeOrder	= $this->deleteUpgradeOrder($idOrden, $upgrade);
            
                    if ($updatePriceOrder && $deleteUpgradeOrder) {
                        return [
                            'voucher' 			=> $code,
                            'valor_descuento' 	=> $priceRaider,
                            'pricer_order' 		=> $price - $priceRaider
                        ];
                    }
                    break;
                case 'changes_for_orders_reported':
                    $api			= $datos['token'];
                    $code			= $datos['codigo'];
                    $status			= $datos['status'];
                    $origin			= $datos['pais_origen'];
                    $destination	= $datos['pais_destino'];
                    $departure		= $datos['fecha_salida'];
                    $return			= $datos['fecha_retorno'];
                    $cost			= $datos['costo'];
                    $procedenciaBack = $datos['procedenciaBack'];
                    if (!$procedenciaBack) {
                        $procedenciaBack = '1';
                    }

                    $statusOrden = $this->selectDynamic(['status' => '9'], 'orders', "codigo='$code'", array("status"))[0]["status"];
                    $dataOrden   = $this->selectDynamic('', 'orders', "codigo='$code'", array("total"));


                    $OrdenTotal  = $dataOrden[0]['total'];

                    $dataValida			= [
                        '6037'	=> !(empty($code) and empty($status) and empty($origin) and empty($destination) and empty($departure) and  empty($cost)),
                        '6023'	=> $code,
                        '9020'	=> !(empty($status) && empty($origin) && empty($destination) && empty($departure) && empty($cost) && empty($return)),
                        '9021'	=> (!empty($status)) ? !(!is_numeric($status) ||  ($status != 1 && $status != 5 && $status != 9)) : true,
                        '1090'	=> (!empty($origin)) ? $this->verifyOrigin($origin) : true,
                        '9023'	=> (!empty($cost)) ? (is_numeric($cost)) : true,
                        '2001'	=> (!empty($departure)) ? $this->checkDates($departure) : true,
                        '2002'	=> (!empty($return)) ? $this->checkDates($return) : true,
                        '1080'	=> (!empty($destination)) ? ($destination == "1" or $destination == "2" or $destination == "9") ? 1 : 0 : true,
                        '9135'  => ($statusOrden && $status != 9) ? 0 : 1
                    ];

                    $validatEmpty	= $this->validatEmpty($dataValida);
                    if (!empty($validatEmpty)) {
                        return $validatEmpty;
                    }
                    $data_broker		= $this->datAgency($api);
                    $idAgency 			= $data_broker[0]['id_broker'];
                    $idUser 			= $data_broker[0]['user_id'];
                    $isoCountry			= $data_broker[0]['id_country'];

                    $verifyVoucher 		= $this->verifyVoucher($code, $idUser, $isoCountry, 'REPORT');
                    if ($verifyVoucher) {
                        return $verifyVoucher;
                    }

                    $dataVoucher		= $this->getOrderData($code);
                    $plan				= $dataVoucher['producto'];
                    $returnTrans 		= (!empty($return)) ? $this->transformerDate($return) : $dataVoucher['retorno'];
                    $departureTrans		= (!empty($departure)) ? $this->transformerDate($departure) : $dataVoucher['salida'];

                    if (!empty($destination)) {
                        $verifyDestination	= $this->verifyRestrictionDestination($destination, $plan);
                        if ($verifyDestination) {
                            return $verifyDestination;
                        }
                    }

                    if (!empty($departure) || !empty($return)) {
                        $validateDateOrder	= $this->validateDateOrder($returnTrans, $departureTrans, $isoCountry);
                        if ($validateDateOrder) {
                            return $validateDateOrder;
                        }
                        $daysByPeople 	= $this->betweenDates($departureTrans, $returnTrans);
                        $verifyDaysPlan = $this->verifyDaysPlan($daysByPeople, $plan);

                        if ($verifyDaysPlan) {
                            return $verifyDaysPlan;
                        }
                    }

                    if (!empty($cost)) {
                        $monto_neto_recibido = $cost;
                    } else {
                        $monto_neto_recibido = $OrdenTotal;
                    }

                    if ($procedenciaBack == '2') {

                        if ($statusOrden == '9') {
                            $data	=
                                [
                                    'status'	=> $status,
                                    'origen'	=> $origin,
                                    'destino'	=> $destination,
                                    'salida'	=> $departureTrans,
                                    //'total'		=> $cost,
                                    'monto_neto_recibido' => $monto_neto_recibido,

                                    'retorno'	=> $returnTrans
                                ];

                            $updateOrder	= $this->updateDynamic('orders', 'codigo', $code, $data);
                        } else {

                            return $this->getError(9137);
                        }
                    } else {

                        $data	=
                            [
                                'status'	=> $status,
                                'origen'	=> $origin,
                                'destino'	=> $destination,
                                'salida'	=> $departureTrans,
                                //'total'		=> $cost,
                                'monto_neto_recibido' => $monto_neto_recibido,
                                'retorno'	=> $returnTrans
                            ];

                        $updateOrder	= $this->updateDynamic('orders', 'codigo', $code, $data);
                    }




                    if ($updateOrder) {
                        if ($status != 5) {
                            if (!empty($departure) ||  !empty($cost) || !empty($destination) || !empty($returnTrans)) {
                                $data	= [
                                    'api'		=> $api,
                                    'action'	=> 'INTERNO',
                                    'codigo'	=> $code,
                                    //'total'		=> $cost,
                                    'monto_neto_recibido' => $monto_neto_recibido
                                ];

                                $crudBeneficiaries	=	$this->crudBeneficiaries($data);
                                if ($crudBeneficiaries['status'] == 'OK') {
                                    return ['status' 	=> 'OK'];
                                } else {
                                    return $crudBeneficiaries;
                                }
                            } else {
                                return ['status'	=> 'OK'];
                            }
                        } else {
                            return ['status'	=> 'OK'];
                        }
                    }
                    # code...
                    break;

                case 'request_changes':
                    $_response = new response;
                    $quoteGeneral 					= new quote_general_new();
                    $api							= $datos['token'];
                    $code							= $datos['codigo'];
                    $reference						= $datos['referencia'];
                    $origin							= $datos['pais'];
                    $numberPassengers				= $datos['pasajeros'];
                    $nameContact					= $datos['nombre_contacto'];
                    $phoneContact					= $datos['telefono_contacto'];
                    $emailContact					= $datos['email_contacto'];
                    $issue							= $datos['emision'];
                    $language						= $datos['lenguaje'];
                    $namePassengerObj				= (is_object($datos['nombres'])) ? (array)$datos['nombres'] : json_decode($datos['nombres'], true);
                    $lastNamePassengerObj			= (is_object($datos['apellidos'])) ? (array)$datos['apellidos'] : json_decode($datos['apellidos'], true);
                    $documentPassengerObj  			= (is_object($datos['documentos'])) ? (array)$datos['documentos'] : json_decode($datos['documentos'], true);
                    $emailPassengerObj				= (is_object($datos['emails'])) ? (array)$datos['emails'] : json_decode($datos['emails'], true);
                    $medicalConditionsPassengerObj	= (is_object($datos['condiciones_medicas'])) ? (array)$datos['medicas'] : json_decode($datos['medicas'], true);
                    $phonePassengerObj				= (is_object($datos['telefonos'])) ? (array)$datos['telefonos'] : json_decode($datos['telefonos'], true);
                    $documentPassenger 				= $datos['documentos'];
                    $lastNamePassenger				= $datos['apellidos'];
                    $emailPassenger					= $datos['emails'];
                    $namePassenger					= $datos['nombres'];
                    $phonePassenger					= $datos['telefonos'];
                    $medicalConditionsPassenger		= $datos['condiciones_medicas'];
                    $nameContact                    = html_entity_decode($nameContact, ENT_QUOTES, "UTF-8");
                    $procedenciaBack = $datos['procedenciaBack'];
                    if (!$procedenciaBack) {
                        $procedenciaBack = '1';
                    }
                    //Solo se han comentado las lineas con el bookmark, tratar de emitir una orden con el correo vacio
                    $dataValida			= [
                        '6037'	=> !(empty($origin) and empty($numberPassengers) and  empty($documentPassenger) and  empty($namePassenger) and empty($lastNamePassenger) and empty($phonePassenger) and empty($emailPassenger) and empty($medicalConditionsPassenger) and empty($nameContact) and empty($phoneContact) and empty($emailContact) and empty($language)),
                        '6027'	=> $origin,
                        '6023'	=> $code,
                        '6026'	=> $numberPassengers,
                        '6021'	=> $language,
                        '6036'	=> $nameContact,
                        '4009'	=> $phoneContact,
                        //'4004'	=> $emailContact,
                        '6035'	=> $issue,
                        //'4004'	=> (!$this->verifyMail($emailContact)) ? 0 : 1,
                        '5010'	=> (!is_numeric($phoneContact)) ? 0 : 1,
                        '4002'	=> (empty($numberPassengers) || $numberPassengers == 0 || !is_numeric($numberPassengers)) ? 0 : 1,
                        '9012'	=> ($issue < 1 || !is_numeric($issue) || $issue > 4) ? 0 : 1,
                        '1030'	=> $this->validLanguage($language),
                        '9049'	=> ($this->countData($namePassenger, $numberPassengers)) ? 0 : 1,
                        '9053'	=> ($this->countData($lastNamePassenger, $numberPassengers)) ? 0 : 1,
                        '9050'	=> ($this->countData($documentPassenger, $numberPassengers)) ? 0 : 1,
                        '9052'	=> ($this->countData($emailPassenger, $numberPassengers)) ? 0 : 1,
                        '9054'	=> ($this->countData($phonePassenger, $numberPassengers)) ? 0 : 1,
                        '9055'	=> ($this->countData($medicalConditionsPassenger, $numberPassengers)) ? 0 : 1,
                        '9059'	=> $this->verifyOrigin($origin),
                        '9060'	=> (!preg_match('(^([a-zA-Z ÑñÁ-ú]{2,50})$)', $nameContact)) ? 0 : 1
                        //'9060'	=> (!preg_match('(^[a-zA-Z ]*$)',$nameContact))?0:1
                    ];

                    $validatEmpty	= $this->validatEmpty($dataValida);

                    if (!empty($validatEmpty)) {
                        return $validatEmpty;
                    }

                    //$plan				= $this->selectDynamic('','orders',"codigo='$code'",["producto"])[0]['producto'];
                    $plan				= $this->selectDynamic('', 'orders', "codigo='$code'", ["producto", "status"]);

                    $datAgency			= $this->datAgency($api);
                    $idAgency			= $datAgency[0]['id_broker'];
                    $isoCountry			= $datAgency[0]['id_country'];
                    $nameAgency			= $datAgency[0]['broker'];
                    $userAgency			= $datAgency[0]['user_id'];
                    $cantPassengerPlan	= $dataPlan[0]['num_pas'];
                    $prefix				= $datAgency[0]['prefijo'];

                    $verifyVoucher 		= $this->verifyVoucher($code, $userAgency, $isoCountry, 'ADD');
                    if ($verifyVoucher) {
                        return $verifyVoucher;
                    }

                    $validatePlans		= $this->validatePlans($plan[0]['producto'], '', $origin, '', '');
                    if ($validatePlans) {
                        return $validatePlans;
                    }

                    $validateDataPassenger	= $this->validateDataPassenger($numberPassengers, $namePassenger, $lastNamePassenger, '00/00/0000', $documentPassenger, $emailPassenger, $phonePassenger, $medicalConditionsPassenger, false);
                    if ($validateDataPassenger) {
                        return $validateDataPassenger;
                    }
                    if ($procedenciaBack == '2') {

                        if ($plan[0]['status'] == '9') {
                            $data	= [
                                'referencia'		=> $reference,
                                'nombre_contacto'	=> $nameContact,
                                'origen'			=> $origin,
                                'telefono_contacto'	=> $phoneContact,
                                'email_contacto'	=> $emailContact
                            ];

                            $updateOrder	= $this->updateDynamic('orders', 'codigo', $code, $data);
                        } else {

                            return $_response->getError(9137);
                        }
                    } else {

                        $data	= [
                            'referencia'		=> $reference,
                            'nombre_contacto'	=> $nameContact,
                            'origen'			=> $origin,
                            'telefono_contacto'	=> $phoneContact,
                            'email_contacto'	=> $emailContact
                        ];

                        $updateOrder	= $this->updateDynamic('orders', 'codigo', $code, $data);
                    }

                    if ($updateOrder) {
                        $idBeneficiarie	= $this->traer_ids_beneficiarios($code);
                        if ($idBeneficiarie) {

                            for ($i = 0; $i < $numberPassengers; $i++) {

                                $data	= [
                                    'email'		=> $emailPassenger[$i],
                                    'nombre'	=> $namePassenger[$i],
                                    'apellido'	=> $lastNamePassenger[$i],
                                    'documento'	=> $documentPassenger[$i],
                                    'condicion_medica'	=> $medicalConditionsPassenger[$i],
                                    'telefono'	=> $phonePassenger[$i]
                                ];

                                $updateBeneficiares	= $this->updateDynamic('beneficiaries', 'id', $idBeneficiarie[$i]['id'], $data);
                            }
                            if ($updateBeneficiares) {
                                return
                                    [
                                        'status' => "OK"
                                    ];
                            }
                        }
                    }
                    # code...
                    break;
                case 'add_order_rci':
                    /**
                     * Damned be the man who trusts in his code
                     */
                    //$quoteGeneral 					= new quote_general_new();

                    $api	      					= $datos['token'];
                    $departure   					= $datos['fecha_salida'];
                    $arrival     					= $datos['fecha_llegada'];
                    $plan        					= trim($datos['id_plan']);
                    $destination 					= trim($datos['pais_destino']);
                    $origin      					= trim($datos['pais_origen']);
                    $coin        					= trim($datos['moneda']);
                    $numberPassengers   			= trim($datos['pasajeros']);
                    $language    					= trim($datos['lenguaje']);
                    $generalConsiderations			= $datos['consideraciones_generales'];
                    $issue       					= $datos['emision'];
                    $upgrade						= $datos['upgrade'];
                    $propertyId						= $datos['propertyid']?$datos['propertyid']:'XXX';
                    $subscriberId					= $datos['subscriberid']?$datos['subscriberid']:0;
                    $relationId						= $datos['relationid']?$datos['relationid']:0;
                    $sequenceId						= $datos['sequenceid']?$datos['sequenceid']:0;
                    $reference   					= $subscriberId.$relationId.$sequenceId;

                    // $namePassengerObj				= (is_object($datos['nombres']))?(array)$datos['nombres']:json_decode($datos['nombres'],true);
                    // $lastNamePassengerObj			= (is_object($datos['apellidos']))?(array)$datos['apellidos']:json_decode($datos['apellidos'],true);
                    // $birthDayPassengerObj  			= (is_object($datos['nacimientos']))?(array)$datos['nacimientos']:json_decode($datos['nacimientos'],true);
                    // $documentPassengerObj  			= (is_object($datos['documentos']))?(array)$datos['documentos']:json_decode($datos['documentos'],true);
                    // $emailPassengerObj				= (is_object($datos['correos']))?(array)$datos['correos']:json_decode($datos['correos'],true);
                    // $medicalConditionsPassengerObj	= (is_object($datos['observaciones_medicas']))?(array)$datos['observaciones_medicas']:json_decode($datos['observaciones_medicas'],true);
                    // $phonePassengerObj				= (is_object($datos['telefonos']))?(array)$datos['telefonos']:json_decode($datos['telefonos'],true);
                    // $typeDocumentsObj				= (is_object($datos['tipo_documentos']))?(array)$datos['tipo_documentos']:json_decode($datos['tipo_documentos'],true);
                    // $sexObj							= (is_object($datos['sexo']))?(array)$datos['sexo']:json_decode($datos['sexo'],true);
                    
                    /**
                     * Esta sección no funcionó con el metodo request changesm hay que cambiarla
                     */
                    
                    $documentPassenger 				= $datos['documentos']; 
                    $birthDayPassenger				= $datos['nacimientos'];
                    $lastNamePassenger				= $datos['apellidos'];
                    $emailPassenger					= $datos['correos'];
                    $namePassenger					= $datos['nombres'];
                    $phonePassenger					= $datos['telefonos'];
                    $medicalConditionsPassenger		= $datos['condiciones_medicas'];
                    $typeDocuments					= $datos['tipo_documentos'];
                    $sexs							= $datos['sexo'];

                    $dataValida			= [
                        '6037'	=> !(empty($departure) AND empty($arrival) AND empty($plan) AND empty($destination) AND empty($origin) AND  empty($coin) AND empty($exchangeRate) AND empty($numberPassengers) AND empty($birthDayPassenger) AND  empty($documentPassenger) AND  empty($namePassenger) AND empty($lastNamePassenger) AND empty($phonePassenger) AND empty($emailPassenger) AND empty($medicalConditionsPassenger) AND empty($nameContact) AND empty($phoneContact) AND empty($emailContact) AND empty($lenguaje) AND empty($upgrade)),
                        '6029'	=> $departure,
                        '6030'	=> $arrival, 
                        '6022'	=> $plan,
                        '6028'	=> $destination,
                        '6027'	=> $origin,
                        "6026"	=> $numberPassengers,
                        '6021'	=> $language,
                        '6035'	=> $issue,
                        '2001'	=> $this->checkDates($departure),
                        '2002'	=> $this->checkDates($arrival),
                        '9059'	=> $this->verifyOrigin($origin),
                        '4029'	=> (empty($numberPassengers) or $numberPassengers == 0 or !is_numeric($numberPassengers))?0:1,
                        '5104'	=> (count($namePassenger)<=$numberPassengers) || ($numberPassengers>=count($namePassenger)),
                        /*se debe verificar esto*///'1080'	=> $this->verifyOrigin($destination),
                        '9012'	=> ($issue<1 || !is_numeric($issue) || $issue>4 )?0:1,
                        '1030'	=> $this->validLanguage($language),
                        '5101'	=> $subscriberId,
                        //'5100'	=> $relationId,
                        '5102'	=> $sequenceId,
                        '4042'  => !empty($propertyId)?($this->selectDynamic('','property_id',"resortId='$propertyId'",array("resortId"))):true,
                        '5002'  => (!$this->selectDynamic(['Relation_id' => $relationId, 'Sequence_id' => $sequenceId],'orders',"Subscriber_id='$subscriberId'",array("Subscriber_id"),false,false,false,'rci')),
                        
                        '4014'  => !empty($sex)?in_array($sex,['M','F']):true,
                        '5000'	=> !($this->validBeneficiariesRCI($namePassenger,$lastNamePassenger) || $this->validBeneficiariesRCI($birthDayPassenger,$emailPassenger)),
                    ];

                    $datAgency			= $this->datAgency($api);
                    $idAgency			= $datAgency[0]['id_broker'];
                    $idContry			= $datAgency[0]['id_country'];

                    if($idAgency==390){
                        $arrValidBrasil = [
                            '5000'	=> !($this->validBeneficiariesRCI($namePassenger,$lastNamePassenger) || $this->validBeneficiariesRCI($birthDayPassenger,$emailPassenger) || $this->validBeneficiariesRCI($documentPassenger,$typeDocuments)),
                            '5001'	=> in_array('CPF',$typeDocuments),
                            '4006'  => count($documentPassenger),
                            //'9099'  => ($this->validaCPF($documentPassenger)),
                        ];

                        $dataValida = $dataValida + $arrValidBrasil;

                    }

                    if($idAgency==383){

                        $arrValidBrasil = [
                            '5000'	=> !($this->validBeneficiariesRCI($namePassenger,$lastNamePassenger) || $this->validBeneficiariesRCI($birthDayPassenger,$emailPassenger) || $this->validBeneficiariesRCI($documentPassenger,$typeDocuments)),
                            '5001'	=> in_array('CPF',$typeDocuments),
                            '4006'  => count($documentPassenger),
                        ];
                        

                        $dataValida = $dataValida + $arrValidBrasil;
                    }

                    $validatEmpty	= $this->validatEmpty($dataValida);
                    
                    if($validatEmpty){
                        return $validatEmpty;
                    }
                    
                    $dataPlan			= $this->selectDynamic('','plans',"id='$plan'",["id_plan_categoria","name","num_pas","voucher_individual","combo"]);	
                    $idCategoryPlan 	= $dataPlan[0]['id_plan_categoria'];
                    $namePlan			= $dataPlan[0]['name'];
                    $individualOrder	= $dataPlan[0]['voucher_individual'];
                    $packPlan			= $dataPlan[0]['combo'];
                    $isoCountry			= $datAgency[0]['id_country'];
                    $nameAgency			= $datAgency[0]['broker'];
                    $userAgency			= $datAgency[0]['user_id'];
                    $numPassengerPlan	= $dataPlan[0]['num_pas'];
                    //$brokerAgency		= $datAgency[0]['broker'];
                    //$prefijoAgency		= $datAgency[0]['prefijo'];

                
                    
                    
                    $prefix				= ($datAgency[0]['prefijo'])?$datAgency[0]['prefijo']:PREFIJO;

                    $dataPlanCategory   = $this->selectDynamic('','plan_category',"id_plan_categoria='$idCategoryPlan '",["name_plan","id_status"]);

                    $name_categorys 	= $dataPlanCategory[0]['name_plan'];

                    $numPass = count($namePassenger);
                    $validateDataPassenger	= $this->validateDataPassenger($numberPassengers ,$namePassenger ,$lastNamePassenger ,$birthDayPassenger ,$documentPassenger ,$emailPassenger ,$phonePassenger ,$medicalConditionsPassenger,false,$type,$numPassengerPlan,true);

                    if($validateDataPassenger){
                        return $validateDataPassenger;
                    }

                    $arrivalTrans		= $this->transformerDate($arrival);
                    $departureTrans		= $this->transformerDate($departure);
                    $daysByPeople 		= $this->betweenDates($departureTrans ,$arrivalTrans);

                    $validateDateOrder	= $this->validateDateOrder($arrivalTrans ,$departureTrans ,$isoCountry);
                    if($validateDateOrder){
                        return $validateDateOrder;
                    }

                    $dataPlanAgency		= $this->selectDynamic('','restriction',"id_plans='$plan'",["id_broker"]);

                    if($userAgency == 910){
                    $Agency			= $dataPlanAgency[0]['id_broker'];	
                    }else{
                    $Agency          = $idAgency;
                    }

                    $validatePlans		= $this->validatePlans($plan ,$Agency ,$origin ,$destination ,$daysByPeople);
                    if($validatePlans){
                        return $validatePlans;
                    }

                    $agesPassenger		= $this->setAges($birthDayPassenger ,$isoCountry);

                    $countryAgency		= $this->getCountryAgency($api);

                    $dataQuoteGeneral	= $quoteGeneral->quotePlanbenefis($idCategoryPlan ,$daysByPeople ,$countryAgency ,$destination ,$origin ,$agesPassenger ,$departure ,$arrival ,$Agency ,$plan);
        
                    $validatBenefits	= $this->verifyBenefits($dataQuoteGeneral);
                    if($validatBenefits){
                        return $validatBenefits;
                    }


                    if($dataQuoteGeneral[0]['banda']=="si"){
                        for ($i=0; $i <$dataQuoteGeneral[0]["total_rangos"] ; $i++) {
                            $pricePassenger[] 		= $price/$numberPassengers; 
                            $costPassenger[]		= $dataQuoteGeneral[0]["costo_banda$i"];
                        }
                    }else{
                        if($dataQuoteGeneral[0]['numero_menores']>0){
                            for ($i=0; $i < $dataQuoteGeneral[0]['numero_menores'] ; $i++) { 
                                $pricePassenger[] 	= $dataQuoteGeneral[0]['valorMenor'];
                                $costPassenger[] 	= $dataQuoteGeneral[0]['costoMenor'];
                            }
                        }if($dataQuoteGeneral[0]['numero_mayores']>0){
                            for ($i=0; $i < $dataQuoteGeneral[0]['numero_mayores'] ; $i++) { 
                                $pricePassenger[] 	= $dataQuoteGeneral[0]['valorMayor'];
                                $costPassenger[] 	= $dataQuoteGeneral[0]['costoMayor'];
                            }
                        }
                    }

                    for ($i=0; $i < $numberPassengers ; $i++) { 
                        $birthDayPassengerTrans[]	= $this->transformerDate($birthDayPassenger[$i]);
                    }


                    $cost				= $costPassenger[0];
                    $price				= $pricePassenger[0];
                    $familyPlan			= $dataQuoteGeneral[0]['family_plan'];
                    $tiempo_x_producto  = $dataQuoteGeneral[0]['tiepoid'];
                    

                    $code			 = $prefix.'-'.$this->valueRandom(6);
                    $language		 = ($language=="spa")?"es":"en";
                    $upgJson         = json_decode($upgrade,true);
                    $validPack		 = true;


                    if(!empty($upgrade) || $packPlan=='1'){

                        $arrUpgrades = [];
                        $validPack	 = false;
                        if($packPlan=='1'){
                            $dataUpg 		 = $this->dataUpgradesPlan($plan,'spa');
                            $arrUpgrades 	 = array_map(function($value){return ['id'=>$value['id_raider']];} ,$dataUpg);

                    
                                if($upgJson){
                                $arrUpg 	 = $upgJson;
                                $arrUpg 	 = array_map(function($value){return ['id'=>$value['id']];} ,$arrUpg['item']);
                                $arrUpgrades = array_merge($arrUpgrades,$arrUpg);
                
                            }else{
                                $arrUpg = (array)$upgrade;
                                $arrUpg = (array)$arrUpg['item'];
                                $arrUpg = (!empty($arrUpg['id']))?[['id'=>$arrUpg['id']]]:$arrUpg;
                                
                                //$arrUpgrades = array_merge($arrUpgrades,$arrUpg);
                                
                            }

                            $upgrades['item'] =$arrUpgrades;
                            $upgrade 	= json_encode($upgrades);
                        }
                        
                        $data	= [
                            "api"				=> $api,
                            "upgrades"			=> $upgrade,
                            "codigo"			=> $code,
                            "plan"				=> $plan,
                            "daybypeople"		=> $daysByPeople,
                            "price"				=> $price,
                            "cost"				=> $cost,
                            "numberPassengers"	=> $numberPassengers,
                            "source"			=> false,
                            'beneficiaries'		=> $documentPassenger,
                            "precio_vta"		=> $pricePassenger,
                            "precio_cost"		=> $costPassenger,
                            "retorno"			=> $arrivalTrans,
                            "combo"				=> $packPlan
                        ];
                        
                        $dataUpgrade			= $this->addUpgrades($data,false,$validPack);

                        if(count($dataUpgrade["id"])==0){
                            return $dataUpgrade;
                        }else{

                            $price		= $dataUpgrade["price"];
                            $cost		= $dataUpgrade["cost"];
                            $idUpgrade 	= $dataUpgrade["id"];
                        }
                    }

                    $datAgency			= $this->datAgency($api);
                    $idAgency			= $datAgency[0]['id_broker'];

                    if($idAgency==383 || $idAgency==390){
                        $departureDate   = $this->transformerDate($departure,1);
                        $data_billeta   = date("Y-m-d",strtotime($departureDate."- 30 days"));
                        if( $data_billeta < date("Y-m-d") ){
                                $data_billeta = date("Y-m-d");	
                        }
                        $data	= [
                            'salida'				=> $departureTrans,
                            'retorno'				=> $arrivalTrans,
                            'referencia'			=> $reference,
                            'producto'				=> $plan,
                            'destino'				=> $destination,
                            'origen'				=> strtoupper($origin),
                            'agencia'				=> $idAgency,
                            'nombre_agencia'		=> $nameAgency,
                            'vendedor'				=> $userAgency,
                            'programaplan'			=> $name_categorys,
                            'family_plan'			=> $familyPlan,
                            'fecha'					=> 'now()',
                            'cantidad'				=> $numPassengerPlan,
                            'status'				=> '1',
                            'origin_ip'				=> $_SERVER['REMOTE_ADDR'],
                            'comentarios'			=> $generalConsiderations,
                            'tiempo_x_producto'		=> $tiempo_x_producto,
                            'comentario_medicas'	=> $generalConsiderations,
                            'id_emision_type'		=> '2',
                            'validez'				=> '1',
                            'hora'					=> 'now()',
                            'alter_cur'				=> "USD",
                            //'territory'				=> $destination,
                            'total_tax'				=> $dataQuoteGeneral[0]['total_tax1'],
                            //'total_tax_mlc'			=> $dataQuoteGeneral[0]['total_tax1']*$exchangeRate,
                            'total_tax_mlc'			=> $dataQuoteGeneral[0]['total_tax1'],
                            'lang'					=> $language,
                            'procedencia_funcion'	=> '1',
                            'property_id'			=> $propertyId,
                            'Subscriber_id'			=> $subscriberId,
                            'Relation_id'			=> $relationId,
                            'Sequence_id'			=> $sequenceId,
                            'fecha_proceso'         => 'now()',
                            'hora_proceso'          => 'now()',
                            'precio_base'			=> $cost,
                            'data_billeta'          => $data_billeta,
                        ];
                    }else{
                        $data	= [
                            'salida'				=> $departureTrans,
                            'retorno'				=> $arrivalTrans,
                            'referencia'			=> $reference,
                            'producto'				=> $plan,
                            'destino'				=> $destination,
                            'origen'				=> strtoupper($origin),
                            'agencia'				=> $idAgency,
                            'nombre_agencia'		=> $nameAgency,
                            'vendedor'				=> $userAgency,
                            'programaplan'			=> $name_categorys,
                            'family_plan'			=> $familyPlan,
                            'fecha'					=> 'now()',
                            'cantidad'				=> $numPassengerPlan,
                            'status'				=> '1',
                            'origin_ip'				=> $_SERVER['REMOTE_ADDR'],
                            'comentarios'			=> $generalConsiderations,
                            'tiempo_x_producto'		=> $tiempo_x_producto,
                            'comentario_medicas'	=> $generalConsiderations,
                            'id_emision_type'		=> '2',
                            'validez'				=> '1',
                            'hora'					=> 'now()',
                            'alter_cur'				=> "USD",
                            //'territory'				=> $destination,
                            'total_tax'				=> $dataQuoteGeneral[0]['total_tax1'],
                            //'total_tax_mlc'			=> $dataQuoteGeneral[0]['total_tax1']*$exchangeRate,
                            'total_tax_mlc'			=> $dataQuoteGeneral[0]['total_tax1'],
                            'lang'					=> $language,
                            'procedencia_funcion'	=> '1',
                            'property_id'			=> $propertyId,
                            'Subscriber_id'			=> $subscriberId,
                            'Relation_id'			=> $relationId,
                            'Sequence_id'			=> $sequenceId,
                            'fecha_proceso'         => 'now()',
                            'hora_proceso'          => 'now()',
                            'precio_base'			=> $cost,
                        ];
                    }

                    
                    
                    if($individualOrder=='Y' && $numberPassengers>1){

                        $data['codigo']= $code;
                        $data['total'] 	=  $price;
                        $data['neto_prov'] 	=  $cost;
                        //$data['total_mlc'] 	=  $price*$exchangeRate;
                        //$data['neto_prov_mlc'] 	=  $cost*$exchangeRate;
                        $data['total_mlc'] 	=  $price;
                        $data['neto_prov_mlc'] 	=  $cost;

                        if($issue == 4){
                            $data['status'] = 9;
                        }


                        $idOrden[] = $this->insertDynamic($data,'orders');

                        for ($i=0; $i < $numPassengerPlan ; $i++) { 
                            
                            $dataBrasil  = [
                                'id_orden'     => $idOrden,
                                'direccion' => ' ',
                                'direccion1' => ' ',
                                'ciudad' => ' ',
                                'estado' => ' ',
                                'zipcode' => '',
                                'pais_iso'   => $origin
                            ];

                            $addBeneficiaries[$i]	= $this->addBeneficiares($documentPassenger[$i] ,$birthDayPassengerTrans[$i] ,$namePassenger[$i] ,$lastNamePassenger[$i] ,$phonePassenger[$i] ,$emailPassenger[$i] ,$idOrden[$i],'1' ,$pricePassenger[$i] ,$costPassenger[$i] ,$medicalConditionsPassenger[$i] ,$pricePassenger[$i]*$exchangeRate ,$costPassenger[$i]*$exchangeRate,0,0,$typeDocuments[$i]);  
                        
                            if($idAgency == 383 || $idAgency==390){

                                $idOrdenAddress[]  = $this->insertDynamic($dataBrasil,'order_address');
                                $codigo = $data['codigo'] ;
                                $link[] = LINK_REPORTE_VENTAS.$data['codigo']."&type=".base64_encode($codigo)."&selectLanguage=$language&broker_sesion=$idAgency";
                        
                            }else{
                                $link[] = LINK_REPORTE_VENTAS.$data['codigo']."&selectLanguage=$language&broker_sesion=$idAgency";
                            }
                        }

                    }else{

                        $data['codigo'] =  $code;
                        $data['total'] 	=  $price;
                        $data['neto_prov'] 	=  $cost;
                        //$data['total_mlc'] 	=  $price*$exchangeRate;
                        //$data['neto_prov_mlc'] 	=  $cost*$exchangeRate;
                        $data['total_mlc'] 	=  $price;
                        $data['neto_prov_mlc'] 	=  $cost;
                        $data['nombre_beneficiario'] 	=  $namePassenger[0];
                        $data['apellido_beneficiario'] 	=  $lastNamePassenger[0];
                        $data['correo_beneficiario'] 	=  $emailPassenger[0];

                        if($issue == 4){
                            $data['status'] = 9;
                        }

                        $idOrden	     = $this->insertDynamic($data,'orders');

                        $dataBrasil  = [
                            'id_orden'     => $idOrden,
                            'direccion' => ' ',
                            'direccion1' => ' ',
                            'ciudad' => ' ',
                            'estado' => ' ',
                            'zipcode' => '',
                            'pais_iso'   => $origin
                        ];
                
                        

                        if($idAgency == 383 || $idAgency==390){
                            $idOrdenAddress  = $this->insertDynamic($dataBrasil,'order_address');
                            $codigo = $data['codigo'] ;
                            $link = LINK_REPORTE_VENTAS.$data['codigo']."&type=".base64_encode($codigo)."&selectLanguage=$language&broker_sesion=$idAgency";
                            
                        }else{
                            $link = LINK_REPORTE_VENTAS.$code."&selectLanguage=$language&broker_sesion=$idAgency";
                        }
                        
                        for($i=0;$i < $numberPassengers ;$i++){
                            $addBeneficiaries[$i]	= $this->addBeneficiares($documentPassenger[$i] ,$birthDayPassengerTrans[$i] ,$namePassenger[$i] ,$lastNamePassenger[$i] ,$phonePassenger[$i] ,$emailPassenger[$i] ,$idOrden,'1' ,$pricePassenger[$i] ,$costPassenger[$i] ,$medicalConditionsPassenger[$i] ,$pricePassenger[$i]*$exchangeRate ,$costPassenger[$i]*$exchangeRate,0,0,$typeDocuments[$i]);  
                        }
                    }



                    if(!empty($addBeneficiaries) && !empty($idOrden)){
                        if(is_array($idOrden)){
                            for ($i=0; $i < count($idOrden) ; $i++) { 
                                $this->addCommission($idAgency ,$idCategoryPlan ,$price ,$idOrden[$i]);
                            }
                        }else{
                            $this->addCommission($idAgency ,$idCategoryPlan ,$price ,$idOrden);
                        }
                        
                        if(count($idUpgrade)>0){
                            foreach ($idUpgrade as $value) {
                                $this->updateDynamic('orders_raider','id' ,$value ,['id_orden'=>$idOrden]);
                                //$this->addUpgradeRCI($idOrden, $dataUpgrade['benefitSpecial'],$dataUpgrade['benefitSpecialType'],$arrivalTrans,$numberPassengers);
                                $this->addUpgradeRCI($idOrden, $dataUpgrade['benefitSpecial'],$dataUpgrade['benefitSpecialType'],$arrivalTrans,$numPassengerPlan);
                            }
                        }

                        

                        switch ($issue) {
                            case '1':

                                //$this->sendOrder($emailPassenger[0] ,$idOrden ,$language ,$language);
                                if($idAgency==383 || $idAgency==390){

                                

                                    return [
                                        "status"		=> "OK", 
                                        "codigo"		=> $code,
                                        "valor"			=> $price,
                                        "costo"         => $cost,
                                        "ruta"			=> $link,
                                        "documento"		=> implode("," ,$documentPassenger),
                                        "referencia"	=> $reference,
                                        "data_billeta"  => $data_billeta
                                    ];
                                

                                }else{
                                    return [
                                        "status"		=> "OK", 
                                        "codigo"		=> $code,
                                        "valor"			=> $price,
                                        "costo"         => $cost,
                                        "ruta"			=> $link,
                                        "documento"		=> implode("," ,$documentPassenger),
                                        "referencia"	=> $reference
                                    ];
                                }
                                break;
                            case '2':
                                if($idAgency==383  || $idAgency==390 ){
                                    return [
                                        "status"		=> "OK", 
                                        "codigo"		=> $code, 
                                        "valor"			=> $price,
                                        "costo"         => $cost,
                                        "referencia"	=> $reference,
                                        "data_billeta"  => $data_billeta
                                    ];
                                }else{
                                    return [
                                        "status"		=> "OK", 
                                        "codigo"		=> $code, 
                                        "valor"			=> $price,
                                        "costo"         => $cost,
                                        "referencia"	=> $reference
                                    ];

                                }
                                break;

                            case '3':
                                //$this->sendOrder($emailPassenger[0] ,$idOrden ,$language ,$language);
                                if($idAgency==383 || $idAgency==390 ){
                                    return [
                                        "status"		=> "OK", 
                                        "codigo"		=> $code,
                                        "documento"		=> implode("," ,$documentPassenger),
                                        "referencia"	=> $reference,
                                        "data_billeta"  => $data_billeta
                                    ];
                                }else{
                                    return [
                                        "status"		=> "OK", 
                                        "codigo"		=> $code,
                                        "documento"		=> implode("," ,$documentPassenger),
                                        "referencia"	=> $reference
                                    ];

                                }
                                
                                break;
                                
                            default:
                            //$this->sendOrder($emailPassenger[0] ,$idOrden ,$language ,$language);
                                if($idAgency==383 || $idAgency==390){

                                

                                    return [
                                        "status"		=> "OK", 
                                        "codigo"		=> $code,
                                        "valor"			=> $price,
                                        "costo"         => $cost,
                                        "ruta"			=> $link,
                                        "documento"		=> implode("," ,$documentPassenger),
                                        "referencia"	=> $reference,
                                        "data_billeta"  => $data_billeta
                                    ];
                                

                                }else{
                                    return [
                                        "status"		=> "OK", 
                                        "codigo"		=> $code,
                                        "valor"			=> $price,
                                        "costo"         => $cost,
                                        "ruta"			=> $link,
                                        "documento"		=> implode("," ,$documentPassenger),
                                        "referencia"	=> $reference
                                    ];
                                }
                                break;
                        }
                    }

                    break;
                case 'get_voucher_rci':
                    # code...
                    $Llego = 'aqui1';
                    $code		= trim($datos['sucriber_id']);
                    $language	= $datos['lenguaje'];
                    $api		= $datos['api'];
                    $type		= $datos['type'];
                    $statusRegister = false;
                    
                    $arrStatus	= [
                        'Canceled',
                        'Active',
                        'To activate',
                        'Expired',
                        'Canceled',
                        'Canceled',
                        'Canceled',
                        'Canceled',
                        'Canceled',
                        'Prueba',
                        
                    ];

                    $dataValida	= [
                        '6037'	=> !(empty($code) AND empty($language) AND empty($type)),
                        '5101'	=> $code,
                        '6021'	=> $language,
                        '4444'	=> (in_array($type,[1,2,3,4,5])),
                        '1030'	=> $this->validLanguage($language)
                    ];

                    $validatEmpty	= $this->validatEmpty($dataValida);
                    if(!empty($validatEmpty)){
                        return $validatEmpty;
                    }
                    $expired =  false;
                    $error = 6063;
                    switch ($type) {
                        case 3:
                            $statusRegister = true;
                            $status 		= false;
                            $error 			= 6062;
                        break;
                        
                        case 1:
                            $status 	= false;
                            $error 		= 6063;
                        break;

                        case 2:
                            $status 	= 1;	
                            $expired 	= true;	
                            $error 		= 6061;
                        break;

                        case 4:
                            $status 	= 4;
                            $error 		= 6060;	
                        break;
                        case 5:
                            $status 	= 9;
                            $error 		= 9107;	
                        break;
                    }

                    $getDataOdersIlsbsys	= $this->getDataOders('','',$code,$status,$statusRegister,'ilsbsys',$expired,50);
                    
                    if($getDataOdersIlsbsys){
                        $getDataOders = $getDataOdersIlsbsys;
                    }else{
                        
                
                        $getDataOdersRCI	= $this->getDataOders('','',$code,$status,$statusRegister,'rci',$expired,50);
                        
                        $getDataOders = $getDataOdersRCI;
                    }

                    $lang			= ($language=='spa')?'es':'en';

                    if (count($getDataOders)){
                        foreach ($getDataOders as $key => $value) {
                            
                            $getUpgradesOrden 				= $this->getUpgradesOrden($value['id'],'rci');
                            $upgOrder = ($getUpgradesOrden)?$getUpgradesOrden:false;
                            $dato[]	= [
                                'code' 		=> $value['codigo'],
                                'departure' => $value['salida'],
                                'return'	=> $value['retorno'],
                                'status'	=> $arrStatus[$value['status_order']],
                                'completion_status' => $value['completion_status'],
                                'product' 	=> $value['producto'],
                                'property_id'=> $value['property_id'],
                                'upgrades' 	=> $upgOrder
                            ];
                        }

                        return $dato;

                    }else{
                        return $_response->getError($error);
                    }
                            break;
                case 'get_upgrades_rci':
                    $code = trim($datos['code']);
                    $api  = $datos['token'];
                    $type = $datos['type'];
                    $lang = $datos['lenguaje'];
                    $today= date('Y-m-d');
                    $dataValida	= [
                        '6037'	=> !(empty($code) AND empty($lang)  AND empty($type)),
                        '6023'	=> $code,
                        '6021'	=> $lang,
                        '1030'	=> $this->validLanguage($lang),
                        '4444'	=> (in_array($type,[1,2])),
                    ];

                    $validatEmpty	= $this->validatEmpty($dataValida);

                    if(!empty($validatEmpty)){
                        return $validatEmpty;
                    }
                    $dataOrderIlsbsys	= $this->selectDynamic('','orders',"codigo='$code'",['id','producto','status','salida'],false,false,false,'ilsbsys')[0];
                    $plataform = "ilsbsys";
                    if($dataOrderIlsbsys){
                        $dataOrder = $dataOrderIlsbsys;
                    }else{
                        $dataOrderRCI	= $this->selectDynamic('','orders',"codigo='$code'",['id','producto','status','salida'],false,false,false,'rci')[0];
                        $dataOrder = $dataOrderRCI;
                        $plataform = "rci";
                    }
                    if($dataOrder){


                        if($dataOrder['status']==1 || $dataOrder['status']==9){
                            $idOrden = $dataOrder['id'];
                            $idPlan  = $dataOrder['producto'];
                            $upgradesOrder 		= $this->getUpgradesOrden($idOrden,$plataform);
                            $idUpgradesExist 	= implode(',',array_column($upgradesOrder,'id_upgrade'));
                
                            switch ($type) {
                                case '1':
                            
                                    return ($upgradesOrder)?$upgradesOrder:$this->getError(4445);
                
                                break;
                
                                case '2':
                                
                                    $dataUpgrades = $this->getDataUpgradesRCI($idPlan,$lang,$idUpgradesExist,$plataform);
                                    return ($dataUpgrades)?$dataUpgrades:$this->getError(4446);
                
                                break;
                            }

                        }else{
                            return $this->getError(1021);
                        }
                        
                    }else{
                        return $this->getError(1020);
                    }
                    break;
                /**
                 * Métodos de suscripción:
                 * Los 5 métodos de suscripción se organizan de la siguiente manera:
                 * addsubscription y reportsubscription llaman a subscriptionsCrud
                 * extend, changes y cancel subscription llaman a subscriptionChanges
                 * 
                 * a subscriptionsCrud se le deben pasar 2 parametros cuyo key es 'one'
                 * ADD y REPORT
                 * para subschanges es
                 * EXTEND, CHANGES y CANCEL
                 */
                case 'add_subscription':
                    
                    return $this->subscriptionsCrud($datos,'ADD');
                    break;
                case 'report_subscription':
                    
                    return $this->subscriptionsCrud($datos,'REPORT');
                    break;
                case 'extend_subscription':
                    
                    return $this->subscriptionChanges($datos,'EXTEND');
                    break;
                case 'change_subscription':
                    
                    return $this->subscriptionChanges($datos,'CHANGES');
                    break;
                case 'cancel_subscription':
                    
                    return $this->subscriptionChanges($datos,'CANCEL');
                    break;
                case 'get_assistance':

                    $code		= $datos['codigo'];
                    $language	= $datos['lenguaje'];
                    $api		= $datos['api'];

                    $dataValida	= [
                        '6037'	=> !(empty($code) and empty($language)),
                        '1020'	=> $code,
                        '6021'	=> $language,
                        '1030'	=> $this->validLanguage($language)
                    ];

                    $validatEmpty	= $this->validatEmpty($dataValida);
                    if (!empty($validatEmpty)) {
                        return $validatEmpty;
                    }

                    $datAgency 		= $this->datAgency($api);
                    $prefijo		= $datAgency[0]['prefijo'];
                    $getDataOders	= $this->getDataOdersPrefijo($code, $prefijo);


                    if ($getDataOders) {

                        $vocuher = $getDataOders['codigo'];
                        $token = api_client_wta_info::initApi()
                            ->functions('getAssistance')
                            ->parameters([
                                'codeVoucher' => $vocuher ?: "NO_ENVIA_VOUCHER"
                            ])->callApi();



                        if ($token["RESPONSE"]["size"] == 0) {
                            return $_respuesta->getError('9178');
                        }

                        return	$token;
                    } else {
                        return $_respuesta->getError('1020');
                    }
                    break;
                case 'get_assistance_detail':
                    
                    $idAsistance = $datos['idAsistance'];
                    $language	= $datos['lenguaje'];
                    $api		= $datos['api'];

                    $dataValida	= [
                        '6037'	=> !(empty($idAsistance) and empty($language)),
                        '9179'	=> $idAsistance,
                        '6021'	=> $language,
                        '1030'	=> $this->validLanguage($language)
                    ];


                    $validatEmpty	= $this->validatEmpty($dataValida);
                    if (!empty($validatEmpty)) {
                        return $validatEmpty;
                    }

                    $datAgency 		= $this->datAgency($api);
                    $idPrefijo		= $datAgency[0]['prefijo'];

                    $token = api_client_wta_info::initApi()
                        ->functions('assistBelongToClient')
                        ->parameters([
                            'idAssist' => $idAsistance ?: "NO_ENVIA_ASISTENCIA",
                            'prefix' => $idPrefijo ?: "NO_ENVIA_PREFIJO"
                        ])->callApi();

                    if ($token["RESPONSE"] == "NO") {
                        return $_respuesta->getError('9181');
                    } else {

                        $token = api_client_wta_info::initApi()
                            ->functions('GetAssistanceDetail')
                            ->parameters([
                                'idAssist' => $idAsistance ?: "NO_ENVIA_ASISTENCIA"
                            ])->callApi();

                        if (!$token["RESPONSE"]) {
                            return $_respuesta->getError('9180');
                        } else {
                            return	$token;
                        }
                    }
                    break;
                case 'get_timeline':
                    
                    $idAsistance = $datos['idAsistance'];
                    $language	= $datos['lenguaje'];
                    $api		= $datos['api'];

                    $dataValida	= [
                        '6037'	=> !(empty($idAsistance) and empty($language)),
                        '9179'	=> $idAsistance,
                        '6021'	=> $language,
                        '1030'	=> $this->validLanguage($language)
                    ];

                    $validatEmpty	= $this->validatEmpty($dataValida);
                    if (!empty($validatEmpty)) {
                        return $validatEmpty;
                    }

                    $datAgency 		= $this->datAgency($api);
                    $idPrefijo		= $datAgency[0]['prefijo'];

                    $token = api_client_wta_info::initApi()
                        ->functions('assistBelongToClient')
                        ->parameters([
                            'idAssist' => $idAsistance ?: "NO_ENVIA_ASISTENCIA",
                            'prefix' => $idPrefijo ?: "NO_ENVIA_PREFIJO"
                        ])->callApi();

                    if ($token["RESPONSE"] == "NO") {
                        return $_respuesta->getError('9181');
                    } else {

                        $token = api_client_wta_info::initApi()
                            ->functions('getTimeLine')
                            ->parameters([
                                'idAssist' => $idAsistance ?: "NO_ENVIA_ASISTENCIA"
                            ])->callApi();


                        if (!$token["RESPONSE"]) {
                            return $_respuesta->getError('9180');
                        } else {
                            return	$token;
                        }
                    }
                    break;
                case 'get_benefit_to_case':
                    
                    $idAsistance = $datos['idAsistance'];
                    $language	 = $datos['lenguaje'];
                    $api		 = $datos['api'];

                    $dataValida	= [
                        '6037'	=> !(empty($idAsistance) and empty($language)),
                        '9179'	=> $idAsistance,
                        '6021'	=> $language,
                        '1030'	=> $this->validLanguage($language)
                    ];

                    $validatEmpty	= $this->validatEmpty($dataValida);
                    if (!empty($validatEmpty)) {
                        return $validatEmpty;
                    }
                    $datAgency 		= $this->datAgency($api);
                    $idPrefijo		= $datAgency[0]['prefijo'];

                    /*if('201.208.47.95' == $_SERVER['REMOTE_ADDR']){
                        die(var_dump("ojo ",$idPrefijo));
                    }*/

                    $token = api_client_wta_info::initApi()
                        ->functions('assistBelongToClient')
                        ->parameters([
                            'idAssist' => $idAsistance ?: "NO_ENVIA_ASISTENCIA",
                            'prefix' => $idPrefijo ?: "NO_ENVIA_PREFIJO"
                        ])->callApi();

                    if ($token["RESPONSE"] == "NO") {
                        return $_respuesta->getError('9181');
                    } else {

                        $token = api_client_wta_info::initApi()
                            ->functions('GetBenefitToCase')
                            ->parameters([
                                'idAssist' => $idAsistance ?: "NO_ENVIA_ASISTENCIA"
                            ])->callApi();

                        if (!$token["RESPONSE"]) {
                            return $_respuesta->getError('9180');
                        } else {
                            return	$token;
                        }
                    }
                    break;
                case 'get_beneficiaries_by_code':
                    $apikey = $datos['token'];
                    $voucher = $datos['code'];
                    if(empty($apikey) AND empty($voucher)){
                        return $_respuesta->geterror("6037");
                    }
                    $ArrayValida= array(
                        '6020'=>$apikey,
                        '6023'=>$voucher
                    );
                    $valid=$this->valida_empty($ArrayValida);
                    if(!empty($valid)){
                        return $valid;
                    }
                    if(!$this->checkapiKey($apikey)){
                        return $this->geterror("1005");
                    }
                    $data_broker=$this->data_broker($apikey);
                    $id_broker = $data_broker['id_broker'];
                    $user_agencia = $data_broker['user_id'];
                    $validar_voucher = $this->validar_voucher($voucher,$user_agencia,$data_broker['id_country'],false,true);
                    if($validar_voucher){
                        return $validar_voucher;
                    }
                    $arraBenef=$this->datos_beneficiaries($voucher,true);
                    return ($arraBenef)?$arraBenef:$this->getError('1051');

                    # Este método debe ser revisado bien a fondo
                    break;
                case 'get_exchange_rate_by_date':
                    
                    if(empty($apikey) AND empty($valueIso) AND empty($date)){
                        return $this->getError('6020');
                    }
            
                    if(!$this->checkapiKey($apikey)){
                        return $this->geterror("1005");
                    }
            
                    $quoteGeneral = new Quote_general();
            
                    $ArrayValida= array(
            
                        '6034'=>$valueIso,
                    );
            
                    
                    if(!empty($date)){
                        $validDays	= $this->buscar_dias($date,date('d/m/Y'))-1;
                        $dateImplode =  explode('/',$date);
                        $validDate = checkdate($dateImplode[1],$dateImplode[0],$dateImplode[2]);
                        if($validDays<0 || !$validDate){
                            return $this->getError('9063');
                        }
                    }
            
                    $validateEmpty	=	$this->valida_empty($ArrayValida);
                    if(!empty($validateEmpty)){
                        return $validateEmpty;
                    }
                    
                    $date 		= !empty($date)?$this->transformer_date($date):date('Y-m-d');
            
                    $idCurrency = $this->getIdCurrency($valueIso);
            
                    if($valueIso=='USD' || empty($idCurrency)){
                        return $this->getError('1022');
                    }
                    $tasa = $quoteGeneral->exchangeRate($idCurrency,$date);
                    if(empty($tasa)){
                        return $this->getError('1022');
                    }else{
                        return [
                            'exchange_rate'=>$tasa,
                        ];
                    }
            
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
            foreach ($arrayValidate as $key) {
                if (!empty($key)) {
                    return  $key;
                }
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
                $plan 				= $dataOrder['producto'];
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

            $upgrd2 = json_decode($data['upgrades'],true)['item'];
            
            for ($i = 0; $i < $countDataUpgrade; $i++) {
                
                /**
                 * $id 		= $data['upgrades'][$i];
                 * $document 	= $data['upgrades'][$i]['documento'];
                 */
                $id 		= $upgrd2[$i]['id'];
                $document 	= $upgrd2[$i]['documento'];
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
                                
                                $pricePassengers = $this->dataBeneficiaries($code, '', $data['documentos']);
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
            $_response = new response;
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
                $newdoc = implode(', ', $document);
                $query .= " AND documento IN ('$newdoc') ";
            }
            $response = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
            return ($response) ? $response : $_response->getError('9028');
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

        public function GetId($prefix)
        {
            $response = apiClientWta::initApi()
                ->method("GET")
                ->functions('getLastIdOrder')
                ->parameters(['prefijo' => $prefix])->callApi();
            return $response['RESPONSE'];
        }

        public function getLastIdOrder()
        {
            $query = "SELECT MAX(id) id FROM orders";
            return $this->selectDynamic('', '', '', '', $query)[0]['id'];
        }

        public function getLastIdBeneficiarie()
        {
            $query = "SELECT MAX(id) id_beneficiarie FROM beneficiaries";
            return $this->selectDynamic('', '', '', '', $query)[0]['id_beneficiarie'];
        }
        public function addBeneficiares($documentPassenger, $birthDayPassenger, $namePassenger, $lastNamePassenger, $phonePassenger, $emailPassenger, $idOrden, $status_ben, $price, $cost, $observacion, $precio_local, $costo_local, $tax_beneficiario, $tax_local_beneficiario, $prefix, $idben)
        {

            $data   =
                [
                    'documento'         => $documentPassenger,
                    'nacimiento'        => $birthDayPassenger,
                    'nombre'            => $namePassenger,
                    'apellido'          => $lastNamePassenger,
                    'telefono'          => $phonePassenger,
                    'email'             => $emailPassenger,
                    'id_orden'          => $idOrden,
                    'ben_status'        => '1',
                    'precio_vta'        => $price,
                    'precio_cost'       => $cost,
                    'condicion_medica'  => $observacion,
                    'precio_vta_mlc'    => $precio_local,
                    'precio_cost_mlc'   => $costo_local,
                    'tax_total'         => $tax_beneficiario,
                    'tax_total_mlc'     => $tax_local_beneficiario,
                    //'prefijo'           => $prefix,
                    'id'                => $idben,
                    'total_neto_benefit'       => $price,
                    'neto_cost'       => $cost

                ];
            return $this->insertDynamic($data, 'beneficiaries');
        }

        public function addCommission($idAgency, $idPlanCategory, $price, $idOrden)
        {
            $agencyLevel           = $this->Get_Broker_Nivel($idAgency);
            $porcentageCommission  = 0;
            for ($i = $agencyLevel['nivel']; $i > 0; $i--) {
                $byCommission           = $this->AgenciaNivelCategoriaComision($idAgency, $idPlanCategory);
                $porcentage             = $byCommission - $porcentageCommission;
                $valueCommission        = ($porcentage > 0) ? (($porcentage / 100) * $price) : 0;

                $this->Add_order_Comision($idOrden, $idAgency, $porcentage, $valueCommission);
                $porcentageCommission   = $byCommission;
                $agencyLevel            = $this->Get_Broker_Nivel($idAgency);
                $idAgency               = $agencyLevel['parent'];
            }
        }

        public function Get_Broker_Nivel($idbroker)
        {
            $query = "SELECT broker_nivel.id, broker_nivel.id_broker,  broker_nivel.nivel, broker_nivel.parent FROM broker_nivel WHERE id_broker ='$idbroker'";
            $response = $this->_SQL_tool($this->SELECT_SINGLE, __METHOD__, $query);
            if ($response) {
                $arrResult['id'] = $response['id'];
                $arrResult['nivel'] = $response['nivel'];
                $arrResult['parent'] = $response['parent'];
                $arrResult['id_broker'] = $response['id_broker'];
            }
            return ($arrResult);
        }

        public function AgenciaNivelCategoriaComision($id_broker, $categoria)
        {
            $query = "SELECT
                    porcentaje
                FROM
                    commissions
                WHERE
                    commissions.id_categoria = '$categoria'
                AND id_agencia = '$id_broker'";
            $response = $this->_SQL_tool($this->SELECT_SINGLE, __METHOD__, $query);
            return isset($response['porcentaje']) ? $response['porcentaje'] : 0;
        }

        public function Add_order_Comision($idorden, $idbroker, $porcentaje, $montocomision)
        {
            $data   = [
                'id_order'          => $idorden,
                'id_broker'         => $idbroker,
                'porcentage'        => $porcentaje,
                'monto_comision'    => $montocomision,
                'tr_date'           => 'NOW()'
            ];
            return $this->insertDynamic($data, 'order_comision');
        }

        /*

        public function sendMailCancel($code, $agency, $language, $templateMail = 'VOUCHER_CANCEL')
        {
            global $CORE_email;
            $logo               = $this->getLogoMaster($agency);
            $dataPassenger      = $this->getBeneficiariesByVoucher($code);
            $emailPassenger     = array_map(function ($value) {
                return $value['email'];
            }, $dataPassenger);
            $today              = date("d-m-Y");
            $variables_email    = [
                "##voucher##"   => stripslashes(strip_tags($code)),
                "##hoy##"       => stripslashes(strip_tags($today)),
                "##system##"    => stripslashes(strip_tags(SYSTEM_NAME)),
                "##logo##"      => $logo
            ];

            foreach ($variables_email as $varstr => $varvalue) {
                $CORE_email->setVariable($varstr, $varvalue);
            }
            $from = [
                'name'  => EMAIL_FROM_NAME,
                'email' => EMAIL_FROM
            ];
            $CORE_email->send($from, $to = $emailPassenger, $templateMail, $language);
        }
        Esta opción se habilitará en pruebas futuras
        */
        public function deleteUpgradeOrder($idorden, $idraider)
        {
            $query = "DELETE 
                FROM
                    orders_raider
                WHERE
                    id_orden    = '$idorden'
                AND id_raider   = '$idraider'";
            return $this->_SQL_tool($this->DELETE, __METHOD__, $query);
        }

        public function validateCorporate($idCategoryPlan, $reference, $code)
        {
            $today  = date('Y-m-d');
            $code   = explode('-', $code);
            if ($idCategoryPlan == 14) {
                $codeMaster   = $this->selectDynamic('', 'orders', "codigo='$reference'", array("codigo", "retorno"))[0];
                if ($codeMaster['codigo'] == '') {
                    return $this->getError('9120');
                }

                if ($today > $codeMaster['retorno']) {
                    return $this->getError('9121');
                }
                if ($code[0] != $reference) {
                    return $this->getError('9122');
                }
            }
        }

        public function getTypeByStatus($apiKey){
            $query = "SELECT user_type FROM users WHERE api_key = '$apiKey'";
            $response = $this->_SQL_tool($this->SELECT_SINGLE, __METHOD__, $query);
            return $response;
        }

        public function validateCategory($idCategoryPlan)
        {
            if ($idCategoryPlan != 14) {
                return $this->getError('9117');
            }
        }

        public function crudBeneficiaries($data)
        {
            $_response = new response;
            $quoteGeneral 				= new quote_general_new;
            $api      					= $data['api'];
            $code        				= $data['codigo'];
            $action		   				= $data['action'];
            $passengerObj				= (is_object($data['databeneficiarie'])) ? (array)$data['databeneficiarie'] : json_decode($data['databeneficiarie'], true);
            $birthDayPassenger			= $passengerObj['nacimiento'];
            $emailPassenger				= $passengerObj['email'];
            $namePassenger				= $passengerObj['nombres'];
            $lastNamePassenger			= $passengerObj['apellidos'];
            $documentPassenger			= $passengerObj['documento'];
            $medicalConditionsPassenger	= $passengerObj['medicas'];
            $phonePassenger				= $passengerObj['telefono'];
            $idPassenger				= $passengerObj['idbeneficiarie'];
            $procedenciaBack = $data['procedenciaBack'];
            if (!$procedenciaBack) {
                $procedenciaBack = '1';
            }

            $dataValida		= [
                '6023'		=> $code,
                '9024'		=> $action
            ];

            $validatEmpty	= $this->validatEmpty($dataValida);
            if (!empty($validatEmpty)) {
                return $validatEmpty;
            }

            $datAgency	 		= $this->datAgency($api);
            $isoCountry			= $datAgency[0]['id_country'];
            $idAgency			= $datAgency[0]['id_broker'];
            $idUser				= $datAgency[0]['user_id'];
            $dataOrder			= $this->getOrderData($code);
            $plan				= $dataOrder['producto'];
            $idOrden			= $dataOrder['id'];
            $status			    = $dataOrder['status'];
            $dataPlan			= $this->selectDynamic('', 'plans', "id='$plan'", array("id_plan_categoria"));
            $idCategoryPlan 	= $dataPlan[0]['id_plan_categoria'];
            $departure			= $dataOrder['salida'];
            $arrival			= $dataOrder['retorno'];
            $exchangeRate		= $dataOrder['tasa_cambio'];

            $departureTrans				= $this->transformerDate($departure, 2);
            $arrivalTrans				= $this->transformerDate($arrival, 2);
            $daysByPeople   			= $this->betweenDates($departure, $arrival);
            $birthDayPassengerTrans		= $this->transformerDate($birthDayPassenger);

            $countryAgency 				= $this->getCountryAgency($api);


            if ($procedenciaBack == '2' && $status != '9') {
                return $_response->getError('9137');
            }

            if ($action != 'INTERNO') {

                $verifyVoucher	= $this->verifyVoucher($code, $idUser, $isoCountry, 'REPORT');
                if (!empty($verifyVoucher)) {
                    return $verifyVoucher;
                }
            }


            if ($action == 'PUT' || $action == 'DELETE') {

                $putValid	= [
                    '9026'	=> $idPassenger,
                    '9027'	=> is_numeric($idPassenger)
                ];

                $validatEmpty	= $this->validatEmpty($putValid);
                if (!empty($validatEmpty)) {
                    return $validatEmpty;
                }

                $verifiedBeneficiaries	= $this->verifiedBeneficiariesByVoucher($code, $idPassenger);
                if ($verifiedBeneficiaries) {
                    return $verifiedBeneficiaries;
                }
            }

            if ($action == 'ADD' || $action == 'PUT') {
                $validateDataPassenger = $this->validateDataPassenger(1, (array)$namePassenger, (array)$lastNamePassenger, (array)$birthDayPassenger, (array)$documentPassenger, (array)$emailPassenger, (array)$phonePassenger, (array)$medicalConditionsPassenger);
                if ($validateDataPassenger) {
                    return $validateDataPassenger;
                }
            }

            $dataBeneficiaries		= $this->getBeneficiariesByVoucher($code);
            $numberPassenger		= count($dataBeneficiaries);
            $birthDayBeneficiaries	=
                array_map(
                    function ($value) {
                        return $value['nacimiento'];
                    },
                    $dataBeneficiaries
                );
            /*if('200.84.219.45' == $_SERVER['REMOTE_ADDR']){
                die(var_dump("prueba",$birthDayBeneficiaries));
            }*/
            if ($birthDayPassengerTrans) {
                array_push($birthDayBeneficiaries, $birthDayPassengerTrans);
            }

            $agesPassenger			= $this->setAges($birthDayBeneficiaries, $isoCountry);

            $dataQuoteGeneral		= $quoteGeneral->quotePlanbenefis($idCategoryPlan, $daysByPeople, $countryAgency, $dataOrder['territory'], $dataOrder['origen'], $agesPassenger, $departureTrans, $arrivalTrans, $idAgency, $plan);

            $validatBenefits		= $this->verifyBenefits($dataQuoteGeneral);
            if ($validatBenefits) {
                return $validatBenefits;
            }


            switch ($action) {
                case 'ADD':
                    $beneficiariesDuplicate = $this->verifiedBeneficiariesDuplicate($idOrden, array($documentPassenger), array($birthDayPassengerTrans), 9062);
                    if (!empty($beneficiariesDuplicate)) {
                        return $beneficiariesDuplicate;
                    }
                    $datAgency	= $this->datAgency($api);
                    $prefix	= $datAgency[0]['prefijo'];
                    $DataWta  = $this->GetId($prefix);
                    $BeneficiarieId = $this->getLastIdBeneficiarie();
                    $WtaopsBen = $DataWta['beneficiary'];
                    $beneficiary = (($WtaopsBen > $BeneficiarieId) ? $WtaopsBen : $BeneficiarieId) + 1;
                    $idben = $beneficiary;

                    $this->addBeneficiares($documentPassenger, $birthDayPassengerTrans, $namePassenger, $lastNamePassenger, $phonePassenger, $emailPassenger, $idOrden, '1', '0', '0', $medicalConditionsPassenger, '0', '0', '0', '0', $prefix, $idben);

                    break;
                case 'PUT':

                    $data	= [
                        'nombre'			=> $namePassenger,
                        'apellido'			=> $lastNamePassenger,
                        'telefono'			=> $phonePassenger,
                        'nacimiento'		=> $birthDayPassengerTrans,
                        'condicion_medica'	=> $medicalConditionsPassenger,
                        'documento'			=> $documentPassenger,
                        'email'				=> $emailPassenger
                    ];

                    break;

                case 'DELETE':

                    if ($dataOrder['cantidad'] == '1') {
                        return $_response->getError('9031');
                    }
                    $data	= [
                        'ben_status'	=> '2',
                    ];
                    break;

                case 'INTERNO':

                    break;
                default:
                    return $_response->getError('9030');
                    break;
            }


            if ($action == 'PUT' || $action == 'DELETE') {

                $updateDynamicBeneficiares = $this->updateDynamic('beneficiaries', 'id', $idPassenger, $data);
            }



            $cost							= $dataQuoteGeneral[0]['total_costo'];
            $price							= $dataOrder['total'];


            $familyPlan						= $dataQuoteGeneral[0]['family_plan'];

            if ($dataQuoteGeneral[0]['banda'] == "si") {
                for ($i = 0; $i < $dataQuoteGeneral[0]["total_rangos"]; $i++) {
                    $pricePassenger[] 		= $price / $numberPassenger;
                    $costPassenger[]		= $dataQuoteGeneral[0]["costo_banda$i"];
                }
            } else {
                if ($dataQuoteGeneral[0]['numero_menores'] > 0) {
                    for ($i = 0; $i < $dataQuoteGeneral[0]['numero_menores']; $i++) {
                        $pricePassenger[] 	= $price / $numberPassenger;
                        $costPassenger[] 	= $dataQuoteGeneral[0]['costoMenor'];
                    }
                }
                if ($dataQuoteGeneral[0]['numero_mayores'] > 0) {
                    for ($i = 0; $i < $dataQuoteGeneral[0]['numero_mayores']; $i++) {
                        $pricePassenger[] 	= $price / $numberPassenger;
                        $costPassenger[] 	= $dataQuoteGeneral[0]['costoMayor'];
                    }
                }
            }

            $idBeneficiaries	= array_map(function ($value) {
                return $value['id'];
            }, $dataBeneficiaries);

            /*if('190.36.165.78' == $_SERVER['REMOTE_ADDR']){
                die(var_dump($pricePassenger));

            }*/

            for ($i = 0; $i < $numberPassenger; $i++) {

                $data	= [
                    'precio_vta'		=> $pricePassenger[$i],
                    'precio_cost'		=> $costPassenger[$i],
                    //'precio_vta_mlc'	=> $costPassenger[$i]/$exchangeRate
                    'precio_vta_mlc'	=> $pricePassenger[$i] / $exchangeRate,
                    'precio_cost_mlc'	=> $costPassenger[$i] / $exchangeRate
                    //	'neto_cost'         => $costPassenger[$i],
                    //	'total_neto_benefit'	=> $pricePassenger[$i]
                ];

                $updateBeneficiares	= $this->updateDynamic('beneficiaries', 'id', $idBeneficiaries[$i], $data);
            }

            $data	= [
                'total'			=> $price,
                'cantidad'		=> $numberPassenger,
                'neto_prov'		=> $cost,
                'neto_prov_mlc'	=> $cost / $exchangeRate,
                'total_mlc'   	=> $price / $exchangeRate,
                'family_plan'	=> $familyPlan
            ];
            $updateOrder	= $this->updateDynamic('orders', 'codigo', $code, $data);

            if ($updateOrder) {
                return array('status' => 'OK', 'Result' => 'successful ' . $action);
            }
        }

        public function verifiedBeneficiariesByVoucher($code, $idPassenger)
        {   
            $_response = new response;
            $query = "SELECT
            beneficiaries.id,
            beneficiaries.ben_status
            FROM
                beneficiaries
            WHERE
                beneficiaries.id_orden IN (
                    SELECT
                        orders.id
                    FROM
                        orders
                    WHERE
                        orders.codigo = '$code'
                )
            AND beneficiaries.id = '$idPassenger' ";
            $response     = $this->_SQL_tool($this->SELECT_SINGLE, __METHOD__, $query);
            if (empty($response)) {
                return $_response->geterror('9028');
            }
            if ($response['ben_status'] == '2') {
                return $_response->geterror('9029');
            }
        }

        public function getBeneficiariesByVoucher($voucher)
        {
            $query = "SELECT
                id,
                nacimiento,
                nombre,
                apellido,
                email
            FROM
                beneficiaries
            WHERE
                id_orden IN (
                    SELECT
                        id
                    FROM
                        orders
                    WHERE
                        codigo = '$voucher'
                )
            AND beneficiaries.ben_status = '1'";
            return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
        }

        function traer_ids_beneficiarios($codigo)
        {
            $_response = new response;
            $query = "SELECT
                beneficiaries.id
            FROM beneficiaries
                Inner Join orders ON orders.id = beneficiaries.id_orden
            where
                orders.codigo ='$codigo'";

            $response = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
            if ($response) {
                return $response;
            } else {
                return $_response->getError('1051');
            }
        }

        public function logsave($operacion,$request,$_response,$prefijo,$procedencia = '1',$token,$id_error,$num_voucher,$num_referencia,$idUser){
            /**
             * Datos que debemos recibir:
             * -fecha
             * -hora
             * -IP
             * -Operación realizada
             * -Datos (facil)
             * -Respuesta obtenida
             * -Prefijo
             * -procedencia ???
             * -apikey
             * -id_error
             * -num_voucher
             * -num_referencia
             * -id_user
             */

            $data   = [
                'fecha'             => 'NOW()',
                'hora'              => 'NOW()',
                'ip'                => $_SERVER['REMOTE_ADDR'],
                'operacion'         => $operacion,
                'datos'             => $request,
                'respuesta'         => $_response,
                'prefijo'           => $prefijo,
                'procedencia'       => $procedencia,
                'apikey'            => $token,
                'id_error'          => $id_error,
                'num_voucher'       => $num_voucher,
                'num_referencia'    => $num_referencia,
                'id_user'           => ($idUser) ? $idUser : 0
            ];
            return $this->insertDynamic($data, 'trans_all_webservice');
        }

        public function dataUpgradesPlan($plan,$language){

            $query="SELECT
                raiders.id_raider,
                raiders_detail.name_raider,
                raiders.type_raider,
                raiders.value_raider,
                raiders.cost_raider,
                raiders.stack,
                raiders.days_add,
                raiders.type_upg,
                raiders.rd_calc_type,
                IF (
                    benefit_special,
                    CONCAT(
                        benefit_special,
                        ' ',
    
                    IF (
                        benefit_special_type = 'P',
                        'Pasajeros Adicionales',
                        'Dias Adicionales'
                    )
                    ),
                    'N/A'
                ) AS additional_value,
                IFNULL(restriction_days, 'N/A') AS restriction_days,
                IFNULL(restriction_pass, 'N/A') AS restriction_pass
            FROM
                raiders
                INNER JOIN raiders_detail ON raiders_detail.id_raider = raiders.id_raider
                INNER JOIN plan_raider ON raiders.id_raider = plan_raider.id_raider
            WHERE
                plan_raider.id_plan = '$plan' 
            AND raiders_detail.language_id='$language'";
            
            return $this->selectDynamic('','','','',$query);
        }

        public function validBeneficiariesRCI($arrStart,$arrEnd)
        {
            return count(
                array_merge(
                    array_diff_key($arrStart,$arrEnd),
                    array_diff_key($arrEnd,$arrStart)
                )
            );
        }

        public function addUpgradeRCI($idOrden,$benefitSpecial,$benefitSpecialType,$arrival,$numPass,$typeBen=true)
        {
            $cantidad   = $this->selectDynamic('','orders',"id='$idOrden'",array("cantidad"))[0]['cantidad'];
            
        

            $dataUpdate = [];
            if($typeBen){
                $typeOpDaysBenefit = "+ $benefitSpecial";
                $typeOpPassBenefit = $benefitSpecial+$numPass;
            }else{
                $typeOpDaysBenefit = "- $benefitSpecial";
                $typeOpPassBenefit = $numPass-$benefitSpecial;
            }
        

            /* if($typeOpPassBenefit > 8){
                return $this->getError('9098');
            }*/


            switch ($benefitSpecialType) {

                
                case 'D':

                    $dataUpdate = [
                        'tiempo_x_producto'=> $benefitSpecial,
                        'retorno'   => date("Y-m-d",strtotime($arrival." $typeOpDaysBenefit days")) 
                    ];

                
                    break;
                case 'P':

                    $dataUpdate = [
                        'cantidad'=> $typeOpPassBenefit
                    ];

                    break;


                case 'A':

                    $benefitSpecialD = 8;
                    $benefitSpecialP = 4;
        
                    /* if('200.84.222.177' == $_SERVER['REMOTE_ADDR']){
        
                            die(var_dump( $dataUpdate , $typeBen ,  $typeOpDaysBenefit , $typeOpPassBenefit , $numPass , $arrival));
        
                        }*/
        
                    if($typeBen){
        
                        $typeOpDaysBenefit = "+ $benefitSpecialD";
                        $typeOpPassBenefit = $benefitSpecialP+$numPass;
        
                    }else{
        
                        $typeOpDaysBenefit = "- $benefitSpecialD";
                        $typeOpPassBenefit = $numPass-$benefitSpecialP;
        
                    }

                    $dataUpdate = [
                        'tiempo_x_producto'=>date("Y-m-d",strtotime($arrival." $typeOpDaysBenefit days")) ,
                        'retorno'   => date("Y-m-d",strtotime($arrival." $typeOpDaysBenefit days")) ,
                        'cantidad'=> $typeOpPassBenefit
                    ];
        
                    break;

            }
        
            if(count($dataUpdate))
            return $this->updateDynamic('orders','id',$idOrden,$dataUpdate);
        }

        public function getDataOders($code,$idUser,$reference,$status,$statusRegister,$database='ilsbsys',$expired,$limit){

            $query = "SELECT
            orders.id,
            orders.origen,
            orders.destino,
            orders.salida,
            orders.retorno,
            orders.programaplan,
            orders.nombre_contacto,
            orders.email_contacto,
            orders.comentarios,
            orders.telefono_contacto,
            orders.producto,
            orders.agencia,
            orders.nombre_agencia,
            orders.total,
            orders.codigo,
            orders.fecha,
            orders.vendedor,
            orders.cantidad,
            orders.status,
            IF (
                DATEDIFF(orders.retorno, CURDATE()) > 0,
                orders.status,
                3
            ) AS status_order,
            orders.es_emision_corp,
            orders.origin_ip,
            orders.alter_cur,
            orders.tasa_cambio,
            orders.family_plan,
            orders.referencia,
            orders.Subscriber_id,
            orders.Sequence_id,
            orders.Relation_id,
            orders.property_id,
            IF (
                orders.status_register NOT IN (1,2,3),
                'completed',
                'pending'
            ) AS completion_status
            FROM
                orders
            WHERE
                1 ";
    
            /*if($idUser){
                $query .= " AND orders.vendedor ='$idUser' ";
            }*/
    
            if($code){
                $query .= " AND orders.codigo ='$code' ";
            }
    
            if($reference){
                $query .= " AND orders.referencia LIKE '%$reference%' ";
            }
    
            if($status){
                $query .= " AND orders.status = '$status' ";
            }
    
            if($statusRegister){
                $query .= " AND (orders.status_register NOT IN (1, 2, 3)
                OR orders.status_register IS NULL ) ";
            }
    
            if($expired){
                $query .= " AND DATEDIFF(orders.retorno,CURDATE())>0 ";
            }
    
            
            return $this->selectDynamic('','','','',$query,false,$limit,$database);  
        }

        public function getUpgradesOrden($idOrder,$database='ilsbsys'){
            if($idOrder){
                $query = "SELECT
                orders_raider.id_raider AS id_upgrade,
                orders_raider.value_raider AS value_upgrade,
                orders_raider.cost_raider AS cost_upgrade,
                name_raider AS name_upgrade
                FROM
                    orders_raider
                INNER JOIN raiders ON raiders.id_raider = orders_raider.id_raider
                WHERE
                    orders_raider.id_orden = $idOrder ";
    
                $response   = $this->selectDynamic('','','','',$query,false,false,$database);
               
                return $response;
            }else{
                return false;
            }
        }

        public function subscriptionsCrud($data, $type = 'ADD')
        {
            
            $api 			= $data["api"];
            $inceptionDate	= $data["InceptionDate"];
            $renewalDate	= $data["RenewalDate"];
            $subscriptionId	= $data["SubscriptionId"];
            $reference 		= $data["Reference"];
            $masterId		= $data["MasterId"];
            $planId			= $data["PlanId"];
            $countryOrigin	= $data["CountryOrigin"];
            $subscriberName	= $data["SubscriberName"];
            $subscriberLastName	= $data["SubscriberLastName"];
            $subscriberEmail	= $data["SubscriberEmail"];
            $subscriberPhone	= $data["SubscriberPhone"];
            $generalConsiderations	= $data['consideraciones_generales'];
            $language			= $data["Language"];
            $emission			= $data["Emission"];
            $procedencia 		= 1;

            $arrValida	= [
                '6037'	=> !(empty($inceptionDate) and empty($subscriptionId)  and empty($masterId)  and empty($planId)  and empty($countryOrigin)  and empty($subscriberName)  and empty($subscriberLastName) and empty($subscriberEmail) and empty($language)),
                '9071'	=> $inceptionDate,
                '9086'	=> ($type == 'REPORT') ? $subscriptionId : true,
                '9072'	=> $masterId,
                '9024'	=> $planId,
                '6027'	=> $countryOrigin,
                '9073'	=> $subscriberName,
                '9074'	=> $subscriberLastName,
                '9075'	=> $subscriberEmail,
                '6021'	=> $language,
                '9076'	=> $this->checkDates($inceptionDate),
                '9077'	=> (!empty($renewalDate)) ? $this->checkDates($renewalDate) : true,
                '9078'	=> (!$this->selectDynamic('', 'orders', "codigo='$subscriptionId'", array("codigo"))) ? 1 : 0,
                '9079'	=> (strlen($subscriptionId) > 30) ? 0 : 1,
                '1090'	=> $this->verifyOrigin($countryOrigin),
                //'9080'	=> (!preg_match('(^[a-zA-Z ]*$)',$subscriberName))?0:1,
                '9080'	=> (!preg_match('(^([a-zA-Z ÑñÁ-ú]{2,50})$)', $subscriberName)) ? 0 : 1,
                //'9081'	=> (!preg_match('(^[a-zA-Z ]*$)',$subscriberLastName))?0:1,
                '9081'	=> (!preg_match('(^([a-zA-Z ÑñÁ-ú]{2,50})$)', $subscriberLastName)) ? 0 : 1,
                '9082'	=> (!empty($subscriberPhone)) ? (is_numeric($subscriberPhone)) : true,
                '9088'	=> (is_numeric($masterId)),
                '9083'	=> (!$this->verifyMail($subscriberEmail)) ? 0 : 1,
                '1030'	=> $this->validLanguage($language),
                '9012'	=> ($type == 'ADD') ? (in_array($emission, [1, 2, 3, 4, 5])) : true
            ];

            $inputValidation = $this->validatEmpty($arrValida);

            if (!empty($inputValidation)) {
                return $inputValidation;
            }

            $datAgency			= $this->datAgency($api);
            $renewalDate 		= (!empty($renewalDate)) ? $renewalDate : '31/12/9999';
            $inceptionDateTrans	= $this->transformerDate($inceptionDate);
            $renewalDateTrans	= $this->transformerDate($renewalDate);
            $daysByPeople   	= $this->betweenDates($inceptionDateTrans, $renewalDateTrans);
            $idAgency			= $datAgency[0]['id_broker'];
            $isoCountry			= $datAgency[0]['id_country'];
            $nameAgency			= $datAgency[0]['broker'];
            $userAgency			= $datAgency[0]['user_id'];
            $dataPlan			= $this->selectDynamic('', 'plans', "id='$planId'", array("id_plan_categoria", "name", "num_pas"));
            $idCategoryPlan 	= $dataPlan[0]['id_plan_categoria'];


            if ($type == 'ADD') {
                $prefix	= $datAgency[0]['prefijo'];
                $subscriptionId	= $prefix . '-' . $this->valueRandom(6);
                $procedencia = 0;
            } else {
                $prefix	= $datAgency[0]['prefijo'];
            }

            $validateDateOrder 	= $this->validateDateOrder($renewalDateTrans, $inceptionDateTrans, $isoCountry);
            if ($validateDateOrder) {
                return $validateDateOrder;
            }

            $validatePlans 		= $this->validatePlans($planId, $idAgency, $countryOrigin, 1);
            if ($validatePlans) {
                return $validatePlans;
            }

            $DataWta = $this->GetId($prefix);
            $OrderId =  $this->getLastIdOrder();


            $BeneficiarieId = $this->getLastIdBeneficiarie();
            $WtaopsId = $DataWta['order'];
            $WtaopsBen = $DataWta['beneficiary'];
            $Id = (($WtaopsId > $OrderId) ? $WtaopsId : $OrderId) + 1;
            $beneficiary = (($WtaopsBen > $BeneficiarieId) ? $WtaopsBen : $BeneficiarieId) + 1;
            $dataSubscriptions	= [
                'id'                    => $Id,
                'codigo'				=> $subscriptionId,
                'salida'				=> $inceptionDateTrans,
                'referencia'			=> $reference,
                'retorno'				=> $renewalDateTrans,
                'producto'				=> $planId,
                'destino'				=> 1,
                'origen'				=> strtoupper($countryOrigin),
                'agencia'				=> $idAgency,
                'nombre_agencia'		=> $nameAgency,
                'vendedor'				=> $userAgency,
                'programaplan'			=> $idCategoryPlan,
                'fecha'					=> 'now()',
                'cantidad'				=> 1,
                'status'				=> 1,
                'origin_ip'				=> $_SERVER['REMOTE_ADDR'],
                'total'					=> 0,
                'tiempo_x_producto'		=> $daysByPeople,
                'neto_prov'				=> 0,
                'id_emision_type'		=> '2',
                'validez'				=> '1',
                'hora'					=> 'now()',
                'territory'				=> 1,
                'lang'					=> $language,
                'procedencia_funcion'	=> $procedencia,
                'masterid'				=> $masterId,
                'prefijo'               => $prefix,
                'comentarios'			=> $generalConsiderations

            ];

            if ($emission == 5) {
                $dataSubscriptions['status'] = 9;
            }
            if ($type == 'REPORT' && strtolower($reference) == '5wbs') {
                $dataSubscriptions['status'] = 9;
            }


            $transactionId	= $this->insertDynamic($dataSubscriptions, 'orders');

            $idben = $beneficiary;
            if ($transactionId) {
                $addSubscriber	= $this->addBeneficiares(0, '0000-00-00', $subscriberName, $subscriberLastName, $subscriberPhone, $subscriberEmail, $transactionId, '1', 0, 0, '', 0, 0, 0, 0, $prefix, $idben);
            }

            $linkSale = "https://ilsbsys.com/app/reports/certificate_subscription.php?codigo=" . $subscriptionId . "&selectLanguage=$language&broker_sesion=$idAgency";

            $arrOutPut =  [
                1	=>	[
                    'transactionStatus'	=> 'OK'
                ],
                2	=>	[
                    'subscriptionId'	=> $subscriptionId,
                ],
                3	=>	[
                    'transactionId'		=> $transactionId,
                ],
                4	=>	[
                    'linkVoucher'		=> $linkSale,
                ]
            ];

            switch ($emission) {
                case '1':
                    return $arrOutPut[1] + $arrOutPut[2];
                    break;
                case '2':
                    $this->sendMailSubscription($subscriberEmail, $transactionId, $language, $this->shortLang[$language]);

                    return $arrOutPut[1] + $arrOutPut[2];

                    break;
                case '3':
                    $this->sendMailSubscription($subscriberEmail, $transactionId, $language, $this->shortLang[$language]);
                    return  array_merge($arrOutPut[1], $arrOutPut[2], $arrOutPut[4]);
                case '4':
                    $shortLink = $this->shortUrl($linkSale);
                    $message = [
                        'spa'	=> "Se ha creado una Nueva Suscripción Código: $subscriptionId, Enlace Para Observar los detalles: $shortLink ",
                        'eng'	=> "A New Subscription Subscription Code has been created: $subscriptionId, Link to Observe the details: $shortLink ",
                        'por'	=> "Um novo código de assinatura de assinatura foi criado: $subscriptionId, Link para observar os detalhes: $shortLink ",
                        'fra' 	=> "Un nouveau code d'abonnement d'abonnement a été créé: $subscriptionId, Lien pour observer les détails: $shortLink "
                    ];
                    $codPhone 	= substr($subscriberPhone, 0, 2);
                    $Phone 		= substr($subscriberPhone, 2, 20);
                    if (!empty($subscriberPhone)) {
                        $sendSms	= $this->sendSms($codPhone, $Phone, $message[$language]);
                    }
                    $responseSms = ($dataSms) ? 'SUCCESS' : 'FAILED';
                    return array_merge($arrOutPut[1], $arrOutPut[2], ['sendSms' => $responseSms]);
                    break;
                default:
                    //return $arrOutPut[1];
                    $this->sendMailSubscription($subscriberEmail, $transactionId, $language, $this->shortLang[$language]);
                    return $arrOutPut[1] + $arrOutPut[2];

                    break;
            }
        }

        public function subscriptionChanges($data, $type)
        {

            $api 			= $data["api"];
            $subscriptionId	= $data["SubscriptionId"];
            $inceptionDate	= $data["InceptionDate"];
            $effectiveDate	= $data["EffectiveDate"];
            $renewalDate	= $data["RenewalDate"];
            $reference 		= $data["Reference"];
            $masterId		= $data["MasterId"];
            $planId			= $data["PlanId"];
            $countryOrigin	= $data["CountryOrigin"];
            $subscriberName	= $data["SubscriberName"];
            $subscriberLastName	= $data["SubscriberLastName"];
            $subscriberEmail	= $data["SubscriberEmail"];
            $subscriberPhone	= $data["SubscriberPhone"];
            $language			= !empty($data["Language"]) ? ($data["Language"]) : 'spa';
            $emission			= $data["Emission"];
            $generalConsiderations	= $data['consideraciones_generales'];

            $arrValidaType 	= [];
            $dataSubscriberUpdate 	= [];
            $dataSubscriptionUpdate = [];
            $procedenciaBack = $data['procedenciaBack'];

            if (!$procedenciaBack) {
                $procedenciaBack = '1';
            }

            $arrayValida	= [
                '9086'	=> $subscriptionId,
                '9087'	=> ($this->selectDynamic('', 'orders', "codigo='$subscriptionId'", array("codigo"))) ? 1 : 0,
                '9079'	=> (strlen($subscriptionId) > 30) ? 0 : 1,
            ];

            $idOrder = $this->selectDynamic('', 'orders', "codigo='$subscriptionId'", array("id"))[0]['id'];


            switch ($type) {
                case 'CHANGES':
                    $status = $this->selectDynamic('', 'orders', "codigo='$subscriptionId'", array("status"))[0]['status'];

                    $arrValidaType	= [
                        '9084'	=> !empty($effectiveDate) ? $this->checkDates($effectiveDate) : true,
                        '9076'	=> !empty($inceptionDate) ? $this->checkDates($inceptionDate) : true,
                        '9077'	=> !empty($renewalDate) ? $this->checkDates($renewalDate) : true,
                        '1090'	=> !empty($countryOrigin) ? $this->verifyOrigin($countryOrigin) : true,
                        '9080'	=> !empty($subscriberName) ? (!preg_match('(^[a-zA-Z ]*$)', $subscriberName)) ? 0 : 1 : true,
                        '9081'	=> !empty($subscriberLastName) ? (!preg_match('(^[a-zA-Z ]*$)', $subscriberLastName)) ? 0 : 1 : true,
                        '9082'	=> (!empty($subscriberPhone)) ? (is_numeric($subscriberPhone)) : true,
                        '9083'	=> !empty($subscriberName) ? (!$this->verifyMail($subscriberEmail)) ? 0 : 1 : true,
                        '1030'	=> !empty($subscriberName) ? $this->validLanguage($language) : true,
                        '9088'	=> !empty($masterId) ? (is_numeric($masterId)) : true,
                        '9012'	=> (in_array($emission, [1, 2, 3, 4, 5])),
                        '9137'	=> ($procedenciaBack == '2'  && $status != '9') ? 0 : 1
                    ];

                    $inceptionDateTrans	= $this->transformerDate($inceptionDate);
                    $renewalDateTrans	= $this->transformerDate($renewalDate);

                    $dataSubscriptionUpdate = [
                        'salida'				=> $inceptionDateTrans,
                        'retorno'				=> $renewalDateTrans,
                        'producto'				=> $planId,
                        'referencia'			=> $reference,
                        'masterid'				=> $masterId,
                        'origen'				=> strtoupper($countryOrigin),
                        'origin_ip'				=> $_SERVER['REMOTE_ADDR'],
                        'comentarios'           => $generalConsiderations
                    ];

                    $dataSubscriberUpdate = [
                        'nombre'            => $subscriberName,
                        'apellido'          => $subscriberLastName,
                        'telefono'          => $subscriberPhone,
                        'email'             => $subscriberEmail
                    ];

                    $typeSms = "Actualizado";

                    break;

                case 'EXTEND':
                    //$departure = $this->selectDynamic('','orders',"codigo='$subscriptionId'",array("salida"))[0]['salida'];
                    $departure = $this->selectDynamic('', 'orders', "codigo='$subscriptionId'", array("salida", "status"));


                    $effectiveDateTrans = $this->transformerDate($effectiveDate);

                    $dataSubscriptionUpdate = [
                        'retorno'	=> $this->transformerDate($effectiveDate),
                        'origin_ip'	=> $_SERVER['REMOTE_ADDR']
                    ];

                    $typeSms = "Extendido";

                    break;
                case 'CANCEL':

                    $statusSuscrip = $this->selectDynamic(['status' => '9'], 'orders', "codigo='$subscriptionId'", array("status"))[0]['status'];


                    $arrValidaType	= [
                        '9085'	=> $effectiveDate,
                        '9076'	=> $this->checkDates($effectiveDate),
                        '9135'	=> ($statusSuscrip) ? 0 : 1,
                        '9012'	=> (in_array($emission, [2, 4])),
                        '9137'	=> ($procedenciaBack == '2'  && !$statusSuscrip) ? 0 : 1,

                    ];

                    $typeSms = "Cancelado";

                    $dataSubscriptionUpdate = [
                        'retorno'				=> $this->transformerDate($effectiveDate),
                        'status'				=> 5,
                        'origin_ip'				=> $_SERVER['REMOTE_ADDR']
                    ];

                    break;
            }

            $dataValida = $arrayValida + $arrValidaType;

            $inputValidation = $this->validatEmpty($dataValida);
            if (!empty($inputValidation)) {
                return $inputValidation;
            }

            if (empty($subscriberEmail) || empty($subscriberPhone)) {
                $subscriberData = $this->selectDynamic('', 'beneficiaries', "id_orden='$idOrder'", ["email", "telefono"]);
                $subscriberPhone	= $subscriberData[0]['telefono'];
                $subscriberEmail	= $subscriberData[0]['email'];
            }

            $datAgency			= $this->datAgency($api);
            $idAgency			= $datAgency[0]['id_broker'];
            $userAgency			= $datAgency[0]['user_id'];
            $isoCountry			= $datAgency[0]['id_country'];

            $verifySubscription	= $this->verifyVoucher($subscriptionId, $userAgency, $isoCountry, 'ADD');

            if ($verifySubscription && $type != 'EXTEND') {
                return $verifySubscription;
            }
            $updateSubscription	= $this->updateDynamic('orders', 'id', $idOrder, $dataSubscriptionUpdate);

            $updateSuscriber	= $this->updateDynamic('beneficiaries', 'id_orden', $idOrder, $dataSubscriberUpdate);


            $linkSale = LINK_REPORTE_VENTAS . $subscriptionId . "&selectLanguage=$language&broker_sesion=$idAgency";

            $arrOutPut =  [
                1	=>	[
                    'transactionStatus'	=> 'OK'
                ],
                2	=>	[
                    'subscriptionId'	=> $subscriptionId,
                ],
                3	=>	[
                    'transactionId'		=> $updateSubscription,
                ],
                4	=>	[
                    'linkVoucher'		=> $linkSale,
                ],

            ];

            switch ($emission) {
                case '1':
                    return $arrOutPut[1] + $arrOutPut[2];
                    break;
                case '2':
                    if ($type == 'CANCEL') {
                        $this->sendMailCancel($subscriptionId, $idAgency, $language, 'SUBSCRIPTION_CANCEL');
                        /**
                         * Este método, si bien se encuentra en este archivo, no esta habilitado debido a las variables que utiliza
                         * se debe probar y verificar cual es el funcionamiento original de dicho método
                         * y los requisitos para su funcionamiento
                         */
                    } else {
                        $this->sendMailSubscription($subscriberEmail, $idOrder, $language, $this->shortLang[$language], $type);
                    }

                    return $arrOutPut[1] + $arrOutPut[2];

                    break;
                case '3':
                    $this->sendMailSubscription($subscriberEmail, $idOrder, $language, $this->shortLang[$language], $type);
                    return  array_merge($arrOutPut[1], $arrOutPut[2], $arrOutPut[4]);
                case '4':
                    $shortLink = $this->shortUrl($linkSale);
                    $message = [
                        'spa'	=> "Se ha $typeSms su Suscripción de Código: $subscriptionId, Enlace Para Observar los detalles: $shortLink ",
                        'eng'	=> "A New Subscription Subscription Code has been created: $subscriptionId, Link to Observe the details: $shortLink ",
                        'por'	=> "Um novo código de assinatura de assinatura foi criado: $subscriptionId, Link para observar os detalhes: $shortLink ",
                        'fra' 	=> "Un nouveau code d'abonnement d'abonnement a été créé: $subscriptionId, Lien pour observer les détails: $shortLink "
                    ];
                    $codPhone 	= substr($subscriberPhone, 0, 2);
                    $Phone 		= substr($subscriberPhone, 2, 20);
                    if (!empty($subscriberPhone)) {
                        $sendSms	= $this->sendSms($codPhone, $Phone, $message[$language]);
                    }
                    $responseSms = ($dataSms) ? 'SUCCESS' : 'FAILED';
                    return array_merge($arrOutPut[1], $arrOutPut[2], ['sendSms' => $responseSms]);
                    break;
                default:
                    return $arrOutPut[1];

                    break;
            }
        }

        public function sendMailSubscription($email, $id_orden, $lg_id, $lang, $type)
        {
            $post_url = 'http://ilsbsys.com/app/reports/email_subscriptions.php?';
            $post_values = [
                "id_orden" => $id_orden,
                "email"    => $email,
                "lang"     => $lg_id,
                "short"    => $lang,
                "broker_sesion" => $this->getBrokerSesion($id_orden),
                "selectLanguage" => $lang,
                "typeMail"      => $type
            ];

            $parameters = http_build_query($post_values);
            $request = curl_init($post_url);
            curl_setopt($request, CURLOPT_HEADER, 0);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($request, CURLOPT_POSTFIELDS, $parameters);
            curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
            $post_response = curl_exec($request);
            curl_close($request);
        }

        public function getBrokerSesion($id)
        {
            $query = "SELECT agencia FROM orders WHERE id='$id'";
            return $this->_SQL_tool($this->SELECT, __METHOD__, $query)[0]['agencia'];
        }

        public function sendSms($codPhone, $phone, $message)
        {
            $post_url = DOMAIN_APP . "/admin/sms.php";
            $dataMessage = array(
                "type"     => 'Send_message',
                "codPhone" => $codPhone,
                "phone"    => $phone,
                "message"  => $message,
                "typeMessage" => 'SEND_SUSCRIPTION',
            );
            $post_values = array(
                "type"     => 'Send_message',
                "codPhone" => $codPhone,
                "phone"    => $phone,
                "message"  => $message,
                "typeMessage" => 'SEND_SUSCRIPTION',
                "dataMessage" => $dataMessage
            );
            $post_string = "";
            foreach ($post_values as $key => $value) {
                $post_string .= "$key=" . urlencode($value) . "&";
            }
            $post_string = rtrim($post_string, "& ");
            $request = curl_init($post_url);
            curl_setopt($request, CURLOPT_HEADER, 0);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
            curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
            $post_response = curl_exec($request);
            curl_close($request);
            return $post_response;
        }

        public function shortUrl($url)
        {
            $arrData = [
                'login'    => 'o_2icvb72cce',
                'apiKey' => 'R_5633378002a147d2b9c03fde3a244b65',
                'uri'    => $url,
                'format' => 'txt'
            ];
            $parameters = http_build_query($arrData);
            $url = "http://api.bit.ly/v3/shorten?" . $parameters;

            return file_get_contents($url);
        }

        public function getDataOdersPrefijo($code, $prefijo)
        {
            $query = "SELECT
            orders.id,
            orders.prefijo,
            orders.codigo
            FROM
                orders
            WHERE
                orders.codigo ='$code'
            AND orders.prefijo ='$prefijo'";
            return $this->_SQL_tool($this->SELECT_SINGLE, __METHOD__, $query);
        }
    }
?>