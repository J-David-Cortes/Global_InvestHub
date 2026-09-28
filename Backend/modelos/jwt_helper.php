<?php
require_once(__DIR__ . '/../../vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// TODO SEGURIDAD: esta clave debe vivir en una variable de entorno (.env,
// fuera del control de versiones) antes de cualquier uso real. Aqui queda
// como constante, siguiendo el mismo patron que el resto de la
// configuracion del proyecto (ej. conexion.php con la clave de MySQL en
// texto), solo para esta demo academica. Generada una sola vez con
// bin2hex(random_bytes(32)): no es memorizable ni corta a proposito.
const JWT_CLAVE_SECRETA = '015bdf51eea6478238ed5f00423d78fada656edaf92f2bc81200f8608a7f63a';
const JWT_ALGORITMO = 'HS256';
const JWT_EXPIRACION_SEGUNDOS = 2 * 60 * 60; // 2 horas

// Genera un JWT para un login exitoso. El payload lleva solo lo minimo
// para identificar al usuario (sub, email) mas iat/exp: fo_permiso
// deliberadamente NO viaja aqui -- si el rol cambia en la BD, un token ya
// emitido no debe quedar con un rol desactualizado hasta que expire.
// Cuando haga falta el rol, se consulta fresco desde la BD con el sub.
function generarToken($idUsuario, $email){
    $ahora = time();
    $payload = [
        'sub' => $idUsuario,
        'email' => $email,
        'iat' => $ahora,
        'exp' => $ahora + JWT_EXPIRACION_SEGUNDOS,
    ];
    return JWT::encode($payload, JWT_CLAVE_SECRETA, JWT_ALGORITMO);
}

// Decodifica y valida un JWT (firma + expiracion). Devuelve el payload
// como array, o null si el token es invalido/expirado/mal firmado. La
// usaran los controladores cuando se conecten al login real (paso
// siguiente), en lugar del $fo_usuario = 2 fijo.
function verificarToken($token){
    try {
        $payload = JWT::decode($token, new Key(JWT_CLAVE_SECRETA, JWT_ALGORITMO));
        return (array) $payload;
    } catch (Exception $e) {
        return null;
    }
}
