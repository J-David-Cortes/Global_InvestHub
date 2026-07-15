<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/bot.php');

    $control = $_GET['control'];
    $bot = new bot($conexion);

    switch($control){
        case 'consulta' :
            $vec = $bot->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $bot->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $bot->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $bot->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>
