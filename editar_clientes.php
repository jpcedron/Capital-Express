<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

// Verificar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Cliente no válido.");
}

$id = (int) $_GET['id'];



// GUARDAR CAMBIOS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $cedula = trim($_POST['cedula'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $estado_cliente = $_POST['estado_cliente'] ?? 'activo';

    // Validaciones básicas
    if ($nombre === '' || $cedula === '') {
        die("El nombre y la cédula son obligatorios.");
    }

        // Verificar que la cédula no pertenezca a otro cliente
    $sqlCedula = "SELECT id
                FROM clientes
                WHERE cedula = ?
                AND id != ?
                LIMIT 1";

    $stmtCedula = $conexion->prepare($sqlCedula);
    $stmtCedula->execute([$cedula, $id]);

    if ($stmtCedula->fetch()) {

    $errorCedula = "La cédula ya está registrada en otro cliente.";

    } else {

        $errorCedula = null;

    }

    // Validar estado
    if (!in_array($estado_cliente, ['activo', 'inactivo'], true)) {
        die("Estado de cliente no válido.");
    }

    // Actualizar únicamente la tabla clientes
   if (!$errorCedula) {

    // Actualizar únicamente la tabla clientes
    $sqlUpdate = "UPDATE clientes
                  SET nombre = ?,
                      cedula = ?,
                      telefono = ?,
                      direccion = ?,
                      estado_cliente = ?
                  WHERE id = ?";

    $stmtUpdate = $conexion->prepare($sqlUpdate);

    $stmtUpdate->execute([
    $nombre,
    $cedula,
    $telefono,
    $direccion,
    $estado_cliente,
    $id
]);
    $actualizado = true;
    }
}


// CONSULTAR CLIENTE
$sql = "SELECT
            id,
            nombre,
            cedula,
            telefono,
            direccion,
            estado_cliente
        FROM clientes
        WHERE id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    die("Cliente no encontrado.");
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Cliente - Capital Express</title>
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
        max-width: 960px;
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
        max-width: 960px;
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

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 4px;
    }

    .status-badge.is-activo {
        background: rgba(34, 139, 87, 0.1);
        color: #1f7a4d;
    }

    .status-badge.is-inactivo {
        background: rgba(180, 60, 60, 0.1);
        color: #a13636;
    }

    .status-badge::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
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

    .mb-3 {
        margin-bottom: 18px !important;
    }

    hr.divider {
        border: none;
        border-top: 1px solid var(--line);
        margin: 8px 0 24px;
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
        <a href="dashboard.php">Inicio</a>
        <span class="sep">/</span>
        <a href="gestionar_clientes.php">Clientes</a>
        <span class="sep">/</span>
        <span class="current">Editar cliente</span>
    </div>

    <div class="page-head">
        <div>
            <h1 class="page-title">Editar cliente</h1>
            <p class="page-desc">Actualiza la información personal y el estado de la cuenta del cliente.</p>
        </div>

        <span class="client-id-tag">
            <i class="bi bi-person-vcard"></i>
            ID Cliente #<?= htmlspecialchars($cliente['id']) ?>
        </span>
    </div>

    <div class="edit-card">

        <form method="POST" action="editar_clientes.php?id=<?= $cliente['id'] ?>">

            <div class="card-section-head">

                <p class="card-section-title">
                    <span class="icon-box"><i class="bi bi-person"></i></span>
                    Información personal
                </p>

                <span class="status-badge <?= $cliente['estado_cliente'] === 'activo' ? 'is-activo' : 'is-inactivo' ?>">
                    <?= $cliente['estado_cliente'] === 'activo' ? 'Cliente activo' : 'Cliente inactivo' ?>
                </span>

            </div>

            <div class="card-body-custom">

                <p class="field-group-title">Datos personales</p>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Nombre completo <span class="req">*</span>
                        </label>

                        <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            value="<?= htmlspecialchars($cliente['nombre']) ?>"
                            required
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Cédula <span class="req">*</span>
                        </label>

                        <input
                            type="text"
                            name="cedula"
                            class="form-control"
                            value="<?= htmlspecialchars($cliente['cedula']) ?>"
                            required
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Teléfono
                        </label>

                        <input
                            type="text"
                            name="telefono"
                            class="form-control"
                            value="<?= htmlspecialchars($cliente['telefono']) ?>"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Dirección
                        </label>

                        <input
                            type="text"
                            name="direccion"
                            class="form-control"
                            value="<?= htmlspecialchars($cliente['direccion']) ?>"
                        >

                    </div>

                </div>

                <hr class="divider">

                <p class="field-group-title">Estado de la cuenta</p>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Estado del cliente
                        </label>

                        <select
                            name="estado_cliente"
                            class="form-select"
                        >

                            <option
                                value="activo"
                                <?= $cliente['estado_cliente'] === 'activo' ? 'selected' : '' ?>
                            >
                                Activo
                            </option>

                            <option
                                value="inactivo"
                                <?= $cliente['estado_cliente'] === 'inactivo' ? 'selected' : '' ?>
                            >
                                Inactivo
                            </option>

                        </select>

                    </div>

                </div>

            </div>

            <div class="card-footer-actions">

                <p class="footer-note">
                    <i class="bi bi-info-circle"></i>
                    Los campos marcados con * son obligatorios.
                </p>

                <div class="action-row">

                    <a
                        href="gestionar_clientes.php"
                        class="btn btn-cancel"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        name="guardar"
                        class="btn btn-save"
                    >
                        <i class="bi bi-check2"></i>
                        Guardar cambios
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (!empty($errorCedula)): ?>

<script>

Swal.fire({
    icon: 'error',
    title: 'Cédula duplicada',
    text: '<?= htmlspecialchars($errorCedula, ENT_QUOTES, 'UTF-8') ?>',
    confirmButtonText: 'Entendido'
});

</script>

<?php endif; ?>

<?php if (!empty($actualizado)): ?>

<script>

Swal.fire({
    icon: 'success',
    title: '¡Cliente actualizado!',
    text: 'Los datos del cliente se actualizaron correctamente.',
    confirmButtonText: 'Continuar',
    allowOutsideClick: false
}).then((result) => {

    if (result.isConfirmed) {
        window.location.href = 'gestionar_clientes.php';
    }

});

</script>

<?php endif; ?>

</body>
</html>
