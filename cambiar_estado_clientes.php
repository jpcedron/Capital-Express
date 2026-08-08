<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

if (
    !isset($_GET['id']) ||
    !isset($_GET['estado'])
) {
    header("Location: gestionar_clientes.php");
    exit;
}

$id = (int) $_GET['id'];
$estado = $_GET['estado'];

// Solo permitimos estos dos estados
if (!in_array($estado, ['activo', 'inactivo'], true)) {
    header("Location: gestionar_clientes.php");
    exit;
}

try {

    $sql = "UPDATE clientes
            SET estado_cliente = ?
            WHERE id = ?";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        $estado,
        $id
    ]);

    header("Location: gestionar_clientes.php");
    exit;

} catch (PDOException $e) {

    die("Error al cambiar el estado del cliente: " . $e->getMessage());

}