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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">
            <i class="bi bi-calendar-check"></i>
            Control de Cuotas
        </h2>

        <a href="cartilla.php?id=<?= $prestamo['id'] ?>" class="btn btn-secondary">
            Volver
        </a>

    </div>

    <div class="card shadow mb-4">

        <div class="card-header text-white" style="background: linear-gradient(135deg, #1a3560, #0d1b3a);">
            <h4 class="mb-0">
                Información del Préstamo
            </h4>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4 mb-3">

                    <strong>Cliente</strong><br>

                    <?= htmlspecialchars($prestamo['cliente_nombre']) ?>

                </div>

                <div class="col-md-4 mb-3">

                    <strong>Cédula</strong><br>

                    <?= htmlspecialchars($prestamo['cliente_cedula']) ?>

                </div>

                <div class="col-md-4 mb-3">

                    <strong>Estado</strong><br>

                    <?php

                    if($prestamo['estado']=="Pagado"){

                        echo '<span class="badge bg-success">Pagado</span>';

                    }elseif($prestamo['estado']=="Mora"){

                        echo '<span class="badge bg-danger">Mora</span>';

                    }else{

                        echo '<span class="badge bg-warning">Activo</span>';

                    }

                    ?>

                </div>

            </div>

            <hr>

            <div class="row">

                <div class="col-md-3">

                    <strong>Monto</strong><br>

                    $<?= number_format($prestamo['monto'],0,",",".") ?>

                </div>

                <div class="col-md-3">

                    <strong>Abonado</strong><br>

                    $<?= number_format($prestamo['abonado'],0,",",".") ?>

                </div>

                <div class="col-md-3">

                    <strong>Pendiente</strong><br>

                    $<?= number_format($prestamo['pendiente'],0,",",".") ?>

                </div>

                <div class="col-md-3">

                    <strong>Mora</strong><br>

                    $<?= number_format($prestamo['mora'],0,",",".") ?>

                </div>

            </div>

        </div>

    </div>

    <div class="card shadow">

        <div class="card-header text-dark" style="background-color: #c9a84c">

            <h5 class="mb-0">

                Historial de Cuotas

            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-secondary">

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

                        <tr>

                            <td><?= $c['numero_cuota']; ?></td>

                            <td><?= $c['fecha_vencimiento']; ?></td>

                            <td>$<?= number_format($c['valor'],0,",","."); ?></td>

                            <td>

                            <?php

                            if($c['pagada']){

                                echo '<span class="badge bg-success">Pagada</span>';

                            }else{

                                echo '<span class="badge bg-warning text-dark">Pendiente</span>';

                            }

                            ?>

                            </td>

                            <td><?= $c['dias_atraso']; ?></td>

                            <td>

                                $<?= number_format($c['mora'],0,",","."); ?>

                            </td>

                            <td>

                                <?php

                                if($c['fecha_pago']){

                                    echo $c['fecha_pago'];

                                }else{

                                    echo "-";

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