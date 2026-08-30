<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Capital Express - Nuevo Préstamo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="css/estilo_nuevo_prestamo.css" rel="stylesheet">
</head>
<body>

<!-- ========================= -->
<!-- TOP BAR -->
<!-- ========================= -->
<header class="ce-topbar">
  <div class="container d-flex align-items-center justify-content-between py-2">
    <div class="d-flex align-items-center gap-2">
      <span class="ce-brand-mark" style="font-family: 'Playfair Display', serif;">CE</span>
    <div>
    <div class="ce-brand-name" style="font-family: 'Playfair Display', serif;">Capital Express</div>
        <div class="ce-brand-tag">Gestión de préstamos</div>
      </div>
    </div>
    <nav class="d-none d-md-flex align-items-center gap-1">
      <a href="index.php" class="nav-link active"><i class="bi bi-file-earmark-plus me-1"></i>Nuevo préstamo</a>
      <a href="listado.php" class="nav-link"><i class="bi bi-list-ul me-1"></i>Listado</a>
      <a href="public/dashboard.php" class="nav-link"><i class="bi bi-person-circle me-1"></i>Panel</a>
    </nav>
  </div>
</header>

<main class="container my-4 my-md-5">

  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">

      <!-- Page heading -->
      <div class="mb-3">
        <span class="ce-kicker">Solicitud de crédito</span>
        <h1 class="ce-page-title fs-3 mt-1 mb-1">Registro de Nuevo Préstamo</h1>
        <p class="ce-page-subtitle mb-0">Completa los datos del cliente y las condiciones del préstamo para generar el registro.</p>
      </div>

      <div class="ce-card mt-4">

        <div class="ce-card-header">
          <h2>Capital Express</h2>
          <small>Registro de Nuevo Préstamo</small>
        </div>

        <div class="ce-card-body">

          <form action="guardar.php" method="POST" id="formPrestamo">

            <input type="hidden" name="cliente_id" id="cliente_id">

            <div class="row">

              <!-- ========================= -->
              <!-- DATOS CLIENTE -->
              <!-- ========================= -->

              <div class="col-12">
                <div class="ce-section-title">
                  <span class="ce-section-icon"><i class="bi bi-person-fill"></i></span>
                  Datos del Cliente
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label" for="cedula">Cédula</label>
                <div class="input-group">
                  <input
                    type="text"
                    class="form-control"
                    name="cedula"
                    id="cedula"
                    placeholder="000-0000000-0"
                    required>
                  <button
                    type="button"
                    class="btn"
                    id="buscarCliente">
                    <i class="bi bi-search"></i> Buscar
                  </button>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label" for="estadoCliente">Estado</label>
                <input
                  type="text"
                  class="form-control"
                  id="estadoCliente"
                  value="Cliente nuevo"
                  readonly>
              </div>

              <div class="col-12">
                <div id="infoCliente" class="card border-0 mt-2 mb-4" style="display:none;">

                  <div class="card-header text-white" id="colorEstado">
                    <h5 class="mb-0">
                      <i class="bi bi-person-fill"></i> Información del Cliente
                    </h5>
                  </div>

                  <div class="card-body">
                    <div class="row">

                      <div class="col-md-6">
                        <div class="ce-info-item">
                          <span class="ce-info-label">Nombre</span>
                          <span class="ce-info-value" id="txtNombre"></span>
                        </div>
                        <div class="ce-info-item">
                          <span class="ce-info-label">Teléfono</span>
                          <span class="ce-info-value" id="txtTelefono"></span>
                        </div>
                        <div class="ce-info-item">
                          <span class="ce-info-label">Dirección</span>
                          <span class="ce-info-value" id="txtDireccion"></span>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="ce-info-item">
                          <span class="ce-info-label">Estado</span>
                          <span class="ce-info-value" id="txtEstado"></span>
                        </div>
                        <div class="ce-info-item">
                          <span class="ce-info-label">Monto</span>
                          <span class="ce-info-value" id="txtMonto"></span>
                        </div>
                        <div class="ce-info-item">
                          <span class="ce-info-label">Abonado</span>
                          <span class="ce-info-value" id="txtAbonado"></span>
                        </div>
                        <div class="ce-info-item">
                          <span class="ce-info-label">Pendiente</span>
                          <span class="ce-info-value" id="txtPendiente"></span>
                        </div>
                        <div class="ce-info-item">
                          <span class="ce-info-label">Mora</span>
                          <span class="ce-info-value" id="txtMora"></span>
                        </div>
                        <div class="ce-info-item">
                          <span class="ce-info-label">Frecuencia</span>
                          <span class="ce-info-value" id="txtFrecuencia"></span>
                        </div>
                        <div class="ce-info-item">
                          <span class="ce-info-label">Fecha límite</span>
                          <span class="ce-info-value" id="txtFecha"></span>
                        </div>
                      </div>

                    </div>
                  </div>

                </div>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label" for="nombre">Nombre</label>
                <input
                  type="text"
                  class="form-control"
                  name="nombre"
                  id="nombre"
                  placeholder="Nombre completo"
                  required>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label" for="telefono">Teléfono</label>
                <input
                  type="text"
                  class="form-control"
                  name="telefono"
                  id="telefono"
                  placeholder="(000) 000-0000"
                  required>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label" for="direccion">Dirección</label>
                <input
                  type="text"
                  class="form-control"
                  name="direccion"
                  id="direccion"
                  placeholder="Calle, número, sector">
              </div>

            </div>

            <hr class="ce-divider">

            <!-- ========================= -->
            <!-- DATOS PRESTAMO -->
            <!-- ========================= -->

            <div class="row">

              <div class="col-12">
                <div class="ce-section-title is-loan">
                  <span class="ce-section-icon"><i class="bi bi-cash-coin"></i></span>
                  Datos del Préstamo
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label" for="monto">Monto Prestado</label>
                <div class="input-group">
                  <span class="input-group-text">RD$</span>
                  <input
                    type="number"
                    class="form-control"
                    name="monto"
                    id="monto"
                    placeholder="0.00"
                    required>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label" for="interes">Interés (%)</label>
                <div class="input-group">
                  <input
                    type="number"
                    class="form-control"
                    name="interes"
                    id="interes"
                    placeholder="0"
                    required>
                  <span class="input-group-text">%</span>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label" for="cuotas">Cuotas</label>
                <input
                  type="number"
                  class="form-control"
                  name="cuotas"
                  id="cuotas"
                  placeholder="Número de cuotas"
                  required>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label" for="frecuencia">Frecuencia</label>
                <select
                  name="frecuencia"
                  id="frecuencia"
                  class="form-select"
                  required>
                  <option value="">Seleccione</option>
                  <option value="Semanal">Semanal</option>
                  <option value="Quincenal">Quincenal</option>
                </select>
              </div>

            </div>

            <hr class="ce-divider">

            <div class="ce-actions">
              <a href="listado.php" class="btn ce-btn-secondary">
                <i class="bi bi-list-ul me-1"></i> Ver listado
              </a>
              <button
                type="submit"
                class="btn"
                id="btnGuardar">
                <i class="bi bi-check-circle me-1"></i> Registrar préstamo
              </button>
            </div>

          </form>

        </div>
      </div>

      <p class="text-center text-secondary small mt-4 mb-0">
        Capital Express &copy; 2026 — Todos los derechos reservados
      </p>

    </div>
  </div>

</main>

<script src="js/nuevo_prestamo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
