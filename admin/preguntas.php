<?php
/**
 * VIOLETA — Listado y gestión de preguntas del quiz (admin/preguntas.php)
 */
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

$paginaAdmin    = 'preguntas';
$tituloPagAdmin = 'Gestión de Preguntas';
$pdo            = obtenerConexion();

$mensaje = '';
$tipoMensaje = 'success';

// Procesar acciones POST (Cambiar estado o Eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarTokenCSRF()) {
        $mensaje = 'Token de seguridad inválido. Operación cancelada.';
        $tipoMensaje = 'danger';
    } else {
        $accion = $_POST['accion'] ?? '';
        $idPregunta = (int)($_POST['id_pregunta'] ?? 0);

        if ($accion === 'toggle_estado' && $idPregunta > 0) {
            $nuevoEstado = (int)($_POST['nuevo_estado'] ?? 1);
            $stmt = $pdo->prepare('UPDATE preguntas SET estado = :estado WHERE id_pregunta = :id');
            $stmt->execute([':estado' => $nuevoEstado, ':id' => $idPregunta]);
            $mensaje = $nuevoEstado ? 'Pregunta activada correctamente.' : 'Pregunta desactivada.';
        } elseif ($accion === 'eliminar' && $idPregunta > 0) {
            try {
                // Verificar si tiene respuestas asociadas
                $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM respuestas_usuario WHERE id_pregunta = :id');
                $stmtCheck->execute([':id' => $idPregunta]);
                $usadaEnIntentos = (int)$stmtCheck->fetchColumn();

                if ($usadaEnIntentos > 0) {
                    // Si ya fue respondida en intentos previos, recomendamos desactivarla en lugar de borrarla
                    $stmt = $pdo->prepare('UPDATE preguntas SET estado = 0 WHERE id_pregunta = :id');
                    $stmt->execute([':id' => $idPregunta]);
                    $mensaje = 'La pregunta tiene historial en intentos de usuarios; ha sido desactivada para proteger la integridad estadística.';
                    $tipoMensaje = 'warning';
                } else {
                    $stmt = $pdo->prepare('DELETE FROM preguntas WHERE id_pregunta = :id');
                    $stmt->execute([':id' => $idPregunta]);
                    $mensaje = 'Pregunta eliminada exitosamente.';
                }
            } catch (PDOException $e) {
                $mensaje = 'Error al procesar la solicitud: ' . $e->getMessage();
                $tipoMensaje = 'danger';
            }
        }
    }
}

// Filtros GET
$filtroCategoria  = (int)($_GET['categoria'] ?? 0);
$filtroDificultad = sanitizarTexto($_GET['dificultad'] ?? '');
$filtroEstado     = isset($_GET['estado']) && $_GET['estado'] !== '' ? (int)$_GET['estado'] : -1;
$busqueda         = sanitizarTexto($_GET['q'] ?? '');

$sql = 'SELECT p.*, c.nombre AS categoria_nombre, c.color AS categoria_color,
               (SELECT COUNT(*) FROM opciones o WHERE o.id_pregunta = p.id_pregunta) AS total_opciones
          FROM preguntas p
          JOIN categorias c ON c.id_categoria = p.id_categoria
         WHERE 1=1';
$params = [];

if ($filtroCategoria > 0) {
    $sql .= ' AND p.id_categoria = :cat';
    $params[':cat'] = $filtroCategoria;
}
if (!empty($filtroDificultad)) {
    $sql .= ' AND p.dificultad = :dif';
    $params[':dif'] = $filtroDificultad;
}
if ($filtroEstado !== -1) {
    $sql .= ' AND p.estado = :est';
    $params[':est'] = $filtroEstado;
}
if (!empty($busqueda)) {
    $sql .= ' AND (p.enunciado LIKE :busq OR p.explicacion LIKE :busq2)';
    $params[':busq']  = '%' . $busqueda . '%';
    $params[':busq2'] = '%' . $busqueda . '%';
}

$sql .= ' ORDER BY p.id_pregunta DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$preguntas = $stmt->fetchAll();

// Categorías para el filtro
$categorias = obtenerCategorias();

require_once __DIR__ . '/includes/header_admin.php';
?>

<div class="admin-header-bar">
  <div>
    <h1>Banco de Preguntas</h1>
    <p class="t-muted fs-sm">Administra las preguntas, niveles de dificultad y opciones de respuesta pedagógicas.</p>
  </div>
  <div>
    <a href="pregunta_form.php" class="btn btn-primario">
      <i class="bi bi-plus-circle me-1" aria-hidden="true"></i> Nueva Pregunta
    </a>
  </div>
</div>

<?php if (!empty($mensaje)): ?>
  <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-info-circle-fill me-2" aria-hidden="true"></i>
    <?= e($mensaje) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<!-- Barra de Filtros y Búsqueda -->
<div class="tarjeta p-3 mb-4">
  <form method="GET" action="preguntas.php" class="row g-2 align-items-center">
    <div class="col-md-4">
      <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="q" class="form-control" placeholder="Buscar por enunciado o explicación..." value="<?= e($busqueda) ?>">
      </div>
    </div>
    <div class="col-md-3">
      <select name="categoria" class="form-select form-select-sm">
        <option value="0">-- Todas las categorías --</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?= $cat['id_categoria'] ?>" <?= $filtroCategoria === (int)$cat['id_categoria'] ? 'selected' : '' ?>>
            <?= e($cat['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <select name="dificultad" class="form-select form-select-sm">
        <option value="">-- Dificultad --</option>
        <option value="basico" <?= $filtroDificultad === 'basico' ? 'selected' : '' ?>>Básico</option>
        <option value="intermedio" <?= $filtroDificultad === 'intermedio' ? 'selected' : '' ?>>Intermedio</option>
        <option value="avanzado" <?= $filtroDificultad === 'avanzado' ? 'selected' : '' ?>>Avanzado</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="estado" class="form-select form-select-sm">
        <option value="">-- Todos los estados --</option>
        <option value="1" <?= $filtroEstado === 1 ? 'selected' : '' ?>>Activas</option>
        <option value="0" <?= $filtroEstado === 0 ? 'selected' : '' ?>>Inactivas</option>
      </select>
    </div>
    <div class="col-md-1 d-flex gap-1">
      <button type="submit" class="btn btn-sm btn-outline-primary w-100" title="Filtrar">
        <i class="bi bi-funnel"></i>
      </button>
      <?php if (!empty($busqueda) || $filtroCategoria > 0 || !empty($filtroDificultad) || $filtroEstado !== -1): ?>
        <a href="preguntas.php" class="btn btn-sm btn-outline-secondary" title="Limpiar filtros">
          <i class="bi bi-x-lg"></i>
        </a>
      <?php endif; ?>
    </div>
  </form>
</div>

<!-- Listado de Preguntas -->
<div class="tarjeta p-0 mb-4 overflow-hidden">
  <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
    <span class="fw-bold fs-sm text-secondary">
      Total encontradas: <?= count($preguntas) ?>
    </span>
    <span class="fs-xs t-muted">
      Las preguntas activas se muestran aleatoriamente en el Quiz.
    </span>
  </div>

  <?php if (empty($preguntas)): ?>
    <div class="text-center py-5">
      <i class="bi bi-patch-question text-muted" style="font-size:3rem;"></i>
      <h5 class="mt-3 text-secondary">No se encontraron preguntas</h5>
      <p class="text-muted fs-sm">Intenta ajustar los criterios de búsqueda o agrega una nueva pregunta.</p>
      <a href="pregunta_form.php" class="btn btn-primario btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Crear primera pregunta
      </a>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light fs-xs text-uppercase text-muted">
          <tr>
            <th style="width: 50px;">ID</th>
            <th>Enunciado y Explicación</th>
            <th style="width: 170px;">Categoría</th>
            <th style="width: 110px;">Dificultad</th>
            <th style="width: 100px;">Estado</th>
            <th style="width: 150px;" class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($preguntas as $p): ?>
            <tr>
              <td class="fw-semibold text-secondary">#<?= $p['id_pregunta'] ?></td>
              <td>
                <div class="fw-medium text-dark mb-1">
                  <?= e($p['enunciado']) ?>
                </div>
                <?php if (!empty($p['explicacion'])): ?>
                  <div class="fs-xs text-muted" style="max-height: 40px; overflow: hidden; text-overflow: ellipsis;">
                    <i class="bi bi-lightbulb text-warning me-1"></i><?= e($p['explicacion']) ?>
                  </div>
                <?php endif; ?>
                <div class="mt-1 fs-xs text-secondary">
                  <span class="badge bg-light text-dark border">
                    <i class="bi bi-list-check me-1"></i><?= $p['total_opciones'] ?> opciones
                  </span>
                </div>
              </td>
              <td>
                <span class="badge" style="background-color: <?= e($p['categoria_color'] ?: '#5636C9') ?>; color: #fff;">
                  <?= e($p['categoria_nombre']) ?>
                </span>
              </td>
              <td>
                <?php if ($p['dificultad'] === 'basico'): ?>
                  <span class="badge bg-success-subtle text-success border border-success-subtle">Básico</span>
                <?php elseif ($p['dificultad'] === 'intermedio'): ?>
                  <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Intermedio</span>
                <?php else: ?>
                  <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Avanzado</span>
                <?php endif; ?>
              </td>
              <td>
                <form method="POST" action="preguntas.php" style="display:inline;">
                  <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">
                  <input type="hidden" name="accion" value="toggle_estado">
                  <input type="hidden" name="id_pregunta" value="<?= $p['id_pregunta'] ?>">
                  <input type="hidden" name="nuevo_estado" value="<?= $p['estado'] ? 0 : 1 ?>">
                  <?php if ($p['estado']): ?>
                    <button type="submit" class="badge bg-success border-0 text-white" title="Clic para desactivar" style="cursor:pointer;">
                      <i class="bi bi-check-circle me-1"></i>Activa
                    </button>
                  <?php else: ?>
                    <button type="submit" class="badge bg-secondary border-0 text-white" title="Clic para activar" style="cursor:pointer;">
                      <i class="bi bi-pause-circle me-1"></i>Inactiva
                    </button>
                  <?php endif; ?>
                </form>
              </td>
              <td class="text-end">
                <a href="pregunta_form.php?id=<?= $p['id_pregunta'] ?>" class="btn btn-sm btn-outline-primary" title="Editar pregunta">
                  <i class="bi bi-pencil-square"></i>
                </a>

                <!-- Botón eliminar con confirmación modal o alert -->
                <form method="POST" action="preguntas.php" style="display:inline;" onsubmit="return confirm('¿Confirmas que deseas eliminar esta pregunta? Si tiene respuestas registradas en intentos previos, será desactivada para conservar el historial.');">
                  <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">
                  <input type="hidden" name="accion" value="eliminar">
                  <input type="hidden" name="id_pregunta" value="<?= $p['id_pregunta'] ?>">
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar pregunta">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer_admin.php'; ?>
