<?php 
    require_once("classes/response.class.php");
    require_once("classes/information.class.php");

    $_respuestas = new response;
    $_information = new information;

    if (!function_exists('getallheaders')) {
        function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
        }
    }
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            //Implementado correctamente el obtener todos los datos
            if(isset($_REQUEST)){
                $send = $_REQUEST;
                $getBody = json_encode($send);
            }else{

                $getBody = file_get_contents("php://input"); 
            }

            $result = $_information->get($getBody);
            header('Content-Type: application/json');
            echo json_encode($result);
            /**
             * La estructura funciona de manera que, llegado a este punto, no existe un error
             * por tanto, el http response code siempre se deja en 200 al final
             * porque solo otros métodos lo cambian
             * 
             * Procedemos a guardar 
             * Debemos decodificar la info recibida parcialmente
             * para obtener la operación 
             */
            
            $saver = json_decode($getBody,true);
            $operation = (!isset($saver['request'])) ? '' : $saver['request'] ;
            $token = (!isset($saver['token'])) ? '' : $saver['token'] ;
            $err = (!isset($result['result'])) ? '' : $result['result']['error_id'] ;
            $saveresult = $_information->logsave($operation,$getBody,json_encode($result),'','',$token,$err,'','','');     
            /**
             * El ultimo campo se deja vacio, en el momento que se implemente 
             * la modificación que ya existe en quote, se cambia
             */
            http_response_code(200);
            break;
        
        default:
            # codigo de error correspondiente
            $result = $_respuestas->error_400("Método incorrecto","400");
            header('Content-Type: application/json');
            echo json_encode($result);
            http_response_code(200);
            break;
    }
?>