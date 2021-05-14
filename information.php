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
            echo json_encode($result);
            http_response_code(200);  
            # pasamos los request al método que los reciba
            break;
        
        default:
            # codigo de error correspondiente
            break;
    }
?>