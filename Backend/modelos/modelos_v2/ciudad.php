<?php
    class ciudad {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // JOIN para traer el nombre del departamento
            $sql = "SELECT c.id_ciudad, c.nombre AS nombre, c.fo_departamento, d.nombre AS nombre_departamento 
                    FROM ciudad c
                    INNER JOIN departamento d ON c.fo_departamento = d.id_departamento 
                    ORDER BY c.nombre";
            
            $res = mysqli_query($this->conexion, $sql) or die("No encontró la tabla ciudad: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function consulta2($id_departamento){
            // JOIN para mantener consistencia con los datos que espera el frontend
            $sql = "SELECT c.id_ciudad, c.nombre AS nombre, c.fo_departamento, d.nombre AS nombre_departamento 
                    FROM ciudad c
                    INNER JOIN departamento d ON c.fo_departamento = d.id_departamento 
                    WHERE c.fo_departamento = " . intval($id_departamento) . " 
                    ORDER BY c.nombre";
            
            $res = mysqli_query($this->conexion, $sql) or die("No encontró la tabla ciudad: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM ciudad WHERE id_ciudad = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("NO eliminó el REGISTRO: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se eliminó el registro"];
        }

        public function insertar($params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $fo_departamento = intval($params->departamento);

            $sql = "INSERT INTO ciudad(nombre, fo_departamento) VALUES('$nombre', $fo_departamento)";
            mysqli_query($this->conexion, $sql) or die("NO insertó el REGISTRO: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se insertó el registro"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $fo_departamento = intval($params->departamento);
            $id = intval($id);

            $sql = "UPDATE ciudad SET nombre = '$nombre', fo_departamento = $fo_departamento WHERE id_ciudad = $id";
            mysqli_query($this->conexion, $sql) or die("NO editó el REGISTRO: " . mysqli_error($this->conexion));

            return ['Resultado' => "OK", 'mensaje' => "Se editó el registro"];
        }
    }
?>