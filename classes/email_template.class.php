<?php
/**
 * D2PW Solutions
 *
 * Description: Carga de templates de correos
 *
 * @author Javier Mijares
 * @version 1.0  2010/10/21
 */
class EmailTemplate extends Model{
    /******************************************************************
    /*******  VARIABLES
    /******************************************************************/
    var $table ='email_templates';
	
    /******************************************************************
    /*******  FUNCIONES
    /******************************************************************/
    /**
     * Carga el esqueleto o template de un correo
     * @param $skeletonFile
     * @param $domainRoot
     * @return string
     */
    function loadTemplateSkeleton($skeletonFile, $domainRoot){
        $arrFile = file($skeletonFile);
        $content = '';
        foreach($arrFile as $line ){
            $content.=$line;
        }
        $content = str_replace('%domain_root%',$domainRoot,$content);
        return $content;
    }
    /**
     * Lista de templates de correos
     * @param $lenguajeId
     * @param $order
     * @param $min
     * @param $max
     * @return array
     */
    function list_correos($lenguajeId = '', $order = '', $min = '', $max = ''){
            $query="SELECT * FROM email_templates WHERE 1 ";
            if (! empty ( $lenguajeId )) {
                    $query .= " AND  language_id=$lenguajeId ";
            }
            if (! empty ( $order )) {
                    $query .= " ORDER BY $order ";
            } else {
                    $query .= " ORDER BY id ";
            }
            if (! empty ( $max )) {
                    $query .= " LIMIT $min,$max ";
            }
            return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
    }
}