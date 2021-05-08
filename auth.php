<?php 
    require_once("classes/auth.class.php");
    require_once("classes/respuestas.class.php");

    $_auth = new auth;
    $_respuestas = new respuestas;
    //La autenticación se hace aquí
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        //Solo se permiten métodos post
        $postbody = file_get_contents("php://input");
        $datosArray = $_auth->login($postbody);
        print_r(json_encode($datosArray));

    }else{
        echo "metodo no permitido";
    }
?>