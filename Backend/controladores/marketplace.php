<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/marketplace.php');

    $control = $_GET['control'];
    $marketplace = new marketplace($conexion);

    switch($control){
        case 'consulta' :
            $vec = $marketplace->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $marketplace->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $marketplace->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $marketplace->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>
