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

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container mt-5">

    <h2>Capital Express</h2>

    <p>Editar Cliente</p>

    <div class="card shadow-sm">

        <div class="card-body">

            <form method="POST" action="editar_clientes.php?id=<?= $cliente['id'] ?>">

                <div class="mb-3">

                    <label class="form-label">
                        Nombre
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="form-control"
                        value="<?= htmlspecialchars($cliente['nombre']) ?>"
                        required
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Cédula
                    </label>

                    <input
                        type="text"
                        name="cedula"
                        class="form-control"
                        value="<?= htmlspecialchars($cliente['cedula']) ?>"
                        required
                    >

                </div>


                <div class="mb-3">

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


                <div class="mb-3">

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


                <div class="mb-3">

                    <label class="form-label">
                        Estado del Cliente
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


                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        name="guardar"
                        class="btn btn-success"
                    >
                        Guardar cambios
                    </button>

                    <a
                        href="gestionar_clientes.php"
                        class="btn btn-secondary"
                    >
                        Cancelar
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (!empty($errorCedula)): ?>

<script>

Swal.fire({
    icon: 'error',
    title: 'Cédula duplicada',
    text: 'La cédula ya está registrada en otro cliente.',
    confirmButtonText: 'Entendido'
});

</script>
<!-- ACTUALIZADO DATOS DE CLIENTE -->
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

<!-- ALERTA DE CEDULA DUPLICADA -->
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

</body>
</html>