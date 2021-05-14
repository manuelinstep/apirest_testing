<?php 
    require_once("classes/respuestas.class.php");
    require_once("classes/information.class.php");

    $_respuestas = new respuestas;
    $_information = new information;
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $getBody = file_get_contents("php://input");
            $result = $_information->get($getBody);
            header('Content-Type: application/json');
            $results = $_respuestas->return_200($result);
            echo json_encode($results);
            /**
             * La estructura funciona de manera que, llegado a este punto, no existe un error
             * por tanto, el http response code siempre se deja en 200 al final
             * porque solo otros métodos lo cambian
             */
            http_response_code(200);  
            break;
        
        default:
            # codigo de error correspondiente
            break;
    }
?>