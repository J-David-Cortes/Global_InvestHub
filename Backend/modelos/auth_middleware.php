<?php
require_once(__DIR__ . '/jwt_helper.php');

// Busca el header Authorization de forma portable: getallheaders() no
// existe en todos los SAPI (ej. CLI, y algunos proxies), asi que hay un
// fallback a $_SERVER. Verificado en este entorno (Apache + mod_php,
// apache2handler): getallheaders() SI funciona y SI trae Authorization,
// pero $_SERVER['HTTP_AUTHORIZATION'] viene null -- por eso getallheaders()
// va primero, no al reves.
function obtenerHeaderAuthorization(){
    if(function_exists('getallheaders')){
        foreach(getallheaders() as $nombre => $valor){
            if(strcasecmp($nombre, 'Authorization') === 0){
                return $valor;
            }
        }
    }
    return $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null;
}

// Corta la ejecucion con un JSON de error y el codigo HTTP dado. El
// controlador nunca llega a tocar el modelo si la autenticacion falla.
function abortarAuth($codigoHttp, $mensaje){
    http_response_code($codigoHttp);
    header('Content-Type: application/json');
    echo json_encode(['Resultado' => 'Error', 'mensaje' => $mensaje]);
    exit;
}

// Exige un JWT valido en "Authorization: Bearer <token>". Devuelve el
// id_usuario (sub del token) como entero, o corta con 401 si falta, esta
// mal formado, la firma no coincide o expiro.
function exigirUsuarioAutenticado(){
    $header = obtenerHeaderAuthorization();
    if($header === null || !preg_match('/^Bearer\s+(.+)$/i', $header, $match)){
        abortarAuth(401, 'No autenticado');
    }

    $payload = verificarToken($match[1]);
    if($payload === null){
        abortarAuth(401, 'Token inválido o expirado');
    }

    return (int) $payload['sub'];
}

// ¿El usuario tiene rol de administrador? El rol se consulta FRESCO desde
// la BD con $fo_usuario, nunca del token (el token no lo lleva: ver
// jwt_helper.php, es a proposito). No aborta por si sola -- solo corta con
// 401 si el usuario del token ya no existe -- para poder combinarse con
// otras condiciones (ver exigirPropioUsuarioOAdmin).
function esAdmin($conexion, $fo_usuario){
    $fo_usuario = intval($fo_usuario);
    $res = mysqli_query($conexion, "SELECT fo_permiso FROM usuario WHERE id_usuario = $fo_usuario")
        or die("Error al verificar el rol: " . mysqli_error($conexion));
    $fila = mysqli_fetch_assoc($res);

    if($fila === null){
        abortarAuth(401, 'No autenticado');
    }
    return in_array((int) $fila['fo_permiso'], [1, 2], true);
}

// Exige que el usuario autenticado tenga rol de administrador. Corta con
// 403 si no lo es.
function exigirRolAdmin($conexion, $fo_usuario){
    if(!esAdmin($conexion, $fo_usuario)){
        abortarAuth(403, 'No tienes permiso para realizar esta acción');
    }
}

// Exige que el usuario autenticado sea el propio $idObjetivo, O tenga rol
// de administrador (para que un admin pueda editar el perfil de otro
// usuario). Corta con 403 si ninguna de las dos se cumple. Usado por
// usuario.php?control=editar para cerrar el IDOR: antes, cualquier
// usuario autenticado podia editar a cualquier otro con solo cambiar el
// id en la URL.
function exigirPropioUsuarioOAdmin($conexion, $fo_usuario, $idObjetivo){
    if(intval($idObjetivo) === intval($fo_usuario)){
        return;
    }
    if(esAdmin($conexion, $fo_usuario)){
        return;
    }
    abortarAuth(403, 'No tienes permiso para editar este usuario');
}
