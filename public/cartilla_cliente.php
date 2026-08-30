<?php

require_once "auth_cliente.php";
require_once "../config/conexion.php";

$conexion = (new Conexion())->conectar();

$cedula = $_SESSION["cliente_cedula"];

$sql = "SELECT
            p.*,
            c.nombre AS cliente_nombre,
            c.cedula AS cliente_cedula,
            c.telefono AS cliente_telefono,
            c.direccion AS cliente_direccion,
            c.estado_cliente
        FROM prestamos p
        INNER JOIN clientes c
            ON c.id = p.cliente_id
        WHERE c.cedula = ?
        AND p.estado IN ('Activo','Mora')
        ORDER BY p.id DESC
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->execute([$cedula]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestamo) {

    require_once "alerta_sin_prestamo.php";
    exit;

}

$id = $prestamo["id"];

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
<title>Mis Cuotas · Capital Express</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<!-- Tipografías del panel de usuario -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<!-- Diseño autentico del panel de usuario -->
<link rel="stylesheet" href="../css/cartilla_cliente.css">
</head>

<body class="ce-body">

<!-- ===== NAVBAR ===== -->
<nav class="ce-navbar">
    <div class="ce-navbar__inner">
        <a href="panel_de_usuario.php" class="ce-navbar__brand">
            <div class="brand-icon brand-heading">CE</div>
            <div class="brand-text">
                <div class="name brand-heading">Capital Express</div>
                <div class="tagline">Gestión de Préstamos</div>
            </div>
        </a>
        <a href="panel_de_usuario.php" class="ce-navbar__back">
            <i class="bi bi-grid-1x2"></i>
            <span>Inicio</span>
        </a>
    </div>
</nav>

<div class="container ce-container">

    <div class="ce-card">

        <!-- ===== ENCABEZADO ===== -->
        <header class="ce-header">
            <div class="ce-header__brand">
                <span class="ce-header__mark"><i class="bi bi-bank2"></i></span>
                <div>
                    <h1 class="ce-brand">CAPITAL EXPRESS</h1>
                    <p class="ce-header__subtitle">Cartilla</p>
                </div>
            </div>

            <?php
                $estado = $prestamo['estado'];
                $estadoClass = 'is-active';
                $estadoIcon  = 'bi-hourglass-split';
                if ($estado == "Pagado") { $estadoClass = 'is-paid';  $estadoIcon = 'bi-check-circle-fill'; }
                elseif ($estado == "Mora") { $estadoClass = 'is-late'; $estadoIcon = 'bi-exclamation-triangle-fill'; }
            ?>
            <span class="ce-status ce-status--header <?= $estadoClass ?>">
                <i class="bi <?= $estadoIcon ?>"></i>
                <?= htmlspecialchars($estado) ?>
            </span>
        </header>

        <div class="ce-body-inner">

            <!-- ===== DATOS DEL CLIENTE ===== -->
            <section class="ce-section">
                <div class="ce-section__title">
                    <i class="bi bi-person-vcard"></i>
                    <h2>Datos del Cliente</h2>
                </div>

                <div class="ce-client">
                    <div class="ce-client__avatar">
                        <?= strtoupper(substr($prestamo['cliente_nombre'], 0, 1)) ?>
                    </div>
                    <div class="ce-client__info">
                        <span class="ce-client__name"><?= htmlspecialchars($prestamo['cliente_nombre']) ?></span>
                        <span class="ce-client__meta">
                            <i class="bi bi-credit-card-2-front"></i>
                            Cédula: <?= htmlspecialchars($prestamo['cliente_cedula']) ?>
                        </span>
                    </div>
                </div>
            </section>

            <!-- ===== INFORMACIÓN DEL PRÉSTAMO ===== -->
            <section class="ce-section">
                <div class="ce-section__title">
                    <i class="bi bi-cash-coin"></i>
                    <h2>Información del Préstamo</h2>
                </div>

                <div class="ce-stats">
                    <div class="ce-stat ce-stat--primary">
                        <span class="ce-stat__label">Monto</span>
                        <span class="ce-stat__value">$<?= number_format($prestamo['monto']) ?></span>
                    </div>
                    <div class="ce-stat">
                        <span class="ce-stat__label">Cuotas</span>
                        <span class="ce-stat__value"><?= $prestamo['cuotas'] ?></span>
                    </div>
                    <div class="ce-stat">
                        <span class="ce-stat__label">Valor cuota</span>
                        <span class="ce-stat__value">$<?= number_format($prestamo['valor_cuota']) ?></span>
                    </div>
                    <div class="ce-stat ce-stat--good">
                        <span class="ce-stat__label">Abonado</span>
                        <span class="ce-stat__value">$<?= number_format($prestamo['abonado']) ?></span>
                    </div>
                    <div class="ce-stat">
                        <span class="ce-stat__label">Capital pendiente</span>
                        <span class="ce-stat__value">$<?= number_format($prestamo['pendiente']) ?></span>
                    </div>
                    <div class="ce-stat ce-stat--bad">
                        <span class="ce-stat__label">Mora acumulada</span>
                        <span class="ce-stat__value">$<?= number_format($prestamo['mora']) ?></span>
                    </div>
                </div>
            </section>

            <!-- ===== PLAN DE CUOTAS ===== -->
            <section class="ce-section">
                <div class="ce-section__title">
                    <i class="bi bi-list-check"></i>
                    <h2>Detalle de Cuotas</h2>
                </div>

                <div class="ce-table-wrap">
                    <table class="ce-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vencimiento</th>
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

                                <tr>
                                    <td data-label="#"><span class="ce-num"><?= $c['numero_cuota'] ?></span></td>
                                    <td data-label="Vencimiento"><?= $c['fecha_vencimiento'] ?></td>
                                    <td data-label="Valor" class="ce-money">$<?= number_format($c['valor']) ?></td>
                                    <td data-label="Estado">
                                        <?php if ($c['pagada']): ?>
                                            <span class="ce-pill is-paid"><i class="bi bi-check-lg"></i> Pagada</span>
                                        <?php else: ?>
                                            <span class="ce-pill is-pending"><i class="bi bi-clock"></i> Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Días atraso">
                                        <?php if (($c['dias_atraso'] ?? 0) > 0): ?>
                                            <span class="ce-late-days"><?= $c['dias_atraso'] ?></span>
                                        <?php else: ?>
                                            <span class="ce-dash">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Mora" class="ce-money">$<?= number_format($c['mora'] ?? 0) ?></td>
                                    <td data-label="Fecha pago"><?= $c['fecha_pago'] ? $c['fecha_pago'] : '<span class="ce-dash">—</span>' ?></td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="7" class="ce-empty">
                                    <i class="bi bi-inbox"></i>
                                    No hay cuotas registradas para este préstamo.
                                </td>
                            </tr>

                        <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ===== ACCIONES ===== -->
            <div class="ce-actions">
                <a href="panel_de_usuario.php" class="btn-ce btn-ce--ghost">
                    <i class="bi bi-arrow-left-circle"></i>
                    Volver
                </a>

                <a href="descargar_cuotas.php?id=<?= $prestamo['id'] ?>" class="btn-ce btn-ce--solid">
                    <i class="bi bi-file-earmark-arrow-down-fill"></i>
                    Descargar Cuotas PDF
                </a>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
