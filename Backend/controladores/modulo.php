<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header('Content-Type: application/json');

    require_once('../modelos/conexion.php');
    require_once('../modelos/modelos_v2/modulo.php'); 

    $control = isset($_GET['control']) ? $_GET['control'] : '';
    $modulo = new Modulo($conexion);

    switch($control){
        case 'consulta' :
            $vec = $modulo->consulta();
        break;

        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $modulo->insertar($params);
        break;

        case 'editar' :
            $json = file_get_contents('php://input');
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $params = json_decode($json);
            $vec = $modulo->editar($id, $params);
        break;

        case 'eliminar' :
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $vec = $modulo->eliminar($id);
        break;

        default:
            $vec = ['resultado' => 'Error', 'mensaje' => 'Controlador no especificado'];
        break;
    }

    echo json_encode($vec);
?>