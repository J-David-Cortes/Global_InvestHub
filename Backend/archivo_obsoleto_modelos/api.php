<?php
class api {
    private $conexion;

    public function __construct($conexion){
        $this->conexion = $conexion;
    }

    public function consulta(){
        // Corregido: FORM -> FROM y ORDER BY id_api
        $sql = "SELECT * FROM api ORDER BY id_api";
        $res = mysqli_query($this->conexion, $sql) or die("No encontro la tabla API");

        $vec = [];
        // Corregido: mysql_ -> mysqli_ y usamos fetch_assoc
        while($row = mysqli_fetch_assoc($res)){
            $vec[] = $row;                
        }
        return $vec;
    }

    public function eliminar($id){
        $sql = "DELETE FROM api WHERE id_api = $id";
        mysqli_query($this->conexion, $sql) or die("NO elimino el REGISTRO");

        $vec = ['Resultado' => 'OK', 'mensaje' => 'Se elimino el registro'];
        return $vec;
    }

    public function insertar($params){
        $sql = "INSERT INTO api(codigo, fo_broker) VALUES('$params->codigo', $params->fo_broker)";
        mysqli_query($this->conexion, $sql) or die("NO inserto el REGISTRO");

        $vec = ['Resultado' => 'OK', 'mensaje' => 'Se inserto el registro'];
        return $vec;
    }

    public function editar($id, $params){
        // Corregido: id_usuario -> id_api
        $sql = "UPDATE api SET codigo = '$params->codigo', fo_broker = $params->fo_broker WHERE id_api = $id";
        mysqli_query($this->conexion, $sql) or die("NO edito el REGISTRO");

        $vec = ['Resultado' => 'OK', 'mensaje' => 'Se edito el registro'];
        return $vec;
    }
}
?>