<?php
    class pais {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // Usamos LEFT JOIN en lugar de INNER para que, si un país 
            // no tiene departamentos registrados aún, igual aparezca en la lista.
            $sql = "SELECT p.id_pais, p.nombre AS nombre, d.id_departamento, d.nombre AS nombre_departamento
                    FROM pais p
                    LEFT JOIN departamento d ON p.id_pais = d.fo_pais 
                    ORDER BY p.nombre";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta pais: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function consulta2($id_pais){
    $sql = "SELECT id_pais, nombre FROM pais WHERE id_pais = " . intval($id_pais);
    $res = mysqli_query($this->conexion, $sql) or die("Error: " . mysqli_error($this->conexion));
    
    $vec = [];
    while($row = mysqli_fetch_assoc($res)){
        $vec[] = $row;                
        }
        return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM pais WHERE id_pais = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("NO eliminó el REGISTRO: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se eliminó el registro"];
        }

        public function insertar($params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            
            $sql = "INSERT INTO pais(nombre) VALUES('$nombre')";
            mysqli_query($this->conexion, $sql) or die("NO insertó el REGISTRO: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se insertó el registro"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $id = intval($id);

            $sql = "UPDATE pais SET nombre = '$nombre' WHERE id_pais = $id";
            mysqli_query($this->conexion, $sql) or die("NO editó el REGISTRO: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se editó el registro"];
        }
    }
?>