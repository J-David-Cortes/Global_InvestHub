<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/fecha_fin.php');

    $control = $_GET['control'];
    $fecha_fin = new fecha_fin($conexion);

    switch($control){
        case 'consulta' :
            $vec = $fecha_fin->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $fecha_fin->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $fecha_fin->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $fecha_fin->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>
