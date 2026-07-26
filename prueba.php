<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

echo "Conexión exitosa a Capital Express";