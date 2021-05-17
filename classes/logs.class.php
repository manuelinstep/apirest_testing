<?php
/**
 * D2PW Solutions
 *
 * Description: Manejo de logs de eventos -- Los Id de eventos pueden ser visto en la tabla log_codigos
 *
 * @author Javier Mijares
 * @version 1.0  2010/10/21
 */

class cls_logs {
	/******************************************************************
	/*******  VARIABLES
	/******************************************************************/
	private $saltodelinea = array ("\t","\n","\r","\0","\x0B");
	private $codigos = array();
	private $log_usuario_id='';
	private $log_usuario_info='';
	private $log_usuario_email='';
	private $log_usuario_compania='';
    private $log_usuario_profile='';
	private $log_navegador='';
	private $data_old='No data';
	private $data_new='No data';
	private $table_name='No data';
	private $where_soap='No data';

	/******************************************************************
	/*******  CONSTRUCTOR
	/******************************************************************/
   
   
        /**
	 * Funcion constructora, obtiene los codigos de los logs
	 * @return array
	 */
	function __construct(){
		$query="SELECT * FROM log_codigos";
		$res=mysql_query($query) or die("Error: ".mysql_error()."<br /><br />Function: ".__METHOD__."<br /><br />Query: ".$query);
		$res_array=array();
		while ( $row = mysql_fetch_array ( $res ) ) {
			foreach ( $row as $key => $value ) {
				$res_array[$row['log_codigo_id']][$key] = $value;
			}
		}
		$this->codigos=$res_array;
	}
	/******************************************************************
	/*******  FUNCIONES
	/******************************************************************/
        /**
	 * Desglosa el query segun su funcionabilidad para almacenar los datos en el log
	 * @param $query
	 * @param $tipo
	 * @return int
	 */
         function toatal_reg($id_user){
    //Para retornar el total de registros si no existiera el limite
            $oDBTools = new cls_dbtools;
            $query="SELECT COUNT(*) as total FROM log_consultas WHERE usuario_id='$id_user'";    
             return $total_reg= $oDBTools->_SQL_tool('SELECT',__METHOD__,$query);
    
          }
    
	function process_query($query, $tipo){
		$query = trim( str_ireplace($this->saltodelinea, " ", $query) );
		$tipo = strtoupper($tipo);
		$this->data_old = 'No data';
		$this->data_new = 'No data';
		$this->table_name = '';
		$var_data_old = '';
		$var_data_new = '';
		$i=0;

		if($tipo=='INSERT'){
			/**
			 * http://dev.mysql.com/doc/refman/5.0/en/insert.html
			 * El query en verdad es un insert?
			 */
			if(strtoupper(substr($query, 0, 6))=='INSERT'){
				$array_rep = array();

				//Obtengo las tablas a actualizar
				$tablas_string = substr($query, (stripos($query,'INTO ')+5) );
				$tablas_string = trim( substr($tablas_string, 0, stripos($tablas_string,'(')) );
				//die($tablas_string);

				//Obtengo la cadena de campos
				$campos = substr($query, (stripos($query,'(')+1));
				$campos = trim( substr($campos, 0, stripos($campos,')')) );
				$arr_campos = explode( ",", $campos);
				//die($campos);

				//Obtengo la cadena de valores
				$var_string= trim( substr($query, stripos($query,')')+1) );
				//die($var_string);

				if( strtoupper(substr($var_string, 0, 6)) != 'SELECT' ){
					//Insert simple
					//$val_string = trim( substr($query, $pos_values+8) );
					//$valores = trim( substr($val_string, (stripos($val_string,'(')+1), strripos($val_string,')')) );

					$valores = substr( $var_string, (stripos($var_string,'(')+1) );
					$valores = trim( substr($valores, 0, strripos($valores,')')) );
					//die($valores);
					$arr_valores = explode(",",$valores);
					if( count($arr_campos) != count($arr_valores) ){
						/**
						 * TODO hacer el arreglo de la cadena cuando la cantidad de valores y columnas no coincide
						 */
					}
					foreach($arr_campos as $key => $value){
						$var_data_new .= trim($value)." = ".$arr_valores[$key]."<br />";
					}

				} else {
					/**
					 * Es un INSERT ... SELECT Syntax
					 * http://dev.mysql.com/doc/refman/5.0/en/insert-select.html
					 */
					$res=mysql_query($var_string) or die("Error: ".mysql_error()."<br /><br />Function: ".__METHOD__."<br /><br />Query: ".$var_string);
					$cant_data=mysql_num_rows($res);
					if($cant_data<>0){
						while($row=mysql_fetch_row($res) ){
							$var_data_new .= "Register No.".$i."<br />";
							foreach($row as $key => $value){
								if(is_numeric($key)){ //Hay que usar los indices numericos
									$var_data_new .= trim($arr_campos[$key])." = ".$value."<br />";
								}
							}
							$i++;
						}
					}
				}

				$this->data_new = $var_data_new;
				$this->table_name = trim($tablas_string);

				return 1;
			}
		}

		if($tipo=='UPDATE'){
			/**
			 * http://dev.mysql.com/doc/refman/5.0/en/update.html
			 * El query en verdad es un update?
			 */
			if(strtoupper(substr($query, 0, 6))=='UPDATE'){
				$array_where = array();
				$arr_valores = array();

				//Obtengo las tablas a actualizar
				$tablas_string = substr($query, 0, stripos($query,' SET '));
				$tablas_string = trim( substr( $tablas_string, 7) );
				//die($tablas_string);

				//Obtengo la cadena de campos y valores
				$campos = stristr($query, ' SET ');
				$campos = trim( substr($campos, 4, (strripos($campos, "WHERE ")-4)) );
				//die($campos);

				//Obtengo la cadena del where
				$where_string = trim( substr($query, (strripos($query,' WHERE ')+7)) );
				//die($where_string);
				$this->where_soap=$where_string;
				//Pico la cadena de campos para obtener los nombres reales
				$es_primero = true;
				$split_campos=explode(",",$campos);
				//print_r($split_campos);
				foreach($split_campos as $indice => $valor){
					//Si existe en la cadena un "=" quizas sea un campo
					if(strpos($valor, "=")!==false){
						$campo=trim( substr($valor, 0,  strpos($valor, "=") ) );
						//Si no se encuentran espacios en el nombre del campo entonces es valido
						if(strpos($campo, " ")===false){
							$campos_string .= $separador.$campo;
							if($es_primero){
								$separador=", ";
								$es_primero = false;
							}
						}
					}
				}
				//Obtenemos los valores viejos antes del cambio
				$this->get_old_data($campos_string,$tablas_string,$where_string);

				$this->data_new = $campos;
				$this->table_name = trim($tablas_string);

				return 1;
			}
		}

		if($tipo=='DELETE'){
			/**
			 * http://dev.mysql.com/doc/refman/5.0/en/delete.html
			 * El query en verdad es un delete?
			 */
			if(strtoupper(substr($query, 0, 6))=='DELETE'){
				$array_where = array();
				$pos_where = stripos($query,' WHERE ');
				$pos_from = stripos($query,' FROM ');

				/**
				 * Se extrae la parte de la sentencia para realizar una busqueda del registro
				 * antes de borrarlo y poder buscar los valores para mostrar en el texto del log
				 */

				//Obtengo las tablas a actualizar
				$tablas_string = stristr($query, ' FROM ');
				$tablas_string = substr($tablas_string, 6) ;
				$tablas_string = trim( substr($tablas_string, 0, stripos($tablas_string,' WHERE ')) ) ;
				//die($tablas_string);

				//Obtengo la cadena del where
				$where_string = trim( substr($query, (stripos($query,' WHERE ')+7)) );
				$this->where_soap=$where_string;
				//die($where_string);

				if(stripos($tablas_string, ",")!==false){
					//Delete en multiples tablas
					$var_campo=''; $var_tabla='';
					$tablas_array = explode(",", $tablas_string);
					foreach($tablas_array as $tabla){
						$var_campo .= $tabla.'.*, ';
						$var_tabla .=  $tabla.', ';
					}
					$var_campo = substr($var_campo,0,-2);
					$var_tabla = substr($var_tabla,0,-2);
				} else {
					//Delete simple
					$var_campo = '*';
					$var_tabla = $tablas_string;
				}
				//die($var_campo);
				//Obtenemos los valores viejos antes del cambio
				$this->get_old_data($var_campo, $var_tabla, $where_string);
				$this->data_new = $campos;
				$this->table_name = trim($var_tabla);

				return 1;
			}

		}
		return 0;
	}

	/**
	 * Obtiene los datos viejos antes de un cambio
	 * @param string $campos campos a usar
	 * @param string $tablas tablas a usar
	 * @param string $sentencia sentencia del where
	 */
	function get_old_data($campos, $tablas, $sentencia){
		$i=0; $var_data_old='';
		$query="SELECT ".$campos." FROM ".$tablas." WHERE ".$sentencia;
		//die($query);
		$res=mysql_query($query) or die("Error: ".mysql_error()."<br /><br />Function: ".__METHOD__."<br /><br />Query: ".$query);
		$cant_data=mysql_num_rows($res);
		if($cant_data<>0){
			while($row=mysql_fetch_array($res) ){
				$var_data_old .= "Register No.".$i."<br />";
				foreach($row as $key => $value){
					if(!is_numeric($key)){ //Hay que obiar los indices numericos
						$var_data_old .= $key." = ".$value."<br />";
					}
				}
				$i++;
			}
			$var_data_old = substr($var_data_old, 0, -6);
		} else {
			$var_data_old = 'No data';
		}
		$this->data_old = $var_data_old;
	}

	/**
         * Obtiene los datos de un usuario para ser guardados en el log
         * @param $session
         */
	private function get_userinfo_log($session){
            $this->log_usuario_info = $session->r('s_first_name') || $session->r('s_last_name') ? $session->r('s_first_name')." ".$session->r('s_last_name') : 'N/A';
            $this->log_usuario_id = $session->r('s_id') ? (int)$session->r('s_id') : 0;
                    $this->log_usuario_email = $session->r('s_email') ? $session->r('s_email') : 'N/A';
                    $this->log_usuario_compania = $session->r('s_company_name') ? $session->r('s_company_name') : 'N/A';
            $this->log_usuario_profile = $session->r('s_profile_id') ? $session->r('s_profile_id') : 0;
	}

	/**
	 * Inserta el log en la bd
	 * @param $query_log
	 * @param $log_codigo_id
	 * @param $comentario
	 * @return array
	 */
	function set_log_consulta($query_log, $log_codigo_id, $session='', $comentario='', $function=''){
		//Para detectar el navegador
		require('browser_detection/your_computer_info.php');
		$this->log_navegador=$html;
		//Para detectar el navegador

		/*if ('191.109.117.190' == $_SERVER['REMOTE_ADDR']) {
			$CORE_session = new SessionManager('AM');
			var_dump($CORE_session->read('s_id'));

			$firstname  = $CORE_session->read('s_firstname');
            $lastname   = $CORE_session->read('s_lastname');

            var_dump($session);

            var_dump($lastname);

			die();
        }*/


		if($session){
  $this->get_userinfo_log($session);  
}
		$query_log = mysql_real_escape_string( str_ireplace($this->saltodelinea, " ", $query_log) );
		$comentario = mysql_real_escape_string($comentario);
		$query = "INSERT INTO log_consultas (en_fecha, log_codigo_id, usuario_id, usuario_info, usuario_email, usuario_compania, usuario_profile, session_id,
			 direccion_ip, navegador, en_tablas, data_vieja, data_nueva, sentencia_sql, comentario, _function)
			 VALUES (NOW(), '$log_codigo_id', '".$this->log_usuario_id."', '".$this->log_usuario_info."',
			 '".$this->log_usuario_email."', '".mysql_real_escape_string($this->log_usuario_compania)."', '".$this->log_usuario_profile."', '".session_id()."',
			 '".$_SERVER['REMOTE_ADDR']."', '".mysql_real_escape_string($this->log_navegador)."', '".$this->table_name."', '".mysql_real_escape_string($this->data_old)."',
			 '".mysql_real_escape_string($this->data_new)."', '".$query_log."', '$comentario', '$function' )";
		$res = mysql_query($query) or die("Error: ".mysql_error()."<br /><br />Function: ".__METHOD__."<br /><br />Query: ".$query);
	}
/**
	 * Inserta el log en la bd
	 * @param $query_log
	 * @param $log_codigo_id
	 * @param $comentario
	 * @return array
	 */
	function set_log_consulta2($query_log, $log_codigo_id,  $comentario='', $function=''){
$CORE_session = new SessionManager('AM');		
//Para detectar el navegador
		require('browser_detection/your_computer_info.php');
		$this->log_navegador=$html;
		//Para detectar el navegador
$usuario_info=$CORE_session->read('s_first_name') ? $CORE_session->read('s_first_name') : 'N/A';
$usuario_compania=$CORE_session->read('s_company_name') ? $CORE_session->read('s_company_name') : 'N/A';
$profile_id=$CORE_session->read('s_profile_id') ? $CORE_session->read('s_profile_id') : 0;
		$query_log = mysql_real_escape_string( str_ireplace($this->saltodelinea, " ", $query_log) );
		$comentario = mysql_real_escape_string($comentario);
		$query = "INSERT INTO log_consultas (en_fecha, log_codigo_id, usuario_id, usuario_info, usuario_email, usuario_compania, usuario_profile, session_id,
			 direccion_ip, navegador, en_tablas, data_vieja, data_nueva, sentencia_sql, comentario, _function)
			 VALUES (NOW(), '$log_codigo_id', '".$CORE_session->read('s_id')."', '".$usuario_info."',
			 '".$CORE_session->read('s_email')."', '".mysql_real_escape_string($usuario_compania)."', '".$profile_id."', '".session_id()."',
			 '".$_SERVER['REMOTE_ADDR']."', '".mysql_real_escape_string($this->log_navegador)."', '".$this->table_name."', '".mysql_real_escape_string($this->data_old)."',
			 '".mysql_real_escape_string($this->data_new)."', '".$query_log."', '$comentario', '$function' )";
                //die($query."sss");
		$res = mysql_query($query) or die("Error: ".mysql_error()."<br /><br />Function: ".__METHOD__."<br /><br />Query: ".$query);
	}
	/**
	 * Lista los logs del sistema
	 * @param $start_date
	 * @param $end_date
	 * @param $compania
	 * @param $usuario
	 * @param $event
	 * @param $order
	 * @param $min
	 * @param $max
	 * @return array
	 */
	function list_consultas($event, $order = '', $min = '', $max = '', $id_user, $search){
            $oDBTools = new cls_dbtools;

            $query="SELECT cons.*,cod.*, firstname FROM log_consultas AS cons, log_codigos AS cod, users
			WHERE cons.log_codigo_id=cod.log_codigo_id ";
            if(!empty($id_user)) $query.=" AND cons.usuario_id='$id_user'";
            if(!empty($event)) $query.=" AND cons.log_codigo_id='$event'";
            if(!empty($search)) $query.=" AND cons.en_fecha like '%$search%' OR users.firstname like '%$search%'";

            if (! empty ( $order )) {
                    $query .= " ORDER BY $order ";
            } else {
                    $query .= " ORDER BY en_fecha DESC ";
            }
            if (! empty ( $max )) {
                    $query .= " LIMIT $min,$max ";
            }  
            //die($query);
             return $this->lista = $oDBTools->_SQL_tool('SELECT',__METHOD__,$query);
            }
            /**
	 * Lista los logs del sistema
	 * @param $start_date
	 * @param $end_date
	 * @param $compania
	 * @param $usuario
	 * @param $event
	 * @param $order
	 * @param $min
	 * @param $max
	 * @return array
	 */
	function list_consultas_2($start_date, $end_date, $compania, $usuario, $event, $order = '', $min = '', $max = ''){
            $oDBTools = new cls_dbtools;
            $query="SELECT cons.*,cod.* FROM log_consultas AS cons, log_codigos AS cod
			WHERE cons.log_codigo_id=cod.log_codigo_id ";
            if($start_date){
                    $query.=" AND cons.en_fecha >= '$start_date 00:00:01' ";
            }
            if($end_date){
                    $query.=" AND cons.en_fecha <= '$end_date 23:59:59' ";
            }
            if($compania){ $query.=" AND cons.usuario_compania='$compania' "; }
            if($usuario){ $query.=" AND cons.usuario_id='$usuario' "; }
            if($event){ $query.=" AND cod.log_codigo_id='$event' "; }
			
			
            if (! empty ( $order )) {
                    $query .= " ORDER BY $order ";
            } else {
                    $query .= " ORDER BY en_fecha DESC ";
            }
            if (! empty ( $max )) {
                    $query .= " LIMIT $min,$max ";
            }
           //echo $query;
            $this->lista = $oDBTools->_SQL_tool('SELECT',__METHOD__,$query);
	}
         
              function toatal_reg2($start_date, $end_date, $compania, $usuario, $event){
    //Para retornar el total de registros si no existiera el limite
            $oDBTools = new cls_dbtools;
            $query="SELECT cons.*,cod.* FROM log_consultas AS cons, log_codigos AS cod WHERE cons.log_codigo_id=cod.log_codigo_id";   
            
            if($start_date){
                    $query.=" AND cons.en_fecha >= '$start_date 00:00:01' ";
            }
            if($end_date){
                    $query.=" AND cons.en_fecha <= '$end_date 23:59:59' ";
            }
            if($compania){ $query.=" AND cons.usuario_compania='$compania' "; }
            if($usuario){ $query.=" AND cons.usuario_id='$usuario' "; }
            if($event){ $query.=" AND cod.log_codigo_id='$event' "; }
            
            //die($query);
             return $total_reg= $oDBTools->_SQL_tool('SELECT',__METHOD__,$query);
    
          }
	/**
	 * Obtiene un log por su id
	 * @param $log_id
	 * @return array
	 */
	function get_log_id($log_id){
            $oDBTools = new cls_dbtools;
            $query="SELECT cons.*,cod.* FROM log_consultas AS cons, log_codigos AS cod
                    WHERE cons.log_codigo_id=cod.log_codigo_id AND cons.log_id='$log_id' ";
            $this->data = $oDBTools->_SQL_tool('SELECT_SINGLE',__METHOD__,$query);
	}
	/**
	 * Cambia el formato del contenido de una sentencia o texto
	 * @param $arrNew
	 */
	protected function setLogdataNew($arrNew){
            $this->data_new=$this->getFormatedDataLog($arrNew);
	}
	/**
	 * Cambia el formato del contenido de una sentencia o texto
	 * @param $arrNew
	 * @return string
	 */
	protected function getFormatedDataLog($arrNew){
		$strReturn="";
		if(is_array($arrNew)){
			$i=0;
			foreach($arrNew as $index=>$array){
				if($i>=1){$strReturn.="<br />";}
				foreach($array as $name=>$val){
					$strReturn.=$name."='".$val."'<br />";
				}
				$i++;
			}
		}
		return $strReturn;
	}
	/**
	 * Asigna el nombre de una tabla a una variable interna
	 * @param $tableName
	 */
	protected function setLogTable($tableName){
            $this->table_name=$tableName;
	}
	/**
	 * Busca un codigo del log en especifico
	 * @param $idCod
	 * @return array
	 */
	function getCodigos($idCod=null){
		$oDBTools = new cls_dbtools;
		$query = "SELECT * FROM log_codigos";
		if(!is_null($idCod)) $query .= " WHERE log_codigo_id='$idCod' ";
		$query .= " order by log_codigo_id";
		return $oDBTools->_SQL_tool('SELECT', __METHOD__, $query);
	}
	function get_tabla_name(){
		return $this->table_name;
	}

	function get_where_query(){
		return $this->where_soap;
	}
}