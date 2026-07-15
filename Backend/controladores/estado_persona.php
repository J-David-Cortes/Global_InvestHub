<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/estado_persona.php');

    $control = $_GET['control'];
    $estado_persona = new estado_persona($conexion);

    switch($control){
        case 'consulta' :
            $vec = $estado_persona->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $estado_persona->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $estado_persona->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $estado_persona->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>