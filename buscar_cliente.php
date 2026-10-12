<?php

require_once "public/admin/auth_admin.php";
require_once "config/conexion.php";

header('Content-Type: application/json; charset=utf-8');

try {

    /* =====================================================
       1. VALIDAR CÉDULA
       ===================================================== */

    if (
        !isset($_GET['cedula']) ||
        !is_string($_GET['cedula']) ||
        trim($_GET['cedula']) === ''
    ) {

        http_response_code(400);

        echo json_encode([
            "error" => true,
            "mensaje" => "Debe ingresar una cédula válida."
        ]);

        exit;
    }

    $cedula = trim($_GET['cedula']);


    /* =====================================================
       2. CONECTAR A LA BASE DE DATOS
       ===================================================== */

    $conexion = (new Conexion())->conectar();


    /* =====================================================
       3. BUSCAR CLIENTE Y SU ÚLTIMO PRÉSTAMO
       ===================================================== */

    $sql = "
        SELECT

            clientes.nombre,
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

        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$cedula]);

    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);


    /* =====================================================
       4. DEVOLVER RESULTADO
       ===================================================== */

    echo json_encode(
        $cliente ?: null,
        JSON_INVALID_UTF8_SUBSTITUTE
    );


} catch (Throwable $e) {

    /* No revelar detalles internos al navegador */
    error_log(
        "Error en buscar_cliente.php: " . $e->getMessage()
    );

    http_response_code(500);

    echo json_encode([
        "error" => true,
        "mensaje" => "No fue posible consultar el cliente. Intente nuevamente."
    ]);

}