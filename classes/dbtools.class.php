<?php
error_reporting(0);
ini_set(display_errors, 0);
require_once("apiclientils.class.php");
class cls_dbtools extends cls_logs
{
    public $return_id='';
    public $time='';
    public $genera_log=true;
    public static $arr_codigosLogNoRewrite=array('INSERT'=>201,'UPDATE'=>202,'DELETE'=>203);
    private $codigos=array('INSERT'=>201,'UPDATE'=>202,'DELETE'=>203);
    public $var_trans = '0';
    public $INSERT = 'INSERT';
    public $UPDATE = 'UPDATE';
    public $DELETE = 'DELETE';
    public $SELECT = 'SELECT';
    public $SELECT_SINGLE = 'SELECT_SINGLE';
    public static $DBParameters = array();
    private $DBconnection = array();
    public static $DBsession = '';
    public $auto_soap = true;
    public static $ApiIls = null;
    public function __construct()
    {
        $this->setCodigosLog(array(), true);
    }
    public static function assignDBParameters($arrDB)
    {
        self::$DBParameters = $arrDB;
    }
    public static function assignSession($varSession)
    {
        self::$DBsession = $varSession;
    }
    public function _connectDB($app){
	$arrDBP = self::$DBParameters[$app];
        $DBconnection='';
        if(!is_object($DBconnection)){
            $this->DBconnection[$app] = mysql_connect($arrDBP['host'], $arrDBP['user'], $arrDBP['pass'], true);
        }
        if (!$this->DBconnection[$app]) {
            die("Error al conectarse a mysql:<pre>".json_encode([
                'host'=>$arrDBP['host'],
                'user'=>$arrDBP['user'],
                'db'=>$arrDBP['db'],
                'error'=>mysql_error()
            ],JSON_PRETTY_PRINT)."</pre>");
        }
	    mysql_select_db($arrDBP['db'], $this->DBconnection[$app]);
        mysql_query("set names 'utf8'"); 
        mysql_query("SET global sql_mode=''"); 
        mysql_query("SET @@global.sql_mode = ''"); 
        mysql_query("SET @@session.sql_mode = ''"); 
        if(!is_object($this->DBconnection[$app])){
            return false;
        } else {
            return true;
        }
    }
    public function check_connect($app)
    {
        $arr_config = '';
        @$DBconnection=$this->DBconnection[$app];
        if (!isset($DBconnection) && !is_object($DBconnection)) {
            $this->_connectDB($app);
        }
    }
    public function _begin_tool($app='_DEFAULT')
    {
        mysql_query("BEGIN WORK") or die(mysql_error($this->DBconnection[$app]));
        $this->var_trans = '1';
    }
    public function _commit_tool($app='_DEFAULT')
    {
        mysql_query("COMMIT") or die(mysql_error($this->DBconnection[$app]));
        $this->var_trans = '0';
    }
    public function _rollback_tool($app='_DEFAULT')
    {
        mysql_query("ROLLBACK") or die(mysql_error($this->DBconnection[$app]));
    }
    public function build_query($table, $data, $action = 'insert', $parameters = '')
    {
        reset($data);
        if ($action == 'insert') {
            $query = 'INSERT INTO ' . $table . ' (';
            while (list($columns, ) = each($data)) {
                $query .= $columns . ', ';
            }
            $query = substr($query, 0, -2) . ') VALUES (';
            reset($data);
            while (list(, $value) = each($data)) {
                switch ((string)$value) {
                            case ('now()' || 'NOW()'):
                                    $query .= 'now(), ';
                            break;
                            case ('null' || 'NULL'):
                                    $query .= 'null, ';
                            break;
                            default:
                                    $query .= '\''.$value.'\', ';
                            break;
                            }
            }
            $query = substr($query, 0, -2) . ')';
        } elseif ($action == 'update') {
            $query = 'update ' . $table . ' set ';
            while (list($columns, $value) = each($data)) {
                switch ((string)$value) {
                            case ('now()' || 'NOW()'):
                                    $query .= $columns . ' = now(), ';
                            break;
                            case ('null' || 'NULL'):
                                    $query .= $columns .= ' = null, ';
                            break;
                            default:
                                    $query .= $columns . ' = \''.$value.'\', ';
                            break;
                            }
            }
            $query = substr($query, 0, -2) . ' WHERE ' . $parameters;
        }
        return $query;
    }
    public function _SQL_tool($tipo, $funct_call, $query, $comentario='', $calcrows=1, $viewQ='', $app='_DEFAULT', $codigo_voucher='')
    {
        $tipo=strtoupper($tipo);
        $this->return_id = '';
        $query = trim($query);
        $this->check_connect($app);
        switch ($tipo) {
                    case 'SELECT':
                            if (stripos($query, 'GROUP_CONCAT') !== false) {
                                $this->alterar_group_concat_max_len($app);
                            }
                            if ($calcrows) {
                                $query = substr($query, 0, 6)." SQL_CALC_FOUND_ROWS ".substr($query, 6);
                            }
                            $inicio = microtime();
                            $result = mysql_query("set names 'utf8'");
                            $result = mysql_query($query, $this->DBconnection[$app]);
                            $fin = microtime();
                            $this->time = $fin - $inicio;
                            $res_array = array();
                            if ($result) {
                                while ($rows=mysql_fetch_assoc($result)) {
                                    $res_array[] = $rows;
                                }
                                mysql_free_result($result);
                                $result = mysql_query('SELECT FOUND_ROWS() as total', $this->DBconnection[$app]);
                                if ($row=mysql_fetch_assoc($result)) {
                                    $this->total_verdadero = $row['total'];
                                } else {
                                    $this->total_verdadero = 0;
                                }
                                return $res_array;
                            } else {
                                $this->print_log_error(debug_backtrace(), $query, $app);
                            }
                            break;
                    case 'SELECT_SINGLE':
                            if (stripos($query, 'GROUP_CONCAT') !== false) {
                                $this->alterar_group_concat_max_len($app);
                            }
                            $inicio = microtime();
                            $result = mysql_query("set names 'utf8'");
                            $result = mysql_query($query, $this->DBconnection[$app]);
                            
                            $fin = microtime();
                            $this->time = $fin - $inicio;
                            $res_array=array();
                            if ($result) {
                                if ($rows=mysql_fetch_assoc($result)) {
                                    foreach ($rows as $columna => $valor) {
                                        $res_array[$columna] = $valor;
                                    }
                                }
                                return $res_array;
                            } else {
                                $this->print_log_error(debug_backtrace(), $query, $app);
                            }
                            break;
                    case 'INSERT':
                    case 'UPDATE':
                    case 'DELETE':
                            $query_valido = $this->process_query($query, $tipo);
                            if ($query_valido) {
                                $inicio = microtime();
                                $result = mysql_query("set names 'utf8'");
                                $result = mysql_query($query, $this->DBconnection[$app]);
                                $fin = microtime();
                                $this->time = $fin - $inicio;
                                if ($result) {
                                    $return_value = true;
                                    if ($tipo=='INSERT') {
                                        $this->return_id = mysql_insert_id($this->DBconnection[$app]);
                                        $return_value = $this->return_id;
                                    }
                                    $codigo=$this->codigos[$tipo];
                                    if ($this->genera_log) {
                                        $id_log = $this->set_log_consulta($query, $codigo, self::$DBsession, $comentario, $funct_call);
                                        if (!empty($codigo_voucher)) {
                                            cls_logs::add_transaccion_voucher($id_log, $codigo_voucher);
                                        }
                                    }
                                    $this->setCodigosLog(array(), true);
                                    if ($this->auto_soap==true) {
                                        $tabla_soap = $this->get_tabla_name();
                                        
                                        if ($id=apiclientils::getTablesAndId($tabla_soap)) {
                                            $where_soap = $this->get_where_query();
                                            self::$ApiIls = self::$ApiIls?:new apiclientils();
                                            if ($tipo=='INSERT') {
                                                $where_soap = "$id = '{$return_value}'";
                                            }
                                           /* if('190.78.76.149' == $_SERVER['REMOTE_ADDR']){
                                                die(var_dump("Probando ",$tabla_soap, $tipo, $where_soap));
                                            }*/
                                            self::$ApiIls->sendApi($tabla_soap, $tipo, $where_soap);
                                        }
                                    }
                                    return $return_value;
                                } else {
                                    $this->print_log_error(debug_backtrace(), $query, $app);
                                }
                            } else {
                                die("Sentencia no corresponde con el primer parametro de la funcion _SQL_tool. Debe ser corregido para continuar");
                            }
                            break;
            }
        $this->setCodigosLog(array(), true);
    }
    private function alterar_group_concat_max_len($app='_DEFAULT')
    {
        $prequery="SET @@group_concat_max_len = 9999999";
        mysql_query($prequery, $this->DBconnection[$app]);
    }
    public function setCodigosLog($arrValues=array(), $autoSet=false)
    {
        if ($autoSet) {
            $this->codigos=cls_dbtools::$arr_codigosLogNoRewrite;
        } else {
            $this->codigos=array_merge($this->codigos, $arrValues);
        }
    }
    public function print_log_error($back_trace, $query, $app)
    {
        if ($this->error_ocurred) {
            return;
        }

        $back_trace =array_reverse($back_trace);

        $var_approot=str_replace("/", "\\", COREROOT);
        $arr_vars=array('file','line','class','function');
        for ($i=0,$max=count($back_trace); $i<$max; $i++) {
            foreach ($arr_vars as $key=>$value) {
                if ($value=='file') {
                    $arr_tree[$i][$value]=str_replace(array($var_approot,"\\"), array(DOMAIN_ROOT,"/"), $back_trace[$i][$value])."<br>";
                    continue;
                }
                $arr_tree[$i][$value]=$back_trace[$i][$value];
            }
        }
        $arr_tree[0]['mysql_errno']=mysql_errno($this->DBconnection[$app]);
        $arr_tree[0]['mysql_error']=str_replace('\'', '"', mysql_error($this->DBconnection[$app]));
        $arr_tree[0]['query']=nl2br($query);
        $arr_tree[0]['user_name']=self::$DBsession->read('s_first_name')." ".self::$DBsession->read('s_last_name');
        $arr_tree[0]['date'] = date("Y-m-d h:i:s");
        $id_error = $this->register_SQL_error(serialize($arr_tree), $app);
        $arr_tree[0]['id_error'] = $id_error;

        if (defined("SEND_SQL_ERRORS_EMAIL") && SEND_SQL_ERRORS_EMAIL==1) {
            $subject="Error detectado: ".SYSTEM_NAME;
            $vars=$this->contruct_body_error($arr_tree);
            $arr_email=$this->get_list_notif_error($app);
            /* Instaciación del nuevo objeto Email */
            $objemail = new Email(array('smtpServer'=>EMAIL_SERVER_HOST,'smtpUser'=>EMAIL_SERVER_USER,'smtpPassword'=>EMAIL_SERVER_PASS,'appDomainRoot'=>DOMAIN_ROOT,'skeletonFile'=>COREROOT.'lib/common/email_skeleton_bug_track.php','emailEngine'=>EMAIL_ENGINE,'transGroupID'=>EMAIL_TRANSACTIONAL_GROUP_ID), array('debug'=>EMAIL_DEBUG_SEND,'emailDebug'=>EMAIL_DEBUG));
            foreach ($vars as $varstr => $varvalue) {
                $objemail->setVariable($varstr, $varvalue);
            }
            $objemail->send($from=array('name'=>EMAIL_FROM_NAME,'email'=>EMAIL_FROM), $to=/* $notify_email */$arr_email, "BUG_TRACK", 'spa');
        }
        $this->error_ocurred=true;
        if (!headers_sent()) { 
            $info = array(
                "status" => "Error",
                "description" => "Ha ocurrido un error interno, hemos comunicado al equipo correspondiente y estamos trabajando en una solucion (Error 500)"
            );
            return $info;
            exit;
        } else {
            include(COREROOT."common/app_mysql_error.php");
        }
    }
    public function register_SQL_error($apperror_text='', $app)
    {
        if ($this->var_trans == '1') {
            $this->_rollback_tool();
        }
        $id=substr(md5(microtime()), 0, 18);
        $query="INSERT app_error (apperror_id, apperror_time, apperror_text, ip, user_id) VALUES
        ('$id',NOW(),'".mysql_real_escape_string($apperror_text, $this->DBconnection[$app])."', '".$_SERVER['REMOTE_ADDR']."', '".self::$DBsession->read('s_id')."')";
        mysql_query($query, $this->DBconnection[$app]) or die("Error: ".mysql_error($this->DBconnection[$app])."<br /><br />Function: ".__METHOD__."<br /><br />Query: ".$query);
        return $id;
    }
    public function get_SQL_error($apperror_id)
    {
        $query="SELECT * FROM app_error WHERE apperror_id='$apperror_id' ";
        $this->SQL_error = $this->_SQL_tool('SELECT_SINGLE', __METHOD__, $query);
    }
    public function contruct_body_error($arr_detail)
    {
        $tab=0;
        $html_detail="<table style=\"font-size:12px; color:#666\" width=\"100%\">";
        for ($i=0,$max=count($arr_detail); $i<$max; $i++) {
            $html_detail.="<tr><td style=\"padding-left:".$tab."px\">
                            <table style=\"font-size:12px; color:#666\" width=\"100%\">
                            <tr><td width=\"5%\"><strong>File:</strong></td><td nowrap>"
                             .$arr_detail[$i]['file'].
                             "<strong> in Line"
                             .$arr_detail[$i]['line'].
                             "</strong></td></tr><tr><td><strong>Class:</strong></td><td>"
                             .$arr_detail[$i]['class'].
                             "</td></tr><tr><td><strong>Function:</strong></td><td>".
                             $arr_detail[$i]['function'].
                             "</td></tr></table><hr /></td></tr>";
            $tab+=5;
        }
        $html_detail.=	"<tr><td><br /><strong>Query:</strong><br />"
                    .$arr_detail[0]["query"]."<br /><br /><strong>Mysql Error No: </strong>"
                    .$arr_detail[0]["mysql_errno"]."<br /><strong>Mysql Error Description: </strong>"
                    .$arr_detail[0]["mysql_error"]."</td></tr></table>";
        $html_detail = ob_get_clean();
        $detail_mess="<strong>Un error ha ocurrido en ".SYSTEM_NAME."</strong>, a continuación se especifica el detalle<br />";
        $footer="Mensaje Generado Automaticamente por ".SYSTEM_NAME;
        $vars=array(
            "##error_detail##"=>$html_detail,
            "##error_id##"=>$arr_detail[0]["id_error"],
            "##date##"=>$arr_detail[0]["date"],
            "##user_name##"=>$arr_detail[0]["user_name"],
            "##message##"=>$detail_mess,
            "##footer##"=>$footer
        );
        return $vars;
    }
    public function get_list_notif_error($app)
    {
        $default=array("errsupport@travelsupport.info");
        $query="SELECT email from app_error_users ";
        $rs=mysql_query($query, $this->DBconnection[$app]) or die(mysql_error($this->DBconnection[$app]));
        $arr_users_error=array();
        if ($rs) {
            while ($row=mysql_fetch_array($rs)) {
                $arr_users_error[]=$row['email'];
            }
        }
        return ($arr_users_error)? $arr_users_error : $default;
    }
    public function get_field_id($tabla, $app='_DEFAULT')
    {
        $this->check_connect($app);
        $campo_id = mysql_query("SHOW COLUMNS FROM $tabla WHERE Extra = 'auto_increment'", $this->DBconnection[$app]);
        $campo_id = mysql_fetch_assoc($campo_id);
        return $campo_id['Field'];
    }
    public function get_tablas_soap()
    {
        return $this->tablas_soap;
    }
}