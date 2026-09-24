<?php
    class Suscripcion {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT id_suscripcion, nombre, precio FROM suscripcion ORDER BY nombre";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta suscripcion: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        // Catalogo completo de planes para las tarjetas de comparacion de Billing
        // (no depende del usuario). Se ordena por precio, no por nombre.
        public function listaPlanes(){
            $sql = "SELECT id_suscripcion AS id, nombre, precio, limite_bots AS limiteBots
                    FROM suscripcion
                    ORDER BY precio, id_suscripcion";

            $res = mysqli_query($this->conexion, $sql) or die("Error en listaPlanes: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $row['id'] = (int) $row['id'];
                $row['precio'] = (float) $row['precio'];
                // NULL = ilimitado (Enterprise), 0 = sin bots: no convertir NULL a 0.
                $row['limiteBots'] = $row['limiteBots'] !== null ? (int) $row['limiteBots'] : null;
                $vec[] = $row;
            }
            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM suscripcion WHERE id_suscripcion = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar suscripción: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó la suscripción"];
        }

        public function insertar($params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $precio = floatval($params->precio);
            
            $sql = "INSERT INTO suscripcion(nombre, precio) VALUES('$nombre', $precio)";
            mysqli_query($this->conexion, $sql) or die("Error al insertar suscripción: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó la suscripción"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $precio = floatval($params->precio);
            $id = intval($id);

            $sql = "UPDATE suscripcion SET nombre = '$nombre', precio = $precio WHERE id_suscripcion = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar suscripción: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó la suscripción"];
        }
    }
?>