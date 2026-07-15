<?php
    class titulo {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // Corregido: Ajuste en la consulta para relacionar correctamente las tablas
            $sql = "SELECT e.*, u.nombre AS Usuario, t.nombre AS Nombre_Titulo 
                    FROM educacion e 
                    INNER JOIN usuario u ON e.fo_usuario = u.id_usuario 
                    INNER JOIN titulo t ON e.fo_titulo = t.id_titulo";
            $res = mysqli_query($this->conexion, $sql) or die("No se pudo consultar la tabla");

            $vec = [];
            // Corregido: mysql_fetch_array -> mysqli_fetch_assoc
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM titulo WHERE id_titulo = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo eliminar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se elimino el registro"];
        }

        public function insertar($params){
            // Corregido: Se eliminaron comillas de las llaves foráneas (INT)
            $sql = "INSERT INTO titulo(fo_nivel_educacion, fo_leccion_educacion, fo_avance_educacion) 
                    VALUES($params->fo_nivel_educacion, $params->fo_leccion_educacion, $params->fo_avance_educacion)";
            mysqli_query($this->conexion, $sql) or die("NO se pudo insertar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se inserto el registro"];
        }

        public function editar($id, $params){
            // Corregido: Se eliminaron comillas de las llaves foráneas (INT)
            $sql = "UPDATE titulo SET fo_nivel_educacion = $params->fo_nivel_educacion, 
                    fo_leccion_educacion = $params->fo_leccion_educacion, 
                    fo_avance_educacion = $params->fo_avance_educacion 
                    WHERE id_titulo = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo editar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se edito el registro"];
        }
    }
?>