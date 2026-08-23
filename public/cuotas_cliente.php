<?php

require_once "../config/conexion.php";
require_once "auth_cliente.php";

// Conexión a la base de datos
$conexion = (new Conexion())->conectar();

// Datos del cliente que inició sesión
$cedula = $_SESSION["cliente_cedula"];

// Buscar el préstamo activo del cliente
$sql = "SELECT p.*
        FROM prestamos p
        INNER JOIN clientes c ON c.id = p.cliente_id
        WHERE c.cedula = ?
        AND p.estado != 'Pagado'
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->execute([$cedula]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestamo) {
    require_once "alerta_sin_cuotas.php";
    exit;
}

// ID del préstamo
$prestamo_id = $prestamo["id"];

// Obtener todas las cuotas del préstamo
$sql = "SELECT *
        FROM cuotas
        WHERE prestamo_id = ?
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
<link rel="stylesheet" href="../css/panel_de_usuario.css">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="card shadow">

        <div class="card-header text-white" style="background: linear-gradient(135deg, #0d1f3c, #1a3560); text-align: center;">

            <h4 class="mb-0">

                Historial de Cuotas

            </h4>

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

            <div class="text-end mt-4">

                <a href="panel_de_usuario.php" class="btn-ce">
                    <i class="bi bi-arrow-left-circle"></i>
                    Regresar al Panel
                </a>
                
            </div>

        </div>

    </div>

</div>

</body>

</html>