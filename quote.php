<?php 
    /**
     * Debemos encontrar la manera de inicializar la clase DBTools en este punto
     * luego, comprobar que haciendo a la clase information heredar de la misma, funcione correctamente
     * Y por ultimo, revisar que todos los campos se registren correctamente en las 3 tablas
     * log_consultas, trans_all_webservice y app_error
     * Ya tenemos todo lo necesario, ahora, debemos inicializar las clases, esperar lo mejor, y prepararnos
     * para lo peor
     * 
     * P.D. un día como hoy, 05/20/2021, se anuncia oficialmente la muerte de Kentaro Miura
     * Autor de Berserk
     * 
     * "Perhaps he belonged to the domain of legend"
     */

    /**
     * Hacen falta tanto elementos del core.lib como del config.var
     * Resulta que SoapClient es una clase nativa de php (???)
     */
    $here = "we go";
    require_once("classes/response.class.php");
    require_once("core.lib.php");
    require_once("classes/quote.class.php");

    $_respuestas = new response;
    $_quote = new quote;
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'PUT':
            if(isset($_REQUEST)){
                $send = $_REQUEST;
                $getBody = json_encode($send);
            }else{

                $getBody = file_get_contents("php://input"); 
            }
           
            /**
             * Podríamos verificar directamente aquí el token
             */
            
            
            $received = json_decode($getBody,true);
            
            $token = $received['token'];
            $verify = $_quote->checkToken($token);
            
            $result = ($verify['status']=='error') ? $verify : $_quote->handle($getBody);
            header('Content-Type: application/json');
            echo json_encode($result);
            /**
             * La estructura funciona de manera que, llegado a este punto, no existe un error
             * por tanto, el http response code siempre se deja en 200 al final
             * porque solo otros métodos lo cambian
             */
            http_response_code(200);  
            break;
        
        default:
            $result = $_respuestas->error_400("Método incorrecto","400");
            header('Content-Type: application/json');
            echo json_encode($result);
            http_response_code(200);
            break;
    }
?>