<?php
/**
 * VIOLETA — Cabecera del panel administrativo
 * @param string $paginaAdmin  slug para marcar el ítem activo
 * @param string $tituloPagAdmin  título de la página
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VIOLETA Admin · <?= htmlspecialchars($tituloPagAdmin ?? 'Panel') ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/estilos.css">
</head>
<body style="padding:0;">
<div class="admin-layout">

  <!-- ══ Sidebar ══════════════════════════════════════════════ -->
  <aside class="admin-sidebar" role="navigation" aria-label="Panel de administración">
    <div class="logo-admin">
      <span>VIOLETA</span>
      <small>Panel administrativo</small>
    </div>

    <nav>
      <a href="<?= BASE_URL ?>/admin/dashboard.php"
         class="admin-nav-item <?= ($paginaAdmin??'')==='dashboard' ? 'activo' : '' ?>">
        <i class="bi bi-speedometer2" aria-hidden="true"></i> Dashboard
      </a>
      <a href="<?= BASE_URL ?>/admin/preguntas.php"
         class="admin-nav-item <?= ($paginaAdmin??'')==='preguntas' ? 'activo' : '' ?>">
        <i class="bi bi-patch-question" aria-hidden="true"></i> Preguntas
      </a>
      <a href="<?= BASE_URL ?>/admin/categorias.php"
         class="admin-nav-item <?= ($paginaAdmin??'')==='categorias' ? 'activo' : '' ?>">
        <i class="bi bi-grid" aria-hidden="true"></i> Categorías
      </a>
      <a href="<?= BASE_URL ?>/admin/recursos.php"
         class="admin-nav-item <?= ($paginaAdmin??'')==='recursos' ? 'activo' : '' ?>">
        <i class="bi bi-telephone" aria-hidden="true"></i> Recursos
      </a>
      <a href="<?= BASE_URL ?>/admin/resultados.php"
         class="admin-nav-item <?= ($paginaAdmin??'')==='resultados' ? 'activo' : '' ?>">
        <i class="bi bi-graph-up" aria-hidden="true"></i> Resultados
      </a>
      <div class="admin-nav-sep"></div>
      <a href="<?= BASE_URL ?>/index.php" class="admin-nav-item" target="_blank">
        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Ver sitio
      </a>
      <a href="<?= BASE_URL ?>/admin/logout.php" class="admin-nav-item" style="color:rgba(255,100,100,.8);">
        <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Cerrar sesión
      </a>
    </nav>

    <div style="position:absolute;bottom:1.5rem;left:0;right:0;padding:0 1.5rem;">
      <div style="background:rgba(255,255,255,.05);border-radius:var(--r-md);padding:.75rem;">
        <p style="font-size:.75rem;color:rgba(255,255,255,.4);margin:0;">
          <i class="bi bi-person-circle" aria-hidden="true"></i>
          <?= e($adminNombre) ?>
        </p>
      </div>
    </div>
  </aside>

  <!-- ══ Contenido principal ══════════════════════════════════ -->
  <div class="admin-main">
