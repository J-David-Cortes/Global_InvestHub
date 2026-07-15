<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    require_once('../modelos/nivel_permiso.php');

    $control = $_GET['control'];
    $nivel_permiso = new nivel_permiso($conexion);

    switch($control){
        case 'consulta' :
            $vec = $nivel_permiso->consulta();
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $nivel_permiso->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $nivel_permiso->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $nivel_permiso->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>