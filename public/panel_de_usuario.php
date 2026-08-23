<?php

require_once "auth_cliente.php";

$nombreUsuario = $_SESSION["cliente_nombre"];
$cedula = $_SESSION["cliente_cedula"];

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Capital Express — Panel de Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <!-- Google Fondos -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../css/panel_de_usuario.css"/>
  <!-- En el navbar -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">  
</head>
<body>

<!--
     NAVBAR
 -->
<nav class="navbar-ce d-flex align-items-center justify-content-between">

  <!-- Logo + Nombre -->
  <a href="pagina_informativa.php" class="navbar-brand-ce">
    <div class="brand-icon brand-heading">CE</div>
    <div class="brand-text">
      <div class="name ">Capital Express</div>
      <div class="tagline">Gestión de Préstamos</div>
    </div>
  </a>

  <!-- Usuario + Cerrar sesión -->
  <div class="d-flex align-items-center gap-3">
    <div class="user-badge">
      <i class="bi bi-person-circle"></i>
      <span><?= htmlspecialchars($nombreUsuario) ?></span>
    </div>
    <a href="logout.php" class="btn-logout">
      <i class="bi bi-box-arrow-right"></i>
      Cerrar Sesión
    </a>
  </div>

</nav>


<!-- 
     HERO / ENCABEZADO
-->
<section class="hero-section text-center">
  <div class="container">

    <div class="hero-badge">
      <i class="bi bi-shield-check"></i>
      Panel de Usuario
    </div>

    <h1 class="hero-title">
      Gestiona tu información<br>
      <span>de forma rápida y segura</span>
    </h1>

    <p class="hero-desc">
      Accede a tu cartilla de pagos, consulta tus recibos y
      revisa el detalle de tus cuotas desde un solo lugar.
      Tu información siempre disponible, clara y actualizada.
    </p>

  </div>
</section>


<!-- 
     TARJETAS DE SERVICIOS
 -->
<section class="cards-section">
  <div class="container">

    <div class="text-center mb-4">
      <div class="gold-divider"></div>
      <h2 class="fs-5 fw-600 text-navy" style="color:var(--navy-mid); font-weight:600;">
        Selecciona una opción
      </h2>
    </div>

    <div class="row g-4 justify-content-center">

      <!-- ── TARJETA 1: Cartilla ── -->
      <div class="col-12 col-sm-6 col-lg-4">
        <div class="service-card">
          <div class="card-img-wrap">
            <img src="img/cartilla.png" alt="Cartilla de pagos" />
            <span class="card-img-overlay-badge">Disponible</span>
          </div>
          <div class="card-body-ce">
            <div class="card-icon-ring">
              <i class="bi bi-journal-bookmark-fill"></i>
            </div>
            <h3 class="card-title-ce">Cartilla</h3>
            <p class="card-desc-ce">
              Consulta y descarga tu cartilla de pagos actualizada.
              Lleva el control de tus abonos de manera ordenada y
              sin complicaciones.
            </p>
            <a href="cartilla_cliente.php" class="btn-card-action">
              <i class="bi bi-arrow-right-circle-fill"></i>
              Ver Cartilla
            </a>
          </div>
        </div>
      </div>

      <!-- ── TARJETA 2: Recibo ── -->
      <div class="col-12 col-sm-6 col-lg-4">
        <div class="service-card">
          <div class="card-img-wrap">
            <img src="img/recibo.png" alt="Recibo de pago" />
            <span class="card-img-overlay-badge">Disponible</span>
          </div>
          <div class="card-body-ce">
            <div class="card-icon-ring">
              <i class="bi bi-receipt-cutoff"></i>
            </div>
            <h3 class="card-title-ce">Recibo</h3>
            <p class="card-desc-ce">
              Accede al historial de tus recibos de pago. Descarga
              o imprime los comprobantes que necesitas en cualquier
              momento.
            </p>
            <a href="recibo_cliente.php" class="btn-card-action">
              <i class="bi bi-arrow-right-circle-fill"></i>
              Ver Recibo
            </a>
          </div>
        </div>
      </div>

      <!-- ── TARJETA 3: Cuotas ── -->
      <div class="col-12 col-sm-6 col-lg-4">
        <div class="service-card">
          <div class="card-img-wrap">
            <img src="img/cuotas.png" alt="Cuotas del préstamo" />
            <span class="card-img-overlay-badge">Disponible</span>
          </div>
          <div class="card-body-ce">
            <div class="card-icon-ring">
              <i class="bi bi-calendar2-check-fill"></i>
            </div>
            <h3 class="card-title-ce">Cuotas</h3>
            <p class="card-desc-ce">
              Revisa el plan de cuotas de tu préstamo activo.
              Conoce las fechas de vencimiento y los montos
              pendientes de manera clara.
            </p>
            <a href="cuotas_cliente.php" class="btn-card-action">
              <i class="bi bi-arrow-right-circle-fill"></i>
              Ver Cuotas
            </a>
          </div>
        </div>
      </div>

    </div><!-- /row -->
  </div><!-- /container -->
</section>


<!--FOOTER -->
<footer class="footer-ce">
  &copy; <?= date('Y') ?> <span>Capital Express</span> &mdash; Todos los derechos reservados.
</footer>


<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
