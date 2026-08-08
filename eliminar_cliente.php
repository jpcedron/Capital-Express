<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();


// Verificar ID del cliente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Cliente no válido.");
}

$id_cliente = (int) $_GET['id'];


try {

    $conexion->beginTransaction();


    
    // 1. Buscar el cliente
    

    $sql = "SELECT id, cedula
            FROM clientes
            WHERE id = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id_cliente]);

    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$cliente) {
        throw new Exception("Cliente no encontrado.");
    }


    $cedula = $cliente['cedula'];


    // 2. Buscar todos los préstamos del cliente
    

    $sql = "SELECT id
            FROM prestamos
            WHERE cedula = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$cedula]);

    $prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);


    
    // 3. Eliminar pagos y cuotas
    

    foreach ($prestamos as $prestamo) {

        $prestamo_id = $prestamo['id'];


        // Eliminar pagos
        $sql = "DELETE FROM pagos
                WHERE prestamo_id = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([$prestamo_id]);


        // Eliminar cuotas
        $sql = "DELETE FROM cuotas
                WHERE prestamo_id = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([$prestamo_id]);
    }


    
    // 4. Eliminar préstamos
    

    $sql = "DELETE FROM prestamos
            WHERE cedula = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$cedula]);


    
    // 5. Eliminar cliente
    

    $sql = "DELETE FROM clientes
            WHERE id = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id_cliente]);


    // 6. Confirmar transacción 
    $conexion->commit();


    header("Location: gestionar_clientes.php");
    exit;


} catch (Exception $e) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    die($e->getMessage());
}