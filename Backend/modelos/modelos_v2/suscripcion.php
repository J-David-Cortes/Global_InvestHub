<?php
    class Suscripcion {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT id_suscripcion, nombre, precio FROM suscripcion ORDER BY nombre";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta suscripcion: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM suscripcion WHERE id_suscripcion = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar suscripción: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó la suscripción"];
        }

        public function insertar($params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $precio = floatval($params->precio);
            
            $sql = "INSERT INTO suscripcion(nombre, precio) VALUES('$nombre', $precio)";
            mysqli_query($this->conexion, $sql) or die("Error al insertar suscripción: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó la suscripción"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $precio = floatval($params->precio);
            $id = intval($id);

            $sql = "UPDATE suscripcion SET nombre = '$nombre', precio = $precio WHERE id_suscripcion = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar suscripción: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó la suscripción"];
        }
    }
?>