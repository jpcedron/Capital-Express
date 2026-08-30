<?php
session_start();

if (isset($_SESSION["cliente_id"])) {
    header("Location: panel_de_usuario.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Capital Express — Iniciar Sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="../css/login.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid vh-100-min d-flex align-items-stretch p-0">
  <div class="row g-0 w-100">

    <!-- ===== PANEL IZQUIERDO — BRANDING ===== -->
    <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-between bg-navy position-relative overflow-hidden p-5">
      <!-- Círculos decorativos -->
      <div class="watermark"></div>
      <div class="watermark-2"></div>

      <!-- Logo y nombre -->
      <div>
        <div class="d-flex align-items-center gap-3 mb-5">
          <div class="icon-box rounded-3 d-flex align-items-center justify-content-center  flex-shrink-0">
            <span class="fw-bold text-gold fs-5 brand-heading">CE</span>
          </div>
          <div>
            <p class="text-white text-uppercase fw-semibold mb-0" style="letter-spacing:.14em;font-size:.7rem;">Sistema de Préstamos</p>
            <h2 class="text-gold brand-heading mb-0 lh-1">Capital Express</h2>
          </div>
        </div>

        <!-- Titular principal -->
        <h1 class="brand-heading text-white fw-bold mb-3" style="font-size:2.4rem;line-height:1.2;">
          Gestión inteligente<br>
          <span class="text-gold">de préstamos</span>
        </h1>
        <p class="text-white mb-4" style="opacity:.65;max-width:360px;line-height:1.7;">
          Controla tu préstamo y verifica su estado en tiempo real.
        </p>

        <hr class="divider-gold my-4" style="width:60px;border-width:2px;">

        <!-- Características -->
        <div class="d-flex flex-column gap-3">
          <div class="d-flex align-items-center gap-3">
            <i class="bi bi-shield-check text-gold fs-5"></i>
            <span class="text-white" style="opacity:.8;">Acceso seguro con cifrado de datos</span>
          </div>
          <div class="d-flex align-items-center gap-3">
            <i class="bi bi-graph-up-arrow text-gold fs-5"></i>
            <span class="text-white" style="opacity:.8;">Reportes y seguimiento en tiempo real</span>
          </div>
          <div class="d-flex align-items-center gap-3">
            <i class="bi bi-people text-gold fs-5"></i>
            <span class="text-white" style="opacity:.8;">Gestión completa de clientes y cobros</span>
          </div>
        </div>
      </div>

      <!-- Estadísticas -->
      <div class="row g-3 mt-2">
        <div class="col-4">
          <div class="stat-card rounded-3 p-3 text-center">
            <div class="text-gold fw-bold brand-heading" style="font-size:1.6rem;">+500</div>
            <div class="text-white" style="opacity:.55;font-size:.72rem;">Préstamos activos</div>
          </div>
        </div>
        <div class="col-4">
          <div class="stat-card rounded-3 p-3 text-center">
            <div class="text-gold fw-bold brand-heading" style="font-size:1.6rem;">+300</div>
            <div class="text-white" style="opacity:.55;font-size:.72rem;">Clientes registrados</div>
          </div>
        </div>
        <div class="col-4">
          <div class="stat-card rounded-3 p-3 text-center">
            <div class="text-gold fw-bold brand-heading" style="font-size:1.6rem;">100%</div>
            <div class="text-white" style="opacity:.55;font-size:.72rem;">Tasa de seguridad</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== PANEL DERECHO — FORMULARIO ===== -->
    <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center bg-navy-mid">
      <div class="w-100 px-4 px-sm-5" style="max-width:460px;">

        <!-- Logo móvil (solo visible en pantallas pequeñas) -->
        <div class="d-flex d-lg-none align-items-center gap-2 mb-5 justify-content-center">
          <div class="icon-box rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
            <span class="fw-bold text-gold brand-heading">CE</span>
          </div>
          <h4 class="text-gold brand-heading mb-0">Capital Express</h4>
        </div>

        <!-- Cabecera del formulario -->
        <div class="mb-5">
          <h3 class="text-white brand-heading fw-bold mb-1" style="font-size:1.75rem;">Bienvenido</h3>
          <p class="mb-0" style="color:rgba(255,255,255,.45);">Ingresa tus credenciales para continuar</p>
        </div>

        <!-- Mensaje de error -->
        <?php
          if (isset($_SESSION["error_login"])) {
          ?>
              <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                  <i class="bi bi-exclamation-triangle-fill me-2"></i>
                  <?= $_SESSION["error_login"]; ?>

                  <button
                      type="button"
                      class="btn-close"
                      data-bs-dismiss="alert"
                      aria-label="Cerrar">
                  </button>
              </div>
          <?php
              unset($_SESSION["error_login"]);
          }
          ?>

        <!-- Formulario -->
        <form action="procesar_login.php" method="POST" novalidate>

          <!-- Cédula -->
          <div class="mb-4">
            <label for="cedula" class="form-label text-white fw-medium mb-2" style="font-size:.85rem;letter-spacing:.03em;">
              Número de Cédula
            </label>
            <div class="input-group">
              <span class="input-group-text bg-navy border-end-0" style="background-color:rgba(255,255,255,.07)!important;border:1px solid rgba(255,255,255,.15);border-right:none;">
                <i class="bi bi-person-vcard" style="color:var(--gold);"></i>
              </span>
              <input
                type="text"
                class="form-control input-navy border-start-0"
                id="cedula"
                name="cedula"
                placeholder="Ej: 12345678"
                inputmode="numeric"
                maxlength="20"
                required
                style="border-left:none;"
              >
            </div>
          </div>

          <!-- Contraseña -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label for="password" class="form-label text-white fw-medium mb-0" style="font-size:.85rem;letter-spacing:.03em;">
                Contraseña
              </label>
              <a href="recuperar.php" class="text-decoration-none" style="color:var(--gold);font-size:.8rem;">
                ¿Olvidaste tu contraseña?
              </a>
            </div>
            <div class="input-group">
              <span class="input-group-text" style="background-color:rgba(255,255,255,.07)!important;border:1px solid rgba(255,255,255,.15);border-right:none;">
                <i class="bi bi-lock" style="color:var(--gold);"></i>
              </span>
              <input
                type="password"
                class="form-control input-navy"
                id="password"
                name="password"
                placeholder="••••••••"
                required
                style="border-left:none;border-right:none;"
              >
              <button class="toggle-pass rounded-end" type="button" id="togglePassword" tabindex="-1" aria-label="Mostrar contraseña">
                <i class="bi bi-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>

          <!-- Recordar sesión -->
          <div class="mb-4 d-flex align-items-center gap-2">
            <input class="form-check-input mt-0" type="checkbox" id="recordar" name="recordar" style="background-color:transparent;border-color:rgba(255,255,255,.3);cursor:pointer;">
            <label class="form-check-label" for="recordar" style="color:rgba(255,255,255,.55);font-size:.85rem;cursor:pointer;">
              Mantener sesión iniciada
            </label>
          </div>

          <!-- Botón ingresar -->
          <div class="d-grid mb-4">
            <button type="submit" class="btn btn-gold btn-lg rounded-3 py-3">
              <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar al Sistema
            </button>
          </div>

          <hr class="divider-gold">

          <!-- Pie del formulario -->
          <div class="text-center mt-3">
            <p class="mb-0" style="color:rgba(255,255,255,.3);font-size:.75rem;">
              <i class="bi bi-shield-lock me-1"></i>
              Conexión segura · Capital Express <?= date('Y') ?>
            </p>
          </div>

        </form>
      </div>
    </div>

  </div><!-- /.row -->
</div><!-- /.container-fluid -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/login.js"></script>

</body>
</html>