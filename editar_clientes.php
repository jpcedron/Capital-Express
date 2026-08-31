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
<link rel="stylesheet" href="css/editar_clientes.css">
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
        <a href="public/dashboard.php">Panel</a>
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