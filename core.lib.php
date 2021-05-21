<?php 
    /**
     * Segunda versión del corelib para la apirest
     * que mALDICION
     * no saben las ganas que tengo de terminar con esto
     * y volver a tener fin de semana
     */

     //PREVENT ATTACK FROM OTHER SITES
     
    session_start();
    set_time_limit(0);
    //$_SESSION['lastAct'] = time();

        
    //if (preg_match("/core.lib.php/", $_SERVER ["PHP_SELF"]))
      //die("Access denied!");
    $ini_request = microtime(true);
    require_once("config.var.php");
    require_once("classes/logs.class.php");
    require_once("classes/dbtools.class.php");
    require_once("classes/session_manager.class.php");
    require_once("classes/quote.class.php");

    cls_dbtools::assignDBParameters($arrDB);

    $CORE_session = new SessionManager('WS');

    cls_dbtools::assignSession($CORE_session);


?>