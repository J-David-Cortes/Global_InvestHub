<?php
    class Broker {
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        public function consulta(){
            $sql = "SELECT id_broker, nombre FROM broker ORDER BY nombre";
            
            $res = mysqli_query($this->conexion, $sql) or die("Error en consulta broker: " . mysqli_error($this->conexion));

            $vec = [];
            while($row = mysqli_fetch_assoc($res)){
                $vec[] = $row;                
            }
            return $vec;
        }

        // Devuelve el id_broker del broker con ese nombre, creandolo si no
        // existe. La comparacion es insensible a mayusculas/tildes/espacios
        // finales por la colacion de la columna, y el indice UNIQUE
        // uq_broker_nombre garantiza que no haya duplicados.
        // Respuesta: ['Resultado' => 'OK', 'id_broker' => int, 'creado' => bool]
        //         o ['Resultado' => 'Error', 'mensaje' => ...]
        public function obtenerOCrear($nombre){
            if(!is_string($nombre)){
                return ['Resultado' => "Error", 'mensaje' => "Datos inválidos"];
            }

            $nombre = trim($nombre);
            if($nombre === ''){
                return ['Resultado' => "Error", 'mensaje' => "El nombre del broker no puede estar vacío"];
            }
            // La columna es varchar(100): se cuentan caracteres, no bytes.
            // (El sql_mode no es estricto: sin este chequeo MariaDB truncaria en silencio.)
            if(mb_strlen($nombre, 'UTF-8') > 100){
                return ['Resultado' => "Error", 'mensaje' => "El nombre del broker es demasiado largo (máximo 100 caracteres)"];
            }

            $id = $this->buscarPorNombre($nombre, false);
            if($id !== null){
                return ['Resultado' => "OK", 'id_broker' => $id, 'creado' => false];
            }

            $nombreSql = mysqli_real_escape_string($this->conexion, $nombre);
            $errno = 0;
            try {
                $ok = mysqli_query($this->conexion, "INSERT INTO broker(nombre) VALUES('$nombreSql')");
                if(!$ok){
                    $errno = mysqli_errno($this->conexion);
                }
            } catch (mysqli_sql_exception $e) {
                // PHP >= 8.1 lanza excepcion en vez de devolver false.
                $ok = false;
                $errno = (int) $e->getCode();
            }

            if($ok){
                return ['Resultado' => "OK", 'id_broker' => (int) mysqli_insert_id($this->conexion), 'creado' => true];
            }

            // Carrera: otro usuario creo el mismo broker entre nuestro SELECT
            // y el INSERT (1062 = clave duplicada). Se vuelve a buscar y se
            // devuelve ese id en vez de fallar.
            if($errno === 1062){
                $id = $this->buscarPorNombre($nombre, true);
                if($id !== null){
                    return ['Resultado' => "OK", 'id_broker' => $id, 'creado' => false];
                }
            }

            die("Error al crear broker: " . mysqli_error($this->conexion));
        }

        // $bloqueo=true agrega LOCK IN SHARE MODE: dentro de una transaccion
        // (REPEATABLE READ) un SELECT normal no veria una fila que otro
        // usuario acaba de confirmar; la lectura con bloqueo si.
        private function buscarPorNombre($nombre, $bloqueo){
            $nombreSql = mysqli_real_escape_string($this->conexion, $nombre);
            $sql = "SELECT id_broker FROM broker WHERE nombre = '$nombreSql' LIMIT 1" . ($bloqueo ? " LOCK IN SHARE MODE" : "");
            $res = mysqli_query($this->conexion, $sql) or die("Error al buscar broker: " . mysqli_error($this->conexion));
            $fila = mysqli_fetch_assoc($res);
            return $fila !== null ? (int) $fila['id_broker'] : null;
        }

        public function eliminar($id){
            $sql = "DELETE FROM broker WHERE id_broker = " . intval($id);
            mysqli_query($this->conexion, $sql) or die("Error al eliminar broker: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se eliminó el broker"];
        }

        public function insertar($params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            
            $sql = "INSERT INTO broker(nombre) VALUES('$nombre')";
            mysqli_query($this->conexion, $sql) or die("Error al insertar broker: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se insertó el broker"];
        }

        public function editar($id, $params){
            $nombre = mysqli_real_escape_string($this->conexion, $params->nombre);
            $id = intval($id);

            $sql = "UPDATE broker SET nombre = '$nombre' WHERE id_broker = $id";
            mysqli_query($this->conexion, $sql) or die("Error al editar broker: " . mysqli_error($this->conexion));
            return ['Resultado' => "OK", 'mensaje' => "Se editó el broker"];
        }
    }
?>