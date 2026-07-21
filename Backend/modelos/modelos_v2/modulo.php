<?php
    class Modulo {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT id_modulo, nombre FROM modulo ORDER BY nombre";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta modulo: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM modulo WHERE id_modulo = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar módulo: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó el módulo"];
        }

        public function insertar($params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            
            $sql = "INSERT INTO modulo(nombre) VALUES('$nombre')";
            mysqli_query($this->conexion, $sql) or die("Error al insertar módulo: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó el módulo"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $id = intval($id);

            $sql = "UPDATE modulo SET nombre = '$nombre' WHERE id_modulo = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar módulo: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó el módulo"];
        }
    }
?>