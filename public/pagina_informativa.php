<?php


require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$empresa = [
    'nombre'    => 'Capital Express',
    'slogan'    => 'Soluciones de préstamo con reglas claras y pagos accesibles',
    'whatsapp'  => '3004383582',
    'correo'    => 'capitalexpressb@gmail.com',
    'horario'   => 'Lunes a Viernes: 8:00 am – 6:00 pm | Sábado: 8:00 am – 4:00 pm',
];

$valores = [
    ['icono' => 'bi-lightbulb',        'titulo' => 'Claridad',         'texto' => 'Cada cliente conoce exactamente cuánto recibe, cuánto pagará y cuáles son las condiciones desde el inicio.'],
    ['icono' => 'bi-list-check',       'titulo' => 'Organización',     'texto' => 'Todos los préstamos se gestionan de forma ordenada para garantizar un seguimiento preciso.'],
    ['icono' => 'bi-check-circle',     'titulo' => 'Cumplimiento',     'texto' => 'Respetamos lo pactado. Las condiciones acordadas son las que rigen durante todo el préstamo.'],
    ['icono' => 'bi-eye',              'titulo' => 'Transparencia',    'texto' => 'Recibos detallados con saldo, mora y capital para que el cliente siempre sepa en qué punto está.'],
    ['icono' => 'bi-cash-coin',        'titulo' => 'Facilidad de pago','texto' => 'Modalidades semanales y quincenales que se adaptan a la realidad de cada cliente.'],
];

$mora_rangos = [
    ['dias' => 'Días 1 – 2',   'tasa' => 'Sin mora',        'clase' => 'success', 'icono' => 'bi-shield-check'],
    ['dias' => 'Días 3 – 14',  'tasa' => '5 % por semana',  'clase' => 'warning', 'icono' => 'bi-exclamation-triangle'],
    ['dias' => 'Días 15 – 30', 'tasa' => '10 % por semana', 'clase' => 'orange',  'icono' => 'bi-exclamation-circle'],
    ['dias' => 'Días 31 – 45', 'tasa' => '15 % por semana', 'clase' => 'danger',  'icono' => 'bi-x-circle'],
    ['dias' => 'Más de 45 días','tasa' => '20 % por semana (máximo)', 'clase' => 'dark', 'icono' => 'bi-x-octagon'],
];

$recibo_items = [
    'Número de recibo',
    'Fecha del pago',
    'Valor abonado',
    'Mora aplicada (si existe)',
    'Saldo pendiente',
    'Total actualizado',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($empresa['slogan']) ?>">
    <title><?= htmlspecialchars($empresa['nombre']) ?> — Préstamos con reglas claras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../css/pagina_informativa.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet"> 
</head>
<body>

<!-- navbar -->
<nav class="navbar navbar-expand-lg navbar-ce fixed-top shadow-sm" aria-label="Navegación principal">
    <div class="container">
        <a class="navbar-brand brand-heading" href="#inicio">
            <i class="bi bi-bank2 me-2"></i><?= htmlspecialchars($empresa['nombre']) ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-3">
                <li class="nav-item"><a class="nav-link" href="#quienes">Quiénes somos</a></li>
                <li class="nav-item"><a class="nav-link" href="#prestamos">Cómo prestamos</a></li>
                <li class="nav-item"><a class="nav-link" href="#mora">Política de mora</a></li>
                <li class="nav-item"><a class="nav-link" href="#compromiso">Compromiso</a></li>
                <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
            </ul>
            <a href="https://wa.me/<?= htmlspecialchars($empresa['whatsapp']) ?>" target="_blank" rel="noopener"
               class="btn btn-gold btn-sm px-3" aria-label="Solicitar préstamo por WhatsApp">
                <i class="bi bi-whatsapp me-1"></i> Solicitar préstamo
            </a>
        </div>
    </div>
</nav>


<!--hero-->
<section id="inicio" class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge badge-gold mb-3 px-3 py-2 fs-6">
                    <i class="bi bi-bank2 me-1"></i> <?= htmlspecialchars($empresa['nombre']) ?>
                </span>
                <h1 class="display-4 mb-4"><?= htmlspecialchars($empresa['slogan']) ?></h1>
                <p class="lead mb-5">
                    Accede a préstamos con condiciones definidas desde el inicio. Conoce cuánto recibes,
                    cuánto pagas y cuáles son las reglas, sin sorpresas.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#prestamos" class="btn btn-gold btn-lg px-4">
                        <i class="bi bi-cash-stack me-2"></i>Ver condiciones
                    </a>
                    <a href="#contacto" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-envelope me-2"></i>Contáctanos
                    </a>
                    <a href="login.php" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
                    </a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-num">2</div>
                            <div class="stat-lbl">Días de tolerancia sin mora</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-num">20%</div>
                            <div class="stat-lbl">Mora máxima por semana</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-num">2</div>
                            <div class="stat-lbl">Modalidades de pago</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-num">100%</div>
                            <div class="stat-lbl">Transparencia en cada recibo</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!--quienes somos-->
<section id="quienes" class="py-6 py-md-7" style="padding-top:5rem;padding-bottom:5rem;">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <div class="section-divider mx-auto"></div>
                <h2 class="section-title display-6 mb-3">¿Quiénes somos?</h2>
                <p class="text-muted fs-5 mb-0">
                    En <strong>Capital Express</strong> ofrecemos préstamos con un modelo basado en claridad,
                    organización y cumplimiento. Creemos que un préstamo debe ser entendible, ordenado y transparente.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($valores as $v): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card valor-card h-100 p-4">
                    <div class="valor-icon mb-3">
                        <i class="bi <?= htmlspecialchars($v['icono']) ?>"></i>
                    </div>
                    <h5 class="fw-700 mb-2" style="font-weight:700;"><?= htmlspecialchars($v['titulo']) ?></h5>
                    <p class="text-muted mb-0 small"><?= htmlspecialchars($v['texto']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!--como prestamos-->
<section id="prestamos" style="padding-top:5rem;padding-bottom:5rem;">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <div class="section-divider mx-auto"></div>
                <h2 class="section-title display-6 mb-3">Nuestra forma de prestar</h2>
                <p class="text-muted fs-5">
                      Trabajamos bajo un modelo de <strong>interés fijo</strong>. Desde el momento en que se aprueba el préstamo se establece el valor total acordado, para que el cliente 
                      conozca desde el inicio cuánto pagará. Nuestro compromiso es ofrecer transparencia, confianza y condiciones claras en cada préstamo.
                </p>
            </div>
        </div>

        <div class="row g-4 align-items-stretch">
            <!-- Ejemplo de cálculo -->
            <div class="col-lg-5">
                <div class="ejemplo-card h-100">
                    <h5 class="text-white mb-4">
                        <i class="bi bi-calculator me-2" style="color:var(--ce-gold);"></i>
                        Ejemplo de cálculo
                    </h5>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between border-bottom border-secondary pb-2 mb-2">
                            <span class="text-white-50">Capital prestado</span>
                            <span class="fw-bold">$500.000</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom border-secondary pb-2 mb-2">
                            <span class="text-white-50">Interés acordado</span>
                            <span class="fw-bold" style="color:var(--ce-gold);">20%</span>
                        </div>
                        <div class="d-flex justify-content-between pt-1">
                            <span class="fw-bold fs-5">Total pactado</span>
                            <span class="monto">$600.000</span>
                        </div>
                    </div>
                    <p class="small text-white-50 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Si el cliente cumple sus pagos en el tiempo establecido, no se generan cargos adicionales.
                    </p>
                </div>
            </div>

            <!-- Modalidades -->
            <div class="col-lg-7">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4" style="color:var(--ce-navy);">
                        <i class="bi bi-calendar-check me-2" style="color:var(--ce-gold);"></i>
                        Modalidades de pago
                    </h5>
                    <div class="d-flex flex-column gap-3 mb-4">
                        <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background:var(--ce-light);">
                            <div style="width:48px;height:48px;border-radius:12px;background:var(--ce-navy);color:var(--ce-gold);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">
                                <i class="bi bi-calendar-week"></i>
                            </div>
                            <div>
                                <strong>Pago Semanal</strong>
                                <p class="text-muted small mb-0">Un pago por semana durante el periodo pactado.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background:var(--ce-light);">
                            <div style="width:48px;height:48px;border-radius:12px;background:var(--ce-navy);color:var(--ce-gold);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">
                                <i class="bi bi-calendar2-range"></i>
                            </div>
                            <div>
                                <strong>Pago Quincenal</strong>
                                <p class="text-muted small mb-0">Un pago cada 15 días durante el periodo pactado.</p>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning border-0 small mb-0" style="background:rgba(201,168,76,.15);">
                        <i class="bi bi-patch-check me-2" style="color:var(--ce-gold);"></i>
                        Cada préstamo se configura según el acuerdo establecido entre las partes.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!--politica de mora-->
<section id="mora" style="padding-top:5rem;padding-bottom:5rem;background:#fff;">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <div class="section-divider mx-auto"></div>
                <h2 class="section-title display-6 mb-3">Política de cumplimiento y mora</h2>
                <p class="text-muted fs-5">
                   Diferenciamos claramente el <strong>interés del préstamo</strong> y la <strong>mora por incumplimiento</strong>. 
                   La mora solo se aplica cuando existe retraso en el pago de las cuotas, de acuerdo con las condiciones establecidas al momento de aprobar el préstamo.
                </p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- Escala de mora -->
            <div class="col-lg-7">
                <h5 class="fw-bold mb-3" style="color:var(--ce-navy);">Escala de mora por rango de días</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center rounded-3 overflow-hidden">
                        <thead style="background:var(--ce-navy);color:#fff;">
                            <tr>
                                <th class="py-3">Rango de días</th>
                                <th class="py-3">Tasa de mora</th>
                                <th class="py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mora_rangos as $r): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($r['dias']) ?></td>
                                <td class="fw-bold">
                                    <?php if ($r['clase'] === 'success'): ?>
                                        <span class="text-success"><?= htmlspecialchars($r['tasa']) ?></span>
                                    <?php elseif ($r['clase'] === 'orange'): ?>
                                        <span style="color:#fd7e14;"><?= htmlspecialchars($r['tasa']) ?></span>
                                    <?php else: ?>
                                        <span class="text-<?= htmlspecialchars($r['clase']) ?>"><?= htmlspecialchars($r['tasa']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $badge_class = match($r['clase']) {
                                        'success' => 'bg-success',
                                        'warning' => 'bg-warning text-dark',
                                        'orange'  => 'bg-warning text-dark',
                                        'danger'  => 'bg-danger',
                                        default   => 'bg-dark',
                                    };
                                    ?>
                                    <span class="badge <?= $badge_class ?>">
                                        <i class="bi <?= htmlspecialchars($r['icono']) ?> me-1"></i>
                                        <?= htmlspecialchars($r['dias']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small mt-2">
                    <i class="bi bi-info-circle me-1"></i>
                    Cuando el atraso entra a un nuevo rango, el porcentaje vigente <strong>reemplaza</strong>
                    al anterior para el nuevo cálculo.
                </p>
            </div>

            <!-- Fórmula y ejemplo -->
            <div class="col-lg-5 d-flex flex-column gap-4">
                <!-- Fórmula -->
                <div>
                    <h5 class="fw-bold mb-3" style="color:var(--ce-navy);">Fórmula de cálculo</h5>
                    <div class="formula-box">
                        Mora = <span>Saldo pendiente</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;× <span>Porcentaje</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;× <span>Semanas de atraso</span>
                    </div>
                </div>

                <!-- Ejemplo -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h6 class="fw-bold mb-3" style="color:var(--ce-navy);">
                        <i class="bi bi-calculator me-1" style="color:var(--ce-gold);"></i> Ejemplo práctico
                    </h6>
                    <ul class="list-unstyled mb-3 small">
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Saldo pendiente</span>
                            <strong>$300.000</strong>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Semanas de atraso</span>
                            <strong>2</strong>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Tasa aplicada</span>
                            <strong class="text-warning">5%</strong>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Mora generada</span>
                            <strong class="text-danger">$30.000</strong>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="fw-bold">Total a pagar</span>
                            <strong class="fs-5" style="color:var(--ce-navy);">$330.000</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Aplicación de pagos + Recibo -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-3" style="color:var(--ce-navy);">
                        <i class="bi bi-sort-numeric-down me-2" style="color:var(--ce-gold);"></i>
                        Orden de aplicación de pagos
                    </h5>
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item border-0 d-flex align-items-center gap-2 px-0">
                            <i class="bi bi-exclamation-circle text-danger"></i> Mora pendiente
                        </li>
                        <li class="list-group-item border-0 d-flex align-items-center gap-2 px-0">
                            <i class="bi bi-cash-coin" style="color:var(--ce-navy);"></i> Reducción del capital pendiente
                        </li>
                    </ol>
                    <p class="text-muted small mt-3 mb-0">
                        Esto permite mantener un control claro del saldo en todo momento.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-3" style="color:var(--ce-navy);">
                        <i class="bi bi-receipt me-2" style="color:var(--ce-gold);"></i>
                        Cada recibo incluye
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($recibo_items as $item): ?>
                        <li class="d-flex align-items-center gap-2 py-1 border-bottom">
                            <i class="bi bi-check-circle-fill text-success small"></i>
                            <span class="small"><?= htmlspecialchars($item) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>


<!----compromiso---->
<section id="compromiso" style="padding-top:5rem;padding-bottom:5rem;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="section-divider"></div>
                <h2 class="section-title display-6 mb-4">Nuestro compromiso</h2>
                <p style="color:rgba(255,255,255,.8);" class="fs-5 mb-4">
                    Queremos construir relaciones basadas en la confianza mutua. Cumplir permite mantener
                    el préstamo bajo las condiciones iniciales.
                </p>
                <a href="#contacto" class="btn btn-gold btn-lg px-4">
                    <i class="bi bi-whatsapp me-2"></i> Hablar con nosotros
                </a>
            </div>
            <div class="col-lg-7">
                <div class="card border-0 rounded-4 p-4" style="background:rgba(255,255,255,.08);">
                    <?php
                    $compromisos = [
                        ['icono'=>'bi-lightbulb',     'titulo'=>'Claridad',          'texto'=>'Condiciones claras desde el primer día, sin letra pequeña.'],
                        ['icono'=>'bi-list-check',    'titulo'=>'Organización',      'texto'=>'Gestión ordenada con registro de cada pago y movimiento.'],
                        ['icono'=>'bi-check-circle',  'titulo'=>'Cumplimiento',      'texto'=>'Respetamos lo pactado; lo mismo esperamos del cliente.'],
                        ['icono'=>'bi-eye',           'titulo'=>'Transparencia',     'texto'=>'Recibos detallados para que siempre sepas en qué punto estás.'],
                        ['icono'=>'bi-cash-coin',     'titulo'=>'Facilidad de pago', 'texto'=>'Modalidades adaptadas a la realidad de cada cliente.'],
                    ];
                    foreach ($compromisos as $c):
                    ?>
                    <div class="compromiso-item">
                        <i class="bi <?= htmlspecialchars($c['icono']) ?>"></i>
                        <div>
                            <strong style="color:#fff;"><?= htmlspecialchars($c['titulo']) ?></strong>
                            <p class="mb-0 small" style="color:rgba(255,255,255,.7);"><?= htmlspecialchars($c['texto']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>


<!--contacto-->
<section id="contacto" style="padding-top:5rem;padding-bottom:5rem;">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <div class="section-divider mx-auto"></div>
                <h2 class="section-title display-6 mb-3">Contáctanos</h2>
                <p class="text-muted fs-5">
                    ¿Listo para solicitar tu préstamo? Comunícate con nosotros por cualquiera de estos medios.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Info de contacto -->
            <div class="col-lg-4">
                <div class="contact-info-card">
                    <h5 class="text-white fw-bold mb-4">Información de contacto</h5>

                    <div class="ci-item">
                        <div class="ci-icon"><i class="bi bi-whatsapp"></i></div>
                        <div>
                            <div class="ci-label">WhatsApp</div>
                            <a href="https://wa.me/<?= htmlspecialchars($empresa['whatsapp']) ?>"
                               class="ci-value text-white text-decoration-none" target="_blank" rel="noopener">
                                <?= htmlspecialchars($empresa['whatsapp']) ?>
                            </a>
                        </div>
                    </div>

                    <div class="ci-item">
                        <div class="ci-icon"><i class="bi bi-envelope"></i></div>
                        <div>
                            <div class="ci-label">Correo electrónico</div>
                            <a href="mailto:<?= htmlspecialchars($empresa['correo']) ?>"
                               class="ci-value text-white text-decoration-none">
                                <?= htmlspecialchars($empresa['correo']) ?>
                            </a>
                        </div>
                    </div>

                    <div class="ci-item">
                        <div class="ci-icon"><i class="bi bi-clock"></i></div>
                        <div>
                            <div class="ci-label">Horario de atención</div>
                            <div class="ci-value"><?= htmlspecialchars($empresa['horario']) ?></div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="https://wa.me/<?= htmlspecialchars($empresa['whatsapp']) ?>"
                           class="btn w-100 btn-gold fw-bold" target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-2"></i> Escribir por WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="col-lg-8">
                <div class="contact-form-card">
                    <h5 class="fw-bold mb-4" style="color:var(--ce-navy);">Envíanos un mensaje</h5>

                    <?php
                    $enviado = false;
                    $error   = '';

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {

                        $nombre   = trim(htmlspecialchars($_POST['nombre'] ?? ''));
                        $correo   = trim($_POST['correo'] ?? '');
                        $telefono = trim(htmlspecialchars($_POST['telefono'] ?? ''));
                        $asunto   = trim(htmlspecialchars($_POST['asunto'] ?? ''));
                        $mensaje  = trim(htmlspecialchars($_POST['mensaje'] ?? ''));

                        if (
                            $nombre &&
                            $correo &&
                            filter_var($correo, FILTER_VALIDATE_EMAIL) &&
                            $telefono &&
                            $asunto &&
                            $mensaje
                        ) {

                            try {

                                $mail = new PHPMailer(true);

                                // Configuración SMTP
                                $mail->isSMTP();
                                $mail->Host       = 'smtp.gmail.com';
                                $mail->SMTPAuth   = true;
                                $mail->Username   = 'capitalexpressb@gmail.com';
                                $mail->Password = 'tpqa vxtu bmmb mzza';
                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                                $mail->Port       = 587;

                                // Remitente y destinatario
                                $mail->setFrom('capitalexpressb@gmail.com', 'Capital Express');
                                $mail->addAddress('capitalexpressb@gmail.com');
                                $mail->addReplyTo($correo, $nombre);

                                // Contenido
                                $mail->isHTML(true);
                                $mail->CharSet = 'UTF-8';

                                $mail->Subject = "Nuevo mensaje desde la página web - $asunto";

                                $mail->Body = "
                                <h2>📩 Nuevo mensaje de contacto</h2>

                                <table style='border-collapse:collapse;' cellpadding='8'>
                                    <tr>
                                        <td><strong>👤 Nombre:</strong></td>
                                        <td>{$nombre}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>📧 Correo:</strong></td>
                                        <td>{$correo}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>📞 Teléfono:</strong></td>
                                        <td>{$telefono}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>📌 Asunto:</strong></td>
                                        <td>{$asunto}</td>
                                    </tr>

                                    <tr>
                                        <td valign='top'><strong>💬 Mensaje:</strong></td>
                                        <td>" . nl2br($mensaje) . "</td>
                                    </tr>
                                </table>

                                <hr>

                                <p><strong>Capital Express</strong></p>
                                <p>Este mensaje fue enviado desde el formulario de contacto de la página web.</p>
                                ";

                                $mail->send();

                                $enviado = true;

                            } catch (Exception $e) {

                                $error = "No se pudo enviar el mensaje. Error: " . $mail->ErrorInfo;

                            }

                        } else {

                            $error = 'Por favor completa todos los campos.';

                        }
                    }
                    ?>

                    <?php if ($enviado): ?>
                        <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>¡Mensaje enviado! Te responderemos a la brevedad.</span>
                        </div>
                    <?php else: ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger small"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="#contacto" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label fw-semibold small">Nombre completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                       placeholder="Tu nombre" required
                                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="correo" class="form-label fw-semibold small">
                                    Correo electrónico <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="email"
                                    class="form-control"
                                    id="correo"
                                    name="correo"
                                    placeholder="ejemplo@correo.com"
                                    required
                                    value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label fw-semibold small">Teléfono / WhatsApp <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="telefono" name="telefono"
                                       placeholder="300 000 0000" required
                                       value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="asunto" class="form-label fw-semibold small">Asunto <span class="text-danger">*</span></label>
                                <select class="form-select" id="asunto" name="asunto" required>
                                    <option value="" disabled <?= empty($_POST['asunto']) ? 'selected' : '' ?>>Selecciona un asunto...</option>
                                    <option value="Solicitar préstamo"     <?= ($_POST['asunto'] ?? '') === 'Solicitar préstamo'     ? 'selected' : '' ?>>Solicitar préstamo</option>
                                    <option value="Consulta de condiciones"<?= ($_POST['asunto'] ?? '') === 'Consulta de condiciones'? 'selected' : '' ?>>Consulta de condiciones</option>
                                    <option value="Información de mora"    <?= ($_POST['asunto'] ?? '') === 'Información de mora'    ? 'selected' : '' ?>>Información de mora</option>
                                    <option value="Otro"                   <?= ($_POST['asunto'] ?? '') === 'Otro'                   ? 'selected' : '' ?>>Otro</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="mensaje" class="form-label fw-semibold small">Mensaje <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="4"
                                          placeholder="Escribe tu mensaje aquí..." required><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-ce-primary w-100 btn-lg">
                                    <i class="bi bi-send me-2"></i> Enviar mensaje
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- footer -->
<footer class="py-5">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="footer-brand mb-2">
                    <i class="bi bi-bank2 me-2"></i><?= htmlspecialchars($empresa['nombre']) ?>
                </div>
                <p class="small mb-3"><?= htmlspecialchars($empresa['slogan']) ?></p>
                <a href="https://wa.me/<?= htmlspecialchars($empresa['whatsapp']) ?>"
                   class="btn btn-gold btn-sm" target="_blank" rel="noopener">
                    <i class="bi bi-whatsapp me-1"></i> WhatsApp
                </a>
            </div>
            <div class="col-sm-6 col-lg-4">
                <h6 class="text-white fw-bold mb-3">Navegación</h6>
                <ul class="list-unstyled small">
                    <li class="mb-1"><a href="#quienes">¿Quiénes somos?</a></li>
                    <li class="mb-1"><a href="#prestamos">Cómo prestamos</a></li>
                    <li class="mb-1"><a href="#mora">Política de mora</a></li>
                    <li class="mb-1"><a href="#compromiso">Compromiso</a></li>
                    <li class="mb-1"><a href="#contacto">Contacto</a></li>
                </ul>
            </div>
            <div class="col-sm-6 col-lg-4">
                <h6 class="text-white fw-bold mb-3">Contacto directo</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <i class="bi bi-whatsapp me-2" style="color:var(--ce-gold);"></i>
                        <a href="https://wa.me/<?= htmlspecialchars($empresa['whatsapp']) ?>">
                            <?= htmlspecialchars($empresa['whatsapp']) ?>
                        </a>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope me-2" style="color:var(--ce-gold);"></i>
                        <a href="mailto:<?= htmlspecialchars($empresa['correo']) ?>">
                            <?= htmlspecialchars($empresa['correo']) ?>
                        </a>
                    </li>
                    <li>
                        <i class="bi bi-clock me-2" style="color:var(--ce-gold);"></i>
                        Lun – Vie: 8:00 am – 6:00 pm
                    </li>
                </ul>
            </div>
        </div>
        <hr class="divider">
        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center small">
            <span>&copy; <?= date('Y') ?> <?= htmlspecialchars($empresa['nombre']) ?>. Todos los derechos reservados.</span>
        </div>
    </div>
</footer>

<!-- Back to top -->
<button id="backToTop" title="Volver arriba" aria-label="Volver arriba">
    <i class="bi bi-chevron-up"></i>
</button>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/pagina_informativa.js"></script>

</body> 
</html>
