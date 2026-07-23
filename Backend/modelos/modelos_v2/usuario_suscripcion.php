<?php
    class UsuarioSuscripcion {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT id_registro, fecha_inicio, fecha_fin, fo_usuario, fo_suscripcion, fo_pasarela, fo_estado FROM usuario_suscripcion ORDER BY id_registro";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta usuario_suscripcion: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM usuario_suscripcion WHERE id_registro = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar usuario_suscripcion: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó la suscripción del usuario"];
        }

        public function insertar($params){
            $fecha_inicio = mysqli_real_escape_string($this->conexion, $params->fecha_inicio);
            $fecha_fin = mysqli_real_escape_string($this->conexion, $params->fecha_fin);
            $fo_usuario = intval($params->fo_usuario);
            $fo_suscripcion = intval($params->fo_suscripcion);
            $fo_pasarela = intval($params->fo_pasarela);
            $fo_estado = intval($params->fo_estado);
            
            $sql = "INSERT INTO usuario_suscripcion(fecha_inicio, fecha_fin, fo_usuario, fo_suscripcion, fo_pasarela, fo_estado) VALUES('$fecha_inicio', '$fecha_fin', $fo_usuario, $fo_suscripcion, $fo_pasarela, $fo_estado)";
            mysqli_query($this->conexion, $sql) or die("Error al insertar usuario_suscripcion: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó la suscripción del usuario"];
        }

        public function editar($id, $params){
            $fecha_inicio = mysqli_real_escape_string($this->conexion, $params->fecha_inicio);
            $fecha_fin = mysqli_real_escape_string($this->conexion, $params->fecha_fin);
            $fo_usuario = intval($params->fo_usuario);
            $fo_suscripcion = intval($params->fo_suscripcion);
            $fo_pasarela = intval($params->fo_pasarela);
            $fo_estado = intval($params->fo_estado);
            $id = intval($id);

            $sql = "UPDATE usuario_suscripcion SET fecha_inicio = '$fecha_inicio', fecha_fin = '$fecha_fin', fo_usuario = $fo_usuario, fo_suscripcion = $fo_suscripcion, fo_pasarela = $fo_pasarela, fo_estado = $fo_estado WHERE id_registro = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar usuario_suscripcion: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó la suscripción del usuario"];
        }
    }
?>