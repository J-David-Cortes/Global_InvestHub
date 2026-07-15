<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/fecha_inicio.php');

    $control = $_GET['control'];
    $fecha_inicio = new fecha_inicio($conexion);

    switch($control){
        case 'consulta' :
            $vec = $fecha_inicio->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $fecha_inicio->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $fecha_inicio->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $fecha_inicio->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>
