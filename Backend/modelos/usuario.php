<?php

    class Usuario{
        //atributos
        private $conexion;

        public function __construct($conexion){
            $this->conexion = $conexion;
        }

        //Metodos

        public function consulta(){
            $sql = "SELECT u.*, p.nombre AS Pais, c.nombre AS Ciudad, pn.nombre AS Permiso, ac.nombre AS Area
                FROM usuario u
                INNER JOIN pais p ON u.fo_pais = p.id_pais
                INNER JOIN ciudad c ON u.fo_ciudad = c.id_ciudad
                INNER JOIN permiso_nivel pn ON u.fo_permiso_nivel = pn.id_permiso_nivel
                INNER JOIN area_cargo ac ON u.fo_area_cargo = ac.id_area_cargo
                ORDER BY u.nombre";
            $res = mysqli_query($this->conexion, $sql) or die("No encontro la tabla USUARIO");

            $vec = [];

            while($row = mysql_fetch_array($res)){
                $vec[] = $row;                
            }

            return $vec;
        }

        public function eliminar($id){
            $sql = "DELETE FROM usurio WHERE id_usuario = $id";
            mysqli_query($this->conexion, $sql) or die("NO elimino el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se elimino el registro";

            return $vec;
        }

        public function insertar($params){
            $sql = "INSERT INTO usuario(nombre, email, clave, fo_pais, fo_ciudad, fo_permiso_nivel, fo_area_cargo) VALUES('$params->nombre', '$params->email', '$params->clave', '$params->fo_pais', '$params->fo_ciudad', $params->fo_permiso_nivel, '$params->fo_area_cargo')";
            mysqli_query($this->conexion, $sql) or die("NO inserto el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se inserto el registro";

            return $vec;
        }

        public function editar($id, $params){
            $sql = "UPDATE usuario SET nombre = '$params->nombre', email = '$params->email', clave = '$params->clave', fo_pais = '$params->fo_pais', fo_ciudad = '$params->fo_ciudad', fo_permiso_nivel = $params->fo_permiso_nivel, fo_area_cargo = '$params->fo_area_cargo' WHERE id_usuario = $id";
            mysqli_query($this->conexion, $sql) or die("NO edito el REGISTRO");

            $vec = [];
            $vec['Resultado'] = "OK";
            $vec['mensaje'] = "Se edito el registro";

            return $vec;
        }
    }
?>