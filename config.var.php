<?php 
    //Evitamos ataques de scripts de otros sitios
    //if(stripos(__FILE__, 'config_var.php')===false){
      //  die ( "Access denied!" );
    //}

    //Directorios
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
    define ( 'LOCAL_READERS_WRITERS_EXCEL_DIR', COREROOT . '/classes/PHPExcel/' );
    // Link webservice para WSDL 'RCI'
    define('LINK_WEBSERVICE','https://' .$_SERVER ['SERVER_NAME'].'/apirest_testing/');

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

    $arrDB = array(
    define ( 'DEBUG', '0' );
?>