<?php
/**
 * VIOLETA — Cabecera compartida de páginas públicas
 * @param string $paginaActual  slug de la página actual para marcar nav
 * @param string $tituloPag    título adicional de la página
 */
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/configuracion.php';
}
$paginaActual  = $paginaActual  ?? '';
$tituloPag     = $tituloPag     ?? '';
$titulo        = $tituloPag ? "VIOLETA · $tituloPag" : 'VIOLETA — Informar, reconocer y actuar';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo) ?></title>
  <meta name="description" content="VIOLETA es una plataforma educativa sobre prevención de la violencia contra la mujer. Infórmate, reconoce las señales y pon a prueba tus conocimientos.">
  <meta name="author" content="Instituto Técnico Nacional de Comercio Federico Álvarez Plata">

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Estilos VIOLETA -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/estilos.css">

  <!-- Favicon SVG inline -->
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Ccircle cx='16' cy='16' r='16' fill='%235636C9'/%3E%3Ctext x='16' y='21' text-anchor='middle' fill='white' font-size='16' font-family='serif'%3E♀%3C/text%3E%3C/svg%3E">
</head>
<body>

<!-- ═══════════════════════════════ NAVBAR ═══════════════════════════════ -->
<nav class="navbar-violeta" role="navigation" aria-label="Navegación principal">
  <div class="navbar-inner">

    <!-- Logo -->
    <a href="<?= BASE_URL ?>/index.php" class="navbar-logo" aria-label="VIOLETA - Inicio">
      <svg width="38" height="38" viewBox="0 0 38 38" aria-hidden="true">
        <circle cx="19" cy="19" r="19" fill="#5636C9"/>
        <circle cx="19" cy="14" r="5" fill="#E9DDFD"/>
        <ellipse cx="13" cy="24" rx="4" ry="3" fill="#D94FC1" opacity=".8"/>
        <ellipse cx="25" cy="24" rx="4" ry="3" fill="#D94FC1" opacity=".8"/>
        <circle cx="19" cy="19" r="3" fill="white"/>
        <line x1="19" y1="22" x2="19" y2="30" stroke="white" stroke-width="2" stroke-linecap="round"/>
        <line x1="15" y1="27" x2="23" y2="27" stroke="white" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <div class="navbar-logo-texto">
        <span class="navbar-logo-nombre">VIOLETA</span>
        <span class="navbar-logo-eslogan">Informar · Reconocer · Actuar</span>
      </div>
    </a>

    <!-- Menú principal (en fila horizontal) -->
    <ul class="navbar-nav d-flex flex-row align-items-center gap-1 m-0 p-0" role="list">
      <li>
        <a href="<?= BASE_URL ?>/index.php"
           class="<?= $paginaActual === 'inicio' ? 'activo' : '' ?>"
           <?= $paginaActual === 'inicio' ? 'aria-current="page"' : '' ?>>
          <i class="bi bi-house" aria-hidden="true"></i> Inicio
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/informacion.php"
           class="<?= $paginaActual === 'informacion' ? 'activo' : '' ?>"
           <?= $paginaActual === 'informacion' ? 'aria-current="page"' : '' ?>>
          <i class="bi bi-book" aria-hidden="true"></i> Información
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/quiz.php"
           class="<?= $paginaActual === 'quiz' ? 'activo' : '' ?>"
           <?= $paginaActual === 'quiz' ? 'aria-current="page"' : '' ?>>
          <i class="bi bi-patch-question" aria-hidden="true"></i> Quiz
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/desarrolladores.php"
           class="<?= $paginaActual === 'desarrolladores' ? 'activo' : '' ?>"
           <?= $paginaActual === 'desarrolladores' ? 'aria-current="page"' : '' ?>>
          <i class="bi bi-person-badge" aria-hidden="true"></i> Desarrollador
        </a>
      </li>
    </ul>

    <!-- Salida rápida -->
    <a href="<?= BASE_URL ?>/salida.php"
       id="btn-salida-rapida"
       class="btn-salida-rapida"
       title="Salida rápida: sale del sitio de inmediato"
       aria-label="Salida rápida del sitio">
      <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Salida rápida
    </a>

  </div>
</nav>
<!-- ══════════════════════════════════════════════════════════════════════ -->
