<?php
require 'app/libs/Config.php';
$conexion = Config::getConexion();
if ($conexion) {
    echo "Conexión exitosa a la base de datos.";
} else {
    echo "Conexión fallida a la base de datos.";
}
