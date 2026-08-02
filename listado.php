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

  <style>
    :root {
      --navy-dark:  #0d1f3c;
      --navy:       #1a3560;
      --navy-mid:   #1e2a3a;
      --gold:       #c9a84c;
      --gold-light: #e8c876;
      --surface:    #f4f6fb;
      --white:      #ffffff;
    }

    * { box-sizing: border-box; }

    body {
      background-color: var(--surface);
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
    }

    /* ── TOP BAR ── */
    .top-bar {
      background: var(--navy-dark);
      padding: 10px 0;
      border-bottom: 3px solid var(--gold);
    }
    .top-bar .brand {
      font-family: 'Montserrat', sans-serif;
      font-weight: 800;
      font-size: 1.3rem;
      color: var(--gold-light);
      letter-spacing: 1px;
    }
    .top-bar .tagline {
      font-size: .75rem;
      color: rgba(255,255,255,.55);
      letter-spacing: 2px;
      text-transform: uppercase;
    }
    .top-bar .brand-icon {
      width: 38px; height: 38px;
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      color: var(--navy-dark);
      font-size: 1.1rem;
      flex-shrink: 0;
    }

    /* ── PAGE WRAPPER ── */
    .page-wrapper {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 16px 60px;
    }

    /* ── PAGE HEADER ── */
    .page-header {
      margin-bottom: 28px;
    }
    .page-header .step-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(201,168,76,.15);
      border: 1px solid rgba(201,168,76,.35);
      color: var(--gold);
      font-size: .72rem;
      font-weight: 600;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      padding: 4px 12px;
      border-radius: 50px;
      margin-bottom: 10px;
    }
    .page-header h1 {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 1.75rem;
      color: var(--navy-dark);
      margin: 0;
    }
    .page-header p {
      color: #6b7a8d;
      font-size: .88rem;
      margin: 4px 0 0;
    }

    /* ── MAIN CARD ── */
    .main-card {
      background: var(--white);
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 8px 40px rgba(13,31,60,.10), 0 2px 8px rgba(13,31,60,.06);
      border: 1px solid rgba(201,168,76,.15);
    }

    /* ── CARD HEADER ── */
    .card-header-custom {
      background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy) 100%);
      padding: 26px 32px;
      position: relative;
      overflow: hidden;
    }
    .card-header-custom::before {
      content: '';
      position: absolute;
      top: -40px; right: -40px;
      width: 180px; height: 180px;
      border-radius: 50%;
      background: rgba(201,168,76,.08);
    }
    .card-header-custom::after {
      content: '';
      position: absolute;
      bottom: -60px; right: 60px;
      width: 140px; height: 140px;
      border-radius: 50%;
      background: rgba(201,168,76,.05);
    }
    .header-icon {
      width: 48px; height: 48px;
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      border-radius: 13px;
      display: flex; align-items: center; justify-content: center;
      color: var(--navy-dark);
      font-size: 1.3rem;
      box-shadow: 0 4px 16px rgba(201,168,76,.35);
      flex-shrink: 0;
    }
    .card-header-custom h2 {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 1.2rem;
      color: var(--white);
      margin: 0 0 2px;
    }
    .card-header-custom p {
      color: rgba(255,255,255,.55);
      font-size: .8rem;
      margin: 0;
    }
    .header-divider {
      width: 36px; height: 3px;
      background: linear-gradient(90deg, var(--gold), var(--gold-light));
      border-radius: 2px;
      margin-top: 10px;
    }

    /* ── ACTION BAR ── */
    .action-bar {
      padding: 20px 32px;
      border-bottom: 1px solid #eef1f7;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
      background: #fafbfd;
    }

    /* ── BUTTONS ── */
    .btn-nuevo {
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
      color: var(--navy-dark);
      border: none;
      border-radius: 10px;
      padding: 10px 22px;
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: .82rem;
      letter-spacing: .4px;
      text-transform: uppercase;
      display: inline-flex; align-items: center; gap: 7px;
      transition: transform .15s, box-shadow .2s;
      box-shadow: 0 4px 14px rgba(201,168,76,.35);
      text-decoration: none;
    }
    .btn-nuevo:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 22px rgba(201,168,76,.45);
      color: var(--navy-dark);
    }

    .btn-outline-navy {
      background: transparent;
      color: var(--navy);
      border: 1.5px solid #dce3ed;
      border-radius: 10px;
      padding: 10px 18px;
      font-family: 'Montserrat', sans-serif;
      font-weight: 600;
      font-size: .82rem;
      display: inline-flex; align-items: center; gap: 7px;
      transition: all .2s;
      text-decoration: none;
    }
    .btn-outline-navy:hover {
      background: var(--surface);
      border-color: var(--navy);
      color: var(--navy-dark);
    }

    /* ── TABLE ── */
    .table-wrapper {
      padding: 0 0 8px;
      overflow-x: auto;
    }

    .table-custom {
      width: 100%;
      border-collapse: collapse;
      font-size: .855rem;
      margin: 0;
    }

    .table-custom thead tr {
      background: var(--navy-dark);
    }
    .table-custom thead th {
      color: rgba(255,255,255,.75);
      font-family: 'Montserrat', sans-serif;
      font-weight: 600;
      font-size: .72rem;
      letter-spacing: 1px;
      text-transform: uppercase;
      padding: 14px 18px;
      border: none;
      white-space: nowrap;
    }
    .table-custom thead th:first-child { padding-left: 28px; }
    .table-custom thead th:last-child  { padding-right: 28px; }

    .table-custom tbody tr {
      border-bottom: 1px solid #eef1f7;
      transition: background .15s;
    }
    .table-custom tbody tr:last-child { border-bottom: none; }
    .table-custom tbody tr:hover { background: #f7f9ff; }

    .table-custom tbody td {
      padding: 14px 18px;
      color: var(--navy-mid);
      vertical-align: middle;
    }
    .table-custom tbody td:first-child { padding-left: 28px; }
    .table-custom tbody td:last-child  { padding-right: 28px; }

    /* ID cell */
    .cell-id {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      color: var(--navy);
      font-size: .8rem;
    }

    /* Name cell */
    .cell-name {
      font-weight: 600;
      color: var(--navy-dark);
    }
    .cell-name small {
      display: block;
      font-weight: 400;
      color: #8a98aa;
      font-size: .77rem;
      margin-top: 1px;
    }

    /* Amount cell */
    .cell-amount {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      color: var(--navy-dark);
    }
    .cell-pending {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      color: var(--gold);
    }

    /* ── BADGES ── */
    .badge-mora {
      display: inline-flex; align-items: center; gap: 5px;
      background: rgba(220,53,69,.12);
      color: #c0392b;
      border: 1px solid rgba(220,53,69,.25);
      font-size: .73rem;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 50px;
      letter-spacing: .3px;
    }
    .badge-mora::before {
      content: '';
      width: 6px; height: 6px;
      background: #c0392b;
      border-radius: 50%;
      display: inline-block;
    }

    .badge-pagado {
      display: inline-flex; align-items: center; gap: 5px;
      background: rgba(25,135,84,.12);
      color: #157347;
      border: 1px solid rgba(25,135,84,.25);
      font-size: .73rem;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 50px;
      letter-spacing: .3px;
    }
    .badge-pagado::before {
      content: '';
      width: 6px; height: 6px;
      background: #157347;
      border-radius: 50%;
      display: inline-block;
    }

    .badge-activo {
      display: inline-flex; align-items: center; gap: 5px;
      background: rgba(201,168,76,.12);
      color: #9a7430;
      border: 1px solid rgba(201,168,76,.3);
      font-size: .73rem;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 50px;
      letter-spacing: .3px;
    }
    .badge-activo::before {
      content: '';
      width: 6px; height: 6px;
      background: var(--gold);
      border-radius: 50%;
      display: inline-block;
    }

    /* ── ACTION BUTTONS IN TABLE ── */
    .btn-action {
      display: inline-flex; align-items: center; gap: 5px;
      font-size: .76rem;
      font-weight: 600;
      font-family: 'Montserrat', sans-serif;
      padding: 6px 12px;
      border-radius: 8px;
      text-decoration: none;
      transition: all .18s;
      white-space: nowrap;
      border: 1.5px solid transparent;
    }

    .btn-action-cartilla {
      background: rgba(26,53,96,.08);
      color: var(--navy);
      border-color: rgba(26,53,96,.18);
    }
    .btn-action-cartilla:hover {
      background: var(--navy);
      color: var(--white);
      border-color: var(--navy);
    }

    .btn-action-pago {
      background: rgba(25,135,84,.09);
      color: #157347;
      border-color: rgba(25,135,84,.22);
    }
    .btn-action-pago:hover {
      background: #157347;
      color: var(--white);
      border-color: #157347;
    }

    .btn-action-historial {
      background: rgba(201,168,76,.12);
      color: #9a7430;
      border-color: rgba(201,168,76,.28);
    }
    .btn-action-historial:hover {
      background: var(--gold);
      color: var(--navy-dark);
      border-color: var(--gold);
    }

    /* ── EMPTY STATE ── */
    .empty-state {
      text-align: center;
      padding: 60px 32px;
    }
    .empty-state .empty-icon {
      width: 72px; height: 72px;
      background: var(--surface);
      border-radius: 18px;
      display: inline-flex; align-items: center; justify-content: center;
      color: #b0bac8;
      font-size: 2rem;
      margin-bottom: 18px;
    }
    .empty-state h5 {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      color: var(--navy-dark);
      margin-bottom: 6px;
    }
    .empty-state p {
      color: #8a98aa;
      font-size: .88rem;
    }

    /* ── CARD FOOTER ── */
    .card-footer-custom {
      padding: 14px 32px;
      background: var(--surface);
      border-top: 1px solid #e5eaf2;
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 8px;
    }
    .footer-note {
      display: flex; align-items: center; gap: 6px;
      color: #7a8898;
      font-size: .78rem;
    }
    .footer-note i { color: var(--gold); }


     /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
      .card-header-custom { padding: 20px 20px; }
      .action-bar { padding: 16px 20px; }
      .table-custom thead th,
      .table-custom tbody td { padding: 12px 12px; }
      .table-custom thead th:first-child,
      .table-custom tbody td:first-child { padding-left: 16px; }
      .table-custom thead th:last-child,
      .table-custom tbody td:last-child  { padding-right: 16px; }
      .card-footer-custom { padding: 12px 16px; }
      .page-header h1 { font-size: 1.35rem; }
      .page-wrapper       { padding: 24px 12px 48px; }
      .card-header-custom { padding: 20px 18px; }
      .action-bar         { padding: 14px 16px; flex-direction: column; align-items: flex-start; }
      .card-footer-custom { padding: 12px 16px; flex-direction: column; gap: 4px; }
      .page-header h1     { font-size: 1.3rem; }
      .page-header p      { font-size: .82rem; }
    }

    /* ── TABLA → TARJETAS EN MÓVIL ── */
    @media (max-width: 640px) {
      /* Ocultar thead */
      .table-custom thead { display: none; }

      /* Cada fila se convierte en tarjeta */
      .table-custom tbody,
      .table-custom tbody tr,
      .table-custom tbody td { display: block; width: 100%; }

      .table-custom tbody tr {
        background: var(--white);
        border: 1px solid #e5eaf2;
        border-radius: 14px;
        margin: 12px 16px;
        width: calc(100% - 32px);
        padding: 16px;
        box-shadow: 0 2px 10px rgba(13,31,60,.06);
        position: relative;
      }
      .table-custom tbody tr:hover { background: var(--white); }

      .table-custom tbody td {
        padding: 5px 0;
        border: none;
        font-size: .85rem;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--navy-mid);
      }

      /* Etiqueta dinámica antes de cada celda */
      .table-custom tbody td::before {
        content: attr(data-label);
        font-family: 'Montserrat', sans-serif;
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #a0acbc;
        min-width: 90px;
        flex-shrink: 0;
      }

      /* ID como badge en esquina superior derecha */
      .table-custom tbody td[data-label="ID"] {
        position: absolute;
        top: 14px; right: 14px;
        padding: 0;
        width: auto;
        min-width: unset;
      }
      .table-custom tbody td[data-label="ID"]::before { display: none; }

      /* Nombre ocupa todo el ancho superior */
      .table-custom tbody td[data-label="Cliente"] {
        padding-bottom: 10px;
        margin-bottom: 8px;
        border-bottom: 1px solid #eef1f7;
        padding-right: 60px; /* espacio para badge ID */
      }
      .table-custom tbody td[data-label="Cliente"] .cell-name {
        font-size: .92rem;
      }

      /* Botones de acción en grid 2 columnas */
      .table-custom tbody td[data-label="Acciones"] {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #eef1f7;
        flex-direction: column;
        align-items: flex-start;
      }
      .table-custom tbody td[data-label="Acciones"]::before { display: none; }
      .table-custom tbody td[data-label="Acciones"] > div {
        display: grid !important;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 6px;
        width: 100%;
      }
      .btn-action {
        justify-content: center;
        font-size: .73rem;
        padding: 7px 8px;
      }

      /* Ocultar columna Teléfono en móvil (opcional) */
      .table-custom tbody td[data-label="Teléfono"] { display: none; }

      /* Table wrapper sin overflow en móvil */
      .table-wrapper { overflow-x: unset; }
    }

    @media (max-width: 380px) {
      .table-custom tbody td[data-label="Acciones"] > div {
        grid-template-columns: 1fr 1fr;
      }
    }
  </style>
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