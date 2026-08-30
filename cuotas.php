<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

if (!isset($_GET['id'])) {
    die("Préstamo no encontrado.");
}

$prestamo_id = $_GET['id'];

/* Obtener información del préstamo */

$sql = "SELECT
            prestamos.*,
            clientes.nombre AS cliente_nombre,
            clientes.cedula AS cliente_cedula
        FROM prestamos
        INNER JOIN clientes
            ON prestamos.cliente_id = clientes.id
        WHERE prestamos.id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$prestamo_id]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestamo) {
    die("Préstamo no encontrado.");
}

/* Obtener cuotas */

$sql = "SELECT *
        FROM cuotas
        WHERE prestamo_id=?
        ORDER BY numero_cuota ASC";

$stmt = $conexion->prepare($sql);
$stmt->execute([$prestamo_id]);

$cuotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Control de Cuotas · Capital Express</title>
<!-- Bootstrap 5.3 + Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<!-- Tipografías del sistema de diseño (idénticas a la cartilla) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<!-- Base compartida con la cartilla + extras de cuotas -->
<link rel="stylesheet" href="css/cartilla.css">
<link rel="stylesheet" href="css/cuotas.css">
</head>

<body>

<!-- ===== TOP BAR (igual a la cartilla) ===== -->
<div class="topbar">
    <div class="topbar-inner">
        <div class="brand-row">
            <div class="brand-mark brand-heading">CE</div>
            <div>
                <p class="brand-name brand-heading">Capital Express</p>
                <p class="brand-sub">Finanzas con confianza</p>
            </div>
        </div>
    </div>
</div>

<div class="page-wrap">

    <!-- ===== BREADCRUMB ===== -->
    <div class="breadcrumb-row">
        <a href="public/dashboard.php">Panel</a>
        <span class="sep">/</span>
        <a href="listado.php">Préstamos</a>
        <span class="sep">/</span>
        <a href="cartilla.php?id=<?= htmlspecialchars($prestamo['id']) ?>">Cartilla</a>
        <span class="sep">/</span>
        <span class="current">Cuotas</span>
    </div>

    <!-- ===== ENCABEZADO ===== -->
    <div class="page-head">
        <div>
            <h1 class="page-title">Control de cuotas</h1>
            <p class="page-desc">Plan de pagos, vencimientos y estado de cada cuota del préstamo.</p>
        </div>

        <div class="head-actions">
            <span class="client-id-tag">
                <i class="bi bi-calendar-check"></i>
                Préstamo #<?= htmlspecialchars($prestamo['id']) ?>
            </span>

        </div>
    </div>

    <!-- ===== INFORMACIÓN DEL PRÉSTAMO ===== -->
    <div class="edit-card">

        <div class="card-section-head">
            <p class="card-section-title">
                <span class="icon-box"><i class="bi bi-person-badge"></i></span>
                Información del préstamo
            </p>

            <?php if ($prestamo['estado'] == "Pagado"): ?>
                <span class="status-badge is-pagado">Pagado</span>
            <?php elseif ($prestamo['estado'] == "Mora"): ?>
                <span class="status-badge is-mora">Mora</span>
            <?php else: ?>
                <span class="status-badge is-activo">Activo</span>
            <?php endif; ?>
        </div>

        <div class="card-body-custom">

            <div class="info-grid">

                <div class="info-item">
                    <span class="info-label">Cliente</span>
                    <span class="info-value"><?= htmlspecialchars($prestamo['cliente_nombre']) ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Cédula</span>
                    <span class="info-value"><?= htmlspecialchars($prestamo['cliente_cedula']) ?></span>
                </div>

            </div>

            <hr class="divider">

            <p class="field-group-title">Resumen financiero</p>

            <!-- ===== RESUMEN FINANCIERO ===== -->
            <div class="money-grid">

                <div class="money-box">
                    <span class="money-label">Monto</span>
                    <span class="money-value">$<?= number_format($prestamo['monto'], 0, ",", ".") ?></span>
                </div>

                <div class="money-box is-success">
                    <span class="money-label">Abonado</span>
                    <span class="money-value">$<?= number_format($prestamo['abonado'], 0, ",", ".") ?></span>
                </div>

                <div class="money-box is-gold">
                    <span class="money-label">Pendiente</span>
                    <span class="money-value">$<?= number_format($prestamo['pendiente'], 0, ",", ".") ?></span>
                </div>

                <div class="money-box is-danger">
                    <span class="money-label">Mora</span>
                    <span class="money-value">$<?= number_format($prestamo['mora'], 0, ",", ".") ?></span>
                </div>

            </div>

        </div>

    </div>

    <!-- ===== HISTORIAL DE CUOTAS ===== -->
    <div class="edit-card">

        <div class="card-section-head">
            <p class="card-section-title">
                <span class="icon-box"><i class="bi bi-list-check"></i></span>
                Historial de cuotas
            </p>
        </div>

        <div class="table-wrap">

            <table class="table-custom">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha vencimiento</th>
                        <th>Valor</th>
                        <th>Estado</th>
                        <th>Días atraso</th>
                        <th>Mora</th>
                        <th>Fecha pago</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($cuotas)): ?>

                    <?php foreach ($cuotas as $c): ?>

                        <tr class="<?= (!$c['pagada'] && $c['dias_atraso'] > 0) ? 'row-mora' : '' ?>">

                            <td><span class="cuota-num"><?= $c['numero_cuota']; ?></span></td>

                            <td><?= $c['fecha_vencimiento']; ?></td>

                            <td class="cell-money">$<?= number_format($c['valor'], 0, ",", "."); ?></td>

                            <td>
                                <?php if ($c['pagada']): ?>
                                    <span class="status-badge is-pagado">Pagada</span>
                                <?php else: ?>
                                    <span class="status-badge is-activo">Pendiente</span>
                                <?php endif; ?>
                            </td>

                            <td><?= $c['dias_atraso']; ?></td>

                            <td class="<?= $c['mora'] > 0 ? 'cell-danger' : 'cell-muted' ?>">
                                $<?= number_format($c['mora'], 0, ",", "."); ?>
                            </td>

                            <td class="<?= $c['fecha_pago'] ? '' : 'cell-muted' ?>">
                                <?= $c['fecha_pago'] ? $c['fecha_pago'] : '&mdash;'; ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="table-empty">No hay cuotas registradas para este préstamo.</td>
                    </tr>

                <?php endif; ?>

                </tbody>
            </table>

        </div>

    </div>

    <div class="footer-actions">
        <a href="cartilla.php?id=<?= $prestamo['id'] ?>" class="btn-cancel">
            <i class="bi bi-arrow-left"></i>
            Volver
        </a>

        <?php if ($prestamo['estado'] !== 'Pagado'): ?>
            <a href="registrar_pago.php?id=<?= $prestamo['id'] ?>" class="btn-save">
                <i class="bi bi-cash-coin"></i>
                Registrar pago
            </a>
        <?php endif; ?>
    </div>

</div>

</body>
</html>