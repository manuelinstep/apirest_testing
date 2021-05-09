<?php 
    require_once("classes/respuestas.class.php");
    require_once("classes/pacientes.class.php");

    $_respuestas = new respuestas;
    $_pacientes = new pacientes;

    switch($_SERVER['REQUEST_METHOD']){
        case "GET":
            if(isset($_GET['page'])){
                $pagina =  $_GET['page'];
                $listapacientes =  $_pacientes->listaPacientes($pagina);
                header('Content-Type: application/json');
                echo json_encode($listapacientes);
                http_response_code(200);
            }else if(isset($_GET['id'])){
                $pacientid = $_GET['id'];
                $datosPaciente = $_pacientes->obtenerPaciente($pacientid);
                header('Content-Type: application/json');
                echo json_encode($datosPaciente);
                http_response_code(200);    
            }
            break;
        case "POST":
            //Recibimos los datos enviados
            $postBody = file_get_contents("php://input");
            //Enviamos al manejador
            $datosArray  = $_pacientes->post($postBody);
            //Devolvemos la respuesta 
            header('Content-type:application/json');
            if(isset($datosArray["result"]["error_id"])){
                $responseCode = $datosArray["result"]["error_id"];
                http_response_code($responseCode);
            }else{
                http_response_code(200);
            }
            echo json_encode($datosArray);
            break;
        case "PUT":
            
            $postBody = file_get_contents("php://input");

            $datosArray = $_pacientes->put($postBody);
            header('Content-type:application/json');
            if(isset($datosArray["result"]["error_id"])){
                $responseCode = $datosArray["result"]["error_id"];
                http_response_code($responseCode);
            }else{
                http_response_code(200);
            }
            echo json_encode($datosArray);
            break;

            break;
        case "DELETE":

            $postBody = file_get_contents("php://input");

            $datosArray = $_pacientes->delete($postBody);

             header('Content-type:application/json');
             if(isset($datosArray["result"]["error_id"])){
                 $responseCode = $datosArray["result"]["error_id"];
                 http_response_code($responseCode);
             }else{
                 http_response_code(200);
             }
             echo json_encode($datosArray);
            
            break;
        default:
            
            header('Content-type:application/json');
            $datosArray = $_respuestas->error_405();
            echo json_encode($datosArray);
        break;
    }
?>