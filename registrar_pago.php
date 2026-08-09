<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$id = $_GET['id'];

$sql = "SELECT * FROM prestamos WHERE id=?";
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
<style>

    :root {
        --navy-deep: #0d1f3c;
        --navy-base: #1e2a3a;
        --navy-mid: #1a3560;
        --gold: #e8c876;
        --gold-dark: #c9a84c;
        --paper: #f4f6fb;
        --line: #e3e7f0;
        --text-muted: #6b7688;
    }

    * {
        box-sizing: border-box;
    }

    html, body {
        margin: 0;
        min-height: 100vh;
        background-color: var(--paper);
        font-family: 'Inter', sans-serif;
        color: var(--navy-base);
    }

    /* Top bar */
    .topbar {
        background: var(--navy-deep);
        border-bottom: 3px solid var(--gold-dark);
    }

    .topbar-inner {
        max-width: 780px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 24px;
    }

    .brand-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-mark {
        width: 34px;
        height: 34px;
        border-radius: 6px;
        background: var(--gold);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        color: var(--navy-deep);
        font-size: 14px;
        flex-shrink: 0;
    }

    .brand-name {
        font-family: 'Sora', sans-serif;
        font-weight: 600;
        font-size: 1.25rem;
        letter-spacing: 0.01em;
        color: var(--paper);
        margin: 0;
        line-height: 1.1;
    }

    .brand-heading {
    font-family: 'Playfair Display', serif;
}

    .brand-sub {
        font-size: 0.68rem;
        color: rgba(244, 246, 251, 0.55);
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin: 0;
    }

    .topbar-user {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        color: rgba(244, 246, 251, 0.75);
    }

    .topbar-user .avatar {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: rgba(232, 200, 118, 0.15);
        border: 1px solid rgba(232, 200, 118, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gold);
        font-size: 0.7rem;
        font-weight: 700;
    }

    /* Page container */
    .page-wrap {
        max-width: 780px;
        margin: 0 auto;
        padding: 28px 24px 64px;
    }

    .breadcrumb-row {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-bottom: 18px;
    }

    .breadcrumb-row a {
        color: var(--text-muted);
        text-decoration: none;
    }

    .breadcrumb-row a:hover {
        color: var(--navy-mid);
    }

    .breadcrumb-row .sep {
        color: #c3c9d6;
    }

    .breadcrumb-row .current {
        color: var(--navy-deep);
        font-weight: 600;
    }

    .page-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .page-title {
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--navy-deep);
        margin: 0 0 4px;
    }

    .page-desc {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin: 0;
    }

    .client-id-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--navy-mid);
        background: #ffffff;
        border: 1px solid var(--line);
        padding: 7px 12px;
        border-radius: 6px;
        white-space: nowrap;
    }

    .client-id-tag i {
        color: var(--gold-dark);
    }

    /* Card */
    .edit-card {
        background: #ffffff;
        border: 1px solid var(--line);
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(13, 31, 60, 0.04);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .card-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 28px;
        border-bottom: 1px solid var(--line);
        background: #fafbfd;
    }

    .card-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: 'Sora', sans-serif;
        font-weight: 600;
        font-size: 0.92rem;
        color: var(--navy-deep);
        margin: 0;
    }

    .card-section-title .icon-box {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        background: rgba(201, 168, 76, 0.12);
        color: var(--gold-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }

    .card-body-custom {
        padding: 28px;
    }

    .field-group-title {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin: 0 0 16px;
    }

    /* Summary grid */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }

    .summary-item {
        border: 1px solid var(--line);
        border-radius: 6px;
        padding: 14px 16px;
        background: #fafbfd;
    }

    .summary-item .label {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin: 0 0 6px;
    }

    .summary-item .value {
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: 1.05rem;
        color: var(--navy-deep);
        margin: 0;
    }

    .summary-item.is-accent {
        background: rgba(201, 168, 76, 0.08);
        border-color: rgba(201, 168, 76, 0.35);
    }

    .summary-item.is-accent .value {
        color: var(--gold-dark);
    }

    .summary-item.is-pending {
        background: rgba(180, 60, 60, 0.06);
        border-color: rgba(180, 60, 60, 0.25);
    }

    .summary-item.is-pending .value {
        color: #a13636;
    }

    /* Form */
    .form-label {
        font-weight: 600;
        font-size: 0.82rem;
        color: var(--navy-base);
        margin-bottom: 6px;
    }

    .form-label .req {
        color: var(--gold-dark);
        margin-left: 2px;
    }

    .form-control,
    .form-select {
        border: 1px solid var(--line);
        border-radius: 6px;
        padding: 10px 14px;
        font-size: 0.9rem;
        color: var(--navy-deep);
        background-color: #ffffff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--navy-mid);
        box-shadow: 0 0 0 3px rgba(26, 53, 96, 0.1);
    }

    .form-hint {
        font-size: 0.76rem;
        color: var(--text-muted);
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .mb-3 {
        margin-bottom: 18px !important;
    }

    .card-footer-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 28px;
        border-top: 1px solid var(--line);
        background: #fafbfd;
        flex-wrap: wrap;
    }

    .footer-note {
        font-size: 0.76rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0;
    }

    .action-row {
        display: flex;
        gap: 10px;
    }

    .btn-save {
        background: var(--navy-deep);
        border: 1px solid var(--navy-deep);
        color: var(--paper);
        font-weight: 600;
        font-size: 0.88rem;
        padding: 10px 22px;
        border-radius: 6px;
        letter-spacing: 0.01em;
        transition: background-color 0.15s ease, border-color 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-save:hover {
        background: var(--navy-mid);
        border-color: var(--navy-mid);
        color: var(--paper);
    }

    .btn-cancel {
        background: #ffffff;
        border: 1px solid var(--line);
        color: var(--navy-base);
        font-weight: 600;
        font-size: 0.88rem;
        padding: 10px 20px;
        border-radius: 6px;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }

    .btn-cancel:hover {
        background-color: #f2f4f8;
        color: var(--navy-base);
        border-color: #c9cfdc;
    }

    /* Modal */
    .modal-content {
        border: none;
        border-radius: 8px;
        overflow: hidden;
    }

    .modal-header-custom {
        background: var(--navy-deep);
        color: var(--paper);
        border-bottom: 3px solid var(--gold-dark);
        padding: 18px 24px;
    }

    .modal-header-custom .modal-title {
        font-family: 'Sora', sans-serif;
        font-weight: 600;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .modal-header-custom .modal-title i {
        color: var(--gold);
    }

    .modal-body-custom {
        padding: 24px;
        font-size: 0.9rem;
        color: var(--navy-base);
        line-height: 1.6;
    }

    .modal-footer-custom {
        padding: 16px 24px;
        border-top: 1px solid var(--line);
        background: #fafbfd;
        gap: 10px;
    }

    /* ===== Responsive ===== */

    @media (max-width: 768px) {
        .topbar-inner {
            padding: 12px 16px;
        }

        .brand-sub {
            display: none;
        }

        .page-wrap {
            padding: 20px 16px 48px;
        }

        .page-head {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
        }

        .client-id-tag {
            align-self: flex-start;
        }

        .card-section-head {
            padding: 14px 18px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .card-body-custom {
            padding: 20px 18px;
        }

        .card-footer-actions {
            padding: 16px 18px;
        }

        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .topbar-user span {
            display: none;
        }

        .breadcrumb-row {
            flex-wrap: wrap;
        }

        .page-title {
            font-size: 1.25rem;
        }

        .page-desc {
            font-size: 0.8rem;
        }

        .card-section-title {
            font-size: 0.85rem;
        }

        .summary-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .summary-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
        }

        .summary-item .label {
            margin: 0;
        }

        .card-footer-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .footer-note {
            order: 2;
            justify-content: center;
        }

        .action-row {
            order: 1;
            flex-direction: column;
        }

        .action-row .btn-save,
        .action-row .btn-cancel {
            width: 100%;
            justify-content: center;
        }

        .modal-footer-custom {
            flex-direction: column;
            align-items: stretch;
        }

        .modal-footer-custom .btn {
            width: 100%;
            margin: 0 !important;
        }
    }

</style>
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
                <?= htmlspecialchars($prestamo['nombre']) ?>
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
                            <strong><?= htmlspecialchars($prestamo['nombre']) ?></strong>?
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
