<?php
    $servidor = "localhost";
    $usuario = "root";
    $clave = "";
    $bd = "marketplace";

    $conexion = mysqli_connect($servidor, $usuario, $clave) or die("No se conecto a Mysql");
    mysqli_select_db($conexion, $bd) or die("No se conecto a la base de datos marketplace");
    mysqli_set_charset($conexion, 'utf8'); //codificacion para el idioma español
?>