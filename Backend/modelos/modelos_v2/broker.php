<?php
    class Broker {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT id_broker, nombre FROM broker ORDER BY nombre";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta broker: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM broker WHERE id_broker = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar broker: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó el broker"];
        }

        public function insertar($params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            
            $sql = "INSERT INTO broker(nombre) VALUES('$nombre')";
            mysqli_query($this->conexion, $sql) or die("Error al insertar broker: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó el broker"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $id = intval($id);

            $sql = "UPDATE broker SET nombre = '$nombre' WHERE id_broker = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar broker: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó el broker"];
        }
    }
?>