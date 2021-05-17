<?php 
    /**
     * Primera versión del corelib para la api rest
     */

    session_start();
    set_time_limit(0);
    //$_SESSION['lastAct'] = time();
        
    if (preg_match("/core.lib.php/", $_SERVER ["PHP_SELF"]))
      die("Access denied!");
    require_once("config_var.php");
    require_once(COREROOT . "classes/logs.class.php");
    require_once(COREROOT . "classes/dbtools.class.php");
    require_once(COREROOT . "classes/debug.class.php");
    //require_once(COREROOT . "rewrite_globals.php"); //REWRITE GLOBALS seria necesario si estuviesemos manejando un front
    require_once(COREROOT . "classes/general.class.php");

    cls_dbtools::assignDBParameters($arrDB);
?>