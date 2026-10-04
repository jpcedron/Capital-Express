
<?php

require_once "public/admin/auth_admin.php";
require_once "config/conexion.php";
require_once "actualizar_mora.php";

$conexion = (new Conexion())->conectar();

/* =========================================================
   VALIDAR DATOS DEL FORMULARIO
========================================================= */

$prestamo_id = filter_input(INPUT_POST, 'prestamo_id', FILTER_VALIDATE_INT);
$valor_pago = filter_input(INPUT_POST, 'valor_pago', FILTER_VALIDATE_FLOAT);

if (!$prestamo_id || $valor_pago === false || $valor_pago <= 0) {
    die("Datos de pago inválidos.");
}

/*
 * Conservamos el valor original que realmente entregó
 * el cliente. Esta variable NO se modifica.
 */
$valor_pago_original = $valor_pago;


/*
 * Una única fecha para todo el registro del pago.
 * Se utilizará tanto en pagos como en cuotas.
 */
$fechaPago = date('Y-m-d H:i:s');


/* =========================================================
   ACTUALIZAR MORA ANTES DE PROCESAR EL PAGO
========================================================= */

/*
 * actualizar_mora.php es la función que contiene la lógica
 * oficial de mora del sistema.
 *
 * Esto permite que, antes de registrar el pago:
 *
 * - se calculen todas las cuotas vencidas;
 * - se respete el período de 2 días sin mora;
 * - se calcule la mora individual de cada cuota;
 * - se acumule la mora total del préstamo.
 */
actualizarMora($conexion, $prestamo_id);


/* =========================================================
   OBTENER PRÉSTAMO ACTUALIZADO
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
   VALIDAR VALOR MÁXIMO DEL PAGO
========================================================= */

/*
 * El cliente puede pagar:
 *
 *     capital pendiente + mora generada
 *
 * La mora es un valor adicional al capital pendiente.
 */
$pendiente = floatval($prestamo['pendiente']);
$mora = floatval($prestamo['mora']);

$maximoPago = $pendiente + $mora;

if ($valor_pago_original > $maximoPago) {
    die("El valor del pago supera el saldo pendiente más la mora.");
}


/* =========================================================
   BUSCAR LA PRIMERA CUOTA PENDIENTE O EN MORA
========================================================= */

$sqlCuota = "
    SELECT *
    FROM cuotas
    WHERE prestamo_id = ?
    AND estado IN ('Pendiente','Mora')
    AND pagada = 0
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

$hoy = new DateTime();

$fecha_vencimiento = new DateTime(
    $cuota['fecha_vencimiento']
);


/* =========================================================
   CALCULAR DÍAS DE ATRASO DE LA CUOTA
========================================================= */

$dias_atraso = 0;

if ($hoy > $fecha_vencimiento) {

    /*
     * Los días se cuentan desde la fecha original
     * de vencimiento de la cuota.
     *
     * Los 2 días de gracia NO se eliminan de este conteo.
     */
    $dias_atraso = $fecha_vencimiento
        ->diff($hoy)
        ->days;
}


/* =========================================================
   ACTUALIZAR INFORMACIÓN DE LA CUOTA ACTUAL
========================================================= */

/*
 * La mora de todas las cuotas ya fue calculada por
 * actualizarMora().
 *
 * Aquí solamente mantenemos actualizada la información
 * de la cuota que se está procesando.
 */

$moraCuota = floatval($cuota['mora']);

$estadoCuota = ($moraCuota > 0)
    ? "Mora"
    : "Pendiente";


$sql = "
    UPDATE cuotas
    SET
        dias_atraso = ?,
        estado = ?
    WHERE id = ?
";

$stmt = $conexion->prepare($sql);

$stmt->execute([
    $dias_atraso,
    $estadoCuota,
    $cuota['id']
]);


/* =========================================================
   PAGAR PRIMERO LA MORA
========================================================= */

$pago_mora = 0;
$pago_capital = 0;


/*
 * La mora utilizada aquí es la mora TOTAL acumulada
 * del préstamo, calculada previamente por actualizarMora().
 */
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
    floatval($prestamo['abonado'])
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

    /*
     * IMPORTANTE:
     * Aquí usamos el valor ORIGINAL que entregó
     * el cliente, no la variable que fue reducida
     * al distribuir mora y capital.
     */
    $valor_pago_original,

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
