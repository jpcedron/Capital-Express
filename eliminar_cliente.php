<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$id = $_GET['id'];



try {

    $conexion->beginTransaction();

    // Buscar la cédula del préstamo
    $sql = "SELECT cedula FROM prestamos WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id]);

    $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prestamo) {
        throw new Exception("Préstamo no encontrado.");
    }

    $cedula = $prestamo['cedula'];


    // Obtener todos los préstamos del cliente
    $sql = "SELECT id FROM prestamos WHERE cedula = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$cedula]);

    $prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Eliminar pagos y cuotas de cada préstamo
    foreach ($prestamos as $prestamo) {

        $prestamo_id = $prestamo['id'];

        // Eliminar pagos
        $sql = "DELETE FROM pagos WHERE prestamo_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$prestamo_id]);

        // Eliminar cuotas
        $sql = "DELETE FROM cuotas WHERE prestamo_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$prestamo_id]);
    }

    // Eliminar todos los préstamos del cliente
    $sql = "DELETE FROM prestamos WHERE cedula = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$cedula]);



    // Eliminar el cliente
    $sql = "DELETE FROM clientes WHERE cedula = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$cedula]);

    $conexion->commit();

    header("Location: gestionar_clientes.php");
    exit;

} catch (Exception $e) {

    $conexion->rollBack();
    die($e->getMessage());

}