<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar Préstamo — Capital Express</title>
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
      max-width: 860px;
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

    /* ── CARD ── */
    .form-card {
      background: var(--white);
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 8px 40px rgba(13,31,60,.10), 0 2px 8px rgba(13,31,60,.06);
      border: 1px solid rgba(201,168,76,.15);
    }

    /* ── CARD HEADER ── */
    .form-card-header {
      background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy) 100%);
      padding: 28px 32px;
      position: relative;
      overflow: hidden;
    }
    .form-card-header::before {
      content: '';
      position: absolute;
      top: -40px; right: -40px;
      width: 180px; height: 180px;
      border-radius: 50%;
      background: rgba(201,168,76,.08);
    }
    .form-card-header::after {
      content: '';
      position: absolute;
      bottom: -60px; right: 60px;
      width: 140px; height: 140px;
      border-radius: 50%;
      background: rgba(201,168,76,.05);
    }
    .header-icon {
      width: 52px; height: 52px;
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      color: var(--navy-dark);
      font-size: 1.4rem;
      margin-bottom: 14px;
      box-shadow: 0 4px 16px rgba(201,168,76,.35);
    }
    .form-card-header h2 {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 1.25rem;
      color: var(--white);
      margin: 0 0 4px;
    }
    .form-card-header p {
      color: rgba(255,255,255,.55);
      font-size: .82rem;
      margin: 0;
    }
    .header-divider {
      width: 40px; height: 3px;
      background: linear-gradient(90deg, var(--gold), var(--gold-light));
      border-radius: 2px;
      margin: 12px 0;
    }

    /* ── CARD BODY ── */
    .form-card-body {
      padding: 32px;
    }

    /* ── SECTION LABELS ── */
    .section-label {
      display: flex;
      align-items: center;
      gap: 8px;
      font-family: 'Montserrat', sans-serif;
      font-weight: 600;
      font-size: .78rem;
      letter-spacing: 1.2px;
      text-transform: uppercase;
      color: var(--navy);
      margin-bottom: 18px;
      padding-bottom: 10px;
      border-bottom: 2px solid var(--surface);
    }
    .section-label .section-icon {
      width: 28px; height: 28px;
      background: linear-gradient(135deg, var(--navy-dark), var(--navy));
      border-radius: 7px;
      display: flex; align-items: center; justify-content: center;
      color: var(--gold-light);
      font-size: .8rem;
    }

    /* ── FORM CONTROLS ── */
    .form-label {
      font-size: .8rem;
      font-weight: 600;
      color: #3a4a5e;
      margin-bottom: 6px;
      letter-spacing: .3px;
    }
    .form-label .required {
      color: var(--gold);
      margin-left: 2px;
    }
    .form-control, .form-select {
      border: 1.5px solid #dce3ed;
      border-radius: 10px;
      padding: 10px 14px;
      font-size: .88rem;
      color: var(--navy-dark);
      background-color: #fafbfd;
      transition: border-color .2s, box-shadow .2s, background-color .2s;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 3px rgba(201,168,76,.15);
      background-color: var(--white);
      outline: none;
    }
    .form-control::placeholder { color: #b0bac8; }

    /* input-group icon */
    .input-wrapper { position: relative; }
    .input-wrapper .field-icon {
      position: absolute;
      left: 13px; top: 50%; transform: translateY(-50%);
      color: #a0acbc;
      font-size: .9rem;
      pointer-events: none;
      transition: color .2s;
    }
    .input-wrapper .form-control,
    .input-wrapper .form-select {
      padding-left: 38px;
    }
    .input-wrapper:focus-within .field-icon { color: var(--gold); }

    /* ── DIVIDER ── */
    .section-divider {
      height: 1px;
      background: var(--surface);
      margin: 28px 0;
    }

    /* ── BUTTONS ── */
    .btn-register {
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
      color: var(--navy-dark);
      border: none;
      border-radius: 10px;
      padding: 12px 28px;
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: .88rem;
      letter-spacing: .5px;
      text-transform: uppercase;
      display: inline-flex; align-items: center; gap: 8px;
      transition: transform .15s, box-shadow .2s;
      box-shadow: 0 4px 16px rgba(201,168,76,.35);
    }
    .btn-register:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(201,168,76,.45);
      color: var(--navy-dark);
    }
    .btn-register:active { transform: translateY(0); }

    .btn-listado {
      background: transparent;
      color: var(--navy);
      border: 1.5px solid #dce3ed;
      border-radius: 10px;
      padding: 12px 24px;
      font-family: 'Montserrat', sans-serif;
      font-weight: 600;
      font-size: .88rem;
      display: inline-flex; align-items: center; gap: 8px;
      transition: all .2s;
    }
    .btn-listado:hover {
      background: var(--surface);
      border-color: var(--navy);
      color: var(--navy-dark);
    }

    /* ── FOOTER NOTE ── */
    .form-footer {
      padding: 16px 32px;
      background: var(--surface);
      border-top: 1px solid #e5eaf2;
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 8px;
    }
    .form-footer .security-note {
      display: flex; align-items: center; gap: 6px;
      color: #7a8898;
      font-size: .78rem;
    }
    .form-footer .security-note i { color: var(--gold); }



     /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
      .page-wrapper       { padding: 24px 12px 48px; }
      .form-card-header   { padding: 22px 18px; }
      .form-card-body     { padding: 24px 18px; }
      .form-footer        { padding: 14px 16px; flex-direction: column; gap: 4px; }
      .page-header h1     { font-size: 1.3rem; }
      .page-header p      { font-size: .82rem; }
    }

    @media (max-width: 576px) {
      .form-card-body { padding: 20px 16px; }
      .form-card-header { padding: 22px 20px; }
      .form-footer { padding: 14px 16px; }
      .page-header h1 { font-size: 1.35rem; }
      /* Stack columns */
      .row.g-3 > [class*="col-md-"] { width: 100% !important; }

      /* Buttons full width */
      .btn-register,
      .btn-listado {
        width: 100%;
        justify-content: center;
        padding: 13px 20px;
      }
      .d-flex.gap-3.flex-wrap {
        flex-direction: column;
        gap: 10px !important;
      }

      /* Header icon smaller */
      .header-icon { width: 42px; height: 42px; font-size: 1.15rem; }

      /* Section divider tighter */
      .section-divider { margin: 20px 0; }

      /* Section label adjust */
      .section-label { font-size: .72rem; }

      /* Form controls bigger touch target */
      .form-control,
      .form-select { padding: 12px 14px 12px 38px; font-size: .9rem; }
    }

    @media (max-width: 380px) {
      .form-card-header { padding: 18px 14px; }
      .form-card-body   { padding: 18px 14px; }
      .page-header h1   { font-size: 1.15rem; }
    }
  </style>
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

</body>
</html>

