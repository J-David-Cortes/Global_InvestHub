<?php
    class Curso {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // Ya no hacemos JOIN porque no existe la columna fo_usuario
            $sql = "SELECT id_curso, titulo FROM curso ORDER BY titulo";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta curso: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM curso WHERE id_curso = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar curso: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó el curso"];
        }

        public function insertar($params){
            $titulo = mysqli_real_escape_string($this->conexion, $params->titulo);
            
            $sql = "INSERT INTO curso(titulo) VALUES('$titulo')";
            mysqli_query($this->conexion, $sql) or die("Error al insertar curso: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó el curso"];
        }

        public function editar($id, $params){
            $titulo = mysqli_real_escape_string($this->conexion, $params->titulo);
            $id = intval($id);

            $sql = "UPDATE curso SET titulo = '$titulo' WHERE id_curso = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar curso: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó el curso"];
        }
    }
?>