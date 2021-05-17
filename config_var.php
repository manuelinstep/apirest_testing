<?php 
    /**
     * Se crea el archivo config_var para traer la conexión 
     * cuando ya todos los archivos esten listos, se procedera a probar dicha clase
     * si funciona, sin errores, se procederá a implementarla en el webservices
     * todo esto mientras se registre tanto app_error, log_consultas y trans_all_webservices MANO
     */
    if(stripos(__FILE__, 'config_var.php')===false){
        die ( "Access denied!" );
    }

    $rootdirectories = array('app', 'core', 'reports', 'wsdl');
$document_root = '';
$cwd = getcwd();

$arrDirectoryHierarchy = explode(DIRECTORY_SEPARATOR, $cwd);
for($i=0, $limit=count($arrDirectoryHierarchy); $i<$limit; $i++){
	if(in_array($arrDirectoryHierarchy[$i], $rootdirectories)){
		break;
	}else{
		$document_root .= $arrDirectoryHierarchy[$i].DIRECTORY_SEPARATOR;
	}
}

define ( 'APPROOT', $document_root.DIRECTORY_SEPARATOR );
define ( 'COREROOT', $document_root . 'lib'.DIRECTORY_SEPARATOR );
// Ej: se usa para los includes
define ( 'DOMAIN_ROOT', 'https://' . $_SERVER ['SERVER_NAME'] . '/wbs_2/app/' );
define ( 'ASSETS', 'https://' . $_SERVER ['SERVER_NAME'] . '/wbs_2/assets/' );
// Ej: se usa para las im�genes, link, etc.
define ( 'LOCAL_PEAR_DIR', COREROOT . 'PEAR/' );
define ( 'LOCAL_READERS_WRITERS_EXCEL_DIR', COREROOT . '/class/PHPExcel/' );
// Link webservice para WSDL 'RCI'
define('LINK_WEBSERVICE','https://' .$_SERVER ['SERVER_NAME'].'/wbs_2/wsdl/service.php?wsdl');//Redefinir o buscarle funcionalidad

//Link Correo
define('LINK_EMAIL','https://' .$_SERVER['SERVER_NAME'].'/app/reports/email_compra_web.php');

//Link Reporte de ventas
define('LINK_REPORTE_VENTAS','https://' .$_SERVER['SERVER_NAME'].'/app/reports/reporte_orderventas.php?codigo=');

//Link Condicionados
define('LINK_CONDICIONADO','https://' .$_SERVER['SERVER_NAME'].'/app/admin/server/php/files/');

//Link Logo Agencia
define('LINK_LOGO_AGENCIA','https://' .$_SERVER['SERVER_NAME'].'/app/admin/pictures/thumbnail/');
define('DOMAIN_APP','https://' .$_SERVER['SERVER_NAME'].'/app/');
//Variables de conexion a la base de datos

    $direccion = dirname(__FILE__);
    $jsondata = file_get_contents($direccion . "/" . "classes" . "/" . "connection" . "/" . "config");
    $arrDB = json_decode($jsondata,true);

define ( 'DEBUG', '0' );
?>