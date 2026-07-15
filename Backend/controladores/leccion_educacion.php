<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/leccion_educacion.php');

    $control = $_GET['control'];
    $leccion_educacion = new leccion_educacion($conexion);

    switch($control){
        case 'consulta' :
            $vec = $leccion_educacion->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $leccion_educacion->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $leccion_educacion->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $leccion_educacion->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>