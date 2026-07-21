<?php
    class Leccion {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT id_leccion, titulo, fo_modulo FROM leccion ORDER BY titulo";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta leccion: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM leccion WHERE id_leccion = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar lección: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó la lección"];
        }

        public function insertar($params){
            $titulo = mysqli_real_escape_string($this->conexion, $params->titulo);
            $fo_modulo = intval($params->fo_modulo);
            
            $sql = "INSERT INTO leccion(titulo, fo_modulo) VALUES('$titulo', $fo_modulo)";
            mysqli_query($this->conexion, $sql) or die("Error al insertar lección: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó la lección"];
        }

        public function editar($id, $params){
            $titulo = mysqli_real_escape_string($this->conexion, $params->titulo);
            $fo_modulo = intval($params->fo_modulo);
            $id = intval($id);

            $sql = "UPDATE leccion SET titulo = '$titulo', fo_modulo = $fo_modulo WHERE id_leccion = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar lección: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó la lección"];
        }
    }
?>