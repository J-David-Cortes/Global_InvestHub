<?php

    class administrador{
        //atributos
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        //Metodos

        public function consulta(){
            // Cambia 'administrador' por la columna real por la que quieras ordenar (ej: id_administrador)
            $sql = "SELECT * FROM administrador ORDER BY id_administrador"; 
            $res = mysqli_query($this->conexion, $sql) or die("Error al consultar: " . mysqli_error     ($this->conexion));

            $vec = [];
            // Usamos fetch_assoc para obtener solo nombres de columna, no índices numéricos
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }

            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM administrador WHERE id_administrador = $id";
            mysqli_query($this->conexion, $sql) or die("NO elimino el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se elimino el registro";

            return $vec;
        }

        public function insertar($params){
            $sql = "INSERT INTO administrador(fo_permiso_nivel, fo_area_cargo) VALUES ($params->permiso_nivel, '$params->area_cargo')";
            mysqli_query($this->conexion, $sql) or die("NO inserto el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se inserto el registro";

            return $vec;
        }

        public function editar($id, $params){
            $sql = "UPDATE administrador SET fo_permiso_nivel = $params->fo_permiso_nivel, fo_area_cargo = '$params->fo_area_cargo' WHERE id_administrador = $id";
            mysqli_query($this->conexion, $sql) or die("NO edito el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se edito el registro";

            return $vec;
        }
    }
?>