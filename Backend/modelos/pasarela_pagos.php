<?php
    class pasarela_pagos {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            // Corregido: FORM -> FROM y columna de ordenamiento
            $sql = "SELECT * FROM pasarela_pagos ORDER BY id_pasarela_pagos";
            $res = mysqli_query($this->conexion, $sql) or die("No se pudo consultar la tabla pasarela_pagos");

            $vec = [];
            // Corregido: mysql_fetch_array -> mysqli_fetch_assoc
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM pasarela_pagos WHERE id_pasarela_pagos = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo eliminar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se elimino el registro"];
        }

        public function insertar($params){
            // Corregido: fo_suscripcion tratada como entero (sin comillas)
            $sql = "INSERT INTO pasarela_pagos(fo_suscripcion) VALUES($params->suscripcion)";
            mysqli_query($this->conexion, $sql) or die("NO se pudo insertar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se inserto el registro"];
        }

        public function editar($id, $params){
            // Corregido: fo_suscripcion tratada como entero
            $sql = "UPDATE pasarela_pagos SET fo_suscripcion = $params->suscripcion WHERE id_pasarela_pagos = $id";
            mysqli_query($this->conexion, $sql) or die("NO se pudo editar el registro");

            return ['Resultado' => "OK", 'mensaje' => "Se edito el registro"];
        }
    }
?>