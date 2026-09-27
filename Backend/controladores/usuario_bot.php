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

        case 'consultaSettings' :
            $vec = $usuarioBot->consultaSettings($fo_usuario);
        break;

        case 'insertar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            // fo_usuario NUNCA se lee del body: se fuerza el del controlador
            // (TEMPORAL hasta que exista login real). Si el JSON es invalido
            // o no es un objeto, se rechaza antes de tocar el modelo.
            if(!is_object($params)){
                $vec = ['Resultado' => 'Error', 'mensaje' => 'Datos inválidos'];
                break;
            }
            $params->fo_usuario = $fo_usuario;
            $vec = $usuarioBot->insertar($params);
        break;

        case 'estadoBroker' :
            $vec = $usuarioBot->estadoBroker($fo_usuario);
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