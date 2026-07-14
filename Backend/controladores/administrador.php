<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/administrador.php');

    $control = $_GET['control'];
    $administrador = new administrador($conexion);

    switch($control){
        case 'consulta' :
            $vec = $administrador->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);

            $vec = $administrador->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);

            $vec = $administrador->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];

            $vec = $administrador->eliminar($id);
        break;
    }

    header('content-Type: application/json');

    $datos = json_encode($vec);
    echo $datos;    
?>