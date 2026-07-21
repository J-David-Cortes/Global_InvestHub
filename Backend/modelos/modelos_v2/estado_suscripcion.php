<?php
    class EstadoSuscripcion {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT id_estado, nombre FROM estado_suscripcion ORDER BY nombre";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta estado_suscripcion: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM estado_suscripcion WHERE id_estado = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar estado de suscripción: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó el estado de suscripción"];
        }

        public function insertar($params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            
            $sql = "INSERT INTO estado_suscripcion(nombre) VALUES('$nombre')";
            mysqli_query($this->conexion, $sql) or die("Error al insertar estado de suscripción: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó el estado de suscripción"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $id = intval($id);

            $sql = "UPDATE estado_suscripcion SET nombre = '$nombre' WHERE id_estado = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar estado de suscripción: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó el estado de suscripción"];
        }
    }
?>