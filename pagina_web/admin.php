<?php
/**
 * login.php — Capital Express
 * Solo interfaz. La lógica de autenticación va en procesar_login.php
 */

// Si ya tienes $empresa cargada en tu proyecto (ej. via config.php), elimina estas líneas:
$empresa = ['nombre' => 'Capital Express'];

// Sesión y token CSRF
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Mensaje de error enviado desde procesar_login.php
$error = '';
if (!empty($_GET['error'])) {
    $error = match ((string) $_GET['error']) {
        '1'      => 'Usuario o contraseña incorrectos.',
        'empty'  => 'Por favor completa todos los campos.',
        'lock'   => 'Cuenta bloqueada temporalmente. Intenta más tarde.',
        default  => 'Ocurrió un error. Intenta de nuevo.',
    };
}
// Alternativa con sesión flash (descomenta si prefieres este método):
// if (!empty($_SESSION['login_error'])) {
//     $error = $_SESSION['login_error'];
//     unset($_SESSION['login_error']);
// }

// Recuperar el último usuario escrito para no borrar el campo tras el error
$last_user = htmlspecialchars($_GET['u'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Administrador &mdash; <?= htmlspecialchars($empresa['nombre']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
<link href="../css/admin.css" rel="stylesheet" />
</head>
<body>

  <!-- ══════════════════════════════════════════
       PANEL IZQUIERDO — Hero / Branding
  ═══════════════════════════════════════════ -->
  <section class="hero-panel">
    <div class="hero-inner">

      <div class="hero-brand">
        <i class="bi bi-bank2 hero-brand-icon"></i>
        <span class="hero-brand-name"><?= htmlspecialchars($empresa['nombre']) ?></span>
      </div>

      <h1 class="hero-title">Sistema de Gestión de Préstamos</h1>
      <p class="hero-subtitle">
        Plataforma de gestión financiera para el control total de créditos,
        clientes y pagos desde un solo lugar.
      </p>

      <div class="hero-stats">
        <div class="stat-card">
          <div class="stat-value">1,240</div>
          <div class="stat-label">Créditos activos</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">98%</div>
          <div class="stat-label">Recuperación</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">24/7</div>
          <div class="stat-label">Disponibilidad</div>
        </div>
      </div>

    </div>
  </section>

  <!-- ══════════════════════════════════════════
       PANEL DERECHO — Formulario de Login
  ═══════════════════════════════════════════ -->
  <section class="form-panel">
    <div class="login-card">

      <div class="card-header-custom">
        <div class="secure-badge">
          <i class="bi bi-shield-check-fill"></i>
          Acceso seguro
        </div>
        <h2>Bienvenido de nuevo</h2>
        <p>Ingresa tus credenciales para acceder al panel de administración.</p>
      </div>

      <?php if (!empty($error)) : ?>
        <div class="alert-error" role="alert">
          <i class="bi bi-exclamation-circle-fill"></i>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <form method="POST" action="procesar_login.php" id="loginForm" novalidate>

        <!-- Token CSRF: valídalo en procesar_login.php con $_SESSION['csrf_token'] -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />

        <!-- Usuario -->
        <div>
          <label for="usuario" class="field-label">Usuario</label>
          <div class="input-group-custom">
            <i class="bi bi-person-fill input-icon"></i>
            <input
              type="text"
              id="usuario"
              name="usuario"
              class="form-control-custom"
              placeholder="Tu nombre de usuario"
              value="<?= $last_user ?>"
              autocomplete="username"
              required
            />
          </div>
        </div>

        <!-- Contraseña -->
        <div>
          <label for="password" class="field-label">Contraseña</label>
          <div class="input-group-custom">
            <i class="bi bi-lock-fill input-icon"></i>
            <input
              type="password"
              id="password"
              name="password"
              class="form-control-custom"
              placeholder="Tu contraseña"
              autocomplete="current-password"
              required
            />
            <button type="button" class="toggle-password" id="togglePwd" aria-label="Mostrar/ocultar contraseña">
              <i class="bi bi-eye-fill" id="pwdIcon"></i>
            </button>
          </div>
        </div>

        <!-- Recordar sesión / Olvidé contraseña -->
        <div class="form-options">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="recordar" name="recordar" />
            <label class="form-check-label" for="recordar">Recordar sesión</label>
          </div>
          <a href="recuperar_password.php" class="forgot-link">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" class="btn-login">
          <i class="bi bi-box-arrow-in-right btn-icon"></i>
          Iniciar sesión
        </button>

      </form>

      <div class="gold-divider"></div>

      <div class="card-footer-custom">
        <p>
          <i class="bi bi-lock-fill me-1" style="color: var(--gold); font-size:.8rem;"></i>
          Conexión cifrada SSL &nbsp;|&nbsp;
          &copy; <?= date('Y') ?> <a href="#"><?= htmlspecialchars($empresa['nombre']) ?></a>
        </p>
        <p class="mt-1">Solo para personal autorizado.</p>
      </div>

    </div>
  </section>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    /* ── Toggle mostrar/ocultar contraseña ── */
    const toggleBtn = document.getElementById('togglePwd');
    const pwdInput  = document.getElementById('password');
    const pwdIcon   = document.getElementById('pwdIcon');

    toggleBtn.addEventListener('click', () => {
      const isHidden    = pwdInput.type === 'password';
      pwdInput.type     = isHidden ? 'text' : 'password';
      pwdIcon.className = isHidden ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill';
    });

    /* ── Validación del lado del cliente ── */
    document.getElementById('loginForm').addEventListener('submit', function (e) {
      const userEl = document.getElementById('usuario');
      const pwdEl  = document.getElementById('password');
      let valid    = true;

      if (!userEl.value.trim()) { userEl.classList.add('is-invalid'); valid = false; }
      if (!pwdEl.value.trim())  { pwdEl.classList.add('is-invalid');  valid = false; }

      if (!valid) e.preventDefault();
    });

    document.getElementById('usuario').addEventListener('input', function () {
      this.classList.remove('is-invalid');
    });
    document.getElementById('password').addEventListener('input', function () {
      this.classList.remove('is-invalid');
    });
  </script>

</body>
</html>
