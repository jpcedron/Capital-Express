<?php

require_once "config/conexion.php";
require_once "vendor/autoload.php";

use Dompdf\Dompdf;

$conexion = (new Conexion())->conectar();

$id = $_GET['id'];

$sql = "SELECT * FROM prestamos WHERE id=?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$prestamo){
die("Préstamo no encontrado");
}

// Obtener la fecha de vencimiento de la última cuota
$sql = "SELECT MAX(fecha_vencimiento) AS ultima_cuota
        FROM cuotas
        WHERE prestamo_id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$ultimaCuota = $stmt->fetch(PDO::FETCH_ASSOC);

// Calcular los días de atraso y la mora
$sql = "SELECT *
        FROM cuotas
        WHERE prestamo_id = ?
        ORDER BY numero_cuota ASC";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$cuotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '

<style>

body{
font-family:Arial;
color:#2c3e50;
}

.header{
background:linear-gradient(
90deg,
#ff6b35,
#f7931e
);

color:white;

padding:25px;

text-align:center;

border-radius:10px;
}

.card{

margin-top:20px;

padding:20px;

background:#f7f7f7;

border-radius:12px;

border-left:8px solid #ff6b35;

}

.info{

width:100%;

border-collapse:collapse;

}

.info td{

padding:12px;

border-bottom:1px solid #ddd;

}

.titulo{

color:#ff6b35;

font-weight:bold;

}

.tabla{

width:100%;

margin-top:25px;

border-collapse:collapse;

}

.tabla th{

background:#2c3e50;

color:white;

padding:12px;

}

.tabla td{

padding:10px;

text-align:center;

border:1px solid #ddd;

}

.footer{

margin-top:40px;

text-align:center;

color:#888;

}

</style>

<div class="header">

<h1>CAPITAL EXPRESS</h1>

<p>Cartilla de cobro</p>

</div>

<div class="card">

<table class="info">

<tr>
<td class="titulo">Cliente</td>
<td>'.$prestamo['nombre'].'</td>
</tr>

<tr>
<td class="titulo">Cédula</td>
<td>'.$prestamo['cedula'].'</td>
</tr>

<tr>
<td class="titulo">Teléfono</td>
<td>'.$prestamo['telefono'].'</td>
</tr>

<tr>
<td class="titulo">Monto</td>
<td>$ '.number_format($prestamo['monto'],0,",",".").'</td>
</tr>

<tr>
<td class="titulo">Interés</td>
<td>'.$prestamo['interes'].'%</td>
</tr>

<tr>
<td class="titulo">Total</td>
<td>$ '.number_format($prestamo['total_pagar'],0,",",".").'</td>
</tr>

<tr>
<td class="titulo">Valor cuota</td>
<td>$ '.number_format($prestamo['valor_cuota'],0,",",".").'</td>
</tr>

<tr>
<td class="titulo">Mora</td>
<td>'.$prestamo['porcentaje_mora'].'%</td>
</tr>

<tr>
<td class="titulo">Fecha préstamo</td>
<td>'.date("d/m/Y", strtotime($prestamo['fecha_prestamo'])).'</td>
</tr>

<tr>
<td class="titulo">Última cuota</td>
<td>'.(!empty($ultimaCuota['ultima_cuota'])
        ? date("d/m/Y", strtotime($ultimaCuota['ultima_cuota']))
        : "No registrada").'</td>
</tr>

</table>

</div>

<h2 style="color:#2c3e50">
Detalle de cuotas
</h2>

<table class="tabla">

<tr>
<th>#</th>
<th>Valor</th>
<th>Estado</th>
</tr>

';

foreach ($cuotas as $cuota) {

$html .= '

<tr>

<td>'.$cuota['numero_cuota'].'</td>

<td>$ '.number_format(
    $cuota['valor'],
    0,
    ",",
    "."
).'
</td>

</td>

<td>'.$cuota['estado'].'</td>

</tr>

';

}

$html .= '

</table>

<div class="footer">

<hr>

Capital Express ©

</div>

';


$pdf = new Dompdf();

$pdf->loadHtml($html);

$pdf->setPaper("A4");

$pdf->render();

$pdf->stream(
"cartilla_".$prestamo["nombre"].".pdf",
[
"Attachment"=>true
]
);

exit;