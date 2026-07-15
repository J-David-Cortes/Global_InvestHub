<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/nivel_educacion.php');

    $control = $_GET['control'];
    $nivel_educacion = new nivel_educacion($conexion);

    switch($control){
        case 'consulta' :
            $vec = $nivel_educacion->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $nivel_educacion->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $nivel_educacion->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $nivel_educacion->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>