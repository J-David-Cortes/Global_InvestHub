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
    }
?>