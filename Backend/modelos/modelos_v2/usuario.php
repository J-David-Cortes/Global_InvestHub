<?php
    require_once(__DIR__ . '/../jwt_helper.php');

    class Usuario {
        private $conexion;

        // Hash bcrypt constante, sin cuenta real asociada. Se usa SOLO para
        // el password_verify() "dummy" cuando el email no existe: evita que
        // el tiempo de respuesta delate si el email esta o no registrado
        // (buscar y no encontrar es mas rapido que encontrar y verificar).
        const HASH_DUMMY = '$2y$10$MpRmZ7bnrtATi8H8COkxj.UICz32yEM0bJ5Ybv7.WIYPEcip0Qa9C';

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // JOIN para traer el nombre del nivel de permiso
            $sql = "SELECT u.id_usuario, u.nombre, u.email, u.fo_permiso, p.nombre AS nombre_nivel
                    FROM usuario u
                    INNER JOIN permiso_nivel p ON u.fo_permiso = p.id_permiso 
                    ORDER BY u.nombre";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta usuario: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function consultaUno($fo_usuario){
            $fo_usuario = intval($fo_usuario);

            $sql = "SELECT u.id_usuario, u.nombre, u.email, u.fo_permiso, p.nombre AS nombre_nivel
                    FROM usuario u
                    INNER JOIN permiso_nivel p ON u.fo_permiso = p.id_permiso
                    WHERE u.id_usuario = $fo_usuario";

            $res = mysqli_query($this->conexion, $sql) or die("Error en consultaUno usuario: " . mysqli_error($this->conexion));

            return mysqli_fetch_assoc($res);
        }

        public function eliminar($id){
            $sql = "DELETE FROM usuario WHERE id_usuario = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("NO eliminó el REGISTRO: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se eliminó el usuario"];
        }

        public function insertar($params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $email = mysqli_real_escape_string($this->conexion, $params->email);
            $fo_ciudad = intval($params->fo_ciudad);
            $fo_permiso = intval($params->fo_permiso);

            // password_hash() con el algoritmo por defecto (bcrypt) produce
            // un string de 60 caracteres; clave es varchar(150), asi que
            // sobra espacio sin tocar el esquema.
            $clave = mysqli_real_escape_string($this->conexion, password_hash($params->clave, PASSWORD_DEFAULT));

            $sql = "INSERT INTO usuario(nombre, email, clave, fo_ciudad, fo_permiso) VALUES('$nombre', '$email', '$clave', $fo_ciudad, $fo_permiso)";
            mysqli_query($this->conexion, $sql) or die("NO insertó el REGISTRO: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se insertó el usuario"];
        }

        public function editar($id, $params, $fo_usuario){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $email = mysqli_real_escape_string($this->conexion, $params->email);
            $id = intval($id);
            $fo_usuario = intval($fo_usuario);

            // fo_permiso solo lo cambia un administrador sobre OTRA cuenta.
            // En cualquier otro caso se ignora el valor del cliente y se
            // conserva el que ya tiene la base (no entra en el UPDATE).
            $sql = "UPDATE usuario SET nombre = '$nombre', email = '$email'";
            if($id !== $fo_usuario && isset($params->fo_permiso) && esAdmin($this->conexion, $fo_usuario)){
                $fo_permiso = intval($params->fo_permiso);
                $sql .= ", fo_permiso = $fo_permiso";
            }

            if(isset($params->clave) && $params->clave !== ''){
                $clave = mysqli_real_escape_string($this->conexion, password_hash($params->clave, PASSWORD_DEFAULT));
                $sql .= ", clave = '$clave'";
            }

            $sql .= " WHERE id_usuario = $id";
            mysqli_query($this->conexion, $sql) or die("NO editó el REGISTRO: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se editó el usuario"];
        }

        public function cambiarClave($fo_usuario, $claveActual, $claveNueva){
            $fo_usuario = intval($fo_usuario);

            // hash_equals() lanza TypeError si recibe algo que no sea string
            // (ej. un numero en el JSON), asi que se rechaza antes.
            if(!is_string($claveActual) || !is_string($claveNueva)){
                return ['Resultado' => "Error", 'mensaje' => "Datos inválidos"];
            }
            if($claveNueva === ''){
                return ['Resultado' => "Error", 'mensaje' => "La contraseña nueva no puede estar vacía"];
            }
            // La columna es varchar(150): se cuentan caracteres, no bytes.
            if(mb_strlen($claveNueva, 'UTF-8') > 150){
                return ['Resultado' => "Error", 'mensaje' => "La contraseña nueva es demasiado larga"];
            }

            $sqlClave = "SELECT clave FROM usuario WHERE id_usuario = $fo_usuario";
            $res = mysqli_query($this->conexion, $sqlClave) or die("Error al consultar la clave: " . mysqli_error($this->conexion));
            $fila = mysqli_fetch_assoc($res);

            if(!$fila){
                return ['Resultado' => "Error", 'mensaje' => "Usuario no encontrado"];
            }

            // La comparacion se hace en PHP y no con WHERE clave = '...' porque
            // la collation utf8mb4_unicode_ci ignora mayusculas/minusculas
            // (y aqui ya no aplicaria: el hash es sensible a mayusculas).
            if(!password_verify($claveActual, $fila['clave'])){
                return ['Resultado' => "Error", 'mensaje' => "La contraseña actual no es correcta"];
            }

            $claveNuevaSql = mysqli_real_escape_string($this->conexion, password_hash($claveNueva, PASSWORD_DEFAULT));
            $sql = "UPDATE usuario SET clave = '$claveNuevaSql' WHERE id_usuario = $fo_usuario";
            mysqli_query($this->conexion, $sql) or die("Error al cambiar la clave: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se cambió la contraseña"];
        }

        public function login($email, $clave){
            if(!is_string($email) || !is_string($clave)){
                return ['Resultado' => "Error", 'mensaje' => "Datos inválidos"];
            }

            $email = trim($email);
            if($email === '' || $clave === ''){
                return ['Resultado' => "Error", 'mensaje' => "Datos inválidos"];
            }

            $emailSql = mysqli_real_escape_string($this->conexion, $email);
            $sql = "SELECT id_usuario, nombre, email, clave, fo_permiso FROM usuario WHERE email = '$emailSql' LIMIT 1";
            $res = mysqli_query($this->conexion, $sql) or die("Error al consultar usuario: " . mysqli_error($this->conexion));
            $fila = mysqli_fetch_assoc($res);

            // Mismo mensaje para "no existe" y "clave incorrecta": no revela
            // que emails estan registrados. El password_verify() "dummy" se
            // ejecuta tambien cuando no existe, para que el tiempo de
            // respuesta sea parejo en ambos casos.
            if($fila === null){
                password_verify($clave, self::HASH_DUMMY);
                return ['Resultado' => "Error", 'mensaje' => "Email o contraseña incorrectos"];
            }

            if(!password_verify($clave, $fila['clave'])){
                return ['Resultado' => "Error", 'mensaje' => "Email o contraseña incorrectos"];
            }

            return $this->respuestaSesion($fila);
        }

        // Arma la respuesta de sesion (token + datos minimos) a partir de una
        // fila con id_usuario, nombre, email y fo_permiso. La usan login() y
        // registro() para no duplicar el formato de respuesta.
        private function respuestaSesion($fila){
            $token = generarToken((int) $fila['id_usuario'], $fila['email']);

            return [
                'Resultado' => "OK",
                'token' => $token,
                'usuario' => [
                    'id' => (int) $fila['id_usuario'],
                    'nombre' => $fila['nombre'],
                    'email' => $fila['email'],
                    'fo_permiso' => (int) $fila['fo_permiso'],
                ],
            ];
        }

        // Mensaje unico para cualquier fallo de registro que no deba revelar
        // detalles (incluido el correo duplicado y cualquier error interno):
        // no dice si el correo ya existe ni expone motivos tecnicos.
        private const MENSAJE_REGISTRO_GENERICO = "No se pudo crear la cuenta con estos datos. Si ya tienes cuenta, inicia sesión.";
        private const VERSION_ACEPTACION = 'borrador-1';

        public function registro($params){
            $transaccionAbierta = false;
            try {
                if(!is_object($params)){
                    return ['Resultado' => "Error", 'mensaje' => self::MENSAJE_REGISTRO_GENERICO];
                }

                $nombre = isset($params->nombre) && is_string($params->nombre) ? trim($params->nombre) : '';
                $email  = isset($params->email) && is_string($params->email) ? mb_strtolower(trim($params->email), 'UTF-8') : '';
                $clave  = isset($params->clave) && is_string($params->clave) ? $params->clave : '';
                $confirmarClave = isset($params->confirmarClave) && is_string($params->confirmarClave) ? $params->confirmarClave : '';
                $aceptaciones = isset($params->aceptaciones) && is_object($params->aceptaciones) ? $params->aceptaciones : null;

                if(mb_strlen($nombre, 'UTF-8') < 2 || mb_strlen($nombre, 'UTF-8') > 150){
                    return ['Resultado' => "Error", 'mensaje' => "Nombre inválido"];
                }
                if(preg_match('/[\x00-\x1F\x7F]/', $nombre)){
                    return ['Resultado' => "Error", 'mensaje' => "Nombre inválido"];
                }
                if(!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100){
                    return ['Resultado' => "Error", 'mensaje' => "Correo inválido"];
                }
                if(mb_strlen($clave, 'UTF-8') < 10){
                    return ['Resultado' => "Error", 'mensaje' => "La contraseña debe tener al menos 10 caracteres"];
                }
                // bcrypt trunca a 72 bytes: se rechaza, nunca se recorta.
                if(strlen($clave) > 72){
                    return ['Resultado' => "Error", 'mensaje' => "La contraseña es demasiado larga (máximo 72 bytes)"];
                }
                if(strpos($clave, "\0") !== false){
                    return ['Resultado' => "Error", 'mensaje' => "La contraseña contiene caracteres no permitidos"];
                }
                if($clave !== $confirmarClave){
                    return ['Resultado' => "Error", 'mensaje' => "Las contraseñas no coinciden"];
                }
                if($aceptaciones === null || ($aceptaciones->terminos ?? null) !== true || ($aceptaciones->riesgo ?? null) !== true){
                    return ['Resultado' => "Error", 'mensaje' => "Debes aceptar los términos y el aviso de riesgo"];
                }
                $aceptaComercial = ($aceptaciones->comercial ?? null) === true;

                // El hash se calcula ANTES de tocar la base: el tiempo de
                // respuesta no debe delatar si el correo ya existe.
                $hash = password_hash($clave, PASSWORD_DEFAULT);

                mysqli_begin_transaction($this->conexion);
                $transaccionAbierta = true;

                $stmtUsuario = mysqli_prepare($this->conexion,
                    "INSERT INTO usuario(nombre, email, clave, fo_permiso) VALUES (?, ?, ?, 4)");
                if($stmtUsuario === false){
                    throw new \RuntimeException('prepare_usuario:' . mysqli_errno($this->conexion));
                }
                mysqli_stmt_bind_param($stmtUsuario, 'sss', $nombre, $email, $hash);
                $okUsuario = mysqli_stmt_execute($stmtUsuario);

                if(!$okUsuario){
                    $duplicado = mysqli_stmt_errno($stmtUsuario) === 1062;
                    $codigoError = mysqli_stmt_errno($stmtUsuario);
                    mysqli_stmt_close($stmtUsuario);
                    mysqli_rollback($this->conexion);
                    $transaccionAbierta = false;
                    if(!$duplicado){
                        error_log("registro(): fallo insert usuario, errno=$codigoError");
                    }
                    return ['Resultado' => "Error", 'mensaje' => self::MENSAJE_REGISTRO_GENERICO];
                }

                $idUsuario = mysqli_insert_id($this->conexion);
                mysqli_stmt_close($stmtUsuario);

                $tipos = ['terminos', 'riesgo'];
                if($aceptaComercial){
                    $tipos[] = 'comercial';
                }
                $version = self::VERSION_ACEPTACION;
                $stmtAceptacion = mysqli_prepare($this->conexion,
                    "INSERT INTO usuario_aceptacion(fo_usuario, tipo, version) VALUES (?, ?, ?)");
                if($stmtAceptacion === false){
                    throw new \RuntimeException('prepare_aceptacion:' . mysqli_errno($this->conexion));
                }
                foreach($tipos as $tipo){
                    mysqli_stmt_bind_param($stmtAceptacion, 'iss', $idUsuario, $tipo, $version);
                    if(!mysqli_stmt_execute($stmtAceptacion)){
                        $codigoError = mysqli_stmt_errno($stmtAceptacion);
                        mysqli_stmt_close($stmtAceptacion);
                        mysqli_rollback($this->conexion);
                        $transaccionAbierta = false;
                        error_log("registro(): fallo insert usuario_aceptacion($tipo), errno=$codigoError");
                        return ['Resultado' => "Error", 'mensaje' => self::MENSAJE_REGISTRO_GENERICO];
                    }
                }
                mysqli_stmt_close($stmtAceptacion);

                // Fechas calculadas en SQL (no en PHP): date.timezone de PHP
                // y la zona horaria de MariaDB pueden diferir, y deben
                // coincidir con el CURDATE() que usa planActual().
                $stmtSuscripcion = mysqli_prepare($this->conexion,
                    "INSERT INTO usuario_suscripcion(fecha_inicio, fecha_fin, fo_usuario, fo_suscripcion, fo_estado)
                     VALUES (CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), ?, 5, 1)");
                if($stmtSuscripcion === false){
                    throw new \RuntimeException('prepare_suscripcion:' . mysqli_errno($this->conexion));
                }
                mysqli_stmt_bind_param($stmtSuscripcion, 'i', $idUsuario);
                if(!mysqli_stmt_execute($stmtSuscripcion)){
                    $codigoError = mysqli_stmt_errno($stmtSuscripcion);
                    mysqli_stmt_close($stmtSuscripcion);
                    mysqli_rollback($this->conexion);
                    $transaccionAbierta = false;
                    error_log("registro(): fallo insert usuario_suscripcion, errno=$codigoError");
                    return ['Resultado' => "Error", 'mensaje' => self::MENSAJE_REGISTRO_GENERICO];
                }
                mysqli_stmt_close($stmtSuscripcion);

                mysqli_commit($this->conexion);
                $transaccionAbierta = false;

                return $this->respuestaSesion([
                    'id_usuario' => $idUsuario,
                    'nombre' => $nombre,
                    'email' => $email,
                    'fo_permiso' => 4,
                ]);
            } catch (\Throwable $e) {
                if($transaccionAbierta){
                    mysqli_rollback($this->conexion);
                }
                error_log("registro(): excepcion " . get_class($e) . ": " . $e->getCode());
                return ['Resultado' => "Error", 'mensaje' => self::MENSAJE_REGISTRO_GENERICO];
            }
        }
    }
?>