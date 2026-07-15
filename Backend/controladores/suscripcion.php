<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/suscripcion.php');

    $control = $_GET['control'];
    $suscripcion = new suscripcion($conexion);

    switch($control){
        case 'consulta' :
            $vec = $suscripcion->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $suscripcion->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $suscripcion->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $suscripcion->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>