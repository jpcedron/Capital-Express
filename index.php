<?php

require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$clienteInactivo = isset($_GET['error']) && $_GET['error'] === 'cliente_inactivo';

?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar Préstamo — Capital Express</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/index.css">
</head>
<body>

  <!-- TOP BAR -->
  <nav class="top-bar">
    <div class="container">
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
      <div class="step-badge"><i class="bi bi-plus-circle-fill"></i> Nuevo registro</div>
      <h1>Registrar Préstamo</h1>
      <p>Complete los datos del cliente y las condiciones del crédito para continuar.</p>
    </div>

    <!-- Main Card -->
    <div class="form-card">

      <!-- Card Header -->
      <div class="form-card-header">
        <div class="header-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
        <h2>Formulario de Préstamo</h2>
        <div class="header-divider"></div>
        <p>Los campos marcados con <strong style="color:var(--gold-light)">*</strong> son obligatorios</p>
      </div>

      <!-- Card Body -->
      <div class="form-card-body">
        <form action="guardar.php" method="POST">

          <!-- SECCIÓN: Datos del Cliente -->
          <div class="section-label">
            <div class="section-icon"><i class="bi bi-person-fill"></i></div>
            Datos del Cliente
          </div>

          <div class="row g-3 mb-2">

            <div class="col-md-6">
              <label class="form-label">Nombre <span class="required">*</span></label>
              <div class="input-wrapper">
                <i class="bi bi-person field-icon"></i>
                <input type="text" id="nombre" name="nombre"
                       class="form-control" placeholder="Nombre completo" required>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Cédula <span class="required">*</span></label>
              <div class="input-wrapper">
                <i class="bi bi-card-text field-icon"></i>
                <input type="text" id="cedula" name="cedula"
                       class="form-control" placeholder="N.º de identificación" required>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Teléfono <span class="required">*</span></label>
              <div class="input-wrapper">
                <i class="bi bi-telephone field-icon"></i>
                <input type="text" id="telefono" name="telefono"
                       class="form-control" placeholder="Número de contacto" required>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Dirección</label>
              <div class="input-wrapper">
                <i class="bi bi-geo-alt field-icon"></i>
                <input type="text" id="direccion" name="direccion"
                       class="form-control" placeholder="Dirección de residencia">
              </div>
            </div>

          </div>

          <div class="section-divider"></div>

          <!-- SECCIÓN: Condiciones del Préstamo -->
          <div class="section-label">
            <div class="section-icon"><i class="bi bi-currency-dollar"></i></div>
            Condiciones del Préstamo
          </div>

          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Monto Prestado <span class="required">*</span></label>
              <div class="input-wrapper">
                <i class="bi bi-cash-coin field-icon"></i>
                <input type="number" name="monto"
                       class="form-control" placeholder="0.00" required>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Interés (%) <span class="required">*</span></label>
              <div class="input-wrapper">
                <i class="bi bi-percent field-icon"></i>
                <input type="number" name="interes"
                       class="form-control" placeholder="Ej: 5" required>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Cuotas <span class="required">*</span></label>
              <div class="input-wrapper">
                <i class="bi bi-list-ol field-icon"></i>
                <input type="number" name="cuotas"
                       class="form-control" placeholder="N.º de pagos" required>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Frecuencia de Pago <span class="required">*</span></label>
              <div class="input-wrapper">
                <i class="bi bi-calendar3 field-icon"></i>
                <select name="frecuencia" class="form-select" required>
                  <option value="">— Seleccione —</option>
                  <option value="Semanal">Semanal</option>
                  <option value="Quincenal">Quincenal</option>
                </select>
              </div>
            </div>

          </div>

          <div class="section-divider"></div>

          <!-- ACTION BUTTONS -->
          <div class="d-flex align-items-center gap-3 flex-wrap">
            <button type="submit" class="btn btn-register">
              <i class="bi bi-check2-circle"></i> Registrar Préstamo
            </button>
            <button type="button" class="btn btn-listado"
                    onclick="window.location.href='listado.php'">
              <i class="bi bi-table"></i> Ver Listado
            </button>
          </div>

        </form>
      </div>

      <!-- Card Footer -->
      <div class="form-footer">
        <div class="security-note">
          <i class="bi bi-shield-lock-fill"></i>
          Información cifrada y protegida
        </div>
        <div class="security-note">
          <i class="bi bi-clock-fill"></i>
          Capital Express &copy; <?php echo date('Y'); ?>
        </div>
      </div>

    </div>
    <!-- /form-card -->

  </div>
  <!-- /page-wrapper -->

  <!-- ═══════════════════════════════════════════
       LÓGICA JAVASCRIPT — NO MODIFICAR
  ═══════════════════════════════════════════ -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const cedula = document.getElementById("cedula");

    cedula.addEventListener("blur", function(){

      if(this.value == "") return;

      fetch("buscar_cliente.php?cedula=" + this.value)
        .then(res => res.json())
        .then(data => {

          if(!data){ return; }

          if(data.estado == "Activo" || data.estado == "Mora"){
            Swal.fire({
              icon: "warning",
              title: "Cliente con préstamo activo",
              text: "Este cliente ya tiene un préstamo activo."
            });
            document.querySelector("button[type=submit]").disabled = true;
            return;
          }

          document.getElementById("nombre").value    = data.nombre;
          document.getElementById("telefono").value  = data.telefono;
          document.getElementById("direccion").value = data.direccion;

          Swal.fire({
            icon: "success",
            title: "Cliente encontrado",
            text: "Se cargaron automáticamente los datos del cliente."
          });
        });
    });
  </script>

<!--alerta de cliente inactivo-->
<?php if ($clienteInactivo): ?>

<script>
Swal.fire({
    icon: 'warning',
    title: 'Cliente inactivo',
    text: 'Este cliente está inactivo y no puede recibir un nuevo préstamo.',
    confirmButtonText: 'Entendido'
});
</script>

<?php endif; ?>

</body>
</html>