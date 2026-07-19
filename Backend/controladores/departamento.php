<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    // CAMBIO AQUÍ: Ahora apunta a la versión 2 que tiene los JOIN y la seguridad
    require_once('../modelos/modelos_v2/departamento.php');

    $control = $_GET['control'];
    $departamento = new departamento($conexion);

    switch($control){
        case 'consulta' :
            $vec = $departamento->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $departamento->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $departamento->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $departamento->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>