<?php

require_once "vendor/autoload.php";
require_once "config/conexion.php";

use Dompdf\Dompdf;

$conexion =
(new Conexion())
->conectar();

$id =
$_GET["id"] ?? 0;

$sql="

SELECT
p.*,

(
SELECT
COALESCE(
SUM(valor_pago),
0
)

FROM pagos

WHERE prestamo_id=p.id

)

AS total_pagado

FROM prestamos p

WHERE p.id=?

";

$stmt=
$conexion->prepare($sql);

$stmt->execute([$id]);

$prestamo=
$stmt->fetch(PDO::FETCH_ASSOC);

if(!$prestamo){

die("Préstamo no encontrado");

}

// Obtener la fecha de vencimiento de la última cuota
$sql = "SELECT MAX(fecha_vencimiento) AS ultima_cuota
        FROM cuotas
        WHERE prestamo_id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$datosCuota = $stmt->fetch(PDO::FETCH_ASSOC);

$ultimaCuota = $datosCuota['ultima_cuota'] ?? null;

$mora=
$prestamo["mora"] ?? 0;

$total=
$prestamo["pendiente"]
+
$mora;

if ($prestamo["estado"] == "Pagado") {
    $estado = "PAGADO";
} elseif ($prestamo["estado"] == "Mora") {
    $estado = "EN MORA";
} else {
    $estado = "ACTIVO";
}

ob_start();

?>

<html>

<head>

<style>

body{

font-family:Arial;

padding:30px;

}

.card{

border:1px solid #ddd;

padding:30px;

}

h1{

color:#ff6b35;

}

.linea{

margin:12px 0;

}

.total{

font-size:22px;

font-weight:bold;

color:red;

}

</style>

</head>

<body>

<div class="card">

<h1>

Capital Express

</h1>

<hr>

<h2>

Comprobante de Préstamo

</h2>

<div class="linea">

Cliente:

<?= $prestamo["nombre"] ?>

</div>

<div class="linea">

Monto:

$<?= number_format(
$prestamo["monto"],
0,
",",
"."
) ?>

</div>

<div class="linea">
Fecha préstamo:
<?= date("d/m/Y", strtotime($prestamo["fecha_prestamo"])) ?>
</div>

<div class="linea">
Última cuota:
<?= $ultimaCuota
    ? date("d/m/Y", strtotime($ultimaCuota))
    : "No registrada"; ?>
</div>

<div class="linea">
Mora:
$<?= number_format($prestamo["mora"],0,",",".") ?>
</div>

<div class="linea">
Estado:
<?= $estado ?>
</div>

<div class="linea">
    Total pagado:
    $<?= number_format($prestamo["abonado"],0,",",".") ?>

</div>

<hr>

Generado:

<?= date(
"d/m/Y H:i"
) ?>

</div>

</body>

</html>

<?php

$html=
ob_get_clean();

$pdf=
new Dompdf();

$pdf->loadHtml($html);

$pdf->setPaper(
"A4",
"portrait"
);

$pdf->render();

$pdf->stream(

"cartilla.pdf",

[
"Attachment"=>false
]

);