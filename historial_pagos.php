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

<!DOCTYPE html>
<html>

<head>
    <title>Historial de pagos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5 mb-5">

    <div class="card shadow">
        <div class="card-header  text-white text-center", style="background: linear-gradient(135deg, #1a3560, #0d1b3a);"> 
            <h2 class="mb-0">Historial de pagos</h2>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered mb-0">
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
                        <td>
                            <?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?>
                        </td>
                        <td>
                            $<?= number_format($pago['valor_pago'],0,",",".") ?>
                        </td>
                        <td>
                            $<?= number_format($pago['saldo_restante'],0,",",".") ?>
                        </td>
                        <td>
                            <?= $pago['observacion'] ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>                                                    
            </table>

        </div> <div class="card-footer white d-flex justify-content-between align-items-center py-3">
            <a href="listado.php" class="btn btn-secondary">
                 Volver al Listado
            </a>
        </div>

    </div> 

</body>
</html>