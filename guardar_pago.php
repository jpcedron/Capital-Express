<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$prestamo_id = $_POST['prestamo_id'];
$valor_pago = floatval($_POST['valor_pago']);

$sql = "SELECT * FROM prestamos WHERE id=?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$prestamo_id]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestamo) {
    die("Préstamo no encontrado.");
}

if ($prestamo['estado'] === 'Pagado') {
    die("Este préstamo ya fue pagado.");
}

/* Buscar la primera cuota pendiente */
$sqlCuota = "
SELECT *
FROM cuotas
WHERE prestamo_id = ?
AND estado IN ('Pendiente','Mora')
ORDER BY numero_cuota ASC
LIMIT 1";

$stmtCuota = $conexion->prepare($sqlCuota);
$stmtCuota->execute([$prestamo_id]);

$cuota = $stmtCuota->fetch(PDO::FETCH_ASSOC);

if (!$cuota) {
    header("Location: listado.php");
    exit;
}

$pendiente = floatval($prestamo['pendiente']);
$mora = floatval($prestamo['mora']);

$hoy = new DateTime();

/* Obtener la fecha de la última cuota */

$sqlUltima = "
SELECT MAX(fecha_vencimiento) AS ultima_fecha
FROM cuotas
WHERE prestamo_id = ?";

$stmtUltima = $conexion->prepare($sqlUltima);
$stmtUltima->execute([$prestamo_id]);

$ultimaCuota = $stmtUltima->fetch(PDO::FETCH_ASSOC);

/* Verificar si el préstamo ya finalizó */

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

/* Definir la base para calcular la mora */

if ($prestamoFinalizado) {

    // El préstamo ya terminó: la mora va sobre el saldo pendiente.
    $baseMora = $pendiente;

} else {

    // El préstamo sigue vigente: la mora va sobre la cuota vencida.
    $baseMora = floatval($cuota['valor']);
}


$fecha_vencimiento = new DateTime($cuota['fecha_vencimiento']);
$dias_atraso = 0;

if ($hoy > $fecha_vencimiento) {
    $dias_atraso = $fecha_vencimiento->diff($hoy)->days;
}

/* calcular mora */
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
        $semanas = max(1, ceil($dias_atraso - 2) / 7);
        /*$semanas = ceil($dias_atraso / 7); */

        $mora = round(
            $baseMora *
            ($porcentaje / 100) *
            $semanas,
            2
        );

        $sql = "
        UPDATE prestamos
        SET
        mora=?,
        porcentaje_mora=?,
        ultima_mora=?,
        estado='Mora'
        WHERE id=?";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $mora,
            $porcentaje,
            date('Y-m-d'),
            $prestamo_id
        ]);
    }
}

$sql = "
UPDATE cuotas
SET
dias_atraso = ?,
mora = ?,
estado = ?
WHERE id = ?";

$stmt = $conexion->prepare($sql);
$estadoCuota = ($mora > 0) ? "Mora" : "Pendiente";

$stmt->execute([
    $dias_atraso,
    $mora,
    $estadoCuota,
    $cuota['id']
]);

/* pagar primero mora */
$pago_mora = 0;
$pago_capital = 0;

if ($mora > 0) {
    $pago_mora = min($valor_pago, $mora);
    $mora -= $pago_mora;
    $valor_pago -= $pago_mora;
}

/* luego capital */
if ($valor_pago > 0) {
    $pago_capital = min($valor_pago, $pendiente);
    $pendiente -= $pago_capital;
}

$nuevo_abonado = $prestamo['abonado'] + $pago_mora + $pago_capital;

$estado = ($pendiente <= 0 && $mora <= 0)
    ? "Pagado"
    : ($mora > 0 ? "Mora" : "Activo");

$sql = "
UPDATE prestamos
SET
abonado=?,
pendiente=?,
mora=?,
estado=?
WHERE id=?";

$stmt = $conexion->prepare($sql);
$stmt->execute([
    $nuevo_abonado,
    $pendiente,
    $mora,
    $estado,
    $prestamo_id
]);

// Si el préstamo quedó totalmente pagado,
// marcar todas las cuotas restantes como pagadas.
if ($estado === "Pagado") {

    $sql = "UPDATE cuotas
            SET
                pagada = 1,
                estado = 'Pagada',
                fecha_pago = CURDATE(),
                dias_atraso = 0,
                mora = 0
            WHERE prestamo_id = ?
            AND pagada = 0";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$prestamo_id]);
}


$saldo_total = $pendiente + $mora;

$sql = "
INSERT INTO pagos
(
prestamo_id,
valor_pago,
pago_mora,
pago_capital,
saldo_restante,
observacion
)
VALUES
(?,?,?,?,?,?)";

$stmt = $conexion->prepare($sql);
$stmt->execute([
    $prestamo_id,
    $_POST['valor_pago'],
    $pago_mora,
    $pago_capital,
    $saldo_total,
    ""
]);

/* Marcar cuota como pagada */
if ($pago_capital > 0 || $pago_mora > 0) {

    $sql = "
    UPDATE cuotas
    SET
        pagada = 1,
        estado = 'Pagada',
        fecha_pago = ?
    WHERE id = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        date('Y-m-d'),
        $cuota['id']
    ]);
}

/* Si el préstamo quedó totalmente pagado,
   marcar todas las cuotas restantes como pagadas */
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
    AND pagada = 0";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        date('Y-m-d'),
        $prestamo_id
    ]);
}

header("Location:listado.php");
exit;