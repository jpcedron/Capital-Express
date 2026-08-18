<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$sql = "SELECT
            id,
            nombre,
            cedula,
            telefono,
            direccion,
            estado_cliente
        FROM clientes
        ORDER BY id DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$clienteEliminado = isset($_GET['eliminado']) && $_GET['eliminado'] == '1';

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Capital Express - Gestionar Clientes</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet"> 
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="../css/gestionar_clientes.css" rel="stylesheet">
</head>
<body>

<!-- ========================= -->
<!-- TOP BAR -->
<!-- ========================= -->
<header class="ce-topbar">
  <div class="container d-flex align-items-center justify-content-between py-2">
    <div class="d-flex align-items-center gap-2">
      <span class="ce-mark" style="font-family: 'Playfair Display', serif;">CE</span>
      <div>
        <div class="ce-name" style="font-family: 'Playfair Display', serif;">Capital Express</div>
        <div class="ce-tag">Gestión de préstamos</div>
      </div>
    </div>
    <nav class="d-none d-md-flex align-items-center gap-1">
      <a href="index.php" class="nav-link"><i class="bi bi-file-earmark-plus me-1"></i>Nuevo préstamo</a>
      <a href="listado.php" class="nav-link"><i class="bi bi-list-ul me-1"></i>Listado</a>
      <!--<a href="gestionar_clientes.php" class="nav-link active"><i class="bi bi-people-fill me-1"></i>Clientes</a>-->
      <a href="pagina_web/dashboard.php" class="nav-link"><i class="bi bi-person-circle me-1"></i>Panel</a>
    </nav>
  </div>
</header>

<main class="container my-4 my-md-5">

  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">

      <!-- Page heading -->
      <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
        <div>
          <span class="ce-kicker">Cartera de clientes</span>
          <h1 class="ce-page-title fs-3 mt-1 mb-1">Gestionar Clientes</h1>
          <p class="ce-page-subtitle mb-0">Consulta, activa o desactiva a los clientes registrados en el sistema.</p>
        </div>

        <a href="listado.php" class="btn ce-btn-secondary">
          <i class="bi bi-arrow-left me-1"></i> Volver al listado
        </a>
      </div>

      <div class="ce-card mt-4">

        <div class="ce-card-header">
          <div>
            <h2>Capital Express</h2>
            <small>Clientes registrados</small>
          </div>
          <span class="ce-header-count">
            <i class="bi bi-people-fill"></i>
            <?= count($clientes) ?> cliente<?= count($clientes) === 1 ? '' : 's' ?>
          </span>
        </div>

        <div class="ce-card-body">

          <div class="ce-toolbar">
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input
                type="text"
                id="buscarClienteTabla"
                class="form-control"
                placeholder="Buscar por nombre o cédula...">
            </div>
          </div>

          <?php if (empty($clientes)): ?>

            <div class="ce-empty">
              <i class="bi bi-people"></i>
              <p class="mb-0">Aún no hay clientes registrados.</p>
            </div>

          <?php else: ?>

          <div class="ce-table-wrap">
            <table class="ce-table" id="tablaClientes">

              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Cédula</th>
                  <th>Teléfono</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>

              <tbody>

              <?php foreach ($clientes as $cliente): ?>

                <tr>

                  <td data-label="ID">
                    <span class="ce-cell-id">#<?= $cliente['id'] ?></span>
                  </td>

                  <td data-label="Nombre">
                    <span class="ce-cell-name"><?= $cliente['nombre'] ?></span>
                  </td>

                  <td data-label="Cédula">
                    <?= $cliente['cedula'] ?>
                  </td>

                  <td data-label="Teléfono">
                    <?= $cliente['telefono'] ?>
                  </td>

                  <td data-label="Estado">
                    <?php if ($cliente['estado_cliente'] == 'activo'): ?>

                      <span class="ce-badge ce-badge-activo">Activo</span>

                    <?php else: ?>

                      <span class="ce-badge ce-badge-inactivo">Inactivo</span>

                    <?php endif; ?>
                  </td>

                  <td data-label="Acciones">
                    <div class="ce-actions-cell">

                      <?php if ($cliente['estado_cliente'] == 'activo'): ?>

                        <a
                          href="#"
                          class="ce-btn-icon ce-btn-deactivate"
                          onclick="confirmarEstado(<?= $cliente['id'] ?>,'inactivo')">
                          <i class="bi bi-slash-circle"></i> Desactivar
                        </a>

                      <?php else: ?>

                        <a
                          href="#"
                          class="ce-btn-icon ce-btn-activate"
                          onclick="confirmarEstado(<?= $cliente['id'] ?>,'activo')">
                          <i class="bi bi-check-circle"></i> Activar
                        </a>

                      <?php endif; ?>

                      <a
                        href="editar_clientes.php?id=<?= $cliente['id'] ?>"
                        class="ce-btn-icon ce-btn-edit">
                        <i class="bi bi-pencil"></i> Editar
                      </a>

                      <a
                        href="#"
                        class="ce-btn-icon ce-btn-delete"
                        onclick="eliminarCliente(<?= $cliente['id'] ?>)">
                        <i class="bi bi-trash3"></i> Eliminar
                      </a>

                    </div>
                  </td>

                </tr>

              <?php endforeach; ?>

              </tbody>

            </table>
          </div>

          <?php endif; ?>

        </div>
      </div>

      <p class="text-center text-secondary small mt-4 mb-0">
        Capital Express &copy; 2026 — Todos los derechos reservados
      </p>

    </div>
  </div>

</main>

<script src="js/gestionar_cliente.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Filtro de búsqueda en vivo por nombre o cédula (solo visual, no altera el PHP)
(function () {
  var input = document.getElementById('buscarClienteTabla');
  var tabla = document.getElementById('tablaClientes');
  if (!input || !tabla) return;

  input.addEventListener('input', function () {
    var termino = input.value.trim().toLowerCase();
    var filas = tabla.querySelectorAll('tbody tr');

    filas.forEach(function (fila) {
      var nombre = fila.querySelector('[data-label="Nombre"]').textContent.toLowerCase();
      var cedula = fila.querySelector('[data-label="Cédula"]').textContent.toLowerCase();
      var coincide = nombre.includes(termino) || cedula.includes(termino);
      fila.style.display = coincide ? '' : 'none';
    });
  });
})();
</script>

<!--ALERTA DE ELIMINACIÓN -->
<?php if ($clienteEliminado): ?>

<script>

Swal.fire({
    icon: 'success',
    title: '¡Cliente eliminado!',
    text: 'El cliente y toda su información relacionada fueron eliminados correctamente.',
    confirmButtonText: 'Continuar',
    allowOutsideClick: false
}).then(() => {

    // Limpiar el parámetro de la URL
    window.history.replaceState(
        {},
        document.title,
        'gestionar_clientes.php'
    );

});

</script>

<?php endif; ?>

</body>
</html>
