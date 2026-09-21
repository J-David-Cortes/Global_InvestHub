<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header('Content-Type: application/json');

    require_once('../modelos/conexion.php');
    require_once('../modelos/modelos_v2/preferencia_notificacion.php');

    $control = isset($_GET['control']) ? $_GET['control'] : '';
    $preferenciaNotificacion = new PreferenciaNotificacion($conexion);

    // TEMPORAL: usuario fijo hasta que exista login real con sesion.
    // Reemplazar por el id del usuario autenticado cuando se implemente login.
    $fo_usuario = 2;

    switch($control){
        case 'obtener' :
            $vec = $preferenciaNotificacion->obtenerPreferencias($fo_usuario);
        break;

        case 'guardar' :
            $json = file_get_contents('php://input');
            $params = json_decode($json);
            $vec = $preferenciaNotificacion->guardarPreferencias($fo_usuario, $params);
        break;

        default:
            $vec = ['resultado' => 'Error', 'mensaje' => 'Controlador no especificado'];
        break;
    }

    echo json_encode($vec);
?>
