<?php 
    require_once("response.class.php");
    //Prueba sexual llanera de la clase model
    class quote extends cls_dbtools //Se omite temporalmente la clase Model ya que no representa una necesidad inmediata
    {

        public function selectDynamic($filters, $table = string, $where = '1', $fields, $querys = '', $die = false, $limit = 6)
        {
            /**
             * Este código viene del anterior webservices
             * Funciona, asi que no debería haber problema implementandolo
             * Lo único que aún no entiendo del todo es como funciona el apartado filters, pero de momento
             * no se usará, buscare un ejemplo en el anterior webservices para cersiorarme de que es lo que sucede
             */
            if (empty($querys)) {
                $fields = !empty($fields) ? implode(',', $fields) : "*";
                $query = "SELECT $fields FROM $table WHERE $where ";
                foreach ($filters as $campo => $value) {
                    if (!empty($campo) && !empty($value)) {
                        $valor   = addslashes($value);
                        $valor   = (is_array($value)) ? implode(',', $value) : "'$valor'";
                        $query  .= " AND $campo IN ($valor) ";
                    }
                }
                $query .= " LIMIT $limit ";
            } else {

                $query = $querys;
            }
            if ($die) {
                die($query);
            }
            //return parent::obtenerDatos($query);
            return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
        }
    }
    
?>