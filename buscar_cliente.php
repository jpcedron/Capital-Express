<?php

require_once "config/conexion.php";

header('Content-Type: application/json');

try {

    $conexion = (new Conexion())->conectar();

    if (!isset($_GET['cedula']) || empty(trim($_GET['cedula']))) {
        echo json_encode(null);
        exit;
    }

    $cedula = trim($_GET['cedula']);

    $sql = "SELECT *
            FROM prestamos
            WHERE cedula = ?
            ORDER BY id DESC
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$cedula]);

    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        echo json_encode($cliente);
    } else {
        echo json_encode(null);
    }

} catch (PDOException $e) {

    echo json_encode([
        "error" => true,
        "mensaje" => $e->getMessage()
    ]);

}