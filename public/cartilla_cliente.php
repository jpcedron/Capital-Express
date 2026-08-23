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
<title>Cartilla Capital Express</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/panel_de_usuario.css">
</head>

<body class="bg-light">

<div class="container mt-4 mb-4">

<div class="card shadow">

<div class="card-header text-white "; style="text-align: center;background: linear-gradient(135deg, #1a3560, #0d1b3a);">

<h2 >CAPITAL EXPRESS</h2>

<p class="mb-0">
Préstamos Financieros
</p>

</div>

<div class="card-body">

<h4>Datos del Cliente</h4>

<hr>

<p><strong>Nombre:</strong>
<?= $prestamo['nombre'] ?>
</p>

<p><strong>Cédula:</strong>
<?= $prestamo['cedula'] ?>
</p>

<p><strong>Teléfono:</strong>
<?= $prestamo['telefono'] ?>
</p>

<p><strong>Dirección:</strong>
<?= $prestamo['direccion'] ?>
</p>

<hr>

<h4>Información del Préstamo</h4>

<?php

$totalExigible =
$prestamo['pendiente']
+
$prestamo['mora'];

?>

<p>
<strong>Monto:</strong>
$<?= number_format($prestamo['monto']) ?>
</p>

<p>
<strong>Interés:</strong>
<?= $prestamo['interes'] ?>%
</p>

<p>
<strong>Cuotas:</strong>
<?= $prestamo['cuotas'] ?>
</p>

<p>
<strong>Total pactado:</strong>
$<?= number_format($prestamo['total_pagar']) ?>
</p>

<p>
<strong>Abonado:</strong>
$<?= number_format($prestamo['abonado']) ?>
</p>

<p>
<strong>Capital pendiente:</strong>
$<?= number_format($prestamo['pendiente']) ?>
</p>

<p>
<strong>Valor cuota:</strong>
$<?= number_format($prestamo['valor_cuota']) ?>
</p>

<hr>

<h4 class="text-danger">

Estado actual del préstamo

</h4>

<p>

<strong>Estado:</strong>

<?php if($prestamo['estado']=="Pagado"): ?>

<span class="badge bg-success">

Pagado

</span>

<?php elseif($prestamo['estado']=="Mora"): ?>

<span class="badge bg-danger">

Mora

</span>

<?php else: ?>

<span class="badge bg-warning">

Activo

</span>

<?php endif; ?>

</p>

<p>

<strong>Fecha préstamo:</strong>

<?= date('d/m/Y', strtotime($prestamo['fecha_prestamo'])); ?>

</p>

<p>
    <strong>Última cuota:</strong>
<?= $datosCuota['ultima_cuota']
    ? date('d/m/Y', strtotime($datosCuota['ultima_cuota']))
    : 'No registrada'; ?>
</p>


<p>

<strong>Días atraso:</strong>

<?= $diasAtraso ?>

</p>

<p>

<strong>% Mora aplicado:</strong>

<?= $porcentajeMora ?>%

</p>

<p>

<strong>Mora acumulada:</strong>

$<?= number_format($prestamo['mora']) ?>

</p>

<p>
    <strong>Total a pagar actualmente:</strong>
    <span class="text-danger fw-bold">
        $<?= number_format($totalExigible, 0, ",", ".") ?>
    </span>
</p>


<div class="alert alert-warning">

<h5>

Total exigible

</h5>

<h3>

$<?= number_format($totalExigible) ?>

</h3>

<small>

Capital pendiente + Mora

</small>

</div>

<hr>

<h4>

Historial de Pagos

</h4>

<table class="table table-bordered">

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

<?php foreach($pagos as $pago): ?>

<tr>

<td>

<?= $pago['fecha_pago'] ?>

</td>

<td>

$<?= number_format($pago['valor_pago']) ?>

</td>

<td>

$<?= number_format(
$pago['pago_mora']
?? 0
) ?>

</td>

<td>

$<?= number_format(
$pago['pago_capital']
?? 0
) ?>

</td>

<td>

$<?= number_format(
$pago['saldo_restante']
) ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<hr>

<a href="panel_de_usuario.php" class="btn-ce">
    <i class="bi bi-arrow-left-circle"></i>
    Volver
</a>

<a href="descargar_cartilla.php?id=<?= $prestamo['id'] ?>" class="btn-ce">
    <i class="bi bi-file-earmark-arrow-down-fill"></i>
    Descargar Cartilla PDF
</a>



</div>

</div>

</div>

</body>

</html>
