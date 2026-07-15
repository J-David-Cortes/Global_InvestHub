<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/avance_educacion.php');

    $control = $_GET['control'];
    $avance_educacion = new avance_educacion($conexion);

    switch($control){
        case 'consulta' :
            $vec = $avance_educacion->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $avance_educacion->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $avance_educacion->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $avance_educacion->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>