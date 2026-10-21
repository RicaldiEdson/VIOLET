<?php
/**
 * VIOLETA — Reporte y Auditoría de Intentos del Quiz (admin/resultados.php)
 */
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

$paginaAdmin    = 'resultados';
$tituloPagAdmin = 'Resultados del Quiz';
$pdo            = obtenerConexion();

// Métricas globales
$statsIntentos = $pdo->query(
    'SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN completado = 1 THEN 1 ELSE 0 END) AS completados,
        SUM(CASE WHEN completado = 0 THEN 1 ELSE 0 END) AS abandonados,
        COALESCE(AVG(CASE WHEN completado = 1 THEN puntuacion_porcentaje END), 0) AS promedio,
        SUM(CASE WHEN completado = 1 AND puntuacion_porcentaje >= 70 THEN 1 ELSE 0 END) AS aprobados
     FROM intentos'
)->fetch();

// Paginación y listado de intentos
$paginaActual = max(1, (int)($_GET['p'] ?? 1));
$porPagina    = 15;
$offset       = ($paginaActual - 1) * $porPagina;

$totalRegistros = (int)$pdo->query('SELECT COUNT(*) FROM intentos')->fetchColumn();
$totalPaginas   = ceil($totalRegistros / $porPagina);

$stmt = $pdo->prepare(
    'SELECT * FROM intentos
      ORDER BY fecha_inicio DESC
      LIMIT :limite OFFSET :offset'
);
$stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$intentos = $stmt->fetchAll();

// Si se solicita ver el detalle de un intento
$intentoDetalle = null;
$revisionDetalle = [];
if (isset($_GET['ver'])) {
    $idVer = (int)$_GET['ver'];
    $intentoDetalle = obtenerIntentoPorId($idVer);
    if ($intentoDetalle) {
        $revisionDetalle = obtenerRevisionIntento($idVer);
    }
}

require_once __DIR__ . '/includes/header_admin.php';
?>

<div class="admin-header-bar">
  <div>
    <h1>Métricas e Historial del Quiz</h1>
    <p class="t-muted fs-sm">Supervisión didáctica de participación y efectividad formativa de los usuarios.</p>
  </div>
</div>

<!-- Métricas Resumen -->
<div class="grid-4 gap-3 mb-4">
  <div class="admin-stat-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <span class="label">Total Intentos</span>
      <div class="icono-circulo ic-violeta" style="width:36px;height:36px;font-size:.9rem;">
        <i class="bi bi-person-lines-fill"></i>
      </div>
    </div>
    <span class="num"><?= (int)$statsIntentos['total'] ?></span>
  </div>

  <div class="admin-stat-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <span class="label">Completados</span>
      <div class="icono-circulo ic-verde" style="width:36px;height:36px;font-size:.9rem;">
        <i class="bi bi-check-all"></i>
      </div>
    </div>
    <span class="num"><?= (int)$statsIntentos['completados'] ?></span>
    <span class="fs-xs text-muted"><?= (int)$statsIntentos['abandonados'] ?> sin finalizar</span>
  </div>

  <div class="admin-stat-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <span class="label">Promedio de Acierto</span>
      <div class="icono-circulo ic-ambar" style="width:36px;height:36px;font-size:.9rem;">
        <i class="bi bi-graph-up-arrow"></i>
      </div>
    </div>
    <span class="num"><?= round((float)$statsIntentos['promedio'], 1) ?>%</span>
  </div>

  <div class="admin-stat-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <span class="label">Dominio Óptimo (≥70%)</span>
      <div class="icono-circulo ic-magenta" style="width:36px;height:36px;font-size:.9rem;">
        <i class="bi bi-award"></i>
      </div>
    </div>
    <span class="num"><?= (int)$statsIntentos['aprobados'] ?></span>
    <span class="fs-xs text-muted">
      <?= $statsIntentos['completados'] > 0 ? round(((int)$statsIntentos['aprobados'] / (int)$statsIntentos['completados']) * 100) : 0 ?>% de los completados
    </span>
  </div>
</div>

<!-- Modal / Panel de Detalle si se seleccionó uno -->
<?php if ($intentoDetalle): ?>
  <div class="tarjeta p-4 mb-4 border-primary">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4 class="fw-bold mb-0 text-primary">
          <i class="bi bi-clipboard2-data me-2"></i>Detalle del Intento #<?= $intentoDetalle['id_intento'] ?>
        </h4>
        <span class="fs-xs text-muted">
          Sesión anónima: <code><?= substr($intentoDetalle['codigo_sesion'], 0, 12) ?>...</code> · Fecha: <?= date('d/m/Y H:i', strtotime($intentoDetalle['fecha_inicio'])) ?>
        </span>
      </div>
      <a href="resultados.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-x-lg me-1"></i> Cerrar detalle
      </a>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="p-3 bg-light rounded text-center">
          <div class="fs-xs text-muted">Resultado</div>
          <div class="fs-4 fw-bold <?= $intentoDetalle['puntuacion_porcentaje'] >= 70 ? 'text-success' : 'text-danger' ?>">
            <?= round($intentoDetalle['puntuacion_porcentaje']) ?>%
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3 bg-light rounded text-center">
          <div class="fs-xs text-muted">Preguntas Totales</div>
          <div class="fs-4 fw-bold text-dark"><?= $intentoDetalle['total_preguntas'] ?></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3 bg-light rounded text-center">
          <div class="fs-xs text-muted">Aciertos</div>
          <div class="fs-4 fw-bold text-success"><?= $intentoDetalle['respuestas_correctas'] ?></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3 bg-light rounded text-center">
          <div class="fs-xs text-muted">Incorrectas</div>
          <div class="fs-4 fw-bold text-danger"><?= $intentoDetalle['respuestas_incorrectas'] ?></div>
        </div>
      </div>
    </div>

    <h6 class="fw-bold text-secondary mb-3">Preguntas evaluadas en este intento:</h6>
    <?php if (empty($revisionDetalle)): ?>
      <p class="text-muted fs-sm">El usuario no alcanzó a registrar respuestas en este intento.</p>
    <?php else: ?>
      <div class="d-flex flex-column gap-3">
        <?php foreach ($revisionDetalle as $idx => $rev): ?>
          <div class="p-3 rounded border <?= $rev['era_correcta'] ? 'border-success bg-success-subtle bg-opacity-10' : 'border-danger bg-danger-subtle bg-opacity-10' ?>">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="fw-bold fs-sm">
                <?= ($idx + 1) ?>. <?= e($rev['enunciado']) ?>
              </span>
              <span class="badge <?= $rev['era_correcta'] ? 'bg-success' : 'bg-danger' ?> ms-2">
                <?= $rev['era_correcta'] ? 'Correcta' : 'Incorrecta' ?>
              </span>
            </div>
            <div class="fs-xs mb-1">
              <strong>Respuesta del usuario:</strong> <?= e($rev['texto_seleccionada']) ?>
            </div>
            <?php if (!$rev['era_correcta']): ?>
              <div class="fs-xs text-success mb-1">
                <strong>Respuesta correcta:</strong> <?= e($rev['texto_correcta']) ?>
              </div>
            <?php endif; ?>
            <?php if (!empty($rev['explicacion'])): ?>
              <div class="fs-xs text-muted mt-2 border-top pt-2">
                <i class="bi bi-info-circle me-1"></i><strong>Explicación:</strong> <?= e($rev['explicacion']) ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>

<!-- Tabla de Intentos -->
<div class="tarjeta p-0 mb-4 overflow-hidden">
  <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
    <span class="fw-bold fs-sm text-secondary">
      Historial de Evaluaciones
    </span>
    <span class="fs-xs text-muted">
      Mostrando página <?= $paginaActual ?> de <?= max(1, $totalPaginas) ?>
    </span>
  </div>

  <?php if (empty($intentos)): ?>
    <div class="text-center py-5">
      <i class="bi bi-clipboard-x text-muted" style="font-size:3rem;"></i>
      <h5 class="mt-3 text-secondary">Aún no hay intentos registrados</h5>
      <p class="text-muted fs-sm">Los intentos de los usuarios en el Quiz aparecerán aquí automáticamente.</p>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light fs-xs text-uppercase text-muted">
          <tr>
            <th style="width: 70px;">ID</th>
            <th>Sesión</th>
            <th style="width: 140px;">Fecha</th>
            <th style="width: 100px;" class="text-center">Preguntas</th>
            <th style="width: 130px;" class="text-center">Aciertos</th>
            <th style="width: 110px;" class="text-center">Porcentaje</th>
            <th style="width: 110px;">Estado</th>
            <th style="width: 90px;" class="text-end">Detalle</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($intentos as $it): ?>
            <tr>
              <td class="fw-bold text-secondary">#<?= $it['id_intento'] ?></td>
              <td>
                <span class="font-monospace fs-xs text-muted" title="<?= e($it['codigo_sesion']) ?>">
                  <?= substr($it['codigo_sesion'], 0, 10) ?>...
                </span>
              </td>
              <td class="fs-xs text-muted">
                <?= date('d/m/Y H:i', strtotime($it['fecha_inicio'])) ?>
              </td>
              <td class="text-center fw-semibold">
                <?= $it['total_preguntas'] ?>
              </td>
              <td class="text-center fs-sm">
                <span class="text-success fw-bold"><?= $it['respuestas_correctas'] ?></span> /
                <span class="text-danger"><?= $it['respuestas_incorrectas'] ?></span>
              </td>
              <td class="text-center">
                <?php if ($it['completado']): ?>
                  <?php
                    $pct = (float)$it['puntuacion_porcentaje'];
                    $colorBadge = $pct >= 80 ? 'bg-success' : ($pct >= 60 ? 'bg-warning text-dark' : 'bg-danger');
                  ?>
                  <span class="badge <?= $colorBadge ?>">
                    <?= round($pct) ?>%
                  </span>
                <?php else: ?>
                  <span class="badge bg-secondary">Incompleto</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($it['completado']): ?>
                  <span class="badge bg-success-subtle text-success border border-success-subtle">
                    <i class="bi bi-check-circle me-1"></i>Finalizado
                  </span>
                <?php else: ?>
                  <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                    <i class="bi bi-hourglass-split me-1"></i>En curso
                  </span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <a href="resultados.php?ver=<?= $it['id_intento'] ?>" class="btn btn-sm btn-outline-primary" title="Ver preguntas y respuestas">
                  <i class="bi bi-eye"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <?php if ($totalPaginas > 1): ?>
      <div class="p-3 border-top d-flex justify-content-center">
        <nav aria-label="Paginación de resultados">
          <ul class="pagination pagination-sm mb-0">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
              <li class="page-item <?= $i === $paginaActual ? 'active' : '' ?>">
                <a class="page-link" href="resultados.php?p=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer_admin.php'; ?>
