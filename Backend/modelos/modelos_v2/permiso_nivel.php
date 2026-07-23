<?php
    class PermisoNivel {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT id_permiso, nombre FROM permiso_nivel ORDER BY nombre";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta permiso_nivel: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM permiso_nivel WHERE id_permiso = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar permiso de nivel: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó el permiso de nivel"];
        }

        public function insertar($params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            
            $sql = "INSERT INTO permiso_nivel(nombre) VALUES('$nombre')";
            mysqli_query($this->conexion, $sql) or die("Error al insertar permiso de nivel: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó el permiso de nivel"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $id = intval($id);

            $sql = "UPDATE permiso_nivel SET nombre = '$nombre' WHERE id_permiso = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar permiso de nivel: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó el permiso de nivel"];
        }
    }
?>