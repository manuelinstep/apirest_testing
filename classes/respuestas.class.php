<?php

    class respuestas{

        /**
         * Recordar cambiar formato para las respuesta:
         * 1xx - informaciónal
         * 2xx - exíto, el request se completó
         * 3xx - redireccion, se indica que el cliente debe hacer cosas adicionales para completar el request
         * 4xx - error del cliente, se introdujeron datos mal, o alguna de las verificaciones no ha sido completada
         * 5xx - nuestro error, después de realizarse las pruebas NUNCA se debe permitir que esto aparezca
         * 
         * Falta:
         * Cambiar las viejas respuestas al nuevo formato ya que algunas no estaban bajo dicho estandar
         * modificar las llamadas a dichos errores para que coincidan con lo que se pide
         * 
         * Viendo la estructura actual, se pueden hacer los 5 errores genericos, y cuando se llamen,
         * se modifica el error que arrojan
         * de esta manera no hay 1000 llamados a diferentes errores desde la DB, solo se sobreescribe 
         * el mismo llamado y se le cambia solamente el mensaje de error
         * potente
         */

        public $response = [
            'status' => "OK",
            'result' => array()
        ];

        //Este permanece
        public function error_400($valor = "Error en el input",$errorid = "400"){
            $this->response['status'] = 'error';
            $this->response['result'] = array(
                "error_id" => $errorid,
                "error_msg" => $valor
            );
            return $this->response;
        }

        //Este permanece
        public function error_500(){
            $this->response['status'] = 'error';
            $this->response['result'] = array(
                "error_id" => "500",
                "error_msg" => "Error interno del servidor"
            );
            return $this->response;
        }
        
        /**
         * Dentro de esta clase se guardaran directamente toda la información recibida para ser enviada a la DB
         * Al empezar cada función, se debe guardar una variable que registre
         * todo el input del usuario, y se le pasa a la clase de respuestas
         * para que, al registrarse cualquier error, sean guardados directamente
         * los siguientes datos deben ser guardados
         * datos del llamado
         * método del llamado (get, post, put, delete, etc)
         * error que causo que no se procediera
         * los demás guardados dentro de las plataformas de forma regular  
        */
    }

?>