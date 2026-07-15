<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/area_cargo.php');

    $control = $_GET['control'];
    $area_cargo = new area_cargo($conexion);

    switch($control){
        case 'consulta' :
            $vec = $area_cargo->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $area_cargo->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $area_cargo->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $area_cargo->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>