<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$sql = "SELECT id, cedula
        FROM clientes
        WHERE password IS NULL
           OR password = ''";

$stmt = $conexion->prepare($sql);
$stmt->execute();

$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($clientes as $cliente) {

    $password = password_hash($cliente['cedula'], PASSWORD_DEFAULT);

    $sqlUpdate = "UPDATE clientes
                  SET password = ?
                  WHERE id = ?";

    $stmtUpdate = $conexion->prepare($sqlUpdate);

    $stmtUpdate->execute([
        $password,
        $cliente['id']
    ]);
}

echo "Contraseñas actualizadas correctamente.";