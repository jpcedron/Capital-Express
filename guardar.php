<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

/* VALIDAR SI YA EXISTE */

$sqlBuscar = "SELECT estado
              FROM prestamos
              WHERE cedula = ?
              ORDER BY id DESC
              LIMIT 1";

$stmtBuscar = $conexion->prepare($sqlBuscar);
$stmtBuscar->execute([$_POST['cedula']]);

$cliente = $stmtBuscar->fetch(PDO::FETCH_ASSOC);

if ($cliente) {

    if ($cliente['estado'] == "Activo" || $cliente['estado'] == "Mora") {

        echo "<script>
        alert('Este cliente ya tiene un préstamo activo y no puede registrar otro.');
        window.location='index.php';
        </script>";
        exit;
    }
}


/* REGISTRAR O ACTUALIZAR CLIENTE */
$sqlCliente = "SELECT id, estado_cliente
               FROM clientes
               WHERE cedula = ?";

$stmtCliente = $conexion->prepare($sqlCliente);
$stmtCliente->execute([$_POST['cedula']]);

$clienteExiste = $stmtCliente->fetch(PDO::FETCH_ASSOC);


if (!$clienteExiste) {

    // La contraseña inicial será la cédula
    $password = password_hash($_POST['cedula'], PASSWORD_DEFAULT);

    $sqlInsertarCliente = "INSERT INTO clientes
    (
        nombre,
        cedula,
        telefono,
        direccion,
        password,
        estado_cliente
    )
    VALUES
    (
        ?, ?, ?, ?, ?, 'activo'
    )";

    $stmtInsertar = $conexion->prepare($sqlInsertarCliente);

    $stmtInsertar->execute([
        $_POST['nombre'],
        $_POST['cedula'],
        $_POST['telefono'],
        $_POST['direccion'],
        $password
    ]);

    // Obtener el ID del cliente recién creado
    $cliente_id = $conexion->lastInsertId();

} else {

    // ID del cliente existente
    $cliente_id = $clienteExiste['id'];

    // Actualizar datos básicos
    $sqlActualizar = "UPDATE clientes
                      SET
                          nombre = ?,
                          telefono = ?,
                          direccion = ?
                      WHERE id = ?";

    $stmtActualizar = $conexion->prepare($sqlActualizar);

    $stmtActualizar->execute([
        $_POST['nombre'],
        $_POST['telefono'],
        $_POST['direccion'],
        $cliente_id
    ]);
}

/* CALCULAR EL PRÉSTAMO */

$total_pagar =
$_POST['monto'] +
($_POST['monto'] * $_POST['interes'] / 100);

$total_pagar = round($total_pagar, 2);

$abonado = 0;

$pendiente = $total_pagar;

$valor_cuota =
round(
    $total_pagar / $_POST['cuotas'],
    2
);

$porcentaje_mora =
$_POST["porcentaje_mora"] ?? 2;

/* GUARDAR */

$sql = "INSERT INTO prestamos
(
cliente_id,
nombre,
cedula,
telefono,
direccion,
monto,
interes,
total_pagar,
cuotas,
valor_cuota,
fecha_prestamo,
abonado,
pendiente,
porcentaje_mora,
frecuencia
)
VALUES
(
?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
)";

$stmt = $conexion->prepare($sql);

$stmt->execute([
    $cliente_id,
    $_POST['nombre'],
    $_POST['cedula'],
    $_POST['telefono'],
    $_POST['direccion'],
    $_POST['monto'],
    $_POST['interes'],
    $total_pagar,
    $_POST['cuotas'],
    $valor_cuota,
    date('Y-m-d'),
    $abonado,
    $pendiente,
    $porcentaje_mora,
    $_POST['frecuencia']
]);

// ID del préstamo recién creado
$prestamo_id = $conexion->lastInsertId();

// Fecha del préstamo
$fecha = new DateTime(date('Y-m-d'));

$sqlCuota = "INSERT INTO cuotas
(prestamo_id, numero_cuota, fecha_vencimiento, valor)
VALUES (?,?,?,?)";

$stmtCuota = $conexion->prepare($sqlCuota);

$ultima_fecha = null;

if ($_POST['frecuencia'] == "Semanal") {

    // Buscar el siguiente sábado
    $primerSabado = clone $fecha;

    if ($primerSabado->format('N') == 6) {
        // Si hoy es sábado, cobrar el siguiente sábado
        $primerSabado->modify('+7 days');
    } else {
        $primerSabado->modify('next saturday');
    }

    for ($i = 1; $i <= $_POST['cuotas']; $i++) {

        $stmtCuota->execute([
            $prestamo_id,
            $i,
            $primerSabado->format('Y-m-d'),
            $valor_cuota
        ]);

        $ultima_fecha = $primerSabado->format('Y-m-d');

        $primerSabado->modify('+7 days');
    }

} else {

    // Lógica quincenal (15 y último día del mes)

    $fechaCuota = clone $fecha;

    $dia = (int)$fechaCuota->format('d');

    if ($dia < 15) {

        $fechaCuota->setDate(
            $fechaCuota->format('Y'),
            $fechaCuota->format('m'),
            15
        );

    } else {

        $ultimoDia = $fechaCuota->format('t');

        if ($dia < $ultimoDia) {

            $fechaCuota->setDate(
                $fechaCuota->format('Y'),
                $fechaCuota->format('m'),
                $ultimoDia
            );

        } else {

            $fechaCuota->modify('first day of next month');
            $fechaCuota->setDate(
                $fechaCuota->format('Y'),
                $fechaCuota->format('m'),
                15
            );
        }
    }

    for ($i = 1; $i <= $_POST['cuotas']; $i++) {

        $stmtCuota->execute([
            $prestamo_id,
            $i,
            $fechaCuota->format('Y-m-d'),
            $valor_cuota
        ]);

        $ultima_fecha = $fechaCuota->format('Y-m-d');

        if ($fechaCuota->format('d') == 15) {

            $fechaCuota->setDate(
                $fechaCuota->format('Y'),
                $fechaCuota->format('m'),
                $fechaCuota->format('t')
            );

        } else {

            $fechaCuota->modify('first day of next month');
            $fechaCuota->setDate(
                $fechaCuota->format('Y'),
                $fechaCuota->format('m'),
                15
            );
        }
    }
}


header("Location:listado.php");
exit;

/*
$total_pagar =
$_POST['monto'] +
($_POST['monto'] * $_POST['interes'] / 100);
$abonado = 0;
$pendiente = $total_pagar;

$valor_cuota =
$total_pagar / $_POST['cuotas'];
*/