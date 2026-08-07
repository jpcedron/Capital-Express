<?php

session_start();

require_once "../config/conexion.php";
$conexion = (new Conexion())->conectar();


/* INDICADORES GENERALES */

/* TOTAL CLIENTES */
$sql = "SELECT COUNT(*) AS total FROM clientes";
$totalClientes = $conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];


/* PRÉSTAMOS ACTIVOS */
$sql = "SELECT COUNT(*) AS total
        FROM prestamos
        WHERE estado = 'Activo'";
$prestamosActivos = $conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];


/* PRÉSTAMOS PAGADOS */
$sql = "SELECT COUNT(*) AS total
        FROM prestamos
        WHERE estado = 'Pagado'";
$prestamosPagados = $conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];


/* CAPITAL PRESTADO EN PRÉSTAMOS ACTIVOS */
$sql = "SELECT COALESCE(SUM(monto), 0) AS total
        FROM prestamos
        WHERE estado = 'Activo'";
$capitalPrestado = $conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];


/* TOTAL RECAUDADO */
$sql = "SELECT COALESCE(SUM(valor_pago), 0) AS total
        FROM pagos";
$totalPagado = $conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];


/* TOTAL MORA */
$sql = "SELECT COALESCE(SUM(mora), 0) AS total
        FROM prestamos";
$totalMora = $conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];


/* PRÉSTAMOS POR ESTADO - GRÁFICAS */

$activos = $prestamosActivos;

$mora = $conexion->query("
    SELECT COUNT(*) AS total
    FROM prestamos
    WHERE estado = 'Mora'
")->fetch(PDO::FETCH_ASSOC)['total'];

$pagados = $prestamosPagados;


/* TOTALES ECONÓMICOS */

$totalPrestado = $conexion->query("
    SELECT COALESCE(SUM(monto), 0) AS total
    FROM prestamos
")->fetch(PDO::FETCH_ASSOC)['total'];


$totalRecuperado = $conexion->query("
    SELECT COALESCE(SUM(abonado), 0) AS total
    FROM prestamos
")->fetch(PDO::FETCH_ASSOC)['total'];


$totalPendiente = $conexion->query("
    SELECT COALESCE(SUM(pendiente), 0) AS total
    FROM prestamos
")->fetch(PDO::FETCH_ASSOC)['total'];


/* ALERTAS INTELIGENTES */


/* 🔴 CLIENTES EN MORA */

$clientesMora = $conexion->query(" SELECT 
        p.id AS prestamo_id,
        c.id AS cliente_id,
        c.nombre,
        c.cedula,
        p.pendiente,
        p.mora
    FROM prestamos p
    INNER JOIN clientes c 
        ON p.cedula = c.cedula
    WHERE p.estado = 'Mora'
    ORDER BY p.mora DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);


/* 🟠 CUOTAS VENCIDAS */

$cuotasVencidas = $conexion->query(" SELECT
        c.id AS cuota_id,
        c.prestamo_id,
        c.numero_cuota,
        c.fecha_vencimiento,
        c.valor,
        cl.id AS cliente_id,
        cl.nombre,
        cl.cedula
    FROM cuotas c
    INNER JOIN prestamos p
        ON p.id = c.prestamo_id
    INNER JOIN clientes cl
        ON p.cedula = cl.cedula
    WHERE c.estado = 'Pendiente'
      AND c.fecha_vencimiento < CURDATE()
    ORDER BY c.fecha_vencimiento ASC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);


/* 🟡 PRÓXIMOS VENCIMIENTOS
   Próximos 3 días */

$proximosVencimientos = $conexion->query(" SELECT
        c.id AS cuota_id,
        c.prestamo_id,
        c.numero_cuota,
        c.fecha_vencimiento,
        c.valor,
        cl.id AS cliente_id,
        cl.nombre,
        cl.cedula
    FROM cuotas c
    INNER JOIN prestamos p
        ON p.id = c.prestamo_id
    INNER JOIN clientes cl
        ON p.cedula = cl.cedula
    WHERE c.estado = 'Pendiente'
      AND c.fecha_vencimiento >= CURDATE()
      AND c.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)
    ORDER BY c.fecha_vencimiento ASC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);


/* ÚLTIMOS PAGOS */

$sqlUltimosPagos = " SELECT
        pa.fecha_pago,
        pa.valor_pago,
        pa.pago_capital,
        pa.pago_mora,
        pa.saldo_restante,
        c.nombre,
        p.cedula
    FROM pagos pa
    INNER JOIN prestamos p
        ON pa.prestamo_id = p.id
    INNER JOIN clientes c
        ON p.cedula = c.cedula
    ORDER BY pa.fecha_pago DESC
    LIMIT 10
";

$stmt = $conexion->query($sqlUltimosPagos);
$ultimosPagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- SALUDO SEGÚN HORA DEL DÍA 
 
date_default_timezone_set("America/Bogota");

$hora = date("H");

if($hora < 12){

    $saludo = "Buenos días";

}elseif($hora < 18){

    $saludo = "Buenas tardes";

}else{

    $saludo = "Buenas noches";

} -->


<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | Capital Express</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link rel="stylesheet" href="../css/dashboard.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Sidebar -->
    <div class="sidebar">

        <h3 class="brand-heading">

            <i class="fa-solid fa-building-columns"></i>

            Capital Express

        </h3>

            <a href="#">

                <i class="fa-solid fa-chart-line"></i>

                Dashboard

            </a>

            <a href="#">

                <i class="fa-solid fa-users"></i>

                Clientes

            </a>

            <a href="#">

                <i class="fa-solid fa-money-bill-wave"></i>

                Préstamos

            </a>

            <a href="#">

                <i class="fa-solid fa-wallet"></i>

                Pagos

            </a>

            <a href="#">

                <i class="fa-solid fa-calendar-days"></i>

                Cuotas

            </a>

            <a href="#">

                <i class="fa-solid fa-file-lines"></i>

                Reportes

            </a>

            <a href="#">

                <i class="fa-solid fa-gear"></i>

                Configuración

            </a>

            <a href="#">

                <i class="fa-solid fa-right-from-bracket"></i>

                Cerrar sesión

            </a>

    </div>

    <!-- Topbar -->
    <div class="topbar">

        <h4>

        Panel Administrativo

        </h4>


        <div class="usuario">

            <i class="fa-solid fa-circle-user"></i>

            Administrador


        </div>


        </div>

        <!-- Contenido -->
        <div class="contenido">

            <div class="bienvenida">

                <h2>

                Bienvenido a Capital Express

                </h2>

                <p>

                Desde este panel podrás administrar clientes, préstamos, pagos, cuotas y consultar toda la información del sistema.

                </p>

        <div class="row mt-2">

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card-mini">
                    <i class="fa-solid fa-circle-check"></i>
                    <h3><?= $prestamosActivos ?></h3>
                    <span>Préstamos Activos</span>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card-mini">
                    <i class="fa-solid fa-handshake"></i>
                    <h3><?= $prestamosPagados ?></h3>
                    <span>Préstamos Pagados</span>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card-mini">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <h3><?= count($clientesMora) ?></h3>
                    <span>Clientes en Mora</span>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card-mini">
                    <i class="fa-solid fa-calendar-days"></i>
                    <h3><?= count($cuotasVencidas) ?></h3>
                    <span>Cuotas Vencidas</span>
                </div>
            </div>

        </div>

    </div>

    <!-- ACCIONES RÁPIDAS -->

<div class="row mt-2 mb-4">

    <div class="col-12">

        <div class="quick-actions-card">

            <div class="quick-actions-header">

                <div>
                    <span class="quick-actions-label">
                        <i class="fa-solid fa-bolt"></i>
                        Acceso rápido
                    </span>

                    <h4>Acciones rápidas</h4>

                    <p>
                        Accede directamente a las funciones principales
                        de Capital Express.
                    </p>
                </div>

            </div>


            <div class="row g-3">

                <!-- NUEVO PRÉSTAMO -->

                <div class="col-xl-3 col-md-6">

                    <a href="../index.php"
                       class="quick-action">

                        <div class="quick-action-icon">
                            <i class="fa-solid fa-file-circle-plus"></i>
                        </div>

                        <div class="quick-action-content">

                            <h5>Nuevo préstamo</h5>

                            <span>
                                Registrar un nuevo crédito
                            </span>

                        </div>

                        <i class="fa-solid fa-arrow-right quick-action-arrow"></i>

                    </a>

                </div>


                <!-- LISTADO DE PRÉSTAMOS -->

                <div class="col-xl-3 col-md-6">

                    <a href="../nuevo_prestamo.php"
                       class="quick-action">

                        <div class="quick-action-icon">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>

                        <div class="quick-action-content">

                            <h5>Préstamos</h5>

                            <span>
                                Consultar y gestionar créditos
                            </span>

                        </div>

                        <i class="fa-solid fa-arrow-right quick-action-arrow"></i>

                    </a>

                </div>


                <!-- GESTIONAR CLIENTES -->

                <div class="col-xl-3 col-md-6">

                    <a href="../gestionar_clientes.php"
                       class="quick-action">

                        <div class="quick-action-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>

                        <div class="quick-action-content">

                            <h5>Clientes</h5>

                            <span>
                                Consultar y gestionar clientes
                            </span>

                        </div>

                        <i class="fa-solid fa-arrow-right quick-action-arrow"></i>

                    </a>

                </div>


                <!-- REGISTRAR PAGO -->

                <div class="col-xl-3 col-md-6">

                    <a href="../listado.php"
                       class="quick-action">

                        <div class="quick-action-icon">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                        </div>

                        <div class="quick-action-content">

                            <h5>Registrar pago</h5>

                            <span>
                                Seleccionar un préstamo
                            </span>

                        </div>

                        <i class="fa-solid fa-arrow-right quick-action-arrow"></i>

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

    <div class="row mt-4">

        <div class="col-xl-3 col-md-6 mb-4">

            <div class="card-dashboard">

                <div>

                    <h6>Clientes</h6>

                    <h3><?php echo number_format($totalClientes); ?></h3>

                </div>

                <i class="fa-solid fa-users"></i>

            </div>

        </div>

        <div class="col-xl-3 col-md-6 mb-4">

            <div class="card-dashboard">

                <div>

                    <h6>Capital Prestado</h6>

                    <h3>$<?php echo number_format($capitalPrestado, 0, ',', '.'); ?></h3>

                </div>

                <i class="fa-solid fa-money-bill-wave"></i>

            </div>

        </div>

        <div class="col-xl-3 col-md-6 mb-4">

            <div class="card-dashboard">

                <div>

                    <h6>Total Recaudado</h6>

                    <h3>$<?php echo number_format($totalPagado, 0, ',', '.'); ?></h3>

                </div>

                <i class="fa-solid fa-wallet"></i>

            </div>

        </div>

        <div class="col-xl-3 col-md-6 mb-4">

            <div class="card-dashboard">

                <div>

                    <h6>Mora</h6>

                    <h3>$<?php echo number_format($totalMora, 0, ',', '.'); ?></h3>

                </div>

                <i class="fa-solid fa-triangle-exclamation"></i>

            </div>

        </div>

    </div>



   <div class="row mt-4">

    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm p-3">

            <h5 class="mb-3">
                <i class="fa-solid fa-chart-column text-primary"></i>
                Préstamos por Estado
            </h5>

            <canvas id="graficaPrestamos"></canvas>

        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm p-3">

            <h5 class="mb-3">
                <i class="fa-solid fa-chart-pie text-warning"></i>
                Distribución
            </h5>

            <canvas id="graficaCircular"></canvas>

        </div>
    </div>

</div>


<div class="row">

    <div class="col-12">

        <div class="card shadow-sm">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="fa-solid fa-money-bill-wave text-success"></i>
                    Últimos pagos registrados
                </h5>

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Cliente</th>
                            <th>Cédula</th>
                            <th>Fecha</th>
                            <th>Valor</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($ultimosPagos as $pago): ?>

                        <tr>

                            <td><?= htmlspecialchars($pago['nombre']) ?></td>

                            <td><?= htmlspecialchars($pago['cedula']) ?></td>

                            <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>

                            <td class="text-end">
                                $<?= number_format($pago['pago_capital'], 0, ',', '.') ?>
                            </td>

                            <td class="text-end text-danger">
                                $<?= number_format($pago['pago_mora'], 0, ',', '.') ?>
                            </td>

                            <td class="text-end text-success fw-bold">
                                $<?= number_format($pago['valor_pago'], 0, ',', '.') ?>
                            </td>

                            <td class="text-end">
                                $<?= number_format($pago['saldo_restante'], 0, ',', '.') ?>
                            </td>

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>   

<!-- PANEL DE ALERTAS INTELIGENTES -->

<div class="card shadow mt-4">

    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">

        <h5 class="mb-0">
            <i class="fa-solid fa-bell me-2"></i>
            Alertas Inteligentes
        </h5>

        <?php

        $totalAlertas =
            count($clientesMora) +
            count($cuotasVencidas) +
            count($proximosVencimientos);

        ?>

        <?php if ($totalAlertas > 0): ?>

            <span class="badge bg-danger">
                <?= $totalAlertas ?> alerta<?= $totalAlertas != 1 ? 's' : '' ?>
            </span>

        <?php else: ?>

            <span class="badge bg-success">
                Todo al día
            </span>

        <?php endif; ?>

    </div>


    <div class="card-body">


        <!-- SIN ALERTAS -->

        <?php if ($totalAlertas === 0): ?>

            <div class="alert alert-success mb-0">

                <i class="fa-solid fa-circle-check me-2"></i>

                <strong>Todo está al día.</strong>

                No hay clientes en mora, cuotas vencidas
                ni vencimientos próximos.

            </div>

        <?php endif; ?>


        <!-- CLIENTES EN MORA -->

        <?php if (!empty($clientesMora)): ?>

            <div class="mb-4">

                <h6 class="text-danger fw-bold mb-3">

                    <i class="fa-solid fa-triangle-exclamation me-2"></i>

                    Clientes en mora

                </h6>


                <?php foreach ($clientesMora as $cliente): ?>

                    <div class="alert alert-danger d-flex justify-content-between align-items-center">

                        <div>

                            <strong>
                                <?= htmlspecialchars($cliente['nombre']) ?>
                            </strong>

                            <br>

                            <small>
                                Cédula:
                                <?= htmlspecialchars($cliente['cedula']) ?>
                            </small>

                            <br>

                            Pendiente:

                            <strong>
                                $<?= number_format($cliente['pendiente'], 0, ',', '.') ?>
                            </strong>

                            <?php if ($cliente['mora'] > 0): ?>

                                <span class="ms-2">

                                    Mora:

                                    <strong>
                                        $<?= number_format($cliente['mora'], 0, ',', '.') ?>
                                    </strong>

                                </span>

                            <?php endif; ?>

                        </div>


                        <div>

                            <a href="../prestamos/cartilla.php?id=<?= $cliente['prestamo_id'] ?>"
                               class="btn btn-sm btn-danger">

                                <i class="fa-solid fa-eye me-1"></i>

                                Ver préstamo

                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


        <!-- CUOTAS VENCIDAS -->

        <?php if (!empty($cuotasVencidas)): ?>

            <div class="mb-4">

                <h6 class="text-warning fw-bold mb-3">

                    <i class="fa-solid fa-calendar-xmark me-2"></i>

                    Cuotas vencidas

                </h6>


                <?php foreach ($cuotasVencidas as $cuota): ?>

                    <div class="alert alert-warning d-flex justify-content-between align-items-center">

                        <div>

                            <strong>
                                <?= htmlspecialchars($cuota['nombre']) ?>
                            </strong>

                            <br>

                            Cuota

                            <strong>
                                #<?= $cuota['numero_cuota'] ?>
                            </strong>

                            vencida desde

                            <strong>
                                <?= date('d/m/Y', strtotime($cuota['fecha_vencimiento'])) ?>
                            </strong>

                            <br>

                            <small>

                                Valor cuota:

                                <strong>
                                    $<?= number_format($cuota['valor'], 0, ',', '.') ?>
                                </strong>

                            </small>

                        </div>


                        <div>

                            <a href="../prestamos/cuotas.php?id=<?= $cuota['prestamo_id'] ?>"
                               class="btn btn-sm btn-warning">

                                <i class="fa-solid fa-calendar-days me-1"></i>

                                Ver cuotas

                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


        <!--  PRÓXIMOS VENCIMIENTOS-->

        <?php if (!empty($proximosVencimientos)): ?>

            <div>

                <h6 class="text-primary fw-bold mb-3">

                    <i class="fa-solid fa-clock me-2"></i>

                    Próximos vencimientos

                </h6>


                <?php foreach ($proximosVencimientos as $cuota): ?>

                    <div class="alert alert-primary d-flex justify-content-between align-items-center">

                        <div>

                            <strong>
                                <?= htmlspecialchars($cuota['nombre']) ?>
                            </strong>

                            <br>

                            Cuota

                            <strong>
                                #<?= $cuota['numero_cuota'] ?>
                            </strong>

                            vence el

                            <strong>
                                <?= date('d/m/Y', strtotime($cuota['fecha_vencimiento'])) ?>
                            </strong>

                            <br>

                            <small>

                                Valor:

                                <strong>
                                    $<?= number_format($cuota['valor'], 0, ',', '.') ?>
                                </strong>

                            </small>

                        </div>


                        <div>

                            <a href="../prestamos/cuotas.php?id=<?= $cuota['prestamo_id'] ?>"
                               class="btn btn-sm btn-primary">

                                <i class="fa-solid fa-eye me-1"></i>

                                Ver cuotas

                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


    </div>

</div>

</div>

</div>  

</div>



<script>
const activos = <?= $activos ?>;
const mora = <?= $mora ?>;
const pagados = <?= $pagados ?>;

new Chart(document.getElementById('graficaPrestamos'),{

    type:'bar',

    data:{

        labels:[
            'Activos',
            'En Mora',
            'Pagados'
        ],

        datasets:[{

            label:'Préstamos',

            data:[
                activos,
                mora,
                pagados
            ]

        }]

    },

    options:{

        responsive:true,

        plugins:{
            legend:{
                display:false
            }
        }

    }

});

new Chart(document.getElementById('graficaCircular'),{

    type:'doughnut',

    data:{

        labels:[
            'Activos',
            'Mora',
            'Pagados'
        ],

        datasets:[{

            data:[
                activos,
                mora,
                pagados
            ]

        }]

    }

});
</script>


</body>
</html>