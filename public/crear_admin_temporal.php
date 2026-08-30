<?php

require_once "../config/conexion.php";

$conexion = (new Conexion())->conectar();

$usuario = "admin";
$password = "1007588499";
$nombre = "Administrador";

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO administradores
        (usuario, password, nombre, estado)
        VALUES (?, ?, ?, 'Activo')";

$stmt = $conexion->prepare($sql);

$stmt->execute([
    $usuario,
    $passwordHash,
    $nombre
]);

echo "Administrador creado correctamente.";