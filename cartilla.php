<?php

require_once "config/conexion.php";

$conexion =
(new Conexion())->conectar();

$id =
$_GET['id'];

$sql =
"SELECT * FROM prestamos WHERE id=?";

$stmt =
$conexion->prepare($sql);

$stmt->execute([
$id
]);

$prestamo =
$stmt->fetch(
PDO::FETCH_ASSOC
);

$sql = "SELECT MAX(fecha_vencimiento) AS ultima_cuota
        FROM cuotas
        WHERE prestamo_id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$datosCuota = $stmt->fetch(PDO::FETCH_ASSOC);

$fechaLimite = null;

if (!empty($datosCuota['ultima_cuota'])) {
    $fechaLimite = new DateTime($datosCuota['ultima_cuota']);
}

$hoy = new DateTime();

$sql = "SELECT MAX(fecha_vencimiento) AS ultima_cuota
        FROM cuotas
        WHERE prestamo_id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$datosCuota = $stmt->fetch(PDO::FETCH_ASSOC);

$fechaLimite = null;

if (!empty($datosCuota['ultima_cuota'])) {
    $fechaLimite = new DateTime($datosCuota['ultima_cuota']);
}

$diasAtraso = 0;

$mora = 0;

$totalActual =
$prestamo['pendiente'];

/* porcentaje inicial */

$porcentajeMora = 0;

if (
    $prestamo['pendiente'] > 0 &&
    $fechaLimite !== null &&
    $hoy > $fechaLimite
) {

$diasAtraso =
$fechaLimite
->diff(
$hoy
)
->days;

/* calcular porcentaje */

if(
$diasAtraso >= 3
&&
$diasAtraso <= 14
){

$porcentajeMora = 5;

}
elseif(
$diasAtraso >= 15
&&
$diasAtraso <= 29
){

$porcentajeMora = 10;

}
elseif(
$diasAtraso >= 30
&&
$diasAtraso <= 44
){

$porcentajeMora = 15;

}
elseif(
$diasAtraso >= 45
){

$porcentajeMora = 20;

}

/* calcular mora */

$mora =

$prestamo['pendiente']

*

(

$porcentajeMora

/

100

);

$totalActual =

$prestamo['pendiente']

+

$mora;

}

/* HISTORIAL */

$sql = "

SELECT *

FROM pagos

WHERE prestamo_id=?

ORDER BY fecha_pago DESC

";

$stmt =
$conexion->prepare($sql); $stmt->execute([
$id ]); $pagos = $stmt->fetchAll( PDO::FETCH_ASSOC );

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cartilla Capital Express</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<style>

    :root {
        --navy-deep: #0d1f3c;
        --navy-base: #1e2a3a;
        --navy-mid: #1a3560;
        --gold: #e8c876;
        --gold-dark: #c9a84c;
        --paper: #f4f6fb;
        --line: #e3e7f0;
        --text-muted: #6b7688;
    }

    * {
        box-sizing: border-box;
    }

    html, body {
        margin: 0;
        min-height: 100vh;
        background-color: var(--paper);
        font-family: 'Inter', sans-serif;
        color: var(--navy-base);
    }

    /* Top bar */
    .topbar {
        background: var(--navy-deep);
        border-bottom: 3px solid var(--gold-dark);
    }

    .topbar-inner {
        max-width: 960px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 24px;
    }

    .brand-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-mark {
        width: 34px;
        height: 34px;
        border-radius: 6px;
        background: var(--gold);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        color: var(--navy-deep);
        font-size: 14px;
        flex-shrink: 0;
    }

    .brand-name {
        font-family: 'Sora', sans-serif;
        font-weight: 600;
        font-size: 1.25rem;
        letter-spacing: 0.01em;
        color: var(--paper);
        margin: 0;
        line-height: 1.1;
    }

    .brand-heading {
    font-family: 'Playfair Display', serif;
}

    .brand-sub {
        font-size: 0.68rem;
        color: rgba(244, 246, 251, 0.55);
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin: 0;
    }

    .topbar-user {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        color: rgba(244, 246, 251, 0.75);
    }

    .topbar-user .avatar {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: rgba(232, 200, 118, 0.15);
        border: 1px solid rgba(232, 200, 118, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gold);
        font-size: 0.7rem;
        font-weight: 700;
    }

    /* Page container */
    .page-wrap {
        max-width: 960px;
        margin: 0 auto;
        padding: 28px 24px 64px;
    }

    .breadcrumb-row {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-bottom: 18px;
    }

    .breadcrumb-row a {
        color: var(--text-muted);
        text-decoration: none;
    }

    .breadcrumb-row a:hover {
        color: var(--navy-mid);
    }

    .breadcrumb-row .sep {
        color: #c3c9d6;
    }

    .breadcrumb-row .current {
        color: var(--navy-deep);
        font-weight: 600;
    }

    .page-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .page-title {
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--navy-deep);
        margin: 0 0 4px;
    }

    .page-desc {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin: 0;
    }

    .client-id-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--navy-mid);
        background: #ffffff;
        border: 1px solid var(--line);
        padding: 7px 12px;
        border-radius: 6px;
        white-space: nowrap;
    }

    .client-id-tag i {
        color: var(--gold-dark);
    }

    /* Card */
    .edit-card {
        background: #ffffff;
        border: 1px solid var(--line);
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(13, 31, 60, 0.04);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .card-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 28px;
        border-bottom: 1px solid var(--line);
        background: #fafbfd;
    }

    .card-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: 'Sora', sans-serif;
        font-weight: 600;
        font-size: 0.92rem;
        color: var(--navy-deep);
        margin: 0;
    }

    .card-section-title .icon-box {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        background: rgba(201, 168, 76, 0.12);
        color: var(--gold-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 4px;
    }

    .status-badge.is-pagado {
        background: rgba(34, 139, 87, 0.1);
        color: #1f7a4d;
    }

    .status-badge.is-mora {
        background: rgba(180, 60, 60, 0.1);
        color: #a13636;
    }

    .status-badge.is-activo {
        background: rgba(201, 168, 76, 0.14);
        color: #93761f;
    }

    .status-badge::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .card-body-custom {
        padding: 28px;
    }

    .field-group-title {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin: 0 0 16px;
    }

    /* Data rows (read-only info) */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px 24px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-label {
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: var(--text-muted);
    }

    .info-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--navy-deep);
    }

    .info-value.money {
        font-family: 'Sora', sans-serif;
    }

    .info-value.danger {
        color: #a13636;
    }

    hr.divider {
        border: none;
        border-top: 1px solid var(--line);
        margin: 22px 0;
    }

    /* Total exigible highlight */
    .total-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        background: linear-gradient(135deg, rgba(201, 168, 76, 0.1), rgba(232, 200, 118, 0.06));
        border: 1px solid rgba(201, 168, 76, 0.35);
        border-radius: 8px;
        padding: 20px 24px;
        margin-top: 8px;
    }

    .total-box .label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--gold-dark);
        margin: 0 0 6px;
    }

    .total-box .sub {
        font-size: 0.78rem;
        color: var(--text-muted);
        margin: 4px 0 0;
    }

    .total-box .amount {
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: 1.9rem;
        color: var(--navy-deep);
        margin: 0;
        line-height: 1;
    }

    /* Table */
    .table-wrap {
        overflow-x: auto;
    }

    .table-custom {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .table-custom thead th {
        text-align: left;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-muted);
        padding: 10px 14px;
        border-bottom: 1px solid var(--line);
        background: #fafbfd;
        white-space: nowrap;
    }

    .table-custom tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid var(--line);
        color: var(--navy-base);
        white-space: nowrap;
    }

    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }

    .table-custom tbody tr:hover {
        background: #fafbfd;
    }

    .table-empty {
        padding: 32px 14px;
        text-align: center;
        color: var(--text-muted);
        font-size: 0.85rem;
    }

    /* Actions */
    .action-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .footer-note {
        font-size: 0.76rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0;
    }

    .action-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-save,
    .btn-ghost-gold {
        font-weight: 600;
        font-size: 0.85rem;
        padding: 10px 20px;
        border-radius: 6px;
        letter-spacing: 0.01em;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    }

    .btn-save {
        background: var(--navy-deep);
        border: 1px solid var(--navy-deep);
        color: var(--paper);
    }

    .btn-save:hover {
        background: var(--navy-mid);
        border-color: var(--navy-mid);
        color: var(--paper);
    }

    .btn-ghost-gold {
        background: #ffffff;
        border: 1px solid var(--line);
        color: var(--navy-base);
    }

    .btn-ghost-gold:hover {
        background-color: #f2f4f8;
        border-color: #c9cfdc;
        color: var(--navy-base);
    }

    .btn-cancel {
        background: #ffffff;
        border: 1px solid var(--line);
        color: var(--navy-base);
        font-weight: 600;
        font-size: 0.85rem;
        padding: 10px 20px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }

    .btn-cancel:hover {
        background-color: #f2f4f8;
        color: var(--navy-base);
        border-color: #c9cfdc;
    }

    /* ===== Responsive ===== */

    @media (max-width: 768px) {
        .topbar-inner {
            padding: 12px 16px;
        }

        .brand-sub {
            display: none;
        }

        .page-wrap {
            padding: 20px 16px 48px;
        }

        .page-head {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
        }

        .client-id-tag {
            align-self: flex-start;
        }

        .card-section-head {
            padding: 14px 18px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .card-body-custom {
            padding: 20px 18px;
        }

        .info-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .total-box {
            padding: 16px 18px;
        }

        .table-wrap {
            -webkit-overflow-scrolling: touch;
        }
    }

    @media (max-width: 576px) {
        .topbar-user span {
            display: none;
        }

        .breadcrumb-row {
            flex-wrap: wrap;
        }

        .page-title {
            font-size: 1.25rem;
        }

        .page-desc {
            font-size: 0.8rem;
        }

        .card-section-title {
            font-size: 0.85rem;
        }

        .total-box {
            flex-direction: column;
            align-items: flex-start;
        }

        .total-box .amount {
            font-size: 1.6rem;
        }

        .total-box i {
            display: none;
        }

        .table-custom {
            font-size: 0.8rem;
        }

        .table-custom thead th,
        .table-custom tbody td {
            padding: 9px 10px;
        }

        .action-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .footer-note {
            order: 2;
        }

        .action-row {
            order: 1;
            justify-content: stretch;
            flex-direction: column;
        }

        .action-row a {
            flex: 1;
            justify-content: center;
            width: 100%;
        }
    }

</style>
</head>

<body>

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

    <div class="breadcrumb-row">
        <a href="pagina_web/dashboard.php">Panel</a>
        <span class="sep">/</span>
        <a href="listado.php">Préstamos</a>
        <span class="sep">/</span>
        <span class="current">Cartilla</span>
    </div>

    <div class="page-head">
        <div>
            <h1 class="page-title">Cartilla de préstamo</h1>
            <p class="page-desc">Detalle del cliente, condiciones del préstamo e historial de pagos.</p>
        </div>

        <span class="client-id-tag">
            <i class="bi bi-file-earmark-text"></i>
            Préstamo #<?= htmlspecialchars($prestamo['id']) ?>
        </span>
    </div>

    <!-- Datos del cliente -->
    <div class="edit-card">

        <div class="card-section-head">
            <p class="card-section-title">
                <span class="icon-box"><i class="bi bi-person"></i></span>
                Datos del cliente
            </p>
        </div>

        <div class="card-body-custom">

            <div class="info-grid">

                <div class="info-item">
                    <span class="info-label">Nombre</span>
                    <span class="info-value"><?= $prestamo['nombre'] ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Cédula</span>
                    <span class="info-value"><?= $prestamo['cedula'] ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Teléfono</span>
                    <span class="info-value"><?= $prestamo['telefono'] ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Dirección</span>
                    <span class="info-value"><?= $prestamo['direccion'] ?></span>
                </div>

            </div>

        </div>

    </div>

    <!-- Información del préstamo -->
    <div class="edit-card">

        <div class="card-section-head">
            <p class="card-section-title">
                <span class="icon-box"><i class="bi bi-cash-coin"></i></span>
                Información del préstamo
            </p>
        </div>

        <div class="card-body-custom">

            <?php $totalExigible = $prestamo['pendiente'] + $prestamo['mora']; ?>

            <div class="info-grid">

                <div class="info-item">
                    <span class="info-label">Monto</span>
                    <span class="info-value money">$<?= number_format($prestamo['monto']) ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Interés</span>
                    <span class="info-value"><?= $prestamo['interes'] ?>%</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Cuotas</span>
                    <span class="info-value"><?= $prestamo['cuotas'] ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Total pactado</span>
                    <span class="info-value money">$<?= number_format($prestamo['total_pagar']) ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Abonado</span>
                    <span class="info-value money">$<?= number_format($prestamo['abonado']) ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Capital pendiente</span>
                    <span class="info-value money">$<?= number_format($prestamo['pendiente']) ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Valor cuota</span>
                    <span class="info-value money">$<?= number_format($prestamo['valor_cuota']) ?></span>
                </div>

            </div>

        </div>

    </div>

    <!-- Estado actual -->
    <div class="edit-card">

        <div class="card-section-head">

            <p class="card-section-title">
                <span class="icon-box"><i class="bi bi-exclamation-triangle"></i></span>
                Estado actual del préstamo
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
                    <span class="info-label">Fecha préstamo</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($prestamo['fecha_prestamo'])); ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Última cuota</span>
                    <span class="info-value">
                        <?= $datosCuota['ultima_cuota']
                            ? date('d/m/Y', strtotime($datosCuota['ultima_cuota']))
                            : 'No registrada'; ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Días atraso</span>
                    <span class="info-value"><?= $diasAtraso ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">% Mora aplicado</span>
                    <span class="info-value"><?= $porcentajeMora ?>%</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Mora acumulada</span>
                    <span class="info-value danger money">$<?= number_format($prestamo['mora']) ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Total a pagar actualmente</span>
                    <span class="info-value danger money">$<?= number_format($totalExigible, 0, ",", ".") ?></span>
                </div>

            </div>

            <div class="total-box">
                <div>
                    <p class="label">Total exigible</p>
                    <p class="amount">$<?= number_format($totalExigible) ?></p>
                    <p class="sub">Capital pendiente + Mora</p>
                </div>
                <i class="bi bi-cash-stack" style="font-size: 2.2rem; color: var(--gold-dark); opacity: 0.5;"></i>
            </div>

        </div>

    </div>

    <!-- Historial de pagos -->
    <div class="edit-card">

        <div class="card-section-head">
            <p class="card-section-title">
                <span class="icon-box"><i class="bi bi-clock-history"></i></span>
                Historial de pagos
            </p>
        </div>

        <div class="table-wrap">

            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Pago</th>
                        <th>Mora</th>
                        <th>Capital</th>
                        <th>Saldo restante</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (!empty($pagos)): ?>

                        <?php foreach ($pagos as $pago): ?>
                            <tr>
                                <td><?= $pago['fecha_pago'] ?></td>
                                <td>$<?= number_format($pago['valor_pago']) ?></td>
                                <td>$<?= number_format($pago['pago_mora'] ?? 0) ?></td>
                                <td>$<?= number_format($pago['pago_capital'] ?? 0) ?></td>
                                <td>$<?= number_format($pago['saldo_restante']) ?></td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="table-empty">No hay pagos registrados para este préstamo.</td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>

        </div>

    </div>

    <!-- Acciones -->
    <div class="edit-card">

        <div class="card-body-custom">

            <div class="action-bar">

                <p class="footer-note">
                    <i class="bi bi-info-circle"></i>
                    Documentos generados a partir de la información registrada.
                </p>

                <div class="action-row">

                    <a href="listado.php" class="btn-cancel">
                        <i class="bi bi-arrow-left"></i>
                        Volver
                    </a>

                    <a href="cuotas.php?id=<?= $prestamo['id'] ?>" class="btn-ghost-gold">
                        <i class="bi bi-calendar-check"></i>
                        Cuotas
                    </a>

                    <a href="descargar_cartilla.php?id=<?= $prestamo['id'] ?>" class="btn-ghost-gold">
                        <i class="bi bi-download"></i>
                        Descargar cartilla
                    </a>

                    <a href="recibo.php?id=<?= $prestamo['id'] ?>" class="btn-save">
                        <i class="bi bi-receipt"></i>
                        Ver recibo
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>

