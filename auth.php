<?php 
    require_once("classes/auth.class.php");
    require_once("classes/response.class.php");

    $_auth = new auth;
    $_respuestas = new response;
    //La autenticación se hace aquí
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        //Solo se permiten métodos post

        //Recibimos los datos
        $postbody = file_get_contents("php://input");

        //Enviamos los datos al manejador
        $datosArray = $_auth->login($postbody);

        //Devolvemos una respuesta
        header('Content-type:application/json');
        if(isset($datosArray["result"]["error_id"])){
            $responseCode = $datosArray["result"]["error_id"];
            http_response_code($responseCode);
        }else{
            http_response_code(200);
        }
        echo json_encode($datosArray);

    }else{
        header('Content-type:application/json');
        $datosArray = $_respuestas->error_400("Metodo no permitido");
        echo json_encode($datosArray);
    }
?>