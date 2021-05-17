<?php
/**
 * D2PW Solutions
 *
 * Description: Funciones generales para cargar y guardar data en cualquier tabla de la base de datos
 *
 * @author Miguel Aguero y Cesar Lizarraga
 * @version 1.0  2010/10/21
 */
class Model extends cls_dbtools {
    /******************************************************************
    /*******  VARIABLES
    /******************************************************************/
    var $id=null;
    var $table;
    var $idName = 'id';

    /******************************************************************
    /*******  CONSTRUCTOR
    /******************************************************************/
    function __construct(){  
        $CORE_email = new Email(array('smtpServer' => EMAIL_SERVER_HOST, 
        'smtpUser' => EMAIL_SERVER_USER, 
        'smtpPassword' => EMAIL_SERVER_PASS, 
        'appDomainRoot' => DOMAIN_ROOT, 
        'skeletonFile' => COREROOT . 'lib/common/email_skeleton.php', 
        'emailEngine' => EMAIL_ENGINE, 
        'transGroupID' => EMAIL_TRANSACTIONAL_GROUP_ID), 
        array('debug' => EMAIL_DEBUG_SEND, 'emailDebug' => EMAIL_DEBUG));

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

    /******************************************************************
    /*******  FUNCIONES
    /******************************************************************/
    /**
     * Realiza una busqueda de una tabla en especifico
     * @param $condition
     * @param $limit
     * @param $fields
     * @param $type
     * @param $order
     * @return boolean
     */
    function find($condition, $limit=null, $fields='*', $type = '', $order = '',$inners = '',$other_conditions='')
    {
        $queryCondition = '1 ';
        $query = 'SELECT ';
                if ($condition) {
        foreach($condition as $field=>$value){
                   // if ($value){
                        if (is_array($value)){
			$queryCondition.= ' AND '.get_class($this).".".$field."in ('".implode("','",$value)."') ";
                        }else{
                        $queryCondition.= ' AND '.get_class($this).".".$field."='".$value."' ";
        }
                   // }
		}
                }
                $queryCondition.= " ".$other_conditions;
        if(is_array($fields)){
                foreach($fields as $i => $value){
                        if($i > 0 ) $query.= ',';
                        $query.= $value;
                }
        }else{
                $query.= $fields;
        }

        if(!empty($order))	$order = " ORDER BY ".$order;

		$query .= " FROM ".$this->table." ".get_class($this)." ".$inners." where ".$queryCondition.$order;
        $result = false;
			if(is_array($limit)) {
				$query .= " LIMIT " . $limit[0].','.$limit[1];
				//die($query);
				$result = $this->_SQL_tool('SELECT', __METHOD__, $query);
			} else if(!is_null($limit)){
                $result = $this->_SQL_tool('SELECT_SINGLE', __METHOD__, $query);
        } else {
                $result = $this->_SQL_tool('SELECT', __METHOD__, $query);
        }
        if(strcasecmp('list',$type) == 0){
                $list = array();
			$key = (count($fields) > 1) ? $fields[0] : $this->idName;
                $value =  (count($fields) > 1) ? $fields[1] : 'name';
                if (count($result)>0)
                        foreach ($result as $element)	$list[$element[$key]] = $element[$value];
                $result = $list;
        }
        return $result;
    }
    /**
     * Guarda la informacion de una tabla en especifico
     * @param $data
     * @return int
     */
    function save($data)
    {
        if($data["$this->idName"]!=null)	$this->id = $data["$this->idName"];

		if(empty($data["$this->idName"]))
			unset($data["$this->idName"]);

        if($this->id!=null){
            $query = "UPDATE " . $this->table ." SET ";
            $values = " ";
            $sw = false;
            foreach($data as $field=>$value) {
                if($sw)
                    $values.= ", ";
                else
                    $sw = true;

                htmlentities(trim($values), ENT_COMPAT);
                $values.= $field.'="'.$value.'"';
            }

            $values.=", modified=now()";
            $query.= $values . ' WHERE ' .$this->idName.'='.$this->id;
            //Debug::pr($query, true);
            $result = $this->_SQL_tool('UPDATE', __METHOD__, $query);

        } else {
            $query = "INSERT INTO " . $this->table;
            $sw = false;

            foreach($data as $field=>$value){
                if($sw){
                    $fields.=", ";
                    $values.=", ";
                } else {
                    $fields =" (";
                    $values =" (";
                    $sw = true;
                }
                $fields.= $field;
                htmlentities(trim($values), ENT_COMPAT);
                $values.= '"'.$value.'"';
            }
            $fields.=",created,modified)";
            $values .=",now(),now())";

            $query.=" " . $fields." VALUES ".$values;
            $result = $this->_SQL_tool('INSERT', __METHOD__, $query);
            $this->id = $result;
        }
        return $result;
    }
    /**
     * Guarda la informacion contenida en un array en una tabla en especifico
     * @param $data
     * @return int
     */
    function saveAll($data){
    	$this->_begin_tool();

    	foreach ($data as $element){
            $result[] = $this->save($element);
            $this->id = null;
	}
    	if($result)	$this->_commit_tool();
    	else		$this->_rollback_tool();

        return $result;
    }
    /**
     * Busca el ultimo ID generada de una tabla en especifico
     * @return array
     */
    function getLastId(){
            $query = "SELECT MAX(id) AS lastId FROM " . $this->table ;
            $result = $this->_SQL_tool($this->SELECT_SINGLE, __METHOD__, $query);
            return $result;
    }

    /**
     * Actualiza los datos indicados en $data de uno � varios registros dependiendo de
     * las condiciones que se coloquen en el segundo parametro ( $where )
     *
     * Ejemplo:
     *
     * ->update(array('nombres' => 'Manuel Aguirre'),'id_person = 3');
     *
     * @param array $data  datos a actualizar e forma de array
     * @param string $where  condicion que debe cumplir el update
     * @param boolean $modified parametro opcional para saber si existe un campo modified en la tabla
     * @return int
     *
     * @author Manuel Aguirre
     */
    public function update($data, $where, $modidied = true) {
    	$values = array();
    	foreach ($data as $field => $value) {
    		$value = mysql_real_escape_string($value);
    		$values[] = "$field = '$value'";
    	}
    	if ($modidied) {
    		$values[] = "modified = now()";
    	}
    	$values = join(', ', $values);
    	$query = "UPDATE {$this->table} SET {$values} WHERE {$where}";
    	return $this->_SQL_tool($this->UPDATE, __METHOD__, $query);
    }

    /**
     * elimina que coincidan con las las condiciones que se coloquen
     * en el parametro ( $where )
     *
     * Ejemplo:
     *
     * ->delete('nombres = "manuel aguirre"');
     *
     * @param string $where
     * @return int
     *
     * @author Manuel Aguirre
     */
    public function delete($where) {
    	$query = "DELETE FROM {$this->table} WHERE {$where}";
    	return $this->_SQL_tool($this->DELETE, __METHOD__, $query);
    }

    
}