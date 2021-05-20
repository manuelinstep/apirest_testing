<?php
/**
 * D2PW Solutions
 *
 * Description: Clase Generica para el manejo de las variables de session
 *
 * @author Miguel Aguero
 * @version 1.0  2010/10/21
 */
class SessionManager {
    /******************************************************************
    /*******  VARIABLES
    /******************************************************************/
    var $prefix=null;
    /******************************************************************
    /*******  CONSTRUCTOR
    /******************************************************************/
    public function __construct($prefix) {
		$this->prefix =$prefix.'_'.$_SERVER['SERVER_NAME']."_" ;
		//$this->prefix =$prefix.'_' ;
    }
    /******************************************************************
    /*******  FUNCIONES
    /******************************************************************/
    /*
     * Crea una variable de session incluyendo la fecha y hora de creacion
     */
    function write($name, $value){
        if(!$this->exist($name))
            $created = date('y-m-d h:i:s');
        else
            $created = $_SESSION[$name]['created'];
        
		$_SESSION[$this->prefix.$name]['value'] = $value;
        $_SESSION[$this->prefix.$name]['created'] = $created;
        $_SESSION[$this->prefix.$name]['accessed'] = date('y-m-d h:i:s');
    }
    /**
     * Lee el contenido de la variable de session
     * @param  $name
     * @param  $delete
     * @return string
     */
    function read($name, $delete = false){
        $_SESSION[$this->prefix.$name]['accessed'] = date('y-m-d h:i:s');
        $value = $_SESSION[$this->prefix.$name]['value']; 
        if($delete) $this->destroy($name);
        return $value;
    }
    /**
     * Escribe una variable en la seccion
     * @param $name
     * @param $value
     */
    function w($name, $value){
        $this->write($name, $value);
    }
    /**
     * Lee una variable de seccion especifica
     * @param $name
     * @param $delete
     */
    function r($name, $delete = false){
        return $this->read($name, $delete);
    }
    /**
     * Destruye una variable de seccion
     * @param $name
     */
    function destroy($name){
        unset($_SESSION[$this->prefix.$name]);
    }
    /**
     * Destruye toda la seccion
     */
    function destroyAll(){
        session_unset();
        session_destroy();
    }
    /**
     * Verifica si existe una varible de seccion
     * @param $name
     * @return boolean
     */
    function exist($name){
        if(isset($_SESSION[$this->prefix.$name]))
            return true;
        else
            return false;
    }
}