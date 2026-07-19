<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    // CAMBIO AQUÍ: Apuntamos al modelo en la carpeta v2
    require_once('../modelos/modelos_v2/pais.php');

    $control = $_GET['control'];
    $pais = new pais($conexion);

    switch($control){
        case 'consulta' :
            $vec = $pais->consulta();
        break;
        case 'consulta2' :
            $id_pais = $_GET['id_pais'];
            $vec = $pais->consulta2($id_pais);
        break;
        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $pais->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = $_GET['id'];
            $params = json_decode($json);
            $vec = $pais->editar($id, $params);
        break;
        case 'eliminar' :
            $id = $_GET['id'];
            $vec = $pais->eliminar($id);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>