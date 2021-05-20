<?php
class soapCliente extends apiclientils
{
    public $extradata = false;
    public function send_soap($tabla, $operacion, $condicion)
    {

        switch ($tabla) {
            case 'plan_times':
                $prefijo = $this->get_prefijo_plan_times($condicion);
                $prefijo = !empty($prefix) ? $prefix : "IX";
                break;
            case 'orders':
                $prefix = $this->get_prefijo_order($condicion);
                $prefijo = !empty($prefix) ? $prefix : "IX";
                break;
            case 'beneficiaries':
                $prefix = $this->get_prefijo_beneficiaries($condicion);
                $prefijo = !empty($prefix) ? $prefix : "IX";
                break;
            case 'relaciotn_restriction':
                $encontrado = stripos($condicion, 'id_restric');
                if ($encontrado !== false) {
                    $result = $this->get_prefijo_plans_restrictions($condicion);
                } else {
                    $result = $this->get_prefijo_relation_details($condicion);
                }
                $prefijo = !empty($result['prefijo']) ? $result['prefijo'] : "IX";
                $id_plan = $result['id_plan'];
                /*if ('190.199.99.165'== $_SERVER['REMOTE_ADDR']) {
                            $this->send_soap_masivo("plans","id={$id_plan}","",$prefijo);
                            $plan_category=$this->get_plan_category($id_plan);
                            $this->send_soap_masivo("plan_category","id_plan_categoria IN ('$plan_category')","",$prefijo);
                            $this->send_soap_masivo("plan_categoria_detail","id_plan_categoria IN ('$plan_category')","",$prefijo);
                            $this->send_soap_masivo("plan_times","id_plan={$id_plan}","",$prefijo);
                            $this->send_soap_masivo("benefit_plan","id_plan={$id_plan}","",$prefijo);
                            $benefit_plan=$this->benefits_by_plan($id_plan);
                            $type_benefit=$benefit_plan['type_benefit'];
                            empty($type_benefit)?:$this->send_soap_masivo("benefit_type","id IN('$type_benefit')","",$prefijo);
                            $id_benefit=$benefit_plan['id'];
                            empty($id_benefit)?:$this->send_soap_masivo("benefit","id IN ('$id_benefit')","",$prefijo);
                            empty($id_benefit)?:$this->send_soap_masivo("benefit_detail","id_benefit IN ('$id_benefit')","",$prefijo);
                            $this->send_soap_masivo("plan_detail","plan_id={$id_plan}","",$prefijo);
                            $raider_plan=$this->raiders_by_plan($id_plan);
                            empty($raider_plan)?:$this->send_soap_masivo("raiders","id_raider IN ('$raider_plan')","",$prefijo);
                            empty($raider_plan)?:$this->send_soap_masivo("raiders_detail","id_raider IN ('$raider_plan')","",$prefijo);
                            $this->send_soap_masivo("plan_raider","id_plan={$id_plan}","",$prefijo);
                            $this->send_soap_masivo("phone_plans","plans_id={$id_plan}","",$prefijo);
                            $this->send_soap_masivo("plan_band_age","id_plan={$id_plan}","",$prefijo);
            }*/
                break;
            case 'broker':
                //$prefix=$this->get_prefijo_broker('',$condicion);
                $prefijo = "IX";
                break;
            case 'plan_detail':
                $prefix = $this->get_prefijo_plan_details($condicion);
                $prefijo = !empty($prefix) ? $prefix : "IX";
                break;
            case 'plan_raider':
                $prefix = $this->get_prefijo_plan_raiders($condicion);
                $prefijo = !empty($prefix) ? $prefix : "IX";
                break;
            case 'phone_plans':
                $prefix = $this->get_prefijo_phone_plans($condicion);
                $prefijo = !empty($prefix) ? $prefix : "IX";
                break;
            case 'benefit_plan':
                $prefix = $this->get_prefijo_benefit_plan($condicion);
                $prefijo = !empty($prefix) ? $prefix : "IX";
                break;
            case 'plan_band_age':
                $prefix = $this->get_prefijo_plan_band_age($condicion);
                $prefijo = !empty($prefix) ? $prefix : "IX";
                break;
            case 'plans':
                $encontrado = stripos($condicion, 'id_plan_categoria');
                $prefix = ($encontrado !== false) ? 0 : $this->get_prefijo_plans($condicion);
                $prefijo = !empty($prefix) ? $prefix : "IX";
                break;
            default:
                $prefijo = $this->getPrefix();
                break;
        }
       
        return $this->sendApi($tabla, $operacion, $condicion);
    }
    public function send_soap_app_user($tabla, $operacion, $condicion, $password)
    {
        $this->extradata = ['pass_ext' => $password];
        return $this->sendApi($tabla, $operacion, $condicion);
    }
    public function GetDataBD($query)
    {
        $result = parent::GetDataBD($query);
        if (is_array($this->extradata)) {
            foreach ($result as &$value) {
                $value = array_merge($value, $this->extradata);
            }
        }
        return $result;
    }
    public function send_soap_masivo($tabla, $condicion)
    {
        return $this->sendApi($tabla, 'UPDATE', $condicion);
    }
    public function add_soap($tabla, $campoid)
    {
        return true;
    }
    public function update_soap($tabla, $campoid, $id, $language_id = '')
    {
        return true;
    }
    public function get_prefijo_benefit_plan($benefit_plan)
    {
        $query = "SELECT
			id_broker
			FROM
			restriction
			WHERE
			id_plans IN (
				SELECT
					id_plan
				FROM
					`benefit_plan`
				WHERE
					$benefit_plan
			)";
        $id_broker = $this->_SQL_tool($this->SELECT, __METHOD__, $query)[0]['id_broker'];
        return $this->get_prefijo_broker($id_broker);
    }
    public function get_prefijo_phone_plans($id_phone_plans)
    {
        $query = "SELECT
			id_broker
			FROM
			restriction
			WHERE
			id_plans IN(
				SELECT
					plans_id
				FROM
					`phone_plans`
				WHERE
					$id_phone_plans
			)";
        $id_broker = $this->_SQL_tool($this->SELECT, __METHOD__, $query)[0]['id_broker'];
        return $this->get_prefijo_broker($id_broker);
    }
    public function get_prefijo_plan_times($id_plan_times)
    {
        $query = "SELECT
				id_broker
				FROM
					restriction
				WHERE
				id_plans IN (
					SELECT
						plans.id
					FROM
						`plans`
					INNER JOIN plan_times ON plan_times.id_plan = plans.id
					WHERE
						plan_times.$id_plan_times
					)";
        $id_broker = $this->_SQL_tool($this->SELECT, __METHOD__, $query)[0]['id_broker'];
        return $this->get_prefijo_broker($id_broker);
    }
    public function get_prefijo_plan_details($plan_detail)
    {
        $query = "SELECT
			id_broker
			FROM
			restriction
			WHERE
			id_plans IN (
				SELECT
					plan_id
				FROM
					`plan_detail`
				WHERE
					$plan_detail
			)";
        $id_broker = $this->_SQL_tool($this->SELECT, __METHOD__, $query)[0]['id_broker'];
        return $this->get_prefijo_broker($id_broker);
    }
    public function get_prefijo_plans_restrictions($id_restrict)
    {
        $query = "SELECT
				id_broker,
				id_plans
			FROM
				restriction
			WHERE
				$id_restrict";
        $result = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
        $prefijo = $this->get_prefijo_broker($result[0]['id_broker']);
        return array("prefijo" => $prefijo, "id_plan" => $result[0]['id_plans']);
    }
    public function get_prefijo_relation_details($detail_restrict)
    {
        $query = "SELECT
				restriction.id_broker,
				restriction.id_plans
			FROM
				restriction
			INNER JOIN relaciotn_restriction ON restriction.id_restric = relaciotn_restriction.id_restric
			WHERE relaciotn_restriction.$detail_restrict";
        $result = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
        $prefijo = $this->get_prefijo_broker($result[0]['id_broker']);
        return array("prefijo" => $prefijo, "id_plan" => $result[0]['id_plans']);
    }
    public function benefits_by_plan($id_plan)
    {
        $query = "SELECT
			benefit.id,
			benefit.type_benefit
			FROM
			benefit
			INNER JOIN benefit_plan ON benefit_plan.id_beneficio = benefit.id
			WHERE
			benefit_plan.id_plan = '$id_plan'";
        $result = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
        $id = implode("','", array_map(function ($value) {
            return $value['id'];
        }, $result));
        $type_benefit = implode("','", array_unique(array_map(function ($value) {
            return $value['type_benefit'];
        }, $result)));
        return array('id' => $id, 'type_benefit' => $type_benefit);
    }
    public function raiders_by_plan($id_plan)
    {
        $query = "SELECT
				raiders.id_raider
			FROM
			raiders
			INNER JOIN plan_raider ON plan_raider.id_raider = raiders.id_raider
			WHERE
			plan_raider.id_plan = '$id_plan'";
        $result = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
        return implode("','", array_map(function ($value) {
            return $value['id_raider'];
        }, $result));
    }
    public function get_prefijo_plans($id_plan)
    {
        $id_plan = str_replace('id', 'id_plans', $id_plan);
        $id_plan = str_replace('plans.', '', $id_plan);

        $query = "SELECT
				id_broker
			FROM
				restriction
			WHERE
			$id_plan";

        $id_broker = $this->_SQL_tool($this->SELECT, __METHOD__, $query)[0]['id_broker'];
        return $this->get_prefijo_broker($id_broker);
    }
    public function get_prefijo_plan_raiders($id_plan_raider)
    {
        $query = "SELECT
			id_broker
			FROM
			restriction
			WHERE
			id_plans IN(
				SELECT
					id_plan
				FROM
					`plan_raider`
				WHERE
					$id_plan_raider
			)";
        $id_broker = $this->_SQL_tool($this->SELECT, __METHOD__, $query)[0]['id_broker'];
        return $this->get_prefijo_broker($id_broker);
    }
    public function get_prefijo_plan_band_age($plan_band_age)
    {
        $query = "SELECT
			id_broker
			FROM
			restriction
			WHERE
			id_plans IN(
				SELECT
					id_plan
				FROM
					`plan_band_age`
				WHERE
					$plan_band_age
			)";
        $id_broker = $this->_SQL_tool($this->SELECT, __METHOD__, $query)[0]['id_broker'];
        return $this->get_prefijo_broker($id_broker);
    }
    public function get_plan_category($id_plan)
    {
        $query = "SELECT 
			id_plan_categoria 
			FROM plans 
			WHERE 
			id='$id_plan'";
        $result = $this->_SQL_tool($this->SELECT, __METHOD__, $query);
        return implode("','", array_map(function ($value) {
            return $value['id_plan_categoria'];
        }, $result));
    }
    public function get_prefijo_broker($id_broker, $broker = '')
    {
        $sqlbroker = empty($broker) ? "id_broker = '$id_broker'" : $broker;
        $query = "SELECT
			prefijo
			FROM
			broker
			WHERE
			$sqlbroker ";
        return $this->_SQL_tool($this->SELECT, __METHOD__, $query)[0]['prefijo'];
    }
    public function get_audit_trasactions($fecha)
    {
        $query = "SELECT * FROM audit_trans WHERE Fecha_Audit = '$fecha'";
        return $this->_SQL_tool($this->SELECT_SINGLE, __METHOD__, $query);
    }
    public function get_prefijo_order($id_orden)
    {
        $query = "SELECT
				agencia
			FROM
				orders
			WHERE
				$id_orden";

        $id_broker = $this->_SQL_tool($this->SELECT, __METHOD__, $query)[0]['agencia'];
        return $this->get_prefijo_broker($id_broker);
    }
    public function get_prefijo_beneficiaries($id_beneficiaries)
    {
        $query = "SELECT
			orders.agencia
			FROM
			orders
			WHERE
			id IN (
				SELECT
					id_orden
				FROM
					beneficiaries
				WHERE
				$id_beneficiaries
			)";
        $id_broker = $this->_SQL_tool($this->SELECT, __METHOD__, $query)[0]['agencia'];
        return $this->get_prefijo_broker($id_broker);
    }
    /*function get_prefijo_broker($id_broker,$broker){
            $sqlbroker=empty($broker)?"id_broker = '$id_broker'":$broker;
            $query="SELECT
            prefijo
            FROM
            broker
            WHERE
            $sqlbroker ";
            return $this->_SQL_tool($this->SELECT, __METHOD__, $query)[0]['prefijo'];
        }*/
}
