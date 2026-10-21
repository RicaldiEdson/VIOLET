<?php
/**
 * VIOLETA — Formulario de Crear / Editar Pregunta (admin/pregunta_form.php)
 */
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

$paginaAdmin    = 'preguntas';
$pdo            = obtenerConexion();
$idPregunta     = (int)($_GET['id'] ?? 0);
$esEdicion      = $idPregunta > 0;
$tituloPagAdmin = $esEdicion ? 'Editar Pregunta #' . $idPregunta : 'Nueva Pregunta';

$errores = [];
$categorias = obtenerCategorias();

// Variables por defecto
$idCategoria  = $categorias[0]['id_categoria'] ?? 1;
$enunciado    = '';
$explicacion  = '';
$dificultad   = 'basico';
$estado       = 1;
$opciones     = [
    ['texto' => '', 'correcta' => 1],
    ['texto' => '', 'correcta' => 0],
    ['texto' => '', 'correcta' => 0],
    ['texto' => '', 'correcta' => 0],
];

// Si es edición, cargar datos de la BD
if ($esEdicion && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $pdo->prepare('SELECT * FROM preguntas WHERE id_pregunta = :id LIMIT 1');
    $stmt->execute([':id' => $idPregunta]);
    $preguntaActual = $stmt->fetch();

    if (!$preguntaActual) {
        redirigir(BASE_URL . '/admin/preguntas.php');
    }

    $idCategoria = (int)$preguntaActual['id_categoria'];
    $enunciado   = $preguntaActual['enunciado'];
    $explicacion = $preguntaActual['explicacion'] ?? '';
    $dificultad  = $preguntaActual['dificultad'];
    $estado      = (int)$preguntaActual['estado'];

    // Cargar opciones
    $stmtOpc = $pdo->prepare('SELECT * FROM opciones WHERE id_pregunta = :id ORDER BY orden ASC');
    $stmtOpc->execute([':id' => $idPregunta]);
    $opcionesDb = $stmtOpc->fetchAll();

    if (!empty($opcionesDb)) {
        $opciones = [];
        foreach ($opcionesDb as $opc) {
            $opciones[] = [
                'id_opcion' => (int)$opc['id_opcion'],
                'texto'     => $opc['texto_opcion'],
                'correcta'  => (int)$opc['es_correcta'] === 1 ? 1 : 0
            ];
        }
    }
}

// Procesar formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarTokenCSRF()) {
        $errores[] = 'Token de seguridad inválido. Por favor recarga el formulario.';
    } else {
        $idCategoria = (int)($_POST['id_categoria'] ?? 0);
        $enunciado   = sanitizarTexto($_POST['enunciado'] ?? '');
        $explicacion = sanitizarTexto($_POST['explicacion'] ?? '');
        $dificultad  = sanitizarTexto($_POST['dificultad'] ?? 'basico');
        $estado      = isset($_POST['estado']) ? 1 : 0;
        $opcionCorrectaIdx = isset($_POST['opcion_correcta']) ? (int)$_POST['opcion_correcta'] : -1;

        $textosOpciones = $_POST['opciones'] ?? [];
        $opciones = [];

        // Validación de datos básicos
        if ($idCategoria <= 0) {
            $errores[] = 'Selecciona una categoría válida.';
        }
        if (estaVacio($enunciado)) {
            $errores[] = 'El enunciado de la pregunta no puede estar vacío.';
        }
        if (!in_array($dificultad, ['basico', 'intermedio', 'avanzado'], true)) {
            $dificultad = 'basico';
        }

        // Construir y validar opciones
        $opcionesConTexto = 0;
        foreach ($textosOpciones as $idx => $txt) {
            $textoLimpio = sanitizarTexto($txt);
            $esEstaCorrecta = ($idx === $opcionCorrectaIdx) ? 1 : 0;
            $opciones[] = [
                'texto'    => $textoLimpio,
                'correcta' => $esEstaCorrecta
            ];
            if (!estaVacio($textoLimpio)) {
                $opcionesConTexto++;
            }
        }

        if ($opcionesConTexto < 2) {
            $errores[] = 'Debes ingresar al menos 2 opciones de respuesta.';
        }
        if ($opcionCorrectaIdx < 0 || $opcionCorrectaIdx >= count($textosOpciones)) {
            $errores[] = 'Debes marcar una opción como la respuesta correcta.';
        } elseif (estaVacio($textosOpciones[$opcionCorrectaIdx] ?? '')) {
            $errores[] = 'La opción seleccionada como correcta no tiene texto ingresado.';
        }

        // Si no hay errores, guardar en BD con transacción
        if (empty($errores)) {
            try {
                $pdo->beginTransaction();

                if ($esEdicion) {
                    // Actualizar pregunta
                    $stmtUpdate = $pdo->prepare(
                        'UPDATE preguntas
                            SET id_categoria = :cat,
                                enunciado    = :enunciado,
                                explicacion  = :explicacion,
                                dificultad   = :dificultad,
                                estado       = :estado
                          WHERE id_pregunta  = :id'
                    );
                    $stmtUpdate->execute([
                        ':cat'         => $idCategoria,
                        ':enunciado'   => $enunciado,
                        ':explicacion' => $explicacion,
                        ':dificultad'  => $dificultad,
                        ':estado'      => $estado,
                        ':id'          => $idPregunta
                    ]);

                    // Eliminar opciones previas
                    $stmtDel = $pdo->prepare('DELETE FROM opciones WHERE id_pregunta = :id');
                    $stmtDel->execute([':id' => $idPregunta]);
                    $targetPreguntaId = $idPregunta;
                } else {
                    // Insertar nueva pregunta
                    $stmtInsert = $pdo->prepare(
                        'INSERT INTO preguntas (id_categoria, enunciado, explicacion, dificultad, estado)
                         VALUES (:cat, :enunciado, :explicacion, :dificultad, :estado)'
                    );
                    $stmtInsert->execute([
                        ':cat'         => $idCategoria,
                        ':enunciado'   => $enunciado,
                        ':explicacion' => $explicacion,
                        ':dificultad'  => $dificultad,
                        ':estado'      => $estado
                    ]);
                    $targetPreguntaId = (int)$pdo->lastInsertId();
                }

                // Insertar las opciones
                $stmtInsertOpc = $pdo->prepare(
                    'INSERT INTO opciones (id_pregunta, texto_opcion, es_correcta, orden)
                     VALUES (:pregunta, :texto, :correcta, :orden)'
                );

                $orden = 1;
                foreach ($opciones as $opc) {
                    if (!estaVacio($opc['texto'])) {
                        $stmtInsertOpc->execute([
                            ':pregunta' => $targetPreguntaId,
                            ':texto'    => $opc['texto'],
                            ':correcta' => $opc['correcta'],
                            ':orden'    => $orden++
                        ]);
                    }
                }

                $pdo->commit();
                redirigir(BASE_URL . '/admin/preguntas.php?mensaje=guardado_exitoso');
            } catch (PDOException $e) {
                $pdo->rollBack();
                $errores[] = 'Error al guardar en base de datos: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/includes/header_admin.php';
?>

<div class="admin-header-bar">
  <div>
    <h1><?= $esEdicion ? 'Editar Pregunta #' . $idPregunta : 'Nueva Pregunta Educativa' ?></h1>
    <p class="t-muted fs-sm">Configura el enunciado, retroalimentación didáctica y alternativas de respuesta.</p>
  </div>
  <div>
    <a href="preguntas.php" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i> Volver a preguntas
    </a>
  </div>
</div>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger mb-4" role="alert">
    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Corrige los siguientes errores:</div>
    <ul class="mb-0 ps-3">
      <?php foreach ($errores as $err): ?>
        <li><?= e($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" action="pregunta_form.php<?= $esEdicion ? '?id=' . $idPregunta : '' ?>">
  <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">

  <div class="row g-4">
    <!-- Columna Izquierda: Datos de la Pregunta -->
    <div class="col-lg-7">
      <div class="tarjeta p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3">
          <i class="bi bi-patch-question me-2 text-primary"></i> Contenido de la Pregunta
        </h5>

        <div class="mb-3">
          <label for="enunciado" class="form-label fw-semibold">
            Enunciado de la pregunta <span class="text-danger">*</span>
          </label>
          <textarea name="enunciado" id="enunciado" rows="3" class="form-control" placeholder="Escribe aquí el enunciado claro y pedagógico..." required><?= e($enunciado) ?></textarea>
          <div class="form-text fs-xs">Redacta de manera clara, empática y reflexiva.</div>
        </div>

        <div class="mb-3">
          <label for="explicacion" class="form-label fw-semibold">
            Explicación didáctica o retroalimentación
          </label>
          <textarea name="explicacion" id="explicacion" rows="3" class="form-control" placeholder="Aparecerá en los resultados para reforzar el aprendizaje de quien responde..."><?= e($explicacion) ?></textarea>
          <div class="form-text fs-xs">Esta explicación fundamenta por qué la respuesta correcta es la indicada y aporta valor educativo.</div>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label for="id_categoria" class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
            <select name="id_categoria" id="id_categoria" class="form-select" required>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id_categoria'] ?>" <?= (int)$cat['id_categoria'] === $idCategoria ? 'selected' : '' ?>>
                  <?= e($cat['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label for="dificultad" class="form-label fw-semibold">Dificultad</label>
            <select name="dificultad" id="dificultad" class="form-select">
              <option value="basico" <?= $dificultad === 'basico' ? 'selected' : '' ?>>Básico (Conceptos iniciales)</option>
              <option value="intermedio" <?= $dificultad === 'intermedio' ? 'selected' : '' ?>>Intermedio (Situaciones cotidianas)</option>
              <option value="avanzado" <?= $dificultad === 'avanzado' ? 'selected' : '' ?>>Avanzado (Marco legal y actuación)</option>
            </select>
          </div>
        </div>

        <div class="form-check form-switch mt-4">
          <input class="form-check-input" type="checkbox" role="switch" name="estado" id="estado" value="1" <?= $estado ? 'checked' : '' ?>>
          <label class="form-check-label fw-semibold" for="estado">
            Pregunta activa (disponible para aparecer en el Quiz)
          </label>
        </div>
      </div>
    </div>

    <!-- Columna Derecha: Opciones de Respuesta -->
    <div class="col-lg-5">
      <div class="tarjeta p-4 mb-4">
        <h5 class="fw-bold text-dark mb-2">
          <i class="bi bi-list-check me-2 text-success"></i> Opciones de Respuesta
        </h5>
        <p class="fs-xs text-muted mb-3">
          Ingresa hasta 4 alternativas. Marca con el botón circular cuál es la <strong>respuesta correcta</strong>.
        </p>

        <?php for ($i = 0; $i < 4; $i++): ?>
          <?php
            $txtOpc = $opciones[$i]['texto'] ?? '';
            $esCorr = !empty($opciones[$i]['correcta']);
          ?>
          <div class="mb-3 p-3 rounded border <?= $esCorr ? 'border-success bg-success-subtle bg-opacity-25' : 'bg-light' ?>">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="fw-semibold fs-sm text-secondary">
                Alternativa <?= chr(65 + $i) ?>
              </span>
              <div class="form-check form-check-inline m-0">
                <input class="form-check-input" type="radio" name="opcion_correcta" id="opc_correcta_<?= $i ?>" value="<?= $i ?>" <?= $esCorr ? 'checked' : '' ?> required>
                <label class="form-check-label fs-xs fw-bold text-success" for="opc_correcta_<?= $i ?>">
                  Correcta
                </label>
              </div>
            </div>
            <textarea name="opciones[<?= $i ?>]" class="form-control form-control-sm" rows="2" placeholder="Texto de la alternativa <?= chr(65 + $i) ?>..."><?= e($txtOpc) ?></textarea>
          </div>
        <?php endfor; ?>

        <div class="d-grid gap-2 mt-4">
          <button type="submit" class="btn btn-primario py-2">
            <i class="bi bi-floppy me-1"></i> <?= $esEdicion ? 'Actualizar Pregunta' : 'Guardar Pregunta' ?>
          </button>
          <a href="preguntas.php" class="btn btn-outline-secondary btn-sm">
            Cancelar
          </a>
        </div>
      </div>
    </div>
  </div>
</form>

<?php require_once __DIR__ . '/includes/footer_admin.php'; ?>
