<?php

function actualizarMora($conexion, $prestamo_id)
{
    // ==========================================================
    // 1. OBTENER EL PRÉSTAMO
    // ==========================================================
    $sql = "SELECT *
            FROM prestamos
            WHERE id = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$prestamo_id]);

    $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prestamo) {
        return false;
    }


    // ==========================================================
    // 2. OBTENER TODAS LAS CUOTAS NO PAGADAS
    // ==========================================================
    $sql = "SELECT *
            FROM cuotas
            WHERE prestamo_id = ?
            AND pagada = 0
            ORDER BY numero_cuota ASC";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$prestamo_id]);

    $cuotas = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // ==========================================================
    // 3. SI NO HAY CUOTAS PENDIENTES
    // ==========================================================
    if (!$cuotas) {

        $sql = "UPDATE prestamos
                SET estado = 'Pagado',
                    mora = 0,
                    porcentaje_mora = 0
                WHERE id = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([$prestamo_id]);

        return true;
    }


    // ==========================================================
    // 4. VARIABLES GENERALES
    // ==========================================================
    $hoy = new DateTime();

    $moraTotal = 0;
    $porcentajeMaximo = 0;


    // ==========================================================
    // 5. RECORRER TODAS LAS CUOTAS PENDIENTES
    // ==========================================================
    foreach ($cuotas as $cuota) {

        $fechaVencimiento = new DateTime(
            $cuota['fecha_vencimiento']
        );

        $diasAtraso = 0;

        // ------------------------------------------------------
        // Calcular días de atraso
        // ------------------------------------------------------
        if ($hoy > $fechaVencimiento) {

            $diasAtraso = $fechaVencimiento
                ->diff($hoy)
                ->days;
        }


        // ------------------------------------------------------
        // Determinar porcentaje de mora
        // ------------------------------------------------------
        $porcentaje = 0;

        if ($diasAtraso >= 3 && $diasAtraso <= 14) {

            $porcentaje = 5;

        } elseif ($diasAtraso >= 15 && $diasAtraso <= 29) {

            $porcentaje = 10;

        } elseif ($diasAtraso >= 30 && $diasAtraso <= 44) {

            $porcentaje = 15;

        } elseif ($diasAtraso >= 45) {

            $porcentaje = 20;
        }


        // ------------------------------------------------------
        // Calcular mora
        // ------------------------------------------------------
        $moraCuota = 0;

        if ($porcentaje > 0) {

            $semanas = max(
                1,
                ceil($diasAtraso / 7)
            );

            $valorCuota = floatval($cuota['valor']);

            $moraCuota = round(
                $valorCuota *
                ($porcentaje / 100) *
                $semanas,
                2
            );
        }


        // ------------------------------------------------------
        // Estado de la cuota
        // ------------------------------------------------------
        if ($moraCuota > 0) {

            $estadoCuota = 'Mora';

        } else {

            $estadoCuota = 'Pendiente';
        }


        // ------------------------------------------------------
        // Actualizar cuota
        // ------------------------------------------------------
        $sql = "UPDATE cuotas
                SET dias_atraso = ?,
                    mora = ?,
                    estado = ?
                WHERE id = ?";

        $stmt = $conexion->prepare($sql);

        $stmt->execute([
            $diasAtraso,
            $moraCuota,
            $estadoCuota,
            $cuota['id']
        ]);


        // ------------------------------------------------------
        // Acumular mora total del préstamo
        // ------------------------------------------------------
        $moraTotal += $moraCuota;


        // Guardamos el porcentaje más alto
        // entre las cuotas que estén en mora
        if ($porcentaje > $porcentajeMaximo) {

            $porcentajeMaximo = $porcentaje;
        }
    }


    // ==========================================================
    // 6. DETERMINAR ESTADO DEL PRÉSTAMO
    // ==========================================================
    if ($moraTotal > 0) {

        $estadoPrestamo = 'Mora';

    } else {

        $estadoPrestamo = 'Activo';
    }


    // ==========================================================
    // 7. ACTUALIZAR PRÉSTAMO
    // ==========================================================
    $sql = "UPDATE prestamos
            SET mora = ?,
                porcentaje_mora = ?,
                estado = ?
            WHERE id = ?";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        $moraTotal,
        $porcentajeMaximo,
        $estadoPrestamo,
        $prestamo_id
    ]);


    return true;
}