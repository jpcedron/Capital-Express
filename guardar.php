<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();


/* =========================================================
   1. BUSCAR CLIENTE POR CÉDULA
   ========================================================= */

$sqlCliente = "SELECT id, nombre, cedula, telefono, direccion, estado_cliente
               FROM clientes
               WHERE cedula = ?";

$stmtCliente = $conexion->prepare($sqlCliente);
$stmtCliente->execute([$_POST['cedula']]);

$clienteExiste = $stmtCliente->fetch(PDO::FETCH_ASSOC);


/* =========================================================
   2. REGISTRAR CLIENTE SI NO EXISTE
   ========================================================= */

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

    // ID del cliente recién creado
    $cliente_id = $conexion->lastInsertId();

    // Cliente nuevo queda activo
    $estado_cliente = "activo";


} else {

    /* =====================================================
       3. OBTENER CLIENTE EXISTENTE
       ===================================================== */

    $cliente_id = $clienteExiste['id'];

    $estado_cliente = strtolower(
        trim($clienteExiste['estado_cliente'])
    );


    /* =====================================================
       4. BLOQUEAR CLIENTE INACTIVO
       ===================================================== */

    if ($estado_cliente === 'inactivo') {

        header("Location: index.php?error=cliente_inactivo");
        exit;
    }


    /* =====================================================
       5. ACTUALIZAR DATOS DEL CLIENTE
       ===================================================== */

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


/* =========================================================
   6. VALIDAR SI EL CLIENTE YA TIENE PRÉSTAMO ACTIVO
      AHORA USAMOS cliente_id
   ========================================================= */

$sqlBuscar = "SELECT estado
              FROM prestamos
              WHERE cliente_id = ?
              ORDER BY id DESC
              LIMIT 1";

$stmtBuscar = $conexion->prepare($sqlBuscar);
$stmtBuscar->execute([$cliente_id]);

$prestamoExistente = $stmtBuscar->fetch(PDO::FETCH_ASSOC);


if ($prestamoExistente) {

    if (
        $prestamoExistente['estado'] === "Activo" ||
        $prestamoExistente['estado'] === "Mora"
    ) {

        echo "<script>
            alert('Este cliente ya tiene un préstamo activo y no puede registrar otro.');
            window.location='index.php';
        </script>";

        exit;
    }
}


/* =========================================================
   7. CALCULAR EL PRÉSTAMO
   ========================================================= */

$monto = floatval($_POST['monto']);
$interes = floatval($_POST['interes']);
$numeroCuotas = intval($_POST['cuotas']);

$total_pagar =
    $monto +
    ($monto * $interes / 100);

$total_pagar = round($total_pagar, 2);

$abonado = 0;

$pendiente = $total_pagar;

$valor_cuota =
    round(
        $total_pagar / $numeroCuotas,
        2
    );

$porcentaje_mora =
    $_POST["porcentaje_mora"] ?? 2;


/* =========================================================
   8. GUARDAR PRÉSTAMO
   ========================================================= */

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
    $monto,
    $interes,
    $total_pagar,
    $numeroCuotas,
    $valor_cuota,
    date('Y-m-d'),
    $abonado,
    $pendiente,
    $porcentaje_mora,
    $_POST['frecuencia']
]);


/* =========================================================
   9. OBTENER ID DEL PRÉSTAMO
   ========================================================= */

$prestamo_id = $conexion->lastInsertId();


/* =========================================================
   10. GENERAR CUOTAS AUTOMÁTICAMENTE
   ========================================================= */

// Fecha del préstamo
$fecha = new DateTime(date('Y-m-d'));


$sqlCuota = "INSERT INTO cuotas
(prestamo_id, numero_cuota, fecha_vencimiento, valor)
VALUES (?,?,?,?)";

$stmtCuota = $conexion->prepare($sqlCuota);


/* =========================================================
   FRECUENCIA SEMANAL
   ========================================================= */

if ($_POST['frecuencia'] === "Semanal") {

    // Buscar el siguiente sábado
    $primerSabado = clone $fecha;

    if ($primerSabado->format('N') == 6) {

        // Si hoy es sábado, cobrar el siguiente sábado
        $primerSabado->modify('+7 days');

    } else {

        $primerSabado->modify('next saturday');
    }


    for ($i = 1; $i <= $numeroCuotas; $i++) {

        $stmtCuota->execute([
            $prestamo_id,
            $i,
            $primerSabado->format('Y-m-d'),
            $valor_cuota
        ]);

        $primerSabado->modify('+7 days');
    }


/* =========================================================
   FRECUENCIA QUINCENAL
   ========================================================= */

} else {

    // Lógica quincenal: día 15 y último día del mes

    $fechaCuota = clone $fecha;

    $dia = (int)$fechaCuota->format('d');


    if ($dia < 15) {

        // Próxima fecha: día 15
        $fechaCuota->setDate(
            $fechaCuota->format('Y'),
            $fechaCuota->format('m'),
            15
        );


    } else {

        $ultimoDia = (int)$fechaCuota->format('t');


        if ($dia < $ultimoDia) {

            // Próxima fecha: último día del mes
            $fechaCuota->setDate(
                $fechaCuota->format('Y'),
                $fechaCuota->format('m'),
                $ultimoDia
            );


        } else {

            // Pasar al día 15 del siguiente mes
            $fechaCuota->modify('first day of next month');

            $fechaCuota->setDate(
                $fechaCuota->format('Y'),
                $fechaCuota->format('m'),
                15
            );
        }
    }


    for ($i = 1; $i <= $numeroCuotas; $i++) {

        $stmtCuota->execute([
            $prestamo_id,
            $i,
            $fechaCuota->format('Y-m-d'),
            $valor_cuota
        ]);


        if ($fechaCuota->format('d') == '15') {

            // Después del 15 viene el último día del mismo mes
            $fechaCuota->setDate(
                $fechaCuota->format('Y'),
                $fechaCuota->format('m'),
                $fechaCuota->format('t')
            );


        } else {

            // Después del último día viene el 15 del siguiente mes
            $fechaCuota->modify('first day of next month');

            $fechaCuota->setDate(
                $fechaCuota->format('Y'),
                $fechaCuota->format('m'),
                15
            );
        }
    }
}


/* =========================================================
   11. REGRESAR AL LISTADO
   ========================================================= */

header("Location: listado.php");
exit;