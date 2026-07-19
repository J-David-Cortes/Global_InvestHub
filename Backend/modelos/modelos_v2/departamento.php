<?php
    class departamento {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // Usamos INNER JOIN y AS para mantener la compatibilidad con tu frontend
            $sql = "SELECT d.id_departamento, d.nombre AS nombre_departamento, 
                    p.id_pais, p.nombre AS nombre_pais 
                    FROM departamento d
                    INNER JOIN pais p ON d.fo_pais = p.id_pais 
                    ORDER BY d.nombre";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en la consulta: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;             
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM departamento WHERE id_departamento = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se eliminó el registro"];
        }

        public function insertar($params){
            // Usamos mysqli_real_escape_string para prevenir errores de sintaxis
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $fo_pais = intval($params->fo_pais);
            
            $sql = "INSERT INTO departamento(nombre, fo_pais) VALUES('$nombre', $fo_pais)";
            mysqli_query($this->conexion, $sql) or die("Error al insertar: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se insertó el registro"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $fo_pais = intval($params->fo_pais);
            $id = intval($id);
            
            $sql = "UPDATE departamento SET nombre = '$nombre', fo_pais = $fo_pais WHERE id_departamento = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se editó el registro"];
        }
    }
?>