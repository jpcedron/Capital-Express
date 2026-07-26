<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$id = $_GET['id'];

try{

    $conexion->beginTransaction();

    // Eliminar pagos
    $sql = "DELETE FROM pagos
            WHERE prestamo_id = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id]);

    // Eliminar préstamo
    $sql = "DELETE FROM prestamos
            WHERE id = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id]);

    $conexion->commit();

    header("Location: gestionar_clientes.php");
    exit;

}catch(Exception $e){

    $conexion->rollBack();

    echo $e->getMessage();

}