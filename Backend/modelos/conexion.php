<?php
    $servidor = "localhost";
    $usuario = "root";
    $clave = "";
    $bd = "Marketplace";

    $conexion = mysqli_connect($servidor, $usuario, $clave) or die("No se conecto a mysql");
    mysqli_select_db($conexion, $bd) or die("No se conecto a la base de datos MARKETPLACE");
    mysqli_set_charset($conexion, "utf8"); //Codificacion para el idioma español
?>