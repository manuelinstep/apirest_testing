<?php 
    require_once("classes/connection/connect.php");
    $conexion = new connect;

    $query = "INSERT INTO pacientes (DNI) VALUE('1')";
    print_r($conexion->nonQueryId($query));
?>