<?php

session_start();

require_once "../config/conexion.php";
$conexion = (new Conexion())->conectar();


/* TOTAL CLIENTES */

$sql = "SELECT COUNT(*) AS total FROM clientes";
$totalClientes = $conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];


/* PRESTAMOS ACTIVOS */

$sql = "SELECT COALESCE(SUM(monto),0) AS total
        FROM prestamos
        WHERE estado='Activo'";

$capitalPrestado = $conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];


/* TOTAL RECAUDADO */

$sql = "SELECT COALESCE(SUM(valor_pago),0) AS total
        FROM pagos";

$totalPagado = $conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];


/* TOTAL MORA */

$sql = "SELECT COALESCE(SUM(mora),0) AS total
        FROM prestamos";

$totalMora = $conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];

/* PRÉSTAMOS ACTIVOS */

$sql = "SELECT COUNT(*) AS total
        FROM prestamos
        WHERE estado = 'Activo'";

$prestamosActivos = $conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];


/*  PRÉSTAMOS PAGADOS */

$sql = "SELECT COUNT(*) AS total
        FROM prestamos
        WHERE estado = 'Pagado'";

$prestamosPagados = $conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];


/* CLIENTES EN MORA */
$clientesMora = $conexion->query("
    SELECT nombre, pendiente, mora
    FROM prestamos
    WHERE estado = 'Mora'
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC); 



/*  CUOTAS VENCIDAS */

$sql = "SELECT COUNT(*) AS total
        FROM cuotas
        WHERE estado <> 'Pagada'
        AND fecha_vencimiento < CURDATE()";

$cuotasVencidas = $conexion->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];

// Préstamos por estado
$activos = $conexion->query("
SELECT COUNT(*) AS total
FROM prestamos
WHERE estado='Activo'
")->fetch(PDO::FETCH_ASSOC)['total'];


$mora = $conexion->query(" 
SELECT COUNT(*) AS total
FROM prestamos
WHERE estado='Mora'
")->fetch(PDO::FETCH_ASSOC)['total'];

$pagados = $conexion->query(" SELECT COUNT(*) AS total
FROM prestamos
WHERE estado='Pagado'
")->fetch(PDO::FETCH_ASSOC)['total'];


// Totales económicos
$totalPrestado = $conexion->query(" SELECT COALESCE(SUM(monto),0) AS total
FROM prestamos
")->fetch(PDO::FETCH_ASSOC)['total'];

$totalRecuperado = $conexion->query(" SELECT COALESCE(SUM(abonado),0) AS total
FROM prestamos
")->fetch(PDO::FETCH_ASSOC)['total'];

$totalPendiente = $conexion->query(" SELECT COALESCE(SUM(pendiente),0) AS total
FROM prestamos
")->fetch(PDO::FETCH_ASSOC)['total'];

$cuotasVencidas = $conexion->query(" SELECT
    c.numero_cuota,
    c.fecha_vencimiento,
    p.nombre
FROM cuotas c
INNER JOIN prestamos p ON p.id = c.prestamo_id
WHERE c.estado='Pendiente'
AND c.fecha_vencimiento < CURDATE()
LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC); 


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



/* SALUDO SEGÚN HORA DEL DÍA 

date_default_timezone_set("America/Bogota");

$hora = date("H");

if($hora < 12){

    $saludo = "Buenos días";

}elseif($hora < 18){

    $saludo = "Buenas tardes";

}else{

    $saludo = "Buenas noches";

}*/

?>

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


    <div class="card shadow mt-4">

    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">⚠️ Alertas del sistema</h5>
    </div>

    <div class="card-body">

        <?php if(empty($clientesMora) && empty($cuotasVencidas)){ ?>

            <div class="alert alert-success mb-0">
                No hay alertas pendientes.
            </div>

        <?php } ?>

        <?php foreach($clientesMora as $cliente){ ?>

            <div class="alert alert-danger">

                <strong><?= htmlspecialchars($cliente['nombre']) ?></strong>

                tiene un préstamo en mora.

                <br>

                Pendiente:
                <strong>$<?= number_format($cliente['pendiente'],0,',','.') ?></strong>

            </div>

        <?php } ?>

        <?php foreach($cuotasVencidas as $cuota){ ?>

            <div class="alert alert-warning">

                <?= htmlspecialchars($cuota['nombre']) ?>

                tiene vencida la cuota

                <strong>#<?= $cuota['numero_cuota'] ?></strong>

                desde

                <strong><?= $cuota['fecha_vencimiento'] ?></strong>

            </div>

        <?php } ?>

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