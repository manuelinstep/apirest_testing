<?php
    require_once("response.class.php");
    require_once("connection/connect.php");

    /**
     * En este apartado van los métodos para obtener información de la plataforma
     *-Función OBTENER API KEY // YA ESTA EN AUTH
     *-Función OBTENER VOUCHER // LISTO
     *-Función OBTENER MONEDAS // LISTO
     *-Función OBTENER PAÍSES // LISTO
     *-Función OBTENER REGIONES //LISTO
     *-Función OBTENER PLANES // LISTO
     *-Función OBTENER COBERTURAS // LISTO
     *-Función OBTENER PRECIO // LISTO
     *-Función OBTENER LENGUAJES // LISTO 
     *-Función OBTENER CATEGORÍA DE PLANES //LISTO
     *-Función OBTENER CONDICIONES //LISTO
     *-Función OBTENER UPGRADE/RAIDER // LISTO
     *-Función PAÍSES DE ORIGEN RESTRINGIDOS // LISTO
     *-Función TASA DE CAMBIO POR PAÍS // LISTO
     *-Función OBTENER PAISES - CIUDADES // LISTO
     *-Función OBTENER PAISES - ESTADOS // LISTO
     *-Función OBTENER ASISTENCIA // -----------------
     *-Función OBTENER DETALLE DE ASISTENCIA // -----------------
     *-Función OBTENER LINEA DE TIEMPO  // -----------------
     *-Función OBTENGA BENEFICIOS DEL CASO // -----------------
     *-Función OBTENER PAISES - ESTADOS - CIUDADES // LISTO
     *-Función OBTENER PRECIO DE LA ORDEN
     *-Función OBTENER PRECIO DE LA ORDEN POR EDAD 
     *-Función OBTENER PRECIO DE LA ORDEN POR EDAD POR PLAN
     *-Función OBTENER PRECIO DE LA ORDEN POR FECHA DE NACIMIENTO
     *-Función OBTENER PRECIO DE LA ORDEN POR FECHA DE NACIMIENTO POR PLAN
     *-Función OBTENER REPORTE DE VENTAS 
     * 
     * Elementos que faltan por implementar:
     * -Cada vez que se finalice una consulta, enviarla a log_consultas y a trans_all_webservices con sus respectivos datos
     * -agregar los errores de la DB
     * 
     * Aparte de eso:
     * -Agregar método para los query traido del webservices anterior (_SQL_tool/selectdynamic)
     * 
     * 9015 error cuando no hay resultados
     */

    class information extends cls_dbtools{
        /**
         * La función get se encargara de recibir todas las solicitudes GET
         * y de redistribuirlas en las funciones
         * para acceder a las funciones, el usuario debera insertar en un campo
         * el nombre de la función
         * por ejemplo, si busca obtener un apikey se mete en auth
         * pero si quier buscar los planes
         * inserta lo siguiente
         * "requesting" : "plans"
         * Se deberan migrar todas las limitaciones y verificaciones existentes dentro de la plataforma
         */
        /**
         * Esta es la clase GET que redistribuye el llamado a todas las funciones
         */
        public function get($data){
            $_respuesta = new response;
            
            $datos = json_decode($data,true);
            $request = $datos['request'];
            //Siempre se debe recibir el parametro request
            switch ($request) {
                case "get_voucher":
                    /**
                     * Se recibe
                     * Token de autenticación
                     * Código del voucher
                     * Lenguaje
                     * 
                     * Para el token de autenticación:
                     * Se debe revisar que el token exista en la DB, y que la IP actual de dicho usuario
                     * sea igual a la IP guardada en la DB
                     * Se creara un método con el fin de revisar dichos parametros
                     */
                    $checkToken = $this->checkToken($datos['token']);
                    /**
                     * Revisamos que el estatus sea 1, que la IP sea la actual y que el token exista
                     * En todo caso, el token no devolvería nada si no fuese igual al de la DB  
                     */
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        /**
                         * Primero, buscamos el id de la agencia asociada al usuario
                         * en base a esto, buscamos el voucher
                         */
                        $id_agencia = $this->get_id_agencia($checkToken[0]['id']);
                        $response = $this->get_voucher($datos['codigo'],$id_agencia[0]['id_associate']);
                        //TODOS los usuarios deben tener una agencia asociada
                        if($response){
                            return $response;
                        }else{
                            return $_respuesta->getError('9015');
                        }
                    }else{
                        return $_respuesta->getError('1005');
                    }

                    break;

                case "get_currencies":

                    //Solo verifica que el API KEY sea correcta
                    
                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        $response = $this->get_currencies();
                        return (!empty($response)) ? $response : $_respuesta->getError('9015');
                    }else{
                        return $_respuesta->getError('1005');
                    }

                    break;

                case "get_countries":

                    //Verifica el API KEY y que haya un lenguaje, el cual será spa o eng
                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        //Verificamos el lenguaje dentro del método
                        $response = ($datos['language'] == 'spa' or $datos['language'] == 'eng') ? $this->get_countries($datos['language']) : $_respuesta->getError('1030');
                        return $response;
                    }else{
                        return $_respuesta->getError('1005');
                    }

                    break;
                
                case "get_regions":
                    
                    /**
                     * Solo verificamos el API KEY
                     * podemos crear una función similar a SELECT DYNAMIC que haga lo mismo
                     * De esta forma, para las consultas sencillas, solo pasamos el nombre de la tabla
                     * y los filtros necesarios para poder utilizarla
                     */
                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        //Verificamos el lenguaje dentro del método
                        $response = $this->selectDynamic('', 'territory', "id_status='1'", ['id_territory', 'desc_small']);
                        if(!empty($response)){
                            return $response;
                        }else{
                            return $_respuesta->getError('9015');
                        }
                    }else{
                        return $_respuesta->getError('1005');
                    }

                    
                    break;
                case 'get_plans':
                    $checkToken = $this->checkToken($datos['token']);
                    
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        //Verificamos el lenguaje dentro del método
                        $id_agencia = $this->get_id_agencia($checkToken[0]['id']);
                        $response = (!empty($datos['language'])) ? $this->get_plans($id_agencia[0]['id_associate'],'',$datos['language'], true) : $_respuesta->getError('6021');
                        /**
                         * Crear método get plans que nos traiga los planes en base al usuario y su agencia
                         */
                        if(!empty($response)){
                            return $response;
                        }else{
                            return $_respuesta->getError('9015');
                        }
                    }else{
                        return $_respuesta->getError('1005');
                    }
                    break;
                case 'get_coverages':
                    /**
                     * Recibe el lenguaje y el plan
                     */
                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        //Verificamos el lenguaje dentro del método
                        if(empty($datos['id_plan']))
                        {
                            return $_respuesta->getError('6022');
                        }
                        $verify = $this->get_plans('',$datos['id_plan'],$datos['language'], false);
                        $response =  $this->dataCoverages($datos['language'],$datos['id_plan']);
                        /**
                         * Crear método get plans que nos traiga los planes en base al usuario y su agencia
                         */
                        if(!empty($response)){
                            return $response;
                        }else{
                            return $_respuesta->getError('9015');
                        }
                    }else{
                        return $_respuesta->error_400("El token proporcionado no es válido","402");
                    }
                    break;
                case 'get_pvp_price':
                    /**
                     * Recibe el plan y el país
                     */

                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        //Ahora verificamos el plan y el país
                        $verify = $this->get_plans('',$datos['id_plan'],'', false);
                        if(isset($verify[0]['id'])){
                            $checkrestriction = $this->verifyRestrictionOrigin($datos['iso_country'],$datos['id_plan']);
                            $country = $datos['iso_country'];
                            if(isset($checkrestriction['status'])){
                                return $checkrestriction;
                            }else if(empty($datos['iso_country'])){
                                $country = 'all';
                            }

                            $data	= [
                                'valor',
                                'age_min',
                                'age_max'
                            ];

                            $plan = $datos['id_plan'];

                            $dataPvpPriceBandas	= $this->selectDynamic('', 'plan_band_age', "id_plan='$plan'", $data, '', '', 260);

                            if (!empty($dataPvpPriceBandas)) {
                                return $dataPvpPriceBandas;
                            } else {

                                $filters = [
                                    'id_country' => $country
                                ];

                                $data	= [
                                    'unidad',
                                    'tiempo',
                                    'valor'
                                ];

                                //$dataPvpPrice	= $this->selectDynamic($filters,'plan_times',"id_plan='$plan'",['unidad','tiempo','valor']);
                                $dataPvpPrice	= $this->selectDynamic($filters, 'plan_times', "id_plan='$plan'", $data, '', '', 260);


                                if (!empty($dataPvpPrice)) {
                                    return $dataPvpPrice;
                                } else {
                                    return $_respuesta->getError('1060');
                                }
                            }

                        }else{
                            //Verificar que error se retorna en este caso
                        }

                    }else{
                        return $_respuesta->error_400("El token proporcionado no es válido","402");
                    }

                    break;
                case 'get_languages':
                    /**
                     * Obtenemos los lenguajes de la plataforma, solo requerimos el token
                     */
                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        $response = $this->selectDynamic('', 'languages', "languages.active = '1'", ['id', 'lg_id', 'name', 'short_name']);
                        if($response){
                            return $response;
                        }else{
                            return $_respuesta->getError('9015');
                        }
                    }else{
                        return $_respuesta->getError('1005');
                    }

                    break;
                case 'get_plan_category':
                    # code...
                    /**
                     * Recibe
                     * -Token
                     * -Lenguaje
                     */
                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        $response = (isset($datos['language'])) ? $this->dataPlanCategory($datos['language']) : $_respuesta->getError('6021') ;
                        if($response){
                            return $response;
                        }else{
                            return $_respuesta->getError('9015');
                        }
                    }else{
                        return $_respuesta->getError('1005');
                    }


                    break;

                case 'get_terms':
                    /**
                     * Recibimos, aparte del token, el id del plan y el lenguaje
                     */
                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        $verify = $this->get_plans('',$datos['id_plan'],$datos['language'], true); //Verificamos que existe el plan master
                        if(isset($verify[0]['id'])){
                            $idplan = $datos['id_plan'];
                            $data = $this->selectDynamic('', 'plans', "id='$idplan'", array("name", "description"));
                            //Necesitamos el broker manaos
                            $id_agencia = $this->get_id_agencia($checkToken[0]['id']);

                            return [
                                'id' => $idplan,#id del plan
                                'name' => $data[0]['name'],#nombre del plan
                                'description' => $data[0]['description'],#descripción del plan
                                'terms' => $this->getTermsMaster($idplan,$datos['language'],$id_agencia)
                            ];
                        }else{
                            return $_respuesta->getError('1050');
                        }
                        
                    }else{
                        return $_respuesta->getError('1005');
                    }

                    break;
                case 'get_upgrade':
                    //Requiere lenguaje y id del plan
                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        $verify = $this->get_plans('',$datos['id_plan'],$datos['language'], true);
                        if(isset($verify[0]['id'])){
                            //El plan existe, procedemos con el resto del método
                            $arrTypeUpgrades	= [

                                '1' => [
                                    'type_raider' => 'Valor',
                                    'rd_calc_type' => 'Comprobante'
                                ],
                                '2' => [
                                    'type_raider' => 'Porcentage %',
                                    'rd_calc_type' => 'Pasajero Especifico'
                                ],
                                '3' => [
                                    'type_raider' => 'Valor',
                                    'rd_calc_type' => 'Pasajero General'
                                ],
                                '4' => [
                                    'type_raider' => 'Valor',
                                    'rd_calc_type' => 'Por dia por Voucher'
                                ],
                                '5' => [
                                    'type_raider' => 'Valor',
                                    'rd_calc_type' => 'Por dia por Pasajero'
                                ]
                            ];

                            $result = $this->dataUpgradesPlan($datos['id_plan'],$datos['language']);

                            if ($result) {

                                foreach ($result as $i => $value) {
                                    $arrResult[]['type_raider']		= $arrTypeUpgrades[$value['type_raider']]['type_raider'];
                                    $arrResult[$i]['rd_calc_type']	= $arrTypeUpgrades[$value['rd_calc_type']]['rd_calc_type'];
                                    $arrResult[$i]['id_raider']		= $value['id_raider'];
                                    $arrResult[$i]['cost_raider']	= $value['cost_raider'];
                                    $arrResult[$i]['name_raider']	= $value['name_raider'];
                                    $arrResult[$i]['value_raider']	= $value['value_raider'];
                                }
                    
                    
                                return $arrResult;
                            } else {
                                return $_respuesta->getError('5017');
                            }
                            //------------------
                        }else{
                            return $_respuesta->getError('1050');
                        }
                    }else{
                        return $_respuesta->getError('1005');
                    }

                    break;

                case 'country_restricted':
                    //Mismos de arriba, plan y lenguaje
                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        $verify = $this->get_plans('',$datos['id_plan'],'', false); //Verificamos que existe el plan master
                        $dataCountryRestricted = ($verify['status']!='error') ? $this->dataCountryRestricted($datos['id_plan'],$datos['language']) : $_respuesta->getError('1050');
                        if (!empty($dataCountryRestricted)) {
                            return $dataCountryRestricted;
                        } else {
                            return $_respuesta->getError('9015');
                        }

                    }else{
                        return $_respuesta->getError('1005');
                    }

                    break;
                
                case 'exchange_rate':
                    //obtiene codigo ISO del país y el lenguaje
                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        $response = ($datos['iso_country']) ? $this->dataExchangeRate($datos['iso_country']) : $_respuesta->getError('3150');
                        return (!empty($response)) ? $response : $_respuesta->getError('5013');
                    }else{
                        return $_respuesta->getError('1005');
                    }

                    break;

                case 'get_country_cities':
                    /**
                     * Mismo de la anterior, requiere iso y el lenguaje
                     * verificamos que existe el pais, luego, buscamos las ciudades
                     */
                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        $verify = ($datos['iso_country']!='') ? $this->checkCountry($datos['iso_country'],$datos['language']) : null;
                        if($verify){
                            $response = $this->dataCitiesCountry($datos['iso_country'],$datos['language']);
                            return ($response) ? $response : $_respuesta->getError('9015');
                        }else{
                            return $_respuesta->getError('9173');
                        }
                    }else{
                        return $_respuesta->getError('1005');
                    }
                    
                    break;

                case 'get_country_states':
                    /**
                     * Mismo de la anterior, requiere iso y el lenguaje
                     * hacemos la misma verificación de arriba, luego, buscamos los estados
                     * DON'T STOP ME NOW
                     */

                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        $verify = ($datos['iso_country']!='') ? $this->checkCountry($datos['iso_country'],$datos['language']) : null;
                        if($verify){
                            $response = $this->dataStatesCountry($datos['iso_country'],$datos['language']);
                            return ($response) ? $response : $_respuesta->getError('9015');
                        }else{
                            return $_respuesta->getError('9173');
                        }
                    }else{
                        return $_respuesta->getError('1005');
                    }
                    
                    break;
                case 'get_country_states_cities':
                    /**
                     * Recibe:
                     * Iso country
                     * iso state
                     * lenguaje
                     */
                    $checkToken = $this->checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        //Primero va datacountries, checkeamos lenguaje
                        if(!empty($datos['language'])){
                            $language = $datos['language'];
                        }else{
                            $_respuesta->getError('6021');
                        }
                        $country_iso = (!empty($datos['iso_country'])) ? $this->checkCountry($datos['iso_country'],$datos['language']) : $_respuesta->getError('9174');
                        if(!$country_iso[0]['iso_country']){
                            return $country_iso;
                        }else{
                            $iso_state = $datos['iso_state'];
                            $state_iso = $this->selectDynamic('', 'states', "iso_state='$iso_state'", array("iso_state"))[0]['iso_state'];
                            if(!$state_iso){
                                return $_respuesta->getError('9177');
                            }else{
                                $dataCountryStatesCities = $this->dataCitiesStatesCountry($country_iso[0]['iso_country'], $state_iso, $language);

                                if (!empty($dataCountryStatesCities)) {
                                    return $dataCountryStatesCities;
                                } else {
                                    return $_respuesta->getError('9015');
                                }
                            }
                        }
                    }else{
                        return $_respuesta->getError('1005');
                    }
                    break;
                case 'get_sales_report':
                    
                    $api 		= $datos["token"];
                    $startDate	= $datos["desde"];
                    $endDate	= $datos["hasta"];
                    $status		= $datos["estatus"];
                    $format		= $datos["formato"];

                    $startDateTrans = $this->transformerDate($startDate);
                    $endDateTrans	= $this->transformerDate($endDate);
                    $today 	= date('Y-m-d');
                    $format	= strtolower($format);

                    $ArrayValida	= [
                        '6037'	=> (!empty($startDate) and !empty($endDate) and !empty($status)),
                        '9063'	=> $startDate,
                        '9064'	=> $endDate,
                        '9065'	=> $status,
                        '9017'	=> (in_array($status, [1, 2, 3, 4])),
                        '9066'	=> (in_array($format, ['', 'json', 'excel'])),
                        '3020'	=> $this->checkDates($startDate),
                        '9067'	=> $this->checkDates($endDate),
                        '3030'	=> !(strtotime($startDateTrans)	> strtotime($endDateTrans)),
                        '9068'	=> !(strtotime($endDateTrans)	> strtotime($today)),
                        '9069'	=> !(strtotime($startDateTrans)	> strtotime($today)),
                    ];

                    $valid	= $this->validatEmpty($ArrayValida);

                    if (!empty($valid)) {
                        return $valid;
                    }
                    switch ($status) {
                        case 2:
                            $statusEx = 5;
                            $status	= 5;
                            break;
                        case 3:
                            $statusEx = 6;
                            $status	= '';
                            break;
                        case 4:
                            $statusEx = 9;
                            $status	  = 9;
                            break;
                        default:
                            $statusEx = 1;
                            $status	= 1;
                            break;
                    }

                    $dataAgency	= $this->datAgency($api);
                    $idUser		= $dataAgency[0]['user_id'];

                    $arrParameters 	= [
                        'wbs'	=> 1,
                        'desde'	=> $startDateTrans,
                        'hasta'	=> $endDateTrans,
                        'id_status'	=> $statusEx,
                        'rangofecha' => 1,
                        'IdUser'	=> $idUser
                    ];

                    $parameters = http_build_query($arrParameters);
                    $urReport	= 'https://fasttravelassistance.ilstechnik.com/app/reports/rpt_xls_reporte_ils.php?';
                    $urlShort	= $this->shortUrl($urReport . $parameters);

                    switch ($format) {
                        case 'json':
                            $response   =  $this->GetOrderdetallada_ils('', $startDateTrans, $endDateTrans, '', 1, $status, '', '', 'orden', '', $idUser, 'spa', 1);
                            break;
                        default:
                            $response	= [
                                'status' => 'OK',
                                'Enlace de Descarga' => trim($urlShort)
                            ];
                            break;
                    }
                    if (empty($response)) {
                        $response = $_respuesta->getError('9015');
                    }
                        return $response;
                    break;
                case 'get_order_price_edad':
                    # code...
                    /**
                     * para order price edad:
                     * typerange: 2
                     * returnrange: 3
                     * se le pasa a la funcion la variable datos con las dos variables de arriba
                     */
                    $result = $this->getOrderPrice($datos,2,3);
                    return $result;

                    break;
                case 'get_order_price_edad_plan':
                    # code...
                    /**
                     * typerange: 2
                     * returnrange: 2
                     */
                    $result = $this->getOrderPrice($datos,2,2);
                    return $result;
                    break;
                case 'get_order_price_fecha':
                    /**
                     * typerange: 1
                     * returnrange: 3
                     */
                    $result = $this->getOrderPrice($datos,1,3);
                    return $result;
                    break;
                case 'get_order_price_fecha_plan':
                    /**
                     * typerange: 1
                     * returnrange: 2
                     */
                    $result = $this->getOrderPrice($datos,1,2);
                    return $result;
                    break;
                case 'get_voucher_rci':
                    # code...
                    $Llego = 'aqui2';
                    
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
                                        
                                                return ($upgradesOrder)?$upgradesOrder:$_respuesta->getError(4445);
                            
                                            break;
                            
                                            case '2':
                                            
                                                $dataUpgrades = $this->getDataUpgradesRCI($idPlan,$lang,$idUpgradesExist,$plataform);
                                                return ($dataUpgrades)?$dataUpgrades:$_respuesta->getError(4446);
                            
                                            break;
                                        }
            
                                    }else{
                                        return $_respuesta->getError(1021);
                                    }
                                    
                                }else{
                                    return $_respuesta->getError(1020);
                                }
                    break;
                default:
                    /**
                     * Se debe determinar si esta vacio, o si viene con un metodo
                     * no existente
                     */
                    return $_respuesta->getError('9030');
                    break;
            }
        }

        /** 
         * /////// Estas son las funciones que obtienen todos los datos ///////
        */

        /**
         * Funcion para obtener vouchers
         */
        private function get_voucher($codigo, $id_agencia){
            //Se buscara el voucher, en base al código, y si es de la agencia del usuario
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
            CASE 
            WHEN orders.status = '1' THEN
                'Activo'
            WHEN orders.status = '3' THEN
                'Expirado'
            WHEN orders.status = '9' THEN
                'Prueba'
            ELSE
                'Status Invalid'
            END AS des_status,
            
            orders.es_emision_corp,
            orders.origin_ip,
            orders.alter_cur,
            orders.tasa_cambio,
            orders.family_plan,
            orders.referencia
            FROM
                orders
            WHERE
                orders.codigo ='$codigo'
            AND orders.agencia ='$id_agencia'";
            $response = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
            if($response){
                return $response;
            }else{
                return 0;
            }
        }


        /**
         * Funcion para obtener ID de una agencia
         */
        private function get_id_agencia($id_user){
            $query = "SELECT 
                        user_associate.id_associate 
                      FROM 
                        user_associate 
                      INNER JOIN 
                        users
                      ON 
                        users.id = user_associate.id_user 
                      where 
                        users.id = '$id_user'";
            $response = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
            if($response){
                return $response;
            }else{
                return 0;
            }
        }

        private function get_currencies(){
            $query = "SELECT id_country, value_iso, desc_small FROM currency WHERE id_status = '1'";
            $response = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
            if($response){
                return $response;
            }else{
                return 0;
            }
        }

        private function get_countries($language){
            
            $query = "SELECT
                            countries_detail.iso_country,
                            countries_detail.description
                        FROM
                            countries
                        INNER JOIN countries_detail ON countries.iso_country = countries_detail.iso_country
                        WHERE
                            countries_detail.language_id = '$language'
                        AND countries.c_status = 'Y'";
            $response = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
            return $response;
        }

        public function selectDynamic($filters,$table=string,$where='1',$fields, $querys='',$die=false,$limit=6,$database){
        
            if(empty($querys)){
                $fields=!empty($fields)?implode(',',$fields):"*";
                $query = "SELECT $fields FROM $table WHERE $where ";
                    foreach ($filters as $campo => $value) {
                        if(!empty($campo) && !empty($value)){
                            $valor   = addslashes($value);
                            $valor   = (is_array($value))?implode(',',$value):"'$valor'";
                            $query  .= " AND $campo IN ($valor) ";
                        }
                    }
                    
                if($limit){
                    $query.=" LIMIT $limit ";
                }
                
            }else{
                
                $query=$querys;
            }
            if($die){
                die($query);
            }
            if($database=='rci'){
                $databaseRci = new database_rci();
               
                return $databaseRci->query($query);
    
            }else{
                return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
            }
            
        }

        private function get_plans($id_agencia, $plan, $language, $details = false){
            $_respuesta = new response;
            $choicePlan = $this->selectDynamic('', 'broker', "id_broker='$id_agencia'", array("opcion_plan"))[0]['opcion_plan'];
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
            (!$details && (empty($language))) ?: $where[] = " plan_detail.language_id = '$language'";
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
                    OR (restriction.dirigido = 2 AND restriction.id_broker = $id_agencia)
                    OR (restriction.dirigido = 6 AND restriction.id_broker = $id_agencia)
                )";
            } else if ($choicePlan == '2') {
                $where[] =
                    "(
                    (restriction.dirigido = 2 AND restriction.id_broker = $id_agencia)
                    OR (restriction.dirigido = 6 AND restriction.id_broker = $id_agencia)
                )";
            }
            $query .= (count($where) > 0 ? " WHERE " . implode(' AND ', $where) : " ");
            $response = $this->selectDynamic('' , '' , '' , '' , $query);

            if (!$response) {
                return $_respuesta->getError('1050');
            } else {
                return $response;
            }
        }

        private function dataCoverages($language,$plan){
            $_respuesta = new response;
            /**
             * Función traida del anterior webservices
             */
            $query =  "SELECT 
            benefit_plan.valor_spa,
            benefit_plan.valor_eng,
            
        
            benefit_detail.id_benefit,
            benefit_detail.name,
            benefit_detail.language_id,
            benefit_detail.extended_info
            FROM
                benefit_plan
            INNER JOIN benefit_detail ON benefit_plan.id_beneficio = benefit_detail.id_benefit
            INNER JOIN plans ON plans.id = benefit_plan.id_plan
            WHERE plans.activo = 1 
            AND benefit_detail.language_id = '$language' 
            AND benefit_plan.id_plan = '$plan'";
            /*if('200.84.222.207' == $_SERVER ['REMOTE_ADDR']){
                die(var_dump("ojo",$query));
            }*/
            $response = $this->selectDynamic('', '', '', '', $query);
            return (!empty($response)) ? $response : $_respuesta->getError('1050');
        }

        private function dataPlanCategory($language)
        {
            $query = "SELECT
                        plan_categoria_detail.name_plan,
                        plan_categoria_detail.id_plan_categoria
                    FROM
                        plan_categoria_detail
                    INNER JOIN plan_category ON plan_categoria_detail.id_plan_categoria = plan_category.id_plan_categoria
                    WHERE
                        plan_categoria_detail.language_id = '$language' 
                    AND
                        plan_category.id_status = 1";
            return $this->selectDynamic('', '', '', '', $query);
        }

        private function getTermsMaster($plan, $language, $idAgency)
        {
            $filters   = [
                'id_status'     => '1',
                'type_document' => '1',
                'language_id'   => $language
            ];
            $termsPlan              = $this->selectDynamic($filters, 'plans_wording', "id_plan='$plan'", array("url_document"))[0]["url_document"];
            $termsAgency            = $this->selectDynamic("language_id='$language'", 'broker_parameters_detail', "id_broker='$idAgency'", array("imagen"))[0]["imagen"];
            $termsInsurance         = $this->selectDynamic("language_id='$language'", 'wording_parameter', "id_status='1'", array("url_document"))[0]["url_document"];
            $typeBroker             = $this->selectDynamic('', 'broker', "id_broker='$agency'", array("type_broker"))[0]["type_broker"];

            if (!empty($termsPlan)) {
                return  $_SERVER['SERVER_NAME'] . "/app/admin/server/php/files/" . $termsPlan;
            } elseif (!empty($termsAgency) && $typeBroker == "1") {
                return  $_SERVER['SERVER_NAME'] . "/app/upload_files/broker_parameters/" . $agency . "/condicionados/" . $termsAgency;
            } elseif (!empty($termsInsurance)) {
                return  $_SERVER['SERVER_NAME'] . "/app/admin/server/php/files/" . $termsInsurance;
            }
        }

        private function dataUpgradesPlan($plan, $language)
        {
            $query = "SELECT
                raiders.id_raider,
                raiders_detail.name_raider,
                raiders.type_raider,
                raiders.value_raider,
                raiders.cost_raider,
                raiders.rd_calc_type
            FROM
                raiders
                INNER JOIN raiders_detail ON raiders_detail.id_raider = raiders.id_raider
                INNER JOIN plan_raider ON raiders.id_raider = plan_raider.id_raider
            WHERE
                plan_raider.id_plan = '$plan' 
            AND raiders_detail.language_id='$language'";

            return $this->selectDynamic('', '', '', '', $query);
        }

        private function dataCountryRestricted($plan, $language)
        {
            $query = "SELECT 
                countries.iso_country,
                countries_detail.description 
            FROM 
                countries
            INNER JOIN relaciotn_restriction ON relaciotn_restriction.iso_country = countries.iso_country
            INNER JOIN restriction ON relaciotn_restriction.id_restric = restriction.id_restric
            INNER JOIN plans ON restriction.id_plans = plans.id
            INNER JOIN countries_detail ON countries.iso_country = countries_detail.iso_country
            WHERE plans.id = '$plan'";

            if (!empty($language)) {
                $query .= " AND 
                    countries_detail.language_id = '$language' ";
            }
            return $this->selectDynamic('', '', '', '', $query);
        }

        private function dataExchangeRate($isoCountry){
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
            return $this->selectDynamic('', '', '', '', $query);
        }

        private function checkCountry($iso_country,$language){
            //Funcion que verifica si un país existe
            $query =  "SELECT
                countries_detail.iso_country,
                countries_detail.description
            FROM
                countries
            INNER JOIN countries_detail ON countries.iso_country = countries_detail.iso_country
            WHERE
                countries_detail.language_id = '$language'
            AND countries.c_status = 'Y' ";


            if (!empty($iso_country)) {
                $query .= " AND 
                    countries.iso_country = '$iso_country' ";
            }

            $response = $this->selectDynamic('', '', '', '', $query);
            return $response;
        }

        private function dataCitiesCountry($country,$language){
            $query = "SELECT
                countries_detail.description AS countries_description,
                cities.description AS cities_description,
                cities.iso_city,
                states.description AS states_description,
                states.iso_state
            FROM
                countries
            INNER JOIN states ON countries.iso_country = states.iso_country
            INNER JOIN cities ON countries.iso_country = cities.iso_country
            INNER JOIN countries_detail ON countries.iso_country = countries_detail.iso_country
            AND cities.iso_state = states.iso_state
            WHERE
                countries.iso_country = '$country'
            AND countries.c_status = 'Y'
            AND	countries_detail.language_id = '$language'
            ORDER BY
                countries_detail.description,
                states.description,
                cities.description ASC";

            return $this->selectDynamic('', '', '', '', $query);
        }

        private function dataStatesCountry($country, $language)
        {
            $query = "SELECT
                countries_detail.description as countries_description,  states.description as states_description,states.iso_state
            FROM
                countries
            INNER JOIN states ON countries.iso_country = states.iso_country
            INNER JOIN countries_detail ON countries.iso_country = countries_detail.iso_country
            WHERE countries.iso_country ='$country'
            AND countries.c_status = 'Y'
            AND	countries_detail.language_id = '$language'
            ORDER BY countries_detail.description,  states.description  asc";

            return $this->selectDynamic('', '', '', '', $query);
        }

        private function verifyRestrictionOrigin($origin, $plan)
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

        private function dataCitiesStatesCountry($country, $iso_state, $language)
        {
            $query = "SELECT
                countries_detail.description as countries_description, 
                cities.description as cities_description,cities.iso_city, states.description as states_description, 
                states.iso_state
    
            FROM
                countries
            INNER JOIN states ON countries.iso_country = states.iso_country
            INNER JOIN cities ON countries.iso_country = cities.iso_country
            INNER JOIN countries_detail ON countries.iso_country = countries_detail.iso_country
            AND cities.iso_state = states.iso_state
            WHERE
                countries.iso_country = '$country'
            AND countries.c_status = 'Y'
            AND states.iso_state = '$iso_state'
            AND	countries_detail.language_id = '$language'
            ORDER BY
                countries_detail.description,
                states.description,
                cities.description ASC";
            return $this->selectDynamic('', '', '', '', $query);
        }

        private function insertDynamic($data = array(), $table = null)
        {
            if (empty($table) || count($data) == 0) {
                return false;
            }
            $arrFiels       = [];
            $arrValues      = [];
            $SQL_functions  = [
                'NOW()'
            ];
            foreach ($data as $key => $value) {
                $arrFiels[] = '`' . $key . '`';
                if (in_array(strtoupper($value), $SQL_functions)) {
                    $arrValues[] = strtoupper($value);
                } else {
                    $arrValues[] = '\'' . $value . '\'';
                }
            }
            $query = "INSERT INTO $table (" . implode(',', $arrFiels) . ") VALUES (" . implode(',', $arrValues) . ")";
            return $this->_SQL_tool($this->INSERT, __METHOD__,$query);
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

        public function checkToken($token){
            $query = "SELECT api_key, ip_remote, id_status, id FROM users WHERE api_key = '$token'";
            $resp = $this->selectDynamic('', '', '', '', $query);
            if($resp){
                return $resp;
            }else{
                return 0;
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

        public function checkDates($date)
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
        public function validatEmpty($parametros)
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
        }

        public function datAgency($api)
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

        public function GetOrderdetallada_ils($vouche = '', $desde = '', $hasta = '', $pasajero = '', $ben_status = '', $id_status = '', $min = '', $max = '', $group_by = '', $id_broker = '', $id_usuario = '', $language_id = 'spa', $rangofecha = ''){

            $query = "SELECT
                 orders.codigo,
                 orders.fecha,
                 orders.salida,
                 orders.retorno,
                 orders.producto,
                 orders.`status`,
                 orders.vendedor,
                 orders.cantidad,
                 orders.referencia,
                 orders.nombre_agencia,
                 orders.email_contacto,
                 orders.nombre_contacto,
                 orders.telefono_contacto,
                 IF ( orders.territory = '',
                                (  SELECT  description  FROM countries  WHERE countries.iso_country = destino ),
                                (  SELECT  desc_small   FROM territory  WHERE territory.id_territory = territory )
                            ) AS eldestino,
                 countries.description as origen,
                 plans.id_provider,
                 beneficiaries.nombre,
                 beneficiaries.apellido,
                 beneficiaries.email,
                 beneficiaries.telefono,
                 beneficiaries.nacimiento,
                 beneficiaries.documento,
                 beneficiaries.precio_vta,
                 IFNULL(
                            (
                                    SELECT
                                        SUM(
                                            CASE orders_raider.id_beneft
                                            WHEN beneficiaries.id THEN
                                                value_raider
                                            WHEN 0 THEN
                                            IF (
                                                ISNULL(beneficiaries.precio_vta),
                                                0,
                                                (
                                                    value_raider / IFNULL(
                                                        (
                                                            SELECT
                                                                COUNT(*)
                                                            FROM
                                                                beneficiaries ben
                                                            WHERE
                                                                ben.id_orden = orders.id
                                                            AND ben.precio_vta IS NOT NULL
                                                            AND ben.precio_vta > 0
                                                        ),
                                                        cantidad
                                                    )
                                                )
                                            )
                                            WHEN NULL THEN
                                                (value_raider / cantidad)
                                            ELSE
                                                0
                                            END
                                        )
                                    FROM
                                        orders_raider
                                    WHERE
                                        orders_raider.id_orden = orders.id
                                ),
                                0
                            ) ben_raider,
    
                 plan_categoria_detail.name_plan AS plan_categoria_name,
                 plan_detail.description AS plan_detail_description,
                 users.users,
                 broker.broker,
                 (SELECT count(id) FROM
                                        orders_raider
                                    WHERE
                                        orders_raider.id_orden = orders.id) numraider
                FROM
                    orders
                INNER JOIN plans ON orders.producto = plans.id
                LEFT JOIN broker ON broker.id_broker = orders.agencia
                INNER JOIN beneficiaries ON orders.id = beneficiaries.id_orden
                LEFT JOIN order_address ON orders.id = order_address.id_orden
                LEFT JOIN users ON users.id = orders.vendedor
                INNER JOIN plan_detail ON plans.id = plan_detail.plan_id    AND plan_detail.language_id = '$language_id'
                INNER JOIN plan_categoria_detail ON plan_categoria_detail.id_plan_categoria = plans.id_plan_categoria AND  plan_categoria_detail.language_id='$language_id'
                LEFT JOIN countries ON iso_country = orders.origen
                WHERE 1";
    
            if (!empty($vouche)) {
                $query .= " AND orders.codigo='$vouche'";
            }
    
            if (!empty($id_status)) {
                $query .= " AND orders.status='$id_status'";
            }
    
            if (!empty($id_broker)) {
                $query .= " AND orders.agencia IN ($id_broker)";
            }
    
            if (!empty($id_usuario)) {
                $query .= " AND orders.vendedor IN ($id_usuario)";
            }
    
            if (!empty($pasajero)) {
                $pasajero = trim($pasajero);
                $pana     = explode(' ', $pasajero);
                $query .= " AND (concat_ws(' ', TRIM(BOTH ' ' FROM beneficiaries.nombre), TRIM(BOTH ' ' FROM beneficiaries.apellido)) LIKE '%$pasajero%'
                    OR TRIM(BOTH ' ' FROM beneficiaries.nombre) LIKE '%$pana[0]%'
                    AND TRIM(BOTH ' ' FROM beneficiaries.apellido) LIKE '%$pana[1]%')";
            }
            if (!empty($rangofecha)) {
                if ($rangofecha == 1) {
                    if (!empty($desde)) {
                        $query .= " AND orders.fecha >='$desde' and orders.fecha <='$hasta'";
                    }
    
                } elseif ($rangofecha == 2) {
                    if (!empty($desde)) {
                        $query .= " AND orders.salida >='$desde' and orders.salida <='$hasta'";
                    }
    
                }
            }
    
            if (!empty($ben_status)) {
                $query .= " AND beneficiaries.ben_status='$ben_status'";
            }
    
            if (!empty($group_by)) {
                $query .= " ORDER BY orders.fecha,orders.codigo DESC";
            } else {
                $query .= " GROUP BY orders.codigo ORDER BY orders.fecha,orders.codigo DESC";
            }
            if (!empty($max)) {
                $query .= " LIMIT $min,$max ";
            }
    
            return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
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

        public function getOrderPrice($data,$typeRange,$returnRange) //Cambiada por RCI
        {
        
            $quoteGeneral 			= new quote_general_new();
            $api					= $data['api'];
            $plan					= $data['id_plan'];
            $origin					= $data['pais_origen'];
            $destination			= $data['territorio_destino'];
            $departure				= $data['fecha_salida'];
            $arrival				= $data['fecha_llegada'];
            $numberPassengers		= $data['pasajeros'];
            $dataRange				= ($typeRange==1)?$data['fecha_nacimiento']:$data['edad'];
            $calculateRangeObj  	= (is_array($dataRange))?$dataRange:json_decode($dataRange,true);
            $calculateRange			= (array)$calculateRangeObj;

            $arrayMerge				= [];
            $arrayRange				= [];
            
            if($typeRange==1){
                $dataRange			= $data['fecha_nacimiento'];
                $nameRange          = "nacimientos";
                $errorRange         = "6026";
                $erroramnt          = "6043";
            }else{
                $dataRange          = $data['edad'];
                $nameRange          = "edades";
                $errorRange         = "6031";
                $erroramnt          = "6042";
            }

            if($returnRange==3){
                $plan="0";
            }
            $dataValidate	= [
                '6037'				=> !(empty($numberPassengers) AND empty($origin) AND empty($destination) AND empty($departure) AND empty($arrival)),
                '9058'				=> (!is_numeric($plan))?0:1,
                '6027'				=> $origin,
                '6028'				=> $destination,
                '6029'				=> $departure,
                '6030'				=> $arrival, 
                '6026'				=> $numberPassengers,
                $errorRange			=> count($calculateRange),
                '4029'				=> (empty($numberPassengers) || $numberPassengers == 0 || !is_numeric($numberPassengers))?0:1,
                $erroramnt			=> ($this->countData($calculateRange ,$numberPassengers))?0:1,
                '2001'				=> $this->checkDates($departure),
                '1080'				=> ($destination == "1" OR $destination == "2" OR $destination == "9"),
                '2002'				=> $this->checkDates($arrival),
                '9059'				=> $this->verifyOrigin($origin),
            ];

            switch ($typeRange) {
                case '1':
                    $arrayRange		= [
                        '1062'		=> $this->checkDates($calculateRange),
                    ];
                    break;
                case '2':
                    $arrayRange		= [
                        '5016'		=> (is_numeric(implode('',$calculateRange))),
                    ];
                    break;
            }

            $arrivalTrans		= $this->transformerDate($arrival);
            $departureTrans		= $this->transformerDate($departure);

            $dataPlan			= $this->selectDynamic('','plans',"id='$plan'",array("id_plan_categoria","name","num_pas"));
            $datAgency			= $this->datAgency($api);

            $idCategoryPlan		= $dataPlan[0]['id_plan_categoria'];
            $idAgency			= $datAgency[0]['id_broker'];
            $isoCountry			= $datAgency[0]['id_country'];
            $daysByPeople 		= $this->betweenDates($departureTrans ,$arrivalTrans);
            $countryAgency		= $this->getCountryAgency($api);

            if($returnRange!=3){
                $arrayMerge	= [
                    '6022'		=> $plan
                ];
                $validatePlans	= $this->validatePlans($plan ,$idAgency ,$origin ,$destination ,$daysByPeople);
                if($validatePlans){
                    return $validatePlans;
                }
            }

            $dataValida			= $dataValidate+$arrayMerge+$arrayRange;

            $validatEmpty		= $this->validatEmpty($dataValida);
            if(!empty($validatEmpty)){
                return $validatEmpty;
            }
            
            $validateDateOrder	= $this->validateDateOrder($arrivalTrans,$departureTrans,$isoCountry);
            if(!empty($validateDateOrder)){
                return $validateDateOrder;
            }

            if ($typeRange==1) {

                $agesPassenger	= $this->setAges($calculateRange ,$isoCountry);
                foreach ($calculateRange as $value) {
                    $rangeTrans[] 	= $this->transformerDate($value);
                }
                $betweenDates 	= $this->betweenDates('',$rangeTrans,'years');
                if(!empty($betweenDates)){
                    return $betweenDates;
                }
            }else{
                $plan           = ($returnRange == 3)?'':$plan;
                $agesPassenger	=  implode(',',$calculateRange);
            }
    
            $dataQuoteGeneral	= $quoteGeneral->quotePlanbenefis($idCategoryPlan ,$daysByPeople ,$countryAgency ,$destination ,$origin ,$agesPassenger ,$departure ,$arrival ,$idAgency,$plan);

            $validatBenefits	= $this->verifyBenefits($dataQuoteGeneral);
            if($validatBenefits){
                return $validatBenefits;
            }

            switch ($returnRange) {
                case 1:
                    $response			= [
                        "total_orden"	=> $dataQuoteGeneral[0]['total'],
                        "idplan"		=> $plan,
                        "fecha_salida"	=> $departure,
                        "fecha_regreso"	=> $arrival,
                        "dias"			=> $daysByPeople,
                        $nameRange      => $calculateRange
                    ];
                    break;
                case 2:
                    $response			= [
                        "total_orden"	=> $dataQuoteGeneral[0]['total'],
                        "idplan"		=> $plan,
                        "fecha_salida"	=> $departure,
                        "fecha_regreso"	=> $arrival,
                        "dias"			=> $daysByPeople,
                        $nameRange      => $calculateRange,
                        "upgrade"		=> $this->dataUpgrades($plan,'spa' ,$dataQuoteGeneral[0]['total'],$daysByPeople,$numberPassengers)
                    ];
                    
                    break;
                case 3:
                    $countDataQuoteGeneral	= count($dataQuoteGeneral);
                    $responseExt = [
                        "fecha_salida"	=> $departure,
                        "fecha_regreso"	=> $arrival,
                        "dias"			=> $daysByPeople,
                        $nameRange      => $calculateRange
                    ];
                    for ($i=0; $i <$countDataQuoteGeneral ; $i++) {
                        $responsePlans[$dataQuoteGeneral[$i]['idp']]= [
                            'name'			=> $dataQuoteGeneral[$i]['name_plan'],
                            'total_orden'	=> $dataQuoteGeneral[$i]['total'],
                            "upgrade"		=> $this->dataUpgrades($dataQuoteGeneral[$i]['idp'],'spa' ,$dataQuoteGeneral[$i]['total'],$daysByPeople,$numberPassengers)
                        ];
                    }

                    $response = $responseExt + ['planes'=>$responsePlans];
                    break;
            }

            return $response;
        }

        public function countData($quantity, $vsquantity) //Listo
        {
            return (count($quantity) != $vsquantity) ? true : false;
        }

        public function verifyOrigin($origin) //Listo
        {
            $response = $this->selectDynamic('', 'countries', "iso_country='$origin'");
            return ($response) ? true : false;
        }

        public function betweenDates($start, $end, $type) // Listo
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

        public function getCountryAgency($api) //Listo
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

        public function validatePlans($plan, $agency, $origin, $destination, $daysByPeople) //Listo
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

        public function verifyRestrictionPlan($agency,$plan,$languaje,$details=false,$api,$simple){ //Listo
            $_respuesta = new response;
            $agency     = (!empty($agency))?$agency:$this->datAgency($api)[0]['id_broker'];
            $choicePlan = $this->selectDynamic('','broker',"id_broker in ($agency)",array("opcion_plan"))[0]['opcion_plan'];
            
            $query = "SELECT
                plans.id ";
            if($details){
                $query.=", plan_detail.titulo,
                    plan_detail.description,
                    plan_detail.language_id,
                    plans.id_plan_categoria,
                    plan_detail.plan_id,
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
                    plans.original_id,
                    plans.combo ";
            }
                $query.=" FROM
                plans
                INNER JOIN plan_detail ON plans.id = plan_detail.plan_id
                INNER JOIN restriction ON plans.id = restriction.id_plans
            ";
            ($details)?:$where[]=" plans.id = '$plan'";
            (!$details && (empty($languaje)))?:$where[]=" plan_detail.language_id = '$languaje'";
            $where[] = " plans.activo = '1' ";
            $where[] = " plans.eliminado = '1' ";
            $where[] ="(
                    plans.modo_plan = 'W'
                    OR plans.modo_plan = 'T'
            )";
            if($choicePlan == '1'){
                $where[] =
                "(
                    restriction.dirigido = 1
                    OR (restriction.dirigido = 2 AND restriction.id_broker in ($agency))
                    OR (restriction.dirigido = 6 AND restriction.id_broker in ($agency))
                )";
            }else if($choicePlan == '2'){
                $where[] =
                "(
                    (restriction.dirigido = 2 AND restriction.id_broker in ($agency))
                    OR (restriction.dirigido = 6 AND restriction.id_broker in ($agency))
                )";
            }
    
            $query.=(count($where)>0?" WHERE ".implode(' AND ',$where):" ");
    
            $response = $this->selectDynamic('','','','',$query);
        
            if(!$response){
                $querycheck1 = "SELECT id from plans where plans.id = '$plan' and plans.activo != '1'";
                $querycheck2 = "SELECT id from plans where plans.id = '$plan' and plans.eliminado != '1'";
                $rpcheck1 = $this->selectDynamic('','','','',$querycheck1);
                $rpcheck2 = $this->selectDynamic('','','','',$querycheck2);
                if($rpcheck1){
                    $error = '9201';
                }else if($rpcheck2){
                    $error = '9202';
                }else{
                    $error = '1050';
                }
                return $_respuesta->getError($error); 
            }elseif($details){
                return $response;
            }
        }

        public function verifyRestrictionDestination($destination, $plan) //Listo
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

        public function verifyDaysPlan($daysByPeople, $plan) //Listo
        {
            $_respuesta = new response;
            $daysConfigPlan  = $this->selectDynamic('', 'plans', "id='$plan'", array("min_tiempo", "max_tiempo", "compra_minima"))[0];


            if ($daysByPeople < $daysConfigPlan['min_tiempo']) {

                return  $_respuesta->getError('1248');
            }

            if ($daysByPeople > $daysConfigPlan['max_tiempo']) {

                return $_respuesta->getError('1247');
            }
            if (!empty($daysConfigPlan['compra_minima']) ? $daysByPeople < $daysConfigPlan['compra_minima'] : false) {
                return $_respuesta->getError('1248');
            }
        }

        public function validateDateOrder($arrival,$departure,$isoCountry){ //Listo
            $_respuesta = new response;
            $this->setTimeZone($isoCountry);
            $today  = date('Y-m-d');
            if($departure < $today or $arrival < $today){
                return $_respuesta->getError('2004');
            }elseif($arrival == $departure || $departure > $arrival){
                return $_respuesta->getError('3030');
            }
        }

        public function setAges($birthDayPassenger, $isoCountry) //Listo
        {
            foreach ($birthDayPassenger as $value) {
                $transformateValue  = $this->transformerDate($value);
                $calculate[]        = $this->calculateAge($transformateValue, $isoCountry);
            }

            return implode(',', $calculate);
        }

        public function verifyBenefits($dataQuoteGeneral) //Listo
        {      
            $_respuesta = new response; 
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
            return !empty($filter[0]) ? $_respuesta->getError($filter[0]) : false;
        }

        public function dataUpgrades($plan, $language, $price, $daysByPeople, $numberPassengers, $upgrade, $pricePassengers,$cost){ //Listo

            $query="SELECT
            raiders.id_raider,
            raiders_detail.name_raider,
            raiders.type_raider,
            raiders.value_raider,
     
            raiders.rd_calc_type,
            raiders.benefit_special,
            raiders.benefit_special_type,
            raiders.restriction_pass,
            raiders.restriction_days,
            CASE
                 WHEN raiders.rd_calc_type= 1  THEN 
                     IF(
                         raiders.type_raider     = 1,
                         ROUND(raiders.value_raider,2),
                         ROUND(((raiders.value_raider / 100) * '$price'),2)
                         
                     )
                 
                     WHEN raiders.rd_calc_type   = 4   THEN 
                     IF(
                     raiders.type_raider  = 1,
                     ROUND(raiders.value_raider * '$daysByPeople',2),
                     ROUND((raiders.value_raider / 100) * '$price' * '$daysByPeople',2)
                     )
                 
                     WHEN raiders.rd_calc_type   = 5  THEN 
                     IF(
                     raiders.type_raider = 1,
                     ROUND((raiders.value_raider * '$daysByPeople' * '$numberPassengers'),2),
                     ROUND(((raiders.value_raider / 100) * '$price') * '$daysByPeople'  * '$numberPassengers',2)
                     )
     
                     WHEN raiders.rd_calc_type   = 2  THEN 
                     IF(
                     raiders.type_raider = 1,
                     ROUND(raiders.value_raider + '$pricePassengers',2),
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
                     ROUND(raiders.cost_raider + '$pricePassengers',2),
                     ROUND((raiders.cost_raider / 100) * '$pricePassengers',2)
                     )
                 WHEN raiders.rd_calc_type   = 4   THEN 
                     IF(
                     raiders.type_raider  = 1,
                     ROUND(raiders.cost_raider * '$daysByPeople',2),
                     ROUND((raiders.cost_raider / 100) * '$cost' * '$daysByPeople',2)
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
     
             if(!empty($upgrade)){
             $query.= "AND 
                 raiders.id_raider IN ($upgrade) ";
             }
     
             $response   = $this->selectDynamic('','','','',$query);
             if($response){
     
                 return $response;
             }else{
     
                 return [
                     "status"   => "No hay resultados",
                     "message"  => "No hay upgrades asociados a éste Plan"
                 ];
             }
        }

        public function calculateAge($birthDayPassenger, $isoCountry) //Listo
        {

            $this->setTimeZone($isoCountry);
            $birthDayPassenger  = new DateTime($birthDayPassenger);
            $today              = new DateTime();
            $difference         = $today->diff($birthDayPassenger);
            return $difference->y;
        }

        public function setTimeZone($isoCountry) //Listo
        {

            $timeZone   = $this->selectDynamic('', 'cities', "iso_country='$isoCountry'", array("Timezone"))[0]['Timezone'];
            $timeZone   = !empty($timeZone) ? $timeZone : 'America/Lima';
            ini_set('date.timezone', $timeZone);
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
                $response   = $this->selectDynamic('','','','',$query);
                return $response;
            }else{
                return false;
            }
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

        public function getDataUpgradesRCI($idPlan,$lang,$idRaiders,$plataform)
        {
            
            $query = "SELECT
            plan_raider.id_raider,
            raiders_detail.name_raider,
            raiders.type_raider AS type_raider,
            raiders.cost_raider AS cost_raider,
            raiders.rd_calc_type AS rd_calc_type,
            raiders.value_raider AS value_raider,
            IF (
                    raiders.benefit_special,
                    CONCAT(
                        raiders.benefit_special,
                        ' ',

                    IF (
                        raiders.benefit_special_type = 'P',
                        'Pasajeros Adicionales',
                        'Dias Adicionales'
                    )
                    ),
                    'N/A'
                ) AS additional_value,
                raiders.restriction_days, 
                raiders.restriction_pass

            FROM
                plan_raider
            INNER JOIN raiders ON raiders.id_raider = plan_raider.id_raider
            INNER JOIN raiders_detail ON plan_raider.id_raider = raiders_detail.id_raider
            AND language_id = '$lang'
            WHERE
                id_status = 1
            AND id_plan = $idPlan ";

            if($idRaiders){

                $query.=" AND plan_raider.id_raider NOT IN($idRaiders) ";
            }

            return $this->selectDynamic('','','','',$query,false,false,$plataform);
        }
    }
?>