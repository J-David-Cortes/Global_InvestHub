<?php
    $servidor = "localhost";
    $usuario = "root";
    $clave = "";
    $bd = "marketplace_2";

    $conexion = mysqli_connect($servidor, $usuario, $clave, $bd) or die("Error al conectar: " . mysqli_connect_error());
    mysqli_set_charset($conexion, "utf8");
?>