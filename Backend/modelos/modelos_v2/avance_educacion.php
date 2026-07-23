<?php
    class AvanceEducacion {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT id_avance, fecha_visto, fo_usuario, fo_leccion FROM avance_educacion ORDER BY id_avance";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta avance_educacion: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM avance_educacion WHERE id_avance = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar avance_educacion: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó el avance de educación"];
        }

        public function insertar($params){
            $fecha_visto = mysqli_real_escape_string($this->conexion, $params->fecha_visto);
            $fo_usuario = intval($params->fo_usuario);
            $fo_leccion = intval($params->fo_leccion);
            
            $sql = "INSERT INTO avance_educacion(fecha_visto, fo_usuario, fo_leccion) VALUES('$fecha_visto', $fo_usuario, $fo_leccion)";
            mysqli_query($this->conexion, $sql) or die("Error al insertar avance_educacion: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó el avance de educación"];
        }

        public function editar($id, $params){
            $fecha_visto = mysqli_real_escape_string($this->conexion, $params->fecha_visto);
            $fo_usuario = intval($params->fo_usuario);
            $fo_leccion = intval($params->fo_leccion);
            $id = intval($id);

            $sql = "UPDATE avance_educacion SET fecha_visto = '$fecha_visto', fo_usuario = $fo_usuario, fo_leccion = $fo_leccion WHERE id_avance = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar avance_educacion: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó el avance de educación"];
        }
    }
?>