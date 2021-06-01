<?php 
    require_once("core.lib.php");
    require_once("classes/response.class.php");
    require_once("classes/auth.class.php");
    /**
     * Para recordar:
     * SIEMPRE llamar primero a corelib antes de la clase que lo va a implementar
     */
    $_auth = new auth;
    $_respuestas = new response;
    //La autenticación se hace aquí
    $here = "we go";
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        //Solo se permiten métodos post

        //Recibimos los datos
        if(isset($_REQUEST['usuario']) && isset($_REQUEST['password'])){
            $send = [
                "usuario" => $_REQUEST["usuario"],
                "password" => $_REQUEST["password"]
            ];
            $postbody = json_encode($send);
        }else{

            $postbody = file_get_contents("php://input"); 
        }

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
        $saver = json_decode($postbody,true);
        $operation = (!isset($saver['request'])) ? '' : $saver['request'] ;
        $token = (!isset($saver['token'])) ? '' : $saver['token'] ;
        $err = (!isset($datosArray)) ? '' : $datosArray ;
        $saveresult = $_auth->logsave('auth',$postbody,json_encode($datosArray),'','',$token,$datosArray["result"]["error_id"],'','','');
        echo json_encode($datosArray);

    }else{
        header('Content-type:application/json');
        $datosArray = $_respuestas->error_400("Metodo no permitido");
        $saver = json_decode($postbody,true);
        $operation = (!isset($saver['request'])) ? '' : $saver['request'] ;
        $token = (!isset($saver['token'])) ? '' : $saver['token'] ;
        $err = (!isset($datosArray)) ? '' : $datosArray ;
        $saveresult = $_auth->logsave('auth',$postbody,json_encode($datosArray),'','',$token,$datosArray["result"]["error_id"],'','','');
        echo json_encode($datosArray);
    }

?>