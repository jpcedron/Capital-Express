<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$sql = " SELECT * FROM prestamos WHERE estado_cliente='activo' ORDER BY id DESC ";

$stmt = $conexion->prepare($sql);
$stmt->execute();

$prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Préstamos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <h2>Capital Express</h2>
    <p>Clientes Registrados</p>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="index.php" class="btn btn-primary">
            Nuevo Préstamo
        </a>
        
       <div>
         <a href="gestionar_clientes.php" class="btn btn-danger">
            Gestionar Clientes
        </a>

         <a href="nuevo_prestamo.php" class="btn btn-primary">
                Listado de Préstamos
            </a>
       </div>

        
    </div>
   

    <table class="table table-bordered table-striped">

        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Cédula</th>
                <th>Teléfono</th>
                <th>Monto</th>
                <th>Total Pagar</th>
                <th>Pendiente</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach($prestamos as $prestamo): ?>

            <tr>
                <td><?= $prestamo['id'] ?></td>
                <td><?= $prestamo['nombre'] ?></td>
                <td><?= $prestamo['cedula'] ?></td>
                <td><?= $prestamo['telefono'] ?></td>
                <td>$<?= number_format($prestamo['monto']) ?></td>
                <td>$<?= number_format($prestamo['total_pagar']) ?></td>
                <td>$<?= number_format($prestamo['pendiente']) ?></td>
                <td>

                    <?php

                    $sql = "SELECT MAX(fecha_vencimiento) AS ultima_cuota
                    FROM cuotas
                    WHERE prestamo_id = ?";

                    $stmt = $conexion->prepare($sql);
                    $stmt->execute([$prestamo['id']]);

                    $datosCuota = $stmt->fetch(PDO::FETCH_ASSOC);

                    $ultimaCuota = !empty($datosCuota['ultima_cuota'])
                        ? new DateTime($datosCuota['ultima_cuota'])
                        : null;

                    $hoy = new DateTime();

                    $hoy = date('Y-m-d');

                    if ($prestamo['estado'] == 'Mora') 
                        {
                        echo '<span class="badge bg-danger">Mora</span>';

                    } elseif ($prestamo['estado'] == 'Pagado') 
                    {
                        echo '<span class="badge bg-success">Pagado</span>';
                        
                    } else {
                        echo '<span class="badge bg-warning text-dark">Activo</span>';
                    }

                    ?>

                </td>

                <td>
                    <a href="cartilla.php?id=<?= $prestamo['id'] ?>"
                    class="btn btn-secondary btn-sm">
                    Ver Cartilla
                    </a>

                    <a href="registrar_pago.php?id=<?= $prestamo['id'] ?>"
                    class="btn btn-success btn-sm">
                    Registrar Pago
                    </a>

                    <a href="historial_pagos.php?prestamo_id=<?= $prestamo['id'] ?>"
                    class="btn btn-info btn-sm">
                    Historial
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

</body>
</html>