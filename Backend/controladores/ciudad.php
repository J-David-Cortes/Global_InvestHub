<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/ciudad.php');

    $control = $_GET['control'];
    $ciudad = new ciudad($conexion);

    switch($control){
        case 'consulta' :
            $vec = $ciudad->consulta();
        break;
        case 'consulta2' :
            $id_departamento = $_GET['id_departamento'];
            $vec = $ciudad->consulta2($id_departamento);
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $ciudad->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $ciudad->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $ciudad->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>