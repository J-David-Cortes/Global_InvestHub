<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header('Content-Type: application/json');

    require_once('../modelos/conexion.php');
    require_once('../modelos/modelos_v2/usuario_bot.php'); 

    $control = isset($_GET['control']) ? $_GET['control'] : '';
    $usuarioBot = new UsuarioBot($conexion);

    // TEMPORAL: usuario fijo hasta que exista login real con sesion.
    // Reemplazar por el id del usuario autenticado cuando se implemente login.
    $fo_usuario = 2;

    switch($control){
        case 'consulta' :
            $vec = $usuarioBot->consulta($fo_usuario);
        break;

        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $usuarioBot->insertar($params);
        break;

        case 'editar' :
            $json = file_get_contents('php://input');
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $params = json_decode($json);
            $vec = $usuarioBot->editar($id, $params);
        break;

        case 'editarApiKey' :
            $json = file_get_contents('php://input');
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $params = json_decode($json);
            $vec = $usuarioBot->editarApiKey($id, $fo_usuario, $params->api_key);
        break;

        case 'eliminar' :
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $vec = $usuarioBot->eliminar($id);
        break;

        default:
            $vec = ['resultado' => 'Error', 'mensaje' => 'Controlador no especificado'];
        break;
    }

    echo json_encode($vec);
?>