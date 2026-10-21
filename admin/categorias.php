<?php
/**
 * VIOLETA — Gestión de Categorías temáticas (admin/categorias.php)
 */
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

$paginaAdmin    = 'categorias';
$tituloPagAdmin = 'Gestión de Categorías';
$pdo            = obtenerConexion();

$mensaje = '';
$tipoMensaje = 'success';

// Procesar formulario POST (Crear / Editar / Toggle estado)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarTokenCSRF()) {
        $mensaje = 'Token de seguridad inválido. Operación cancelada.';
        $tipoMensaje = 'danger';
    } else {
        $accion = $_POST['accion'] ?? '';

        if ($accion === 'guardar') {
            $idCategoria = (int)($_POST['id_categoria'] ?? 0);
            $nombre      = sanitizarTexto($_POST['nombre'] ?? '');
            $slug        = sanitizarTexto($_POST['slug'] ?? '');
            $descripcion = sanitizarTexto($_POST['descripcion'] ?? '');
            $icono       = sanitizarTexto($_POST['icono'] ?? 'bookmark');
            $color       = sanitizarTexto($_POST['color'] ?? '#5636C9');
            $orden       = (int)($_POST['orden'] ?? 0);
            $estado      = isset($_POST['estado']) ? 1 : 0;

            if (estaVacio($slug) && !estaVacio($nombre)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nombre)));
            }

            if (estaVacio($nombre) || estaVacio($slug)) {
                $mensaje = 'El nombre y el slug de la categoría son obligatorios.';
                $tipoMensaje = 'danger';
            } else {
                try {
                    if ($idCategoria > 0) {
                        $stmt = $pdo->prepare(
                            'UPDATE categorias
                                SET nombre = :nom, slug = :slug, descripcion = :desc,
                                    icono = :ico, color = :color, orden = :orden, estado = :est
                              WHERE id_categoria = :id'
                        );
                        $stmt->execute([
                            ':nom'   => $nombre,
                            ':slug'  => $slug,
                            ':desc'  => $descripcion,
                            ':ico'   => $icono,
                            ':color' => $color,
                            ':orden' => $orden,
                            ':est'   => $estado,
                            ':id'    => $idCategoria
                        ]);
                        $mensaje = 'Categoría actualizada exitosamente.';
                    } else {
                        $stmt = $pdo->prepare(
                            'INSERT INTO categorias (nombre, slug, descripcion, icono, color, orden, estado)
                             VALUES (:nom, :slug, :desc, :ico, :color, :orden, :est)'
                        );
                        $stmt->execute([
                            ':nom'   => $nombre,
                            ':slug'  => $slug,
                            ':desc'  => $descripcion,
                            ':ico'   => $icono,
                            ':color' => $color,
                            ':orden' => $orden,
                            ':est'   => $estado
                        ]);
                        $mensaje = 'Categoría creada exitosamente.';
                    }
                } catch (PDOException $e) {
                    $mensaje = 'Error al guardar la categoría: el slug podría estar duplicado.';
                    $tipoMensaje = 'danger';
                }
            }
        } elseif ($accion === 'toggle_estado') {
            $idCategoria = (int)($_POST['id_categoria'] ?? 0);
            $nuevoEstado = (int)($_POST['nuevo_estado'] ?? 1);
            if ($idCategoria > 0) {
                $stmt = $pdo->prepare('UPDATE categorias SET estado = :est WHERE id_categoria = :id');
                $stmt->execute([':est' => $nuevoEstado, ':id' => $idCategoria]);
                $mensaje = $nuevoEstado ? 'Categoría activada.' : 'Categoría desactivada.';
            }
        }
    }
}

// Obtener todas las categorías con el conteo de preguntas
$stmt = $pdo->query(
    'SELECT c.*,
            (SELECT COUNT(*) FROM preguntas p WHERE p.id_categoria = c.id_categoria) AS total_preguntas
       FROM categorias c
      ORDER BY c.orden ASC, c.id_categoria ASC'
);
$categoriasList = $stmt->fetchAll();

// Si se solicitó editar una categoría en específico
$catEditar = null;
if (isset($_GET['editar'])) {
    $idEdit = (int)$_GET['editar'];
    foreach ($categoriasList as $cat) {
        if ((int)$cat['id_categoria'] === $idEdit) {
            $catEditar = $cat;
            break;
        }
    }
}

require_once __DIR__ . '/includes/header_admin.php';
?>

<div class="admin-header-bar">
  <div>
    <h1>Categorías Temáticas</h1>
    <p class="t-muted fs-sm">Organización de contenidos pedagógicos y clasificación del banco de preguntas.</p>
  </div>
</div>

<?php if (!empty($mensaje)): ?>
  <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-info-circle-fill me-2" aria-hidden="true"></i>
    <?= e($mensaje) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<div class="row g-4">
  <!-- Columna Formulario (Crear / Editar) -->
  <div class="col-lg-4">
    <div class="tarjeta p-4 mb-4">
      <h5 class="fw-bold text-dark mb-3">
        <i class="bi <?= $catEditar ? 'bi-pencil-square text-warning' : 'bi-plus-circle text-primary' ?> me-2"></i>
        <?= $catEditar ? 'Editar Categoría' : 'Nueva Categoría' ?>
      </h5>

      <form method="POST" action="categorias.php">
        <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">
        <input type="hidden" name="accion" value="guardar">
        <input type="hidden" name="id_categoria" value="<?= $catEditar ? (int)$catEditar['id_categoria'] : 0 ?>">

        <div class="mb-3">
          <label for="nombre" class="form-label fw-semibold fs-sm">Nombre <span class="text-danger">*</span></label>
          <input type="text" name="nombre" id="nombre" class="form-control form-control-sm" value="<?= e($catEditar['nombre'] ?? '') ?>" placeholder="Ej. Violencia Digital" required>
        </div>

        <div class="mb-3">
          <label for="slug" class="form-label fw-semibold fs-sm">Slug (URL amigable)</label>
          <input type="text" name="slug" id="slug" class="form-control form-control-sm" value="<?= e($catEditar['slug'] ?? '') ?>" placeholder="ej. violencia-digital">
          <div class="form-text fs-xs">Identificador único en minúsculas y guiones.</div>
        </div>

        <div class="mb-3">
          <label for="descripcion" class="form-label fw-semibold fs-sm">Descripción educativa</label>
          <textarea name="descripcion" id="descripcion" rows="3" class="form-control form-control-sm" placeholder="Breve resumen del contenido de esta categoría..."><?= e($catEditar['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="row g-2 mb-3">
          <div class="col-6">
            <label for="icono" class="form-label fw-semibold fs-sm">Ícono Bootstrap</label>
            <input type="text" name="icono" id="icono" class="form-control form-control-sm" value="<?= e($catEditar['icono'] ?? 'bookmark') ?>" placeholder="ej. shield-exclamation">
          </div>
          <div class="col-6">
            <label for="color" class="form-label fw-semibold fs-sm">Color distintivo</label>
            <input type="color" name="color" id="color" class="form-control form-control-color w-100" value="<?= e($catEditar['color'] ?? '#5636C9') ?>">
          </div>
        </div>

        <div class="row g-2 mb-3">
          <div class="col-6">
            <label for="orden" class="form-label fw-semibold fs-sm">Orden</label>
            <input type="number" name="orden" id="orden" class="form-control form-control-sm" value="<?= (int)($catEditar['orden'] ?? 1) ?>" min="0">
          </div>
          <div class="col-6 d-flex align-items-end">
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" role="switch" name="estado" id="estado_cat" value="1" <?= (!isset($catEditar) || $catEditar['estado']) ? 'checked' : '' ?>>
              <label class="form-check-label fs-xs fw-semibold" for="estado_cat">Activa</label>
            </div>
          </div>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primario btn-sm py-2">
            <i class="bi bi-floppy me-1"></i> <?= $catEditar ? 'Actualizar' : 'Crear Categoría' ?>
          </button>
          <?php if ($catEditar): ?>
            <a href="categorias.php" class="btn btn-outline-secondary btn-sm">Cancelar edición</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <!-- Columna Listado -->
  <div class="col-lg-8">
    <div class="tarjeta p-0 mb-4 overflow-hidden">
      <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-bold fs-sm text-secondary">
          Categorías registradas (<?= count($categoriasList) ?>)
        </span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light fs-xs text-uppercase text-muted">
            <tr>
              <th style="width: 50px;">Ord.</th>
              <th>Nombre y Descripción</th>
              <th style="width: 100px;">Color</th>
              <th style="width: 110px;" class="text-center">Preguntas</th>
              <th style="width: 100px;">Estado</th>
              <th style="width: 100px;" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categoriasList as $cat): ?>
              <tr>
                <td class="fw-bold text-muted"><?= (int)$cat['orden'] ?></td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:30px;height:30px;background-color:<?= e($cat['color']) ?>;color:#fff;">
                      <i class="bi bi-<?= e($cat['icono'] ?: 'bookmark') ?> fs-xs"></i>
                    </span>
                    <div>
                      <span class="fw-semibold text-dark"><?= e($cat['nombre']) ?></span>
                      <span class="fs-xs text-muted ms-1">(<code><?= e($cat['slug']) ?></code>)</span>
                      <?php if (!empty($cat['descripcion'])): ?>
                        <div class="fs-xs text-muted" style="max-width:320px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                          <?= e($cat['descripcion']) ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge" style="background-color:<?= e($cat['color']) ?>;color:#fff;">
                    <?= e($cat['color']) ?>
                  </span>
                </td>
                <td class="text-center">
                  <span class="badge bg-light text-dark border">
                    <?= (int)$cat['total_preguntas'] ?>
                  </span>
                </td>
                <td>
                  <form method="POST" action="categorias.php" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">
                    <input type="hidden" name="accion" value="toggle_estado">
                    <input type="hidden" name="id_categoria" value="<?= $cat['id_categoria'] ?>">
                    <input type="hidden" name="nuevo_estado" value="<?= $cat['estado'] ? 0 : 1 ?>">
                    <?php if ($cat['estado']): ?>
                      <button type="submit" class="badge bg-success border-0 text-white" title="Clic para desactivar" style="cursor:pointer;">
                        Activa
                      </button>
                    <?php else: ?>
                      <button type="submit" class="badge bg-secondary border-0 text-white" title="Clic para activar" style="cursor:pointer;">
                        Inactiva
                      </button>
                    <?php endif; ?>
                  </form>
                </td>
                <td class="text-end">
                  <a href="categorias.php?editar=<?= $cat['id_categoria'] ?>" class="btn btn-sm btn-outline-primary" title="Editar categoría">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer_admin.php'; ?>
