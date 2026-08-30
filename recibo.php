<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$id = $_GET['id'];

$sql = "SELECT 
            prestamos.*,
            clientes.nombre AS nombre_cliente,
            clientes.cedula,
            clientes.telefono,
            clientes.direccion
        FROM prestamos
        INNER JOIN clientes 
            ON prestamos.cliente_id = clientes.id
        WHERE prestamos.id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestamo) {
    die("Préstamo no encontrado.");
}


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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Comprobante de Préstamo - Capital Express</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/recibo.css">
</head>
<body>

<div class="topbar">
    <div class="topbar-inner">
        <div class="brand">
            <div class="brand-mark brand-heading">CE</div>
            <div class="brand-text">
                <div class="brand-title brand-heading">Capital Express</div>
                <div class="brand-sub">PANEL ADMINISTRATIVO</div>
            </div>
        </div>
    </div>
</div>

<div class="page-wrap">

    <div class="breadcrumb-row">
        <a href="listado.php">Préstamos</a>
        <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
        <a href="cartilla.php?id=<?= $prestamo['id']; ?>">Cartilla</a>
        <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
        <span class="current">Comprobante</span>
    </div>

    <div class="page-head">
        <div>
            <h1 class="page-title">Comprobante de préstamo</h1>
            <div class="page-desc">Resumen detallado y estado actual del crédito</div>
        </div>
        <div class="count-tag">
            <i class="bi bi-file-earmark-text"></i>
            ID Crédito: #<?= htmlspecialchars($prestamo['id']); ?>
        </div>
    </div>

    <div class="card-custom mx-auto" style="max-width: 620px;">

        <div class="card-section-head">
            <div class="card-section-title">
                <i class="bi bi-receipt"></i>
                Detalles del Comprobante
            </div>
            <div>
                <?php if($prestamo['estado'] == "Pagado"): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-semibold">
                        <i class="bi bi-check-circle-fill"></i> Pagado
                    </span>
                <?php elseif($prestamo['estado'] == "Mora"): ?>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-semibold">
                        <i class="bi bi-exclamation-triangle-fill"></i> Mora
                    </span>
                <?php else: ?>
                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-3 py-1 fw-semibold">
                        <i class="bi bi-clock-history"></i> Activo
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="p-4">
            <div class="row g-3">
                <div class="col-sm-6">
                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Cliente</small>
                    <div class="fw-semibold text-navy fs-6"><?= htmlspecialchars($prestamo['nombre_cliente']); ?></div>
                </div>

                <div class="col-sm-6">
                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Cédula / Documento</small>
                    <div class="fw-semibold text-navy fs-6"><?= htmlspecialchars($prestamo['cedula']); ?></div>
                </div>

                <div class="col-12"><hr class="my-1" style="border-color: #e3e7f0;"></div>

                <div class="col-sm-6">
                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Monto Prestado</small>
                    <div class="fw-bold cell-balance fs-5">$<?= number_format($prestamo['monto'], 0, ',', '.'); ?></div>
                </div>

                <div class="col-sm-6">
                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Tasa de Interés</small>
                    <div class="fw-semibold text-navy fs-6"><?= $prestamo['interes']; ?>%</div>
                </div>

                <div class="col-sm-6">
                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Fecha Préstamo</small>
                    <div class="cell-date fs-6"><?= date("d/m/Y", strtotime($prestamo['fecha_prestamo'])); ?></div>
                </div>

                <div class="col-sm-6">
                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Última Cuota Registrada</small>
                    <div class="cell-date fs-6">
                        <?= !empty($ultimaCuota['ultima_cuota'])
                            ? date("d/m/Y", strtotime($ultimaCuota['ultima_cuota']))
                            : "No registrada"; ?>
                    </div>
                </div>

                <div class="col-12"><hr class="my-1" style="border-color: #e3e7f0;"></div>

                <div class="col-sm-6">
                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Mora Acumulada</small>
                    <div class="fw-bold text-danger fs-6">$<?= number_format($prestamo['mora'], 0, ',', '.'); ?></div>
                </div>

                <div class="col-sm-6">
                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Total Pagado</small>
                    <div class="fw-bold cell-pay fs-5">$<?= number_format($prestamo['abonado'], 0, ',', '.'); ?></div>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2 mt-4 pt-2">
                <a href="descargar_recibo.php?id=<?= $prestamo['id']; ?>" class="btn-back-custom justify-center w-100" style="background: var(--navy-deep); color: #ffffff; border-color: var(--navy-deep);">
                    <i class="bi bi-file-earmark-pdf-fill" style="color: var(--gold);"></i>
                    Descargar PDF
                </a>
            </div>
        </div>

        <div class="card-footer-actions">
            <div class="footer-note">
                <i class="bi bi-info-circle"></i>
                Documento informativo generado por Capital Express
            </div>
            <a href="cartilla.php?id=<?= $prestamo['id']; ?>" class="btn-back-custom">
                <i class="bi bi-arrow-left"></i>
                Volver a la Cartilla
            </a>
        </div>

    </div>

</div>

</body>
</html>