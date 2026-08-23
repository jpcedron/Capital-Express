<?php

require_once "auth_cliente.php";
require_once "../config/conexion.php";

$conexion = (new Conexion())->conectar();

$cedula = $_SESSION["cliente_cedula"];

$sql = "SELECT p.*
        FROM prestamos p
        INNER JOIN clientes c ON c.id = p.cliente_id
        WHERE c.cedula = ?
        AND p.estado IN ('Activo','Mora')
        ORDER BY p.id DESC
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->execute([$cedula]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestamo) {

    require_once "alerta_sin_recibo.php";
    exit;

}

$id = $prestamo["id"];


// Obtener la última cuota pagada
$sql = "SELECT MAX(fecha_vencimiento) AS ultima_cuota
        FROM cuotas
        WHERE prestamo_id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$ultimaCuota = $stmt->fetch(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recibo Capital Express</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/panel_de_usuario.css">
</head>

<body class="bg-light">

    <!-- Contenedor principal centrado -->
    <div class="container mt-5 mb-5 d-flex flex-column align-items-center">
        
        <!-- Tarjeta del Recibo -->
        <div class="card shadow w-100" style="max-width: 500px;">
            
            <div class="card-header text-white text-center py-3" style="background: linear-gradient(135deg, #1a3560, #0d1b3a);">
                <h2 class="mb-0 fs-3">CAPITAL EXPRESS</h2>
                <p class="mb-0 small">COMPROBANTE DE PRÉSTAMO</p>
            </div>

            <div class="card-body">
                
                <div class="mb-3">
                    <p class="mb-1"><strong>Cliente:</strong> <?= $prestamo['nombre']; ?></p>
                    <p class="mb-1"><strong>Monto:</strong> $<?= number_format($prestamo['monto'], 0, ',', '.'); ?></p>
                    <p class="mb-1"><strong>Interés:</strong> <?= $prestamo['interes']; ?>%</p>
                    <p class="mb-1"><strong>Fecha préstamo:</strong> <?= date("d/m/Y", strtotime($prestamo['fecha_prestamo'])); ?></p>
                    <p class="mb-1">
                        <strong>Última cuota:</strong> 
                        <?= !empty($ultimaCuota['ultima_cuota'])
                            ? date("d/m/Y", strtotime($ultimaCuota['ultima_cuota']))
                            : "No registrada"; ?>
                    </p>
                    <p class="mb-1"><strong>Mora:</strong> <span class="text-danger">$<?= number_format($prestamo['mora'], 0, ',', '.'); ?></span></p>
                    <p class="mb-1">
                        <strong>Estado:</strong> 
                        <?php if($prestamo['estado'] == "Pagado"): ?>
                            <span class="badge bg-success">Pagado</span>
                        <?php elseif($prestamo['estado'] == "Mora"): ?>
                            <span class="badge bg-danger">Mora</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Activo</span>
                        <?php endif; ?>
                    </p>
                    
                    <hr class="my-2">
                    
                    <p class="mb-0"><strong>Total pagado:</strong> <span class="text-success fw-bold">$<?= number_format($prestamo['abonado'], 0, ',', '.'); ?></span></p>
                </div>

                <hr class="my-2">

                <div class="d-grid gap-2 my-3">

                   <a href="descargar_recibo.php?id=<?= $prestamo['id']; ?>" class="btn-ce">
                        <i class="bi bi-file-earmark-arrow-down-fill"></i>
                        Descargar PDF
                    </a>

                    <a href="panel_de_usuario.php" class="btn-ce">
                        <i class="bi bi-arrow-left-circle"></i>
                        Regresar al Panel
                    </a>
                </div>

                <hr class="my-2">

                <div class="text-center text-muted small mt-2">
                    <p class="mb-0">Documento informativo del préstamo.</p>
                    <strong class="text-secondary">Capital Express</strong>
                </div>

            </div>
        </div>
    </div>

</body>

</html>