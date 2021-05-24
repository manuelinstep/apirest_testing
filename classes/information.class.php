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

    class information extends connect{
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
                    $checkToken = parent::checkToken($datos['token']);
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
                    $checkToken = parent::checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        $response = $this->get_currencies();
                        return (!empty($response)) ? $response : $_respuesta->getError('9015');
                    }else{
                        return $_respuesta->getError('1005');
                    }

                    break;

                case "get_countries":

                    //Verifica el API KEY y que haya un lenguaje, el cual será spa o eng
                    $checkToken = parent::checkToken($datos['token']);
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
                    $checkToken = parent::checkToken($datos['token']);
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
                    $checkToken = parent::checkToken($datos['token']);
                    
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
                    $checkToken = parent::checkToken($datos['token']);
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

                    $checkToken = parent::checkToken($datos['token']);
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
                    $checkToken = parent::checkToken($datos['token']);
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
                    $checkToken = parent::checkToken($datos['token']);
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
                    $checkToken = parent::checkToken($datos['token']);
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
                    $checkToken = parent::checkToken($datos['token']);
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
                    $checkToken = parent::checkToken($datos['token']);
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
                    $checkToken = parent::checkToken($datos['token']);
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
                    $checkToken = parent::checkToken($datos['token']);
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

                    $checkToken = parent::checkToken($datos['token']);
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
                    $checkToken = parent::checkToken($datos['token']);
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
            $response = parent::obtenerDatos($query);
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
            $response = parent::obtenerDatos($query);
            if($response){
                return $response;
            }else{
                return 0;
            }
        }

        private function get_currencies(){
            $query = "SELECT id_country, value_iso, desc_small FROM currency WHERE id_status = '1'";
            $response = parent::obtenerDatos($query);
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
            $response = parent::obtenerDatos($query);
            return $response;
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
            return parent::obtenerDatos($query);
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
            return parent::nonQuery($query);
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
    }
?>