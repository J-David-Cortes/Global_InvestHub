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
    }
?>