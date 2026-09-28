<?php
    header('Access-Control-Allow-Origin: http://localhost:4200');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

    // El navegador manda un preflight OPTIONS (sin Authorization) antes de
    // cualquier peticion real que incluya ese header. Si no se corta aqui,
    // el middleware de mas abajo lo rechazaria con 401 y el navegador
    // bloquearia la peticion real como un fallo de CORS.
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        http_response_code(200);
        exit;
    }

    require_once('../modelos/conexion.php');
    // RUTA CORREGIDA: entra a modelos y luego a modelos_v2
    require_once('../modelos/modelos_v2/usuario.php');
    require_once('../modelos/auth_middleware.php');

    $control = isset($_GET['control']) ? $_GET['control'] : '';
    $usuario = new Usuario($conexion);

    // 'login' es la unica accion que no exige un token todavia -- es como
    // se obtiene uno. Todo lo demas exige autenticacion.
    if($control !== 'login'){
        $fo_usuario = exigirUsuarioAutenticado();
    }

    switch($control){
        case 'consulta' :
            // Lista completa de usuarios: solo administradores.
            exigirRolAdmin($conexion, $fo_usuario);
            $vec = $usuario->consulta();
        break;

        case 'consultaUno' :
            $vec = $usuario->consultaUno($fo_usuario);
        break;

        case 'insertar' :
            // Crear la cuenta de otra persona: solo administradores. No hay
            // flujo de auto-registro en el frontend (ningun caller de este
            // metodo existe hoy fuera de este controlador).
            exigirRolAdmin($conexion, $fo_usuario);
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
            // Borrar la cuenta de otra persona: solo administradores.
            exigirRolAdmin($conexion, $fo_usuario);
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $vec = $usuario->eliminar($id);
        break;

        case 'cambiarClave' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $claveActual = isset($params->claveActual) ? $params->claveActual : '';
            $claveNueva = isset($params->claveNueva) ? $params->claveNueva : '';
            $vec = $usuario->cambiarClave($fo_usuario, $claveActual, $claveNueva);
        break;

        case 'login' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $email = isset($params->email) ? $params->email : '';
            $clave = isset($params->clave) ? $params->clave : '';
            $vec = $usuario->login($email, $clave);
        break;

        default:
            $vec = ['resultado' => 'Error', 'mensaje' => 'Controlador no especificado'];
        break;
    }

    header('Content-Type: application/json');
    echo json_encode($vec);
?>