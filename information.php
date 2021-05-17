<?php 
    require_once("classes/response.class.php");
    require_once("classes/information.class.php");

    $_respuestas = new response;
    $_information = new information;
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $getBody = file_get_contents("php://input");
            $result = $_information->get($getBody);
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
            # codigo de error correspondiente
            $result = $_respuestas->error_400("Método incorrecto","400");
            header('Content-Type: application/json');
            echo json_encode($result);
            http_response_code(200);
            break;
    }
?>