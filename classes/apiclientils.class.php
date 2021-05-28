<?php
class apiclientils extends cls_dbtools
{
    private $url = 'https://ilsadmin.com/app/api-ils';
    private $status = '';
    private $prefijo = '';
    public  $TIMEOUT = '3';
    public static $token='';
    public $auth=['users'=>'ApiRest','pass'=>'3m6%26%mF+#tC?2k'];
    public $totalToSend=0;
    public function __construct()
    {
        $this->auth['users'] = $this->auth['users'];
        $this->auth['pass'] = $this->auth['pass'];
        $this->status = SEND_ILSADMIN?:0;
        $this->prefijo = $this->getPrefixToParameters()?:PREFIJO;
    }
    public function setPrefix($prefijo)
    {
        $this->prefijo=$prefijo?:$this->prefijo;
        return $this;
    }
    public function getPrefix()
    {
        return $this->prefijo;
    }
    public static function sendCustomPrefix($prefijo,$TIMEOUT=50)
    {
        $self=new apiclientils();
        $self->setPrefix($prefijo);
        $self->TIMEOUT=$TIMEOUT;
        return $self;
    }
    public static function getTablesAndId($table='')
    {
        
        $tablasIds=[
            'beneficiaries' =>'id',
            'broker' =>'id_broker',
            'broker_nivel' =>'id',
            'broker_parameters' =>'id_broker',
            'broker_parameters_detail' =>'id',
            'orders' =>'id',
            'transactimport' =>'id',
            'orders_raider' =>'id',
            'order_comision' =>'id',
            'coupons' =>'id',
            'relaciotn_coupons' =>'id_detail',
            'plans' =>'id',
            'plan_category' =>'id_plan_categoria',
            'plan_categoria_detail' =>'id',
            'plan_detail' =>'id',
            'plan_raider' =>'id',
            'plans_wording' =>'id_plan_wording',
            'plans_wording_type' =>'id_plans_wording_type',
            'policies_parameter' =>'id',
            'phone_plans' =>'id_phone',
            'phone_plans_text' =>'id',
            'phone_parameter' =>'id_phone',
            'phone_parameter_text' =>'id',
            'plan_times' =>'id',
            'relacion_plan_cupon' =>'id_plan_coupon',
            'relaciotn_restriction' =>'id_detail',
            'restriction' =>'id_restric',
            'plan_type' =>'id',
            'plan_voucher' =>'id',
            'benefit' =>'id',
            'benefit_type' =>'id',
            'benefit_detail' =>'id',
            'benefit_plan' =>'id',
            'benefit_product' =>'id',
            'raiders' =>'id_raider',
            'raiders_detail' =>'id',
            'revocation_parameter' =>'id',
            'territory' =>'id_territory',
            'user_associate' =>'id',
            'users' =>'id',
            'usoweb_parameter' =>'id',
            'wording_parameter' =>'id',
            'plan_times_ag_net'=>'id',
            'plan_band_age'=>'id',
            'plans_renewal'=>'id',
        ];
        return empty($table)?$tablasIds:$tablasIds[$table];
    }
    public function getPrefixToParameters()
    {
        $query = 'SELECT parameter_value FROM `parameters` WHERE parameter_key like "PREFIJO"';
        return $this->_SQL_tool($this->SELECT_SINGLE, __METHOD__, $query)['parameter_value'];
    }
    public function obtener_parametros_webservice()
    {
        $query = "SELECT * FROM webservice WHERE estatus_service = 1";
        return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
    }
    public function CallApiPost($url, $data)
    {
        $curl = curl_init();
        $headers['authorization']="Basic ".base64_encode($this->auth['users'] . ":" . $this->auth['pass']);
        if (self::$token) {
            $headers['TOKEN']=self::$token;
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->TIMEOUT,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array_map(function ($key, $value) {
                return "$key: $value";
            }, array_keys($headers), $headers),
        ));
        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headersResponse = explode("\r\n", substr($response, 0, $header_size));
        $headersResponse = array_reduce(
            $headersResponse,
            function ($response, $element) {
                $val =explode(": ", $element);
                if (!empty($val[1])) {
                    $response[$val[0]]=$val[1];
                }
                return $response;
            },
            []
        );
        if ($headersResponse['TOKEN']) {
            self::$token=$headersResponse['TOKEN'];
        }
        $body = substr($response, $header_size);
        $err = curl_error($curl);
        curl_close($curl);
        return json_decode(trim($body), true)?:(json_decode(trim($err), true)?:$err);
    }
    public function GetDataBD($query)
    {
        $registros = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
        $this->totalToSend=$this->total_verdadero;
        return $registros;
    }
    public function sendApi($tabla, $operacion, $condicion)
    {
        if (empty($condicion)||$this->status != 1) {
            return false;
        }

    
        if ($this->status == 1) {
            $query = "SELECT * FROM $tabla WHERE $condicion";
            $registros = $this->GetDataBD($query);
            $id=apiclientils::getTablesAndId($tabla);
            foreach ($registros as &$value) {
                $ids[]=$value[$id];
                if(is_numeric($value['cargado'])){
                    $cargado=true;
                }
                unset($value['cargado']);
                unset($value['original_id_user']);
                $value['prefijo']=$value['prefijo']?:$this->prefijo;
                $value=array_filter($value, "strlen");
            }
            if(empty($registros)){
                return false;
            }
			$check_cola = $this->comprobarCola();
            if ($check_cola == 0) {
                try {
                    $CallApiPost=$this->CallApiPost($this->url.'/SetDataArray', json_encode([
                        'operacion'=>$operacion,
                        'tabla'=> $tabla,
                        'registros'=> $registros,
                    ]));
                    if ($CallApiPost['STATUS'] != 'OK') {
                        throw new Exception(json_encode($CallApiPost));
                    }
                    if(!empty($cargado)){
                        $this->auto_soap = false;
                        $cargado=implode("','",$ids);
						$this->_SQL_tool("UPDATE", __METHOD__, "UPDATE $tabla SET cargado=1 WHERE {$id} in('{$cargado}')");
                        $this->auto_soap = true;
                    }
                    $CallApiPost['IDS']=$ids;
                    return $CallApiPost;
                } catch (Exception $e) {
                    $this->add_cola($operacion, $tabla, $condicion, $registros, $e->getMessage());
                }
            } else {
                $this->add_cola($operacion, $tabla, $condicion, $registros, 'Hay registros en cola. ');
            }
        } else {
            return false;
        }
    }
    protected function add_cola($operacion, $tabla, $condicion, $registros, $excepcion)
    {
        $this->genera_log=false;
        $registros = addslashes(json_encode($registros));
        $error = base64_encode($excepcion);
        $condicion = addslashes($condicion);
        $query = "INSERT INTO cola_soap(operacion, tabla, condicion, registros, errores, fecha_hora) VALUES ('$operacion','$tabla', '$condicion', '$registros', '$error', now())";
		return $this->_SQL_tool($this->INSERT, __METHOD__, $query);
        $this->genera_log=true;
    }
    public function delete_cola($id)
    {
        $this->genera_log=false;
		$query = "DELETE FROM cola_soap WHERE id = '$id'";
        $this->_SQL_tool($this->DELETE, __METHOD__, $query);
        $this->genera_log=true;
    }
    public function update_cola($id, $error)
    {
        $this->genera_log=false;
        $error = base64_encode($error);
		$query = "UPDATE cola_soap SET errores = '{$error}' WHERE id = '$id'";
        $this->_SQL_tool($this->UPDATE, __METHOD__, $query);
        $this->genera_log=true;
    }
    public function Send_Cola($cola)
    {
		$cola['registros'] = json_decode($cola['registros'], true);
        try {
            $CallApiPost=$this->CallApiPost($this->url.'/SetDataArray', json_encode([
                'operacion'=>$cola['operacion'],
                'tabla'=> $cola['tabla'],
                'registros'=> $cola['registros'],
			]));
            if ($CallApiPost['STATUS'] != 'OK') {
                throw new Exception(json_encode($CallApiPost));
            }
            $this->delete_cola($cola['id']);
            return 0;
        } catch (Exception $e) {
            $error = addslashes($e->getMessage());
            $this->update_cola($cola['id'], $error);
            return 1;
        }
    }
    public function comprobarCola()
    {
        $maxReg=250;
        $query = "SELECT * FROM cola_soap LIMIT $maxReg";
        $cola_soap = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
        $total=($this->total_verdadero-$maxReg);
        if ($cola_soap) {
            foreach ($cola_soap as $cola) {
				$respuesta = $this->Send_Cola($cola);
                if ($respuesta == 1) {
                    return 1;
                }
            }
            return ($total>0)?$this->comprobarCola():$respuesta;
        }
        return 0;
    }
    public static function sendPlans($idPlan, $tablas=['plans','plan_category','plan_times','raiders','plans_wording','benefit'])
    {
        $model=new static();
        $prefix=$model->_SQL_tool('SELECT_SINGLE', __METHOD__, "SELECT provider_id_plan FROM plans WHERE id='{$idPlan}'")['provider_id_plan'];
        if (preg_match("/^([A-Z0-9]{2,3})$/", $prefix)) {
            $Api=apiclientils::sendCustomPrefix($prefix);
            if (in_array('plans', $tablas)) {
                $Api->sendApi('plans', 'UPDATE', "id='{$idPlan}'");
                $Api->sendApi('plan_detail', 'UPDATE', "plan_id='{$idPlan}'");
            }
            if (in_array('plan_category', $tablas)) {
                $idCat=$Api->sendApi('plan_category', 'UPDATE', "EXISTS(SELECT 1 FROM plans WHERE id='{$idPlan}' AND plan_category.id_plan_categoria=plans.id_plan_categoria)");
                $idCat=implode("','", $idCat['IDS']);
                $Api->sendApi('plan_categoria_detail', 'UPDATE', "plan_categoria_detail.id_plan_categoria in('{$idCat}')");
            }
            if (in_array('plan_times', $tablas)) {
                $Api->sendApi('plan_times', 'UPDATE', "id_plan='{$idPlan}'");
            }
            if (in_array('benefit', $tablas)) {
                $Api->sendApi('benefit_plan', 'UPDATE', "id_plan = '{$idPlan}'");
                $idBenf=$Api->sendApi('benefit', 'UPDATE', "EXISTS(SELECT 1 FROM benefit_plan WHERE benefit_plan.id_plan = '{$idPlan}' AND benefit.id = benefit_plan.id_beneficio)");
                $idBenf=implode("','", $idBenf['IDS']);
                $Api->sendApi('benefit_detail', 'UPDATE', "id_benefit in('{$idBenf}')");
                $Api->sendApi('benefit_type', 'UPDATE', "EXISTS(SELECT 1 FROM benefit WHERE benefit.id in('{$idBenf}') AND benefit.Type_benefit = benefit_type.id)");
            }
            if (in_array('plans_wording', $tablas)) {
                $Api->sendApi('plans_wording_type', 'UPDATE', "1");
                $Api->sendApi('plans_wording', 'UPDATE', "id_plan='{$idPlan}'");
            }
            if (in_array('restriction', $tablas)) {
                $idRest=$Api->sendApi('restriction', 'UPDATE', "id_plans='{$idPlan}'");
                $idRest=implode("','", $idRest['IDS']);
                $Api->sendApi('relaciotn_restriction', 'UPDATE', "id_restric in('{$idRest}')");
            }
            if (in_array('raiders', $tablas)) {
                $Api->sendApi('plan_raider', 'UPDATE', "id_plan ='{$idPlan}'");
                $idRaider=$Api->sendApi('raiders', 'UPDATE', "EXISTS(SELECT 1 FROM plan_raider WHERE plan_raider.id_plan ='{$idPlan}' AND plan_raider.id_raider = raiders.id_raider)");
                $idRaider=implode("','", $idRaider['IDS']);
                $Api->sendApi('raiders_detail', 'UPDATE', "id_raider in('{$idRaider}')");
            }
        }
    }
}
