<?php

function actualizarMora($conexion, $prestamo_id)
{
    // Buscar el préstamo
    $sql = "SELECT * FROM prestamos WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$prestamo_id]);

    $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prestamo) {
        return;
    }

    // Buscar la primera cuota pendiente
    $sql = "SELECT *
            FROM cuotas
            WHERE prestamo_id=?
            AND pagada=0
            ORDER BY numero_cuota ASC
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$prestamo_id]);

    $cuota = $stmt->fetch(PDO::FETCH_ASSOC);

    // Buscar la fecha de la última cuota
    $sql = "SELECT MAX(fecha_vencimiento) AS ultima_fecha
            FROM cuotas
            WHERE prestamo_id=?";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$prestamo_id]);

    $ultimaCuota = $stmt->fetch(PDO::FETCH_ASSOC);


    // Si no hay cuotas pendientes, el préstamo está pagado
    if (!$cuota) {

        $sql = "UPDATE prestamos
                SET estado='Pagado',
                    mora=0
                WHERE id=?";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([$prestamo_id]);

        return;
    }

    // Calcular días de atraso
    $hoy = new DateTime();

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

    if ($prestamoFinalizado) {

    $fecha = new DateTime($ultimaCuota['ultima_fecha']);

    } else {

        $fecha = new DateTime($cuota['fecha_vencimiento']);

    }

    $dias_atraso = 0;

    if ($hoy > $fecha) {

        $dias_atraso = $fecha->diff($hoy)->days;

    }

    // Definir la base de la mora
    if ($prestamoFinalizado) {

        $baseMora = floatval($prestamo['pendiente']);

    } else {

        $baseMora = floatval($cuota['valor']);
    }

    $porcentaje = 0;

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
}

$mora = 0;

if ($porcentaje > 0) {

    $semanas = max(1, ceil(($dias_atraso - 2) / 7));

    $mora = round(
        $baseMora *
        ($porcentaje / 100) *
        $semanas,
        2
    );
}

    // Guardar los días de atraso en la cuota
    $estadoCuota = ($mora > 0) ? "Mora" : "Pendiente";

    $sql = "
    UPDATE cuotas
    SET
        dias_atraso=?,
        mora=?,
        estado=?
    WHERE id=?";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        $dias_atraso,
        $mora,
        $estadoCuota,
        $cuota['id']
    ]);¿


    $estadoPrestamo = ($mora > 0) ? "Mora" : "Activo";

    $sql = "
    UPDATE prestamos
    SET
        mora=?,
        porcentaje_mora=?,
        estado=?
    WHERE id=?";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        $mora,
        $porcentaje,
        $estadoPrestamo,
        $prestamo_id
    ]);
}