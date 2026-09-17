<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    require_once('../modelos/conexion.php');
    // RUTA CORREGIDA: entra a modelos y luego a modelos_v2
    require_once('../modelos/modelos_v2/usuario.php');

    $control = isset($_GET['control']) ? $_GET['control'] : '';
    $usuario = new Usuario($conexion);

    // TEMPORAL: usuario fijo hasta que exista login real con sesion.
    // Reemplazar por el id del usuario autenticado cuando se implemente login.
    $fo_usuario = 2;

    switch($control){
        case 'consulta' :
            $vec = $usuario->consulta();
        break;

        case 'consultaUno' :
            $vec = $usuario->consultaUno($fo_usuario);
        break;

        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $usuario->insertar($params);
        break;
        case 'editar' :
            $json = file_get_contents('php://input');
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $params = json_decode($json);
            $vec = $usuario->editar($id, $params);
        break;
        case 'eliminar' :
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $vec = $usuario->eliminar($id);
        break;

        default:
            $vec = ['resultado' => 'Error', 'mensaje' => 'Controlador no especificado'];
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>