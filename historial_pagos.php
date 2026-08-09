<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$prestamo_id = $_GET['prestamo_id'] ?? null;

if (!$prestamo_id) {
    die("Préstamo no encontrado");
}

$sql = "
SELECT
fecha_pago,
valor_pago,
saldo_restante,
observacion
FROM pagos
WHERE prestamo_id = ?
ORDER BY fecha_pago DESC
";

$stmt = $conexion->prepare($sql);
$stmt->execute([$prestamo_id]);

$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php /* Lógica PHP original sin modificar: se asume que $pagos ya viene definido desde el controlador */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Historial de pagos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --navy-deep: #0d1f3c;
        --navy-mid: #1a3560;
        --navy-base: #1e2a3a;
        --ivory: #f4f6fb;
        --gold: #e8c876;
        --gold-dark: #c9a84c;
    }

    * { box-sizing: border-box; }

    body {
        background-color: var(--ivory);
        font-family: 'Inter', sans-serif;
        color: var(--navy-base);
        min-height: 100vh;
    }

    /* ===== Topbar ===== */

    .topbar {
        background-color: var(--navy-deep);
        border-bottom: 3px solid var(--gold);
    }

    .topbar-inner {
        max-width: 1040px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 24px;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .brand-mark {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: var(--gold);
        color: var(--navy-deep);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .brand-text {
        line-height: 1.1;
    }

    .brand-title {
        font-family: 'Sora', sans-serif;
        font-weight: 600;
        font-size: 1.25rem;
        color: #ffffff;
    }
    
    .brand-heading {
    font-family: 'Playfair Display', serif;
}

    .brand-sub {
        font-size: 0.80rem;
        color: #9fb0cc;
        letter-spacing: 0.03em;
    }

    .topbar-user {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #d7deec;
        font-size: 0.82rem;
    }

    .topbar-user i {
        color: var(--gold);
    }

    /* ===== Page wrap ===== */

    .page-wrap {
        max-width: 1040px;
        margin: 0 auto;
        padding: 32px 24px 56px;
    }

    .breadcrumb-row {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: #6b7690;
        margin-bottom: 18px;
    }

    .breadcrumb-row a {
        color: #6b7690;
        text-decoration: none;
    }

    .breadcrumb-row a:hover {
        color: var(--navy-mid);
    }

    .breadcrumb-row .current {
        color: var(--navy-base);
        font-weight: 500;
    }

    .page-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
    }

    .page-title {
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--navy-deep);
        margin: 0;
    }

    .page-desc {
        font-size: 0.85rem;
        color: #6b7690;
        margin-top: 2px;
    }

    .count-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid #dfe4ee;
        border-radius: 20px;
        padding: 6px 14px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--navy-mid);
        white-space: nowrap;
    }

    .count-tag i {
        color: var(--gold-dark);
        font-size: 0.85rem;
    }

    /* ===== Card ===== */

    .card-custom {
        background: #ffffff;
        border: 1px solid #e3e7f0;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(13, 31, 60, 0.06);
        overflow: hidden;
    }

    .card-section-head {
        background: #f7f8fb;
        border-bottom: 1px solid #e3e7f0;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .card-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: 'Sora', sans-serif;
        font-weight: 600;
        font-size: 0.92rem;
        color: var(--navy-deep);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .card-section-title i {
        color: var(--gold-dark);
        font-size: 1rem;
    }

    /* ===== Table ===== */

    .table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-custom {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
        font-size: 0.87rem;
    }

    .table-custom thead th {
        background: #ffffff;
        color: #6b7690;
        font-weight: 600;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 12px 24px;
        border-bottom: 2px solid #e3e7f0;
        white-space: nowrap;
        text-align: left;
    }

    .table-custom tbody td {
        padding: 13px 24px;
        border-bottom: 1px solid #eef0f6;
        color: var(--navy-base);
        vertical-align: middle;
    }

    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }

    .table-custom tbody tr:hover {
        background: #fbfaf6;
    }

    .cell-date {
        color: #6b7690;
        font-size: 0.83rem;
        white-space: nowrap;
    }

    .cell-pay {
        font-weight: 600;
        color: #1f7a4d;
    }

    .cell-pay i {
        font-size: 0.75rem;
        margin-right: 4px;
    }

    .cell-balance {
        font-weight: 600;
        color: var(--navy-mid);
    }

    .cell-obs {
        color: #6b7690;
    }

    .empty-state {
        text-align: center;
        padding: 56px 24px;
        color: #9aa4bc;
    }

    .empty-state i {
        font-size: 2.2rem;
        color: #d8dce8;
        margin-bottom: 10px;
        display: block;
    }

    .empty-state p {
        margin: 0;
        font-size: 0.9rem;
    }

    /* ===== Footer ===== */

    .card-footer-actions {
        background: #ffffff;
        border-top: 1px solid #e3e7f0;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .footer-note {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        color: #9aa4bc;
    }

    .footer-note i {
        color: var(--gold-dark);
    }

    .btn-back-custom {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1px solid #d7deec;
        color: var(--navy-mid);
        font-weight: 600;
        font-size: 0.85rem;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-back-custom:hover {
        background: #f2f4f8;
        border-color: #c9cfdc;
        color: var(--navy-deep);
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

        .count-tag {
            align-self: flex-start;
        }

        .card-section-head {
            padding: 14px 18px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .table-custom thead th,
        .table-custom tbody td {
            padding: 11px 16px;
        }

        .card-footer-actions {
            padding: 16px 18px;
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

        .table-custom {
            font-size: 0.8rem;
        }

        .table-custom thead th,
        .table-custom tbody td {
            padding: 10px 12px;
        }

        .cell-obs {
            min-width: 140px;
        }

        .card-footer-actions {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }

        .footer-note {
            order: 2;
            justify-content: center;
        }

        .btn-back-custom {
            order: 1;
            justify-content: center;
            width: 100%;
        }
    }
</style>
</head>
<body>

<div class="topbar">
    <div class="topbar-inner">
        <div class="brand">
            <div class="brand-mark brand-heading">CE</div>
            <div class="brand-text">
                <div class="brand-title brand-heading">Capital Express</div>
                <div class="brand-sub">Panel administrativo</div>
            </div>
        </div>
    </div>
</div>

<div class="page-wrap">

    <div class="breadcrumb-row">
        <a href="listado.php">Préstamos</a>
        <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
        <span class="current">Historial de pagos</span>
    </div>

    <div class="page-head">
        <div>
            <h1 class="page-title">Historial de pagos</h1>
            <div class="page-desc">Registro cronológico de los abonos realizados</div>
        </div>
        <div class="count-tag">
            <i class="bi bi-receipt"></i>
            <?= count($pagos) ?> pago<?= count($pagos) == 1 ? '' : 's' ?> registrado<?= count($pagos) == 1 ? '' : 's' ?>
        </div>
    </div>

    <div class="card-custom">

        <div class="card-section-head">
            <div class="card-section-title">
                <i class="bi bi-clock-history"></i>
                Movimientos
            </div>
        </div>

        <?php if (count($pagos) > 0): ?>
        <div class="table-wrap">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Pago</th>
                        <th>Saldo restante</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pagos as $pago): ?>
                    <tr>
                        <td class="cell-date">
                            <?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?>
                        </td>
                        <td class="cell-pay">
                            <i class="bi bi-arrow-down-circle-fill"></i>$<?= number_format($pago['valor_pago'],0,",",".") ?>
                        </td>
                        <td class="cell-balance">
                            $<?= number_format($pago['saldo_restante'],0,",",".") ?>
                        </td>
                        <td class="cell-obs">
                            <?= $pago['observacion'] ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>Aún no se han registrado pagos.</p>
        </div>
        <?php endif; ?>

        <div class="card-footer-actions">
            <div class="footer-note">
                <i class="bi bi-info-circle"></i>
                Los valores se muestran en pesos colombianos
            </div>
            <a href="listado.php" class="btn-back-custom">
                <i class="bi bi-arrow-left"></i>
                Volver al listado
            </a>
        </div>

    </div>

</div>

</body>
</html>
