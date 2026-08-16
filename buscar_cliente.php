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

   $sql = "SELECT
            clientes.id AS cliente_id,
            clientes.nombre,
            clientes.cedula,
            clientes.telefono,
            clientes.direccion,
            clientes.estado_cliente,

            prestamos.id AS prestamo_id,
            prestamos.monto,
            prestamos.abonado,
            prestamos.pendiente,
            prestamos.mora,
            prestamos.frecuencia,
            prestamos.estado AS estado,

            (
                SELECT MAX(cuotas.fecha_vencimiento)
                FROM cuotas
                WHERE cuotas.prestamo_id = prestamos.id
            ) AS fecha_limite

        FROM clientes

        LEFT JOIN prestamos
            ON prestamos.cliente_id = clientes.id

        WHERE clientes.cedula = ?

        ORDER BY prestamos.id DESC

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