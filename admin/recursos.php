<?php
/**
 * VIOLETA — Gestión de Recursos e Instituciones de Ayuda (admin/recursos.php)
 */
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

$paginaAdmin    = 'recursos';
$tituloPagAdmin = 'Recursos de Ayuda';
$pdo            = obtenerConexion();

$mensaje = '';
$tipoMensaje = 'success';

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarTokenCSRF()) {
        $mensaje = 'Token de seguridad inválido. Operación cancelada.';
        $tipoMensaje = 'danger';
    } else {
        $accion = $_POST['accion'] ?? '';

        if ($accion === 'guardar') {
            $idRecurso         = (int)($_POST['id_recurso'] ?? 0);
            $nombre            = sanitizarTexto($_POST['nombre'] ?? '');
            $tipoRecurso       = sanitizarTexto($_POST['tipo_recurso'] ?? '');
            $contacto          = sanitizarTexto($_POST['contacto'] ?? '');
            $urlOficial        = sanitizarTexto($_POST['url_oficial'] ?? '');
            $descripcion       = sanitizarTexto($_POST['descripcion'] ?? '');
            $notaVerificacion  = sanitizarTexto($_POST['nota_verificacion'] ?? '');
            $estado            = isset($_POST['estado']) ? 1 : 0;

            if (estaVacio($nombre)) {
                $mensaje = 'El nombre de la institución o servicio es obligatorio.';
                $tipoMensaje = 'danger';
            } else {
                try {
                    if ($idRecurso > 0) {
                        $stmt = $pdo->prepare(
                            'UPDATE recursos_ayuda
                                SET nombre = :nom, tipo_recurso = :tipo, contacto = :cont,
                                    url_oficial = :url, descripcion = :desc,
                                    nota_verificacion = :nota, estado = :est
                              WHERE id_recurso = :id'
                        );
                        $stmt->execute([
                            ':nom'  => $nombre,
                            ':tipo' => $tipoRecurso,
                            ':cont' => $contacto,
                            ':url'  => $urlOficial,
                            ':desc' => $descripcion,
                            ':nota' => $notaVerificacion,
                            ':est'  => $estado,
                            ':id'   => $idRecurso
                        ]);
                        $mensaje = 'Recurso actualizado con éxito.';
                    } else {
                        $stmt = $pdo->prepare(
                            'INSERT INTO recursos_ayuda (nombre, tipo_recurso, contacto, url_oficial, descripcion, nota_verificacion, estado)
                             VALUES (:nom, :tipo, :cont, :url, :desc, :nota, :est)'
                        );
                        $stmt->execute([
                            ':nom'  => $nombre,
                            ':tipo' => $tipoRecurso,
                            ':cont' => $contacto,
                            ':url'  => $urlOficial,
                            ':desc' => $descripcion,
                            ':nota' => $notaVerificacion,
                            ':est'  => $estado
                        ]);
                        $mensaje = 'Recurso de ayuda registrado exitosamente.';
                    }
                } catch (PDOException $e) {
                    $mensaje = 'Error al guardar el recurso: ' . $e->getMessage();
                    $tipoMensaje = 'danger';
                }
            }
        } elseif ($accion === 'toggle_estado') {
            $idRecurso   = (int)($_POST['id_recurso'] ?? 0);
            $nuevoEstado = (int)($_POST['nuevo_estado'] ?? 1);
            if ($idRecurso > 0) {
                $stmt = $pdo->prepare('UPDATE recursos_ayuda SET estado = :est WHERE id_recurso = :id');
                $stmt->execute([':est' => $nuevoEstado, ':id' => $idRecurso]);
                $mensaje = $nuevoEstado ? 'Recurso activado.' : 'Recurso desactivado.';
            }
        } elseif ($accion === 'eliminar') {
            $idRecurso = (int)($_POST['id_recurso'] ?? 0);
            if ($idRecurso > 0) {
                $stmt = $pdo->prepare('DELETE FROM recursos_ayuda WHERE id_recurso = :id');
                $stmt->execute([':id' => $idRecurso]);
                $mensaje = 'Recurso eliminado correctamente.';
            }
        }
    }
}

// Obtener todos los recursos
$stmt = $pdo->query('SELECT * FROM recursos_ayuda ORDER BY id_recurso ASC');
$recursos = $stmt->fetchAll();

// Si se solicitó editar un recurso específico
$recEditar = null;
if (isset($_GET['editar'])) {
    $idEdit = (int)$_GET['editar'];
    foreach ($recursos as $r) {
        if ((int)$r['id_recurso'] === $idEdit) {
            $recEditar = $r;
            break;
        }
    }
}

require_once __DIR__ . '/includes/header_admin.php';
?>

<div class="admin-header-bar">
  <div>
    <h1>Directorio de Recursos y Servicios de Apoyo</h1>
    <p class="t-muted fs-sm">Administra los canales oficiales, líneas de emergencia y centros de orientación en Bolivia.</p>
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
        <i class="bi <?= $recEditar ? 'bi-pencil-square text-warning' : 'bi-plus-circle text-primary' ?> me-2"></i>
        <?= $recEditar ? 'Editar Recurso' : 'Nuevo Recurso de Ayuda' ?>
      </h5>

      <form method="POST" action="recursos.php">
        <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">
        <input type="hidden" name="accion" value="guardar">
        <input type="hidden" name="id_recurso" value="<?= $recEditar ? (int)$recEditar['id_recurso'] : 0 ?>">

        <div class="mb-3">
          <label for="nombre" class="form-label fw-semibold fs-sm">Nombre de la institución o servicio <span class="text-danger">*</span></label>
          <input type="text" name="nombre" id="nombre" class="form-control form-control-sm" value="<?= e($recEditar['nombre'] ?? '') ?>" placeholder="Ej. FELCV - Fuerza Especial de Lucha Contra la Violencia" required>
        </div>

        <div class="mb-3">
          <label for="tipo_recurso" class="form-label fw-semibold fs-sm">Tipo de servicio</label>
          <input type="text" name="tipo_recurso" id="tipo_recurso" class="form-control form-control-sm" value="<?= e($recEditar['tipo_recurso'] ?? '') ?>" placeholder="Ej. Línea telefónica gratuita / Atención policial / Asesoría legal">
        </div>

        <div class="mb-3">
          <label for="contacto" class="form-label fw-semibold fs-sm">Contacto / Teléfono</label>
          <input type="text" name="contacto" id="contacto" class="form-control form-control-sm" value="<?= e($recEditar['contacto'] ?? '') ?>" placeholder="Ej. 156 / 800-140348">
        </div>

        <div class="mb-3">
          <label for="url_oficial" class="form-label fw-semibold fs-sm">Sitio web oficial (URL)</label>
          <input type="url" name="url_oficial" id="url_oficial" class="form-control form-control-sm" value="<?= e($recEditar['url_oficial'] ?? '') ?>" placeholder="https://...">
        </div>

        <div class="mb-3">
          <label for="descripcion" class="form-label fw-semibold fs-sm">Descripción del servicio</label>
          <textarea name="descripcion" id="descripcion" rows="3" class="form-control form-control-sm" placeholder="Horarios de atención, alcance y modalidad de apoyo..."><?= e($recEditar['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
          <label for="nota_verificacion" class="form-label fw-semibold fs-sm">Nota de verificación / Fuente</label>
          <input type="text" name="nota_verificacion" id="nota_verificacion" class="form-control form-control-sm" value="<?= e($recEditar['nota_verificacion'] ?? '') ?>" placeholder="Ej. Ley 348 / Ministerio de Justicia">
        </div>

        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" role="switch" name="estado" id="estado_rec" value="1" <?= (!isset($recEditar) || $recEditar['estado']) ? 'checked' : '' ?>>
          <label class="form-check-label fs-xs fw-semibold" for="estado_rec">Visible en el portal público</label>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primario btn-sm py-2">
            <i class="bi bi-floppy me-1"></i> <?= $recEditar ? 'Actualizar Recurso' : 'Registrar Recurso' ?>
          </button>
          <?php if ($recEditar): ?>
            <a href="recursos.php" class="btn btn-outline-secondary btn-sm">Cancelar edición</a>
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
          Recursos registrados (<?= count($recursos) ?>)
        </span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light fs-xs text-uppercase text-muted">
            <tr>
              <th>Institución / Tipo</th>
              <th style="width: 150px;">Contacto</th>
              <th style="width: 100px;">Estado</th>
              <th style="width: 130px;" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recursos as $r): ?>
              <tr>
                <td>
                  <div class="fw-semibold text-dark"><?= e($r['nombre']) ?></div>
                  <?php if (!empty($r['tipo_recurso'])): ?>
                    <span class="badge bg-light text-secondary border fs-xs">
                      <?= e($r['tipo_recurso']) ?>
                    </span>
                  <?php endif; ?>
                  <?php if (!empty($r['descripcion'])): ?>
                    <div class="fs-xs text-muted mt-1" style="max-width:350px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                      <?= e($r['descripcion']) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($r['contacto'])): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                      <i class="bi bi-telephone-fill me-1"></i><?= e($r['contacto']) ?>
                    </span>
                  <?php endif; ?>
                  <?php if (!empty($r['url_oficial'])): ?>
                    <div class="mt-1">
                      <a href="<?= e($r['url_oficial']) ?>" target="_blank" rel="noopener noreferrer" class="fs-xs text-primary text-decoration-none">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Enlace oficial
                      </a>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <form method="POST" action="recursos.php" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">
                    <input type="hidden" name="accion" value="toggle_estado">
                    <input type="hidden" name="id_recurso" value="<?= $r['id_recurso'] ?>">
                    <input type="hidden" name="nuevo_estado" value="<?= $r['estado'] ? 0 : 1 ?>">
                    <?php if ($r['estado']): ?>
                      <button type="submit" class="badge bg-success border-0 text-white" title="Clic para desactivar" style="cursor:pointer;">
                        Activo
                      </button>
                    <?php else: ?>
                      <button type="submit" class="badge bg-secondary border-0 text-white" title="Clic para activar" style="cursor:pointer;">
                        Inactivo
                      </button>
                    <?php endif; ?>
                  </form>
                </td>
                <td class="text-end">
                  <a href="recursos.php?editar=<?= $r['id_recurso'] ?>" class="btn btn-sm btn-outline-primary" title="Editar recurso">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                  <form method="POST" action="recursos.php" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este recurso?');">
                    <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id_recurso" value="<?= $r['id_recurso'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar recurso">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
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
