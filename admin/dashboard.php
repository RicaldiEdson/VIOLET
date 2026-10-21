<?php
/**
 * VIOLETA — Dashboard administrativo (admin/dashboard.php)
 */
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

$paginaAdmin   = 'dashboard';
$tituloPagAdmin= 'Dashboard';
$stats         = obtenerEstadisticasGenerales();

// Últimos 5 intentos
$pdo  = obtenerConexion();
$ultimos = $pdo->query(
    'SELECT id_intento, puntuacion_porcentaje, total_preguntas,
            respuestas_correctas, fecha_inicio, fecha_fin
       FROM intentos
      WHERE completado = 1
      ORDER BY fecha_fin DESC
      LIMIT 5'
)->fetchAll();

require_once __DIR__ . '/includes/header_admin.php';
?>

<div class="admin-header-bar">
  <div>
    <h1>Dashboard</h1>
    <p class="t-muted fs-sm">Resumen del sistema VIOLETA · <?= date('d/m/Y H:i') ?></p>
  </div>
  <a href="<?= BASE_URL ?>/admin/preguntas.php?accion=nueva"
     class="btn btn-primario btn-sm">
    <i class="bi bi-plus-circle" aria-hidden="true"></i> Nueva pregunta
  </a>
</div>

<!-- Estadísticas -->
<div class="grid-4 gap-3 mb-4">
  <div class="admin-stat-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <span class="label">Preguntas activas</span>
      <div class="icono-circulo ic-violeta" style="width:40px;height:40px;font-size:1rem;">
        <i class="bi bi-patch-question" aria-hidden="true"></i>
      </div>
    </div>
    <span class="num"><?= $stats['total_preguntas'] ?></span>
  </div>
  <div class="admin-stat-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <span class="label">Categorías</span>
      <div class="icono-circulo ic-magenta" style="width:40px;height:40px;font-size:1rem;">
        <i class="bi bi-grid" aria-hidden="true"></i>
      </div>
    </div>
    <span class="num"><?= $stats['total_categorias'] ?></span>
  </div>
  <div class="admin-stat-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <span class="label">Intentos completados</span>
      <div class="icono-circulo ic-verde" style="width:40px;height:40px;font-size:1rem;">
        <i class="bi bi-clipboard-check" aria-hidden="true"></i>
      </div>
    </div>
    <span class="num"><?= $stats['total_intentos'] ?></span>
  </div>
  <div class="admin-stat-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <span class="label">Promedio de aciertos</span>
      <div class="icono-circulo ic-ambar" style="width:40px;height:40px;font-size:1rem;">
        <i class="bi bi-graph-up" aria-hidden="true"></i>
      </div>
    </div>
    <span class="num"><?= $stats['total_intentos'] > 0 ? round($stats['promedio_puntaje']) . '%' : '—' ?></span>
  </div>
</div>

<!-- Accesos rápidos -->
<div class="grid-3 gap-3 mb-4">
  <a href="preguntas.php" class="tarjeta" style="padding:1.5rem;text-decoration:none;color:inherit;">
    <div style="display:flex;align-items:center;gap:1rem;">
      <div class="icono-circulo ic-violeta"><i class="bi bi-patch-question" aria-hidden="true"></i></div>
      <div>
        <h5 style="margin:0;font-size:1rem;">Gestionar preguntas</h5>
        <p class="fs-xs t-muted mb-0">Crear, editar y activar preguntas del quiz.</p>
      </div>
    </div>
  </a>
  <a href="categorias.php" class="tarjeta" style="padding:1.5rem;text-decoration:none;color:inherit;">
    <div style="display:flex;align-items:center;gap:1rem;">
      <div class="icono-circulo ic-magenta"><i class="bi bi-grid" aria-hidden="true"></i></div>
      <div>
        <h5 style="margin:0;font-size:1rem;">Gestionar categorías</h5>
        <p class="fs-xs t-muted mb-0">Administrar las categorías del contenido.</p>
      </div>
    </div>
  </a>
  <a href="resultados.php" class="tarjeta" style="padding:1.5rem;text-decoration:none;color:inherit;">
    <div style="display:flex;align-items:center;gap:1rem;">
      <div class="icono-circulo ic-verde"><i class="bi bi-graph-up" aria-hidden="true"></i></div>
      <div>
        <h5 style="margin:0;font-size:1rem;">Ver resultados</h5>
        <p class="fs-xs t-muted mb-0">Historial de intentos y estadísticas.</p>
      </div>
    </div>
  </a>
</div>

<!-- Últimos intentos -->
<div class="tarjeta">
  <div class="tarjeta-cabecera flex-between">
    <span>Últimos intentos completados</span>
    <a href="resultados.php" class="btn btn-contorno btn-sm">Ver todos</a>
  </div>
  <div style="padding:0;">
    <?php if (empty($ultimos)): ?>
    <div class="estado-vacio">
      <div class="icono-grande"><i class="bi bi-clipboard-x" aria-hidden="true"></i></div>
      <p>Aún no hay intentos completados.</p>
    </div>
    <?php else: ?>
    <table class="tabla-violeta">
      <thead>
        <tr>
          <th>ID</th>
          <th>Puntuación</th>
          <th>Correctas / Total</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ultimos as $u): ?>
        <tr>
          <td><span class="badge badge-violeta">#<?= $u['id_intento'] ?></span></td>
          <td>
            <span style="font-weight:700;color:<?= $u['puntuacion_porcentaje'] >= 70 ? 'var(--verde)' : 'var(--rojo)' ?>">
              <?= round($u['puntuacion_porcentaje']) ?>%
            </span>
          </td>
          <td><?= $u['respuestas_correctas'] ?> / <?= $u['total_preguntas'] ?></td>
          <td class="fs-xs t-muted"><?= date('d/m/Y H:i', strtotime($u['fecha_fin'] ?? $u['fecha_inicio'])) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer_admin.php'; ?>
