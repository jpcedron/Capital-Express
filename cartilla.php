<?php

require_once "config/conexion.php";

$conexion =
(new Conexion())->conectar();

$id =
$_GET['id'];

$sql = "SELECT
            prestamos.*,
            clientes.nombre AS cliente_nombre,
            clientes.cedula AS cliente_cedula,
            clientes.telefono AS cliente_telefono,
            clientes.direccion AS cliente_direccion,
            clientes.estado_cliente
        FROM prestamos
        INNER JOIN clientes
            ON prestamos.cliente_id = clientes.id
        WHERE prestamos.id = ?";

//$sql =
//"SELECT * FROM prestamos WHERE id=?";

$stmt = $conexion->prepare($sql); $stmt->execute([ $id ]);
$prestamo = $stmt->fetch( PDO::FETCH_ASSOC );


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
<link rel="stylesheet" href="css/cartilla.css">
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
        <a href="public/dashboard.php">Panel</a>
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
                    <span class="info-value"><?= $prestamo['cliente_nombre'] ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Cédula</span>
                    <span class="info-value"><?= $prestamo['cliente_cedula'] ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Teléfono</span>
                    <span class="info-value"><?= $prestamo['cliente_telefono'] ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Dirección</span>
                    <span class="info-value"><?= $prestamo['cliente_direccion'] ?></span>
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