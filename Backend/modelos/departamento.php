<?php
    class departamento{
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
    // Unimos (JOIN) la tabla departamento con la tabla pais
    $sql = "SELECT d.id_departamento, d.nombre AS nombre_departamento, p.nombre AS nombre_pais 
            FROM departamento d
            INNER JOIN pais p ON d.fo_pais = p.id_pais 
            ORDER BY d.nombre";
    
    $res = mysqli_query($this->conexion, $sql) or die("No se pudo consultar la tabla departamento");

    $vec = [];
    while($row = mysqli_fetch_assoc($res)){
        $vec[] = $row;                
    }
    return $vec;
}

        public function eliminar($id){
            $sql = "DELETE FROM departamento WHERE id_departamento = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo eliminar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se elimino el registro"];
        }

        // AGREGADO: Incluimos fo_pais en la inserción
        public function insertar($params){
            $sql = "INSERT INTO departamento(nombre, fo_pais) VALUES('$params->nombre', $params->fo_pais)";
            mysqli_query($this->conexion, $sql) or die("NO se pudo insertar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se inserto el registro"];
        }

        // AGREGADO: Incluimos fo_pais en la edición
        public function editar($id, $params){
            $sql = "UPDATE departamento SET nombre = '$params->nombre', fo_pais = $params->fo_pais WHERE id_departamento = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo editar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se edito el registro"];
        }
    }
?>