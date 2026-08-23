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
<title>Control de Cuotas</title>
<!-- Bootstrap 5.3 + Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Tipografías del sistema de diseño -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<!-- Estilos Control de Cuotas -->
<link rel="stylesheet" href="../css/cuotas.css">
</head>

<body class="cuotas-page">

<div class="container py-4">

    <!-- ENCABEZADO -->
    <div class="page-head">

        <h2 style="font-family: 'Inter', sans-serif">
            <span class="title-icon"><i class="bi bi-calendar-check"></i></span>
            Control de Cuotas
        </h2>

        <a href="cartilla.php?id=<?= $prestamo['id'] ?>" class="btn-volver">
            <i class="bi bi-arrow-left"></i>
            Volver
        </a>

    </div>

    <!-- INFORMACIÓN DEL PRÉSTAMO -->
    <div class="ce-card">

        <div class="ce-card-header">
            <i class="bi bi-person-badge"></i>
            <h4 style="font-family: 'Inter', sans-serif">Información del Préstamo</h4>
        </div>

        <div class="ce-card-body">

            <div class="row">

                <div class="col-md-4 mb-3">
                    <div class="info-item">
                        <span class="info-label">Cliente</span>
                        <span class="info-value"><?= htmlspecialchars($prestamo['cliente_nombre']) ?></span>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="info-item">
                        <span class="info-label">Cédula</span>
                        <span class="info-value"><?= htmlspecialchars($prestamo['cliente_cedula']) ?></span>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="info-item">
                        <span class="info-label">Estado</span>
                        <span class="info-value">
                            <?php
                            if($prestamo['estado']=="Pagado"){
                                echo '<span class="ce-badge success"><i class="bi bi-check-circle-fill"></i> Pagado</span>';
                            }elseif($prestamo['estado']=="Mora"){
                                echo '<span class="ce-badge danger"><i class="bi bi-exclamation-triangle-fill"></i> Mora</span>';
                            }else{
                                echo '<span class="ce-badge warning"><i class="bi bi-clock-fill"></i> Activo</span>';
                            }
                            ?>
                        </span>
                    </div>
                </div>

            </div>

            <hr class="info-divider">

            <!-- RESUMEN FINANCIERO -->
            <div class="money-grid">

                <div class="money-box">
                    <span class="money-label">Monto</span>
                    <span class="money-value">$<?= number_format($prestamo['monto'],0,",",".") ?></span>
                </div>

                <div class="money-box success">
                    <span class="money-label">Abonado</span>
                    <span class="money-value">$<?= number_format($prestamo['abonado'],0,",",".") ?></span>
                </div>

                <div class="money-box gold">
                    <span class="money-label">Pendiente</span>
                    <span class="money-value">$<?= number_format($prestamo['pendiente'],0,",",".") ?></span>
                </div>

                <div class="money-box danger">
                    <span class="money-label">Mora</span>
                    <span class="money-value">$<?= number_format($prestamo['mora'],0,",",".") ?></span>
                </div>

            </div>

        </div>

    </div>

    <!-- HISTORIAL DE CUOTAS -->
    <div class="ce-card">

        <div class="ce-card-header gold">
            <i class="bi bi-list-check"></i>
            <h5 style="font-family: 'Inter', sans-serif">Historial de Cuotas</h5>
        </div>

        <div class="ce-card-body">

            <div class="table-responsive">

                <table>

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha Vencimiento</th>
                            <th>Valor</th>
                            <th>Estado</th>
                            <th>Días Atraso</th>
                            <th>Mora</th>
                            <th>Fecha Pago</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach($cuotas as $c): ?>

                        <tr class="<?= (!$c['pagada'] && $c['dias_atraso'] > 0) ? 'row-mora' : '' ?>">

                            <td><span class="cuota-num"><?= $c['numero_cuota']; ?></span></td>

                            <td><?= $c['fecha_vencimiento']; ?></td>

                            <td class="valor-cell">$<?= number_format($c['valor'],0,",","."); ?></td>

                            <td>
                                <?php
                                if($c['pagada']){
                                    echo '<span class="ce-badge success"><i class="bi bi-check-circle-fill"></i> Pagada</span>';
                                }else{
                                    echo '<span class="ce-badge warning"><i class="bi bi-clock-fill"></i> Pendiente</span>';
                                }
                                ?>
                            </td>

                            <td><?= $c['dias_atraso']; ?></td>

                            <td class="<?= $c['mora'] > 0 ? 'mora-cell' : 'text-muted-cell' ?>">
                                $<?= number_format($c['mora'],0,",","."); ?>
                            </td>

                            <td class="<?= $c['fecha_pago'] ? '' : 'text-muted-cell' ?>">
                                <?php
                                if($c['fecha_pago']){
                                    echo $c['fecha_pago'];
                                }else{
                                    echo "&mdash;";
                                }
                                ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>
