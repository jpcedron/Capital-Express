<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$id = $_GET['id'];

$sql = "SELECT
            prestamos.*,
            clientes.nombre AS cliente_nombre,
            clientes.cedula AS cliente_cedula
        FROM prestamos
        INNER JOIN clientes
            ON prestamos.cliente_id = clientes.id
        WHERE prestamos.id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registrar Pago - Capital Express</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/registrar_pago.css">
</head>

<body>

<div class="topbar">
    <div class="topbar-inner">

        <div class="brand-row">
            <div class="brand-mark brand-heading">CE</div>
            <div>
                <p class="brand-name brand-heading">Capital Express</p>
                <p class="brand-sub">Panel administrativo</p>
            </div>
        </div>

    </div>
</div>

<div class="page-wrap">

    <div class="breadcrumb-row">
        <a href="pagina_web/dashboard.php">Panel</a>
        <span class="sep">/</span>
        <a href="listado.php">Préstamos</a>
        <span class="sep">/</span>
        <span class="current">Registrar pago</span>
    </div>

    <div class="page-head">
        <div>
            <h1 class="page-title">Registrar pago</h1>
            <p class="page-desc">Aplica un abono al préstamo y actualiza el saldo pendiente del cliente.</p>
        </div>

        <span class="client-id-tag">
            <i class="bi bi-person-vcard"></i>
            Préstamo #<?= htmlspecialchars($prestamo['id']) ?>
        </span>
    </div>

    <div class="edit-card">

        <div class="card-section-head">
            <p class="card-section-title">
                <span class="icon-box"><i class="bi bi-person"></i></span>
                Resumen del préstamo
            </p>
        </div>

        <div class="card-body-custom">

            <p class="field-group-title">Cliente</p>

            <p style="font-family:'Sora', sans-serif; font-weight:600; font-size:1.05rem; color:var(--navy-deep); margin:0 0 20px;">
                <?= htmlspecialchars($prestamo['cliente_nombre']) ?>
            </p>

            <div class="summary-grid">

                <div class="summary-item">
                    <p class="label">Total</p>
                    <p class="value">$<?= number_format($prestamo['total_pagar']) ?></p>
                </div>

                <div class="summary-item is-accent">
                    <p class="label">Abonado</p>
                    <p class="value">$<?= number_format($prestamo['abonado']) ?></p>
                </div>

                <div class="summary-item is-pending">
                    <p class="label">Pendiente</p>
                    <p class="value">$<?= number_format($prestamo['pendiente']) ?></p>
                </div>

            </div>

        </div>

    </div>

    <div class="edit-card">

        <form action="guardar_pago.php" method="POST" id="formPago">
            <input type="hidden" name="prestamo_id" value="<?= $prestamo['id'] ?>">

            <div class="card-section-head">
                <p class="card-section-title">
                    <span class="icon-box"><i class="bi bi-cash-coin"></i></span>
                    Datos del pago
                </p>
            </div>

            <div class="card-body-custom">

                <div class="mb-3">

                    <label class="form-label">
                        Valor del pago <span class="req">*</span>
                    </label>

                    <input
                        type="number"
                        name="valor_pago"
                        class="form-control"
                        min="1"
                        max="<?= $prestamo['pendiente'] ?>"
                        step="0.01"
                        required
                    >

                    <p class="form-hint">
                        <i class="bi bi-info-circle"></i>
                        El valor no puede superar el saldo pendiente de $<?= number_format($prestamo['pendiente']) ?>.
                    </p>

                </div>

            </div>

            <div class="card-footer-actions">

                <p class="footer-note">
                    <i class="bi bi-shield-check"></i>
                    El pago se aplicará una vez confirmado.
                </p>

                <div class="action-row">

                    <button
                        type="button"
                        class="btn btn-cancel"
                        onclick="window.location.href='listado.php'"
                    >
                        Listado
                    </button>

                    <button
                        type="button"
                        class="btn btn-save"
                        data-bs-toggle="modal"
                        data-bs-target="#modalConfirmacion"
                    >
                        <i class="bi bi-check2"></i>
                        Guardar pago
                    </button>

                </div>

            </div>

            <!-- MODAL DE CONFIRMACIÓN -->
            <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalConfirmacionLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header-custom">
                            <h5 class="modal-title" id="modalConfirmacionLabel">
                                <i class="bi bi-check-circle-fill"></i>
                                Confirmar transacción
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body-custom">
                            ¿Estás seguro de que deseas registrar este pago para el cliente
                            <strong><?= htmlspecialchars($prestamo['cliente_nombre']) ?></strong>?
                        </div>

                        <div class="modal-footer modal-footer-custom">
                            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-save">
                                <i class="bi bi-check2"></i>
                                Sí, confirmar pago
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>