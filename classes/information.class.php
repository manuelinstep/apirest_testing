<?php
    require_once("response.class.php");
    require_once("connection/connect.php");

    /**
     * En este apartado van los métodos para obtener información de la plataforma
     *-Función OBTENER API KEY // YA ESTA EN AUTH
     *-Función OBTENER VOUCHER // Implementar de primero // LISTO
     *-Función OBTENER MONEDAS // LISTO
     *-Función OBTENER PAÍSES 
     *-Función OBTENER REGIONES
     *-Función OBTENER PLANES
     *-Función OBTENER COBERTURAS
     *-Función OBTENER PRECIO
     *-Función OBTENER LENGUAJES
     *-Función OBTENER CATEGORÍA DE PLANES
     *-Función OBTENER PRECIO DE LA ORDEN POR FECHA DE NACIMIENTO POR PLAN
     *-Función OBTENER PRECIO DE LA ORDEN POR EDAD POR PLAN
     *-Función OBTENER PRECIO DE LA ORDEN POR FECHA DE NACIMIENTO
     *-Función OBTENER PRECIO DE LA ORDEN POR EDAD
     *-Función OBTENER CONDICIONES
     *-Función OBTENER UPGRADE
     *-Función PAÍSES DE ORIGEN RESTRINGIDOS
     *-Función TASA DE CAMBIO POR PAÍS
     *-Función OBTENER REPORTE DE VENTAS
     *-Función OBTENER PAISES - CIUDADES
     *-Función OBTENER PAISES - ESTADOS
     *-Función OBTENER ASISTENCIA
     *-Función OBTENER DETALLE DE ASISTENCIA
     *-Función OBTENER LINEA DE TIEMPO
     *-Función OBTENGA BENEFICIOS DEL CASO
     *-Función OBTENER PAISES - ESTADOS - CIUDADES
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
                            return $_respuesta->error_400("No se ha encontrado el voucher","403");
                        }
                    }else{
                        return $_respuesta->error_400("El token proporcionado no es válido","402");
                    }

                    break;

                case "get_currencies":

                    //Solo verifica que el API KEY sea correcta
                    $checkToken = parent::checkToken($datos['token']);
                    if($checkToken[0]['id_status'] == 1 && $checkToken[0]['ip_remote']==$_SERVER['REMOTE_ADDR']){
                        $response = $this->get_currencies();
                        return (!empty($response)) ? $response : $_respuesta->error_400("No se han encontrado monedas","404");
                    }else{
                        return $_respuesta->error_400("El token proporcionado no es válido","402");
                    }

                    break;
                default:
                    /**
                     * Se debe determinar si esta vacio, o si viene con un metodo
                     * no existente
                     */
                    return $_respuesta->error_400("El método solicitado no existe o ha dejado el campo vacío","401");
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
    }
?>