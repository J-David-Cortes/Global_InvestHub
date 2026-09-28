<?php
    header('Access-Control-Allow-Origin: http://localhost:4200');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
    header('Content-Type: application/json');

    // El navegador manda un preflight OPTIONS (sin Authorization) antes de
    // cualquier peticion real que incluya ese header. Si no se corta aqui,
    // el middleware de mas abajo lo rechazaria con 401 y el navegador
    // bloquearia la peticion real como un fallo de CORS.
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        http_response_code(200);
        exit;
    }

    require_once('../modelos/conexion.php');
    require_once('../modelos/modelos_v2/usuario_bot.php');
    require_once('../modelos/auth_middleware.php');

    $control = isset($_GET['control']) ? $_GET['control'] : '';
    $usuarioBot = new UsuarioBot($conexion);

    $fo_usuario = exigirUsuarioAutenticado();

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

        case 'cambiarEstado' :
            $json = file_get_contents('php://input');
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $params = json_decode($json);
            // Solo se acepta un booleano real o 0/1: evita que "false" (string)
            // se interprete como true.
            if(!is_object($params) || !isset($params->activo) || !in_array($params->activo, [0, 1, true, false], true)){
                $vec = ['Resultado' => 'Error', 'mensaje' => 'Datos inválidos'];
                break;
            }
            $vec = $usuarioBot->cambiarEstado($id, $fo_usuario, $params->activo);
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