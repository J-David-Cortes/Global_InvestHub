<?php
    class Usuario {
        private $conexion;

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

            // TODO SEGURIDAD: la clave se guarda en texto plano. Antes de
            // cualquier uso real (mas alla de esta demo academica), esto DEBE
            // reemplazarse por password_hash()/password_verify() de PHP.
            // No usar este sistema con contraseñas reales de usuarios.
            $clave = mysqli_real_escape_string($this->conexion, $params->clave);

            $sql = "INSERT INTO usuario(nombre, email, clave, fo_ciudad, fo_permiso) VALUES('$nombre', '$email', '$clave', $fo_ciudad, $fo_permiso)";
            mysqli_query($this->conexion, $sql) or die("NO insertó el REGISTRO: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se insertó el usuario"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $email = mysqli_real_escape_string($this->conexion, $params->email);
            $fo_permiso = intval($params->fo_permiso);
            $id = intval($id);

            $sql = "UPDATE usuario SET nombre = '$nombre', email = '$email', fo_permiso = $fo_permiso";

            // TODO SEGURIDAD: la clave se guarda en texto plano. Antes de
            // cualquier uso real (mas alla de esta demo academica), esto DEBE
            // reemplazarse por password_hash()/password_verify() de PHP.
            // No usar este sistema con contraseñas reales de usuarios.
            if(isset($params->clave) && $params->clave !== ''){
                $clave = mysqli_real_escape_string($this->conexion, $params->clave);
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
            // la collation utf8mb4_unicode_ci ignora mayusculas/minusculas.
            // TODO SEGURIDAD: la clave se guarda en texto plano. Antes de
            // cualquier uso real (mas alla de esta demo academica), esto DEBE
            // reemplazarse por password_hash()/password_verify() de PHP.
            // No usar este sistema con contraseñas reales de usuarios.
            if(!hash_equals($fila['clave'], $claveActual)){
                return ['Resultado' => "Error", 'mensaje' => "La contraseña actual no es correcta"];
            }

            $claveNuevaSql = mysqli_real_escape_string($this->conexion, $claveNueva);
            $sql = "UPDATE usuario SET clave = '$claveNuevaSql' WHERE id_usuario = $fo_usuario";
            mysqli_query($this->conexion, $sql) or die("Error al cambiar la clave: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se cambió la contraseña"];
        }
    }
?>