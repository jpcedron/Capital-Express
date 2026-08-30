<?php

require_once "../config/conexion.php";

$conexion = (new Conexion())->conectar();

$usuario = "admin";
$password = "1007588499";

$sql = "SELECT password FROM administradores WHERE usuario = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$usuario]);

$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    die("Administrador no encontrado.");
}

if (password_verify($password, $admin["password"])) {
    echo "CONTRASEÑA CORRECTA";
} else {
    echo "CONTRASEÑA INCORRECTA";
}