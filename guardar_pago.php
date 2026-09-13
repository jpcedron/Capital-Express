<?php

require_once "public/auth_admin.php";
require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

/* =========================================================
   VALIDAR DATOS DEL FORMULARIO
========================================================= */

$prestamo_id = filter_input( INPUT_POST, 'prestamo_id', FILTER_VALIDATE_INT );
$valor_pago = filter_input( INPUT_POST, 'valor_pago', FILTER_VALIDATE_FLOAT );

if (!$prestamo_id || $valor_pago === false || $valor_pago <= 0) {
    die("Datos de pago inválidos.");
}

/*
 * Una única fecha para todo el registro del pago.
 * Se utilizará tanto en pagos como en cuotas.
 */
$fechaPago = date('Y-m-d H:i:s');


/* =========================================================
   OBTENER PRÉSTAMO
========================================================= */

$sql = "SELECT *
        FROM prestamos
        WHERE id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$prestamo_id]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestamo) {
    die("Préstamo no encontrado.");
}

if ($prestamo['estado'] === 'Pagado') {
    die("Este préstamo ya fue pagado.");
}


/* =========================================================
   BUSCAR LA PRIMERA CUOTA PENDIENTE O EN MORA
========================================================= */

$sqlCuota = "
    SELECT *
    FROM cuotas
    WHERE prestamo_id = ?
    AND estado IN ('Pendiente','Mora')
    ORDER BY numero_cuota ASC
    LIMIT 1
";

$stmtCuota = $conexion->prepare($sqlCuota);
$stmtCuota->execute([$prestamo_id]);

$cuota = $stmtCuota->fetch(PDO::FETCH_ASSOC);

if (!$cuota) {
    header("Location: listado.php");
    exit;
}


/* =========================================================
   DATOS INICIALES
========================================================= */

$pendiente = floatval($prestamo['pendiente']);
$mora = floatval($prestamo['mora']);

$hoy = new DateTime();


/* =========================================================
   OBTENER FECHA DE LA ÚLTIMA CUOTA
========================================================= */

$sqlUltima = "
    SELECT MAX(fecha_vencimiento) AS ultima_fecha
    FROM cuotas
    WHERE prestamo_id = ?
";

$stmtUltima = $conexion->prepare($sqlUltima);
$stmtUltima->execute([$prestamo_id]);

$ultimaCuota = $stmtUltima->fetch(PDO::FETCH_ASSOC);


/* =========================================================
   VERIFICAR SI EL PRÉSTAMO YA FINALIZÓ
========================================================= */

$prestamoFinalizado = false;

if (!empty($ultimaCuota['ultima_fecha'])) {
    $fechaFinal = new DateTime($ultimaCuota['ultima_fecha']);
    if ($hoy > $fechaFinal) {
        $diasFinal = $fechaFinal->diff($hoy)->days;
        if ($diasFinal >= 3) {
            $prestamoFinalizado = true;
        }
    }
}


/* =========================================================
   DEFINIR BASE PARA CALCULAR MORA
========================================================= */

if ($prestamoFinalizado) {
    // El préstamo ya terminó:
    // la mora se calcula sobre el saldo pendiente.
    $baseMora = $pendiente;
} else {
    // El préstamo sigue vigente:
    // la mora se calcula sobre la cuota vencida.
    $baseMora = floatval($cuota['valor']);
}


/* =========================================================
   CALCULAR DÍAS DE ATRASO
========================================================= */

$fecha_vencimiento = new DateTime( $cuota['fecha_vencimiento'] );

$dias_atraso = 0;

if ($hoy > $fecha_vencimiento) {
    $dias_atraso = $fecha_vencimiento
        ->diff($hoy)
        ->days;
}


/* =========================================================
   CALCULAR MORA
========================================================= */

if ($dias_atraso >= 3) {
    if ($dias_atraso <= 14) {
        $porcentaje = 5;
    } elseif ($dias_atraso <= 29) {
        $porcentaje = 10;
    } elseif ($dias_atraso <= 44) {
        $porcentaje = 15;
    } else {
        $porcentaje = 20;
    }

    $ultima_mora = $prestamo['ultima_mora'];


    if ($ultima_mora !== date('Y-m-d')) {
        $semanas = max( 1, ceil(($dias_atraso - 2) / 7) );

        $mora = round(
            $baseMora *
            ($porcentaje / 100) *
            $semanas,
            2
        );


        $sql = "
            UPDATE prestamos
            SET
                mora = ?,
                porcentaje_mora = ?,
                ultima_mora = ?,
                estado = 'Mora'
            WHERE id = ?
        ";

        $stmt = $conexion->prepare($sql);

        $stmt->execute([
            $mora,
            $porcentaje,
            date('Y-m-d'),
            $prestamo_id
        ]);
    }
}


/* =========================================================
   ACTUALIZAR ESTADO DE LA CUOTA ANTES DEL PAGO
========================================================= */

$estadoCuota = ($mora > 0)
    ? "Mora"
    : "Pendiente";


$sql = "
    UPDATE cuotas
    SET
        dias_atraso = ?,
        mora = ?,
        estado = ?
    WHERE id = ?
";

$stmt = $conexion->prepare($sql);

$stmt->execute([
    $dias_atraso,
    $mora,
    $estadoCuota,
    $cuota['id']
]);


/* =========================================================
   PAGAR PRIMERO LA MORA
========================================================= */

$pago_mora = 0;
$pago_capital = 0;

if ($mora > 0) {
    $pago_mora = min(
        $valor_pago,
        $mora
    );

    $mora -= $pago_mora;

    $valor_pago -= $pago_mora;
}


/* =========================================================
   DESPUÉS PAGAR CAPITAL
========================================================= */

if ($valor_pago > 0) {

    $pago_capital = min(
        $valor_pago,
        $pendiente
    );

    $pendiente -= $pago_capital;
}


/* =========================================================
   ACTUALIZAR ABONADO
========================================================= */

$nuevo_abonado =
    $prestamo['abonado']
    + $pago_mora
    + $pago_capital;


/* =========================================================
   DETERMINAR ESTADO DEL PRÉSTAMO
========================================================= */

$estado = ($pendiente <= 0 && $mora <= 0)
    ? "Pagado"
    : ($mora > 0 ? "Mora" : "Activo");


/* =========================================================
   ACTUALIZAR PRÉSTAMO
========================================================= */

$sql = "
    UPDATE prestamos
    SET
        abonado = ?,
        pendiente = ?,
        mora = ?,
        estado = ?
    WHERE id = ?
";

$stmt = $conexion->prepare($sql);

$stmt->execute([
    $nuevo_abonado,
    $pendiente,
    $mora,
    $estado,
    $prestamo_id
]);


/* =========================================================
   MARCAR LA CUOTA ACTUAL COMO PAGADA
========================================================= */

if ($pago_capital > 0 || $pago_mora > 0) {

    $sql = "
        UPDATE cuotas
        SET
            pagada = 1,
            estado = 'Pagada',
            fecha_pago = ?,
            dias_atraso = 0,
            mora = 0
        WHERE id = ?
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        $fechaPago,
        $cuota['id']
    ]);
}


/* =========================================================
   SI EL PRÉSTAMO QUEDÓ PAGADO,
   MARCAR LAS CUOTAS RESTANTES
========================================================= */

if ($estado === "Pagado") {

    $sql = "
        UPDATE cuotas
        SET
            pagada = 1,
            estado = 'Pagada',
            fecha_pago = COALESCE(fecha_pago, ?),
            dias_atraso = 0,
            mora = 0
        WHERE prestamo_id = ?
        AND pagada = 0
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        $fechaPago,
        $prestamo_id
    ]);
}


/* =========================================================
   SALDO RESTANTE
========================================================= */

$saldo_total = $pendiente + $mora;


/* =========================================================
   REGISTRAR HISTORIAL DEL PAGO
========================================================= */

$sql = "
    INSERT INTO pagos
    (
        prestamo_id,
        valor_pago,
        fecha_pago,
        pago_mora,
        pago_capital,
        saldo_restante,
        observacion
    )
    VALUES
    (?,?,?,?,?,?,?)
";

$stmt = $conexion->prepare($sql);

$stmt->execute([
    $prestamo_id,
    $valor_pago,
    $fechaPago,
    $pago_mora,
    $pago_capital,
    $saldo_total,
    ""
]);


/* =========================================================
 REDIRECCIÓN
========================================================= */

header("Location: listado.php");
exit;