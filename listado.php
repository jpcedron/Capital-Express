<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$sql = " SELECT * FROM prestamos WHERE estado_cliente='activo' ORDER BY id DESC ";

$stmt = $conexion->prepare($sql);
$stmt->execute();

$prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listado de Préstamos — Capital Express</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/listado.css">
</head>
<body>

  <!-- TOP BAR -->
  <nav class="top-bar">
    <div class="container-fluid px-4">
      <div class="d-flex align-items-center gap-3">
        <div class="brand-icon"><i class="bi bi-bank2"></i></div>
        <div>
          <div class="brand">Capital Express</div>
          <div class="tagline">Finanzas con Confianza</div>
        </div>
      </div>
    </div>
  </nav>

  <!-- PAGE WRAPPER -->
  <div class="page-wrapper">

    <!-- Page Header -->
    <div class="page-header">
      <div class="step-badge"><i class="bi bi-table"></i> Panel de control</div>
      <h1>Listado de Préstamos</h1>
      <p>Consulta y gestiona todos los créditos registrados en el sistema.</p>
    </div>

    <!-- Main Card -->
    <div class="main-card">

      <!-- Card Header -->
      <div class="card-header-custom">
        <div class="d-flex align-items-center gap-3">
          <div class="header-icon"><i class="bi bi-clipboard2-data-fill"></i></div>
          <div>
            <h2>Clientes Registrados</h2>
            <p>Historial completo de préstamos activos, en mora y pagados</p>
            <div class="header-divider"></div>
          </div>
        </div>
      </div>

      <!-- Action Bar -->
      <div class="action-bar">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <a href="index.php" class="btn-nuevo">
            <i class="bi bi-plus-circle-fill"></i> Nuevo Préstamo
          </a>
          <a href="gestionar_clientes.php" class="btn-outline-navy">
            <i class="bi bi-people-fill"></i> Gestionar Clientes
          </a>
          <a href="nuevo_prestamo.php" class="btn-outline-navy">
            <i class="bi bi-file-earmark-bar-graph-fill"></i> Listado de Préstamos
          </a>
        </div>
        <div class="footer-note">
          <i class="bi bi-circle-fill" style="font-size:.5rem;color:var(--gold)"></i>
          <span><?php echo count($prestamos) ?> registro(s) encontrado(s)</span>
        </div>
      </div>

      <!-- Table -->
      <div class="table-wrapper">

        <?php if(empty($prestamos)): ?>
        <div class="empty-state">
          <div class="empty-icon"><i class="bi bi-inbox-fill"></i></div>
          <h5>Sin préstamos registrados</h5>
          <p>Aun no hay créditos en el sistema. Comience registrando un nuevo préstamo.</p>
        </div>
        <?php else: ?>

        <table class="table-custom">
          <thead>
            <tr>
              <th>#</th>
              <th><i class="bi bi-person-fill me-1"></i>Cliente</th>
              <th><i class="bi bi-telephone-fill me-1"></i>Teléfono</th>
              <th><i class="bi bi-cash-coin me-1"></i>Monto</th>
              <th><i class="bi bi-calculator me-1"></i>Total a Pagar</th>
              <th><i class="bi bi-hourglass-split me-1"></i>Pendiente</th>
              <th><i class="bi bi-flag-fill me-1"></i>Estado</th>
              <th><i class="bi bi-gear-fill me-1"></i>Acciones</th>
            </tr>
          </thead>
          <tbody>

          <?php foreach($prestamos as $prestamo): ?>
            <tr>
              <td class="cell-id">#<?= $prestamo['id'] ?></td>

              <td>
                <span class="cell-name">
                  <?= htmlspecialchars($prestamo['nombre']) ?>
                  <small><?= htmlspecialchars($prestamo['cedula']) ?></small>
                </span>
              </td>

              <td><?= htmlspecialchars($prestamo['telefono']) ?></td>

              <td class="cell-amount">$<?= number_format($prestamo['monto']) ?></td>

              <td class="cell-amount">$<?= number_format($prestamo['total_pagar']) ?></td>

              <td class="cell-pending">$<?= number_format($prestamo['pendiente']) ?></td>

              <td>
                <?php
                  $sql = "SELECT MAX(fecha_vencimiento) AS ultima_cuota
                          FROM cuotas
                          WHERE prestamo_id = ?";
                  $stmt = $conexion->prepare($sql);
                  $stmt->execute([$prestamo['id']]);
                  $datosCuota = $stmt->fetch(PDO::FETCH_ASSOC);

                  $ultimaCuota = !empty($datosCuota['ultima_cuota'])
                    ? new DateTime($datosCuota['ultima_cuota'])
                    : null;

                  $hoy = new DateTime();
                  $hoy = date('Y-m-d');

                  if ($prestamo['estado'] == 'Mora') {
                    echo '<span class="badge-mora">Mora</span>';
                  } elseif ($prestamo['estado'] == 'Pagado') {
                    echo '<span class="badge-pagado">Pagado</span>';
                  } else {
                    echo '<span class="badge-activo">Activo</span>';
                  }
                ?>
              </td>

              <td>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                  <a href="cartilla.php?id=<?= $prestamo['id'] ?>"
                     class="btn-action btn-action-cartilla">
                    <i class="bi bi-journal-text"></i> Cartilla
                  </a>
                  <a href="registrar_pago.php?id=<?= $prestamo['id'] ?>"
                     class="btn-action btn-action-pago">
                    <i class="bi bi-cash-stack"></i> Pago
                  </a>
                  <a href="historial_pagos.php?prestamo_id=<?= $prestamo['id'] ?>"
                     class="btn-action btn-action-historial">
                    <i class="bi bi-clock-history"></i> Historial
                  </a>
                </div>
              </td>

            </tr>
          <?php endforeach; ?>

          </tbody>
        </table>

        <?php endif; ?>

      </div>
      <!-- /table-wrapper -->

      <!-- Card Footer -->
      <div class="card-footer-custom">
        <div class="footer-note">
          <i class="bi bi-shield-lock-fill"></i>
          Información cifrada y protegida
        </div>
        <div class="footer-note">
          <i class="bi bi-clock-fill"></i>
          Capital Express &copy; <?php echo date('Y'); ?>
        </div>
      </div>

    </div>
    <!-- /main-card -->

  </div>
  <!-- /page-wrapper -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>