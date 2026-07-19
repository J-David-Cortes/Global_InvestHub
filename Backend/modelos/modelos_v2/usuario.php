<?php
    class Usuario {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // JOIN para traer el nombre del nivel de permiso
            $sql = "SELECT u.id_usuario, u.nombre, u.email, u.fo_permiso_nivel, p.nombre AS nombre_nivel
                    FROM usuario u
                    INNER JOIN permiso_nivel p ON u.fo_permiso_nivel = p.id_permiso_nivel 
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
            $fo_permiso_nivel = intval($params->fo_permiso_nivel);
            
            $sql = "INSERT INTO usuario(nombre, email, fo_permiso_nivel) VALUES('$nombre', '$email', $fo_permiso_nivel)";
            mysqli_query($this->conexion, $sql) or die("NO insertó el REGISTRO: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se insertó el usuario"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $email = mysqli_real_escape_string($this->conexion, $params->email);
            $fo_permiso_nivel = intval($params->fo_permiso_nivel);
            $id = intval($id);

            $sql = "UPDATE usuario SET nombre = '$nombre', email = '$email', fo_permiso_nivel = $fo_permiso_nivel WHERE id_usuario = $id";
            mysqli_query($this->conexion, $sql) or die("NO editó el REGISTRO: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se editó el usuario"];
        }
    }
?>