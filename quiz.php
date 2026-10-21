<?php
/**
 * VIOLETA — Quiz interactivo (quiz.php)
 * Gestiona el inicio del quiz, la navegación entre preguntas
 * y el envío de respuestas al servidor para su evaluación.
 */
require_once __DIR__ . '/config/configuracion.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

iniciarSesionSegura();

$paginaActual = 'quiz';
$tituloPag    = 'Quiz interactivo';

// ── Acción POST ───────────────────────────────────────────────────────────────
$accion  = $_POST['accion'] ?? '';
$error   = '';

// ── Iniciar el quiz ───────────────────────────────────────────────────────────
if ($accion === 'iniciar') {
    if (!validarTokenCSRF()) {
        $error = 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.';
    } else {
        // Obtener preguntas aleatorias
        $preguntas = obtenerPreguntasAleatorias(QUIZ_NUM_PREGUNTAS);
        if (count($preguntas) < 1) {
            $error = 'No hay preguntas disponibles en este momento. Inténtelo más tarde.';
        } else {
            // Guardar en sesión (solo IDs + enunciados, NO las respuestas correctas)
            $codigo = generarCodigo(16);
            $_SESSION['quiz_codigo']      = $codigo;
            $_SESSION['quiz_preguntas']   = $preguntas;          // array con opciones sin es_correcta
            $_SESSION['quiz_respuestas']  = [];                   // [id_pregunta => id_opcion]
            $_SESSION['quiz_actual']      = 0;                    // índice de pregunta actual
            $_SESSION['quiz_total']       = count($preguntas);

            // Crear intento en BD
            $idIntento = crearIntento($codigo, count($preguntas));
            $_SESSION['quiz_id_intento'] = $idIntento;
        }
    }
}

// ── Guardar una respuesta ─────────────────────────────────────────────────────
if ($accion === 'responder' && isset($_SESSION['quiz_preguntas'])) {
    if (!validarTokenCSRF()) {
        $error = 'Token inválido.';
    } else {
        $idPregunta = (int)($_POST['id_pregunta'] ?? 0);
        $idOpcion   = (int)($_POST['id_opcion']   ?? 0);
        $irA        = (int)($_POST['ir_a']        ?? 0);

        // Validar que la pregunta pertenece al intento
        $ids = array_column($_SESSION['quiz_preguntas'], 'id_pregunta');
        if (in_array($idPregunta, array_map('intval', $ids)) && $idOpcion > 0) {
            $_SESSION['quiz_respuestas'][$idPregunta] = $idOpcion;
        }

        // Navegar a la pregunta solicitada
        $total = $_SESSION['quiz_total'];
        if ($irA >= 0 && $irA < $total) {
            $_SESSION['quiz_actual'] = $irA;
        }

        // Si se solicitó abrir el modal de finalizar (después de guardar la última respuesta)
        $abrirModal = (int)($_POST['abrir_modal'] ?? 0);
        if ($abrirModal === 1) {
            header('Location: quiz.php?confirmar=1');
        } else {
            header('Location: quiz.php');
        }
        exit;
    }
}

// ── Finalizar el quiz ─────────────────────────────────────────────────────────
if ($accion === 'finalizar' && isset($_SESSION['quiz_preguntas'])) {
    if (!validarTokenCSRF()) {
        $error = 'Token inválido.';
    } else {
        // Verificar que todas las preguntas estén respondidas
        $respondidas = count($_SESSION['quiz_respuestas'] ?? []);
        $totalPr     = $_SESSION['quiz_total'];

        if ($respondidas < $totalPr) {
            $error = "Debes responder todas las preguntas antes de finalizar. "
                   . "Quedan " . ($totalPr - $respondidas) . " sin responder.";
        } else {
            // Calcular en servidor
            $resumen = finalizarIntento(
                $_SESSION['quiz_id_intento'],
                array_map('intval', $_SESSION['quiz_respuestas']),
                $_SESSION['quiz_preguntas']
            );

            // Guardar ID de intento para la página de resultados y limpiar sesión del quiz
            $idFinal = $_SESSION['quiz_id_intento'];
            unset(
                $_SESSION['quiz_codigo'],
                $_SESSION['quiz_preguntas'],
                $_SESSION['quiz_respuestas'],
                $_SESSION['quiz_actual'],
                $_SESSION['quiz_total'],
                $_SESSION['quiz_id_intento']
            );

            header('Location: resultado.php?intento=' . $idFinal);
            exit;
        }
    }
}

// ── Estado del quiz ───────────────────────────────────────────────────────────
$enCurso   = isset($_SESSION['quiz_preguntas']) && count($_SESSION['quiz_preguntas']) > 0;
$preguntas = $enCurso ? $_SESSION['quiz_preguntas'] : [];
$actual    = $enCurso ? (int)$_SESSION['quiz_actual'] : 0;
$total     = $enCurso ? (int)$_SESSION['quiz_total']  : 0;
$respuestas= $enCurso ? ($_SESSION['quiz_respuestas'] ?? []) : [];

$preguntaActual = $preguntas[$actual] ?? null;

require_once __DIR__ . '/includes/header.php';
?>

<?php if (!$enCurso): ?>
<!-- ════════════════════ PANTALLA DE INICIO ════════════════════ -->
<section style="background:linear-gradient(135deg,var(--violeta-prof),#3a1d6e);padding:5rem 0;">
  <div class="contenedor">
    <div class="quiz-pantalla-inicio">
      <div class="icono-circulo ic-oscuro" style="width:90px;height:90px;font-size:2.2rem;margin:0 auto 2rem;">
        <i class="bi bi-patch-question-fill" aria-hidden="true"></i>
      </div>
      <span class="badge badge-violeta mb-3" style="background:rgba(233,221,253,.15);color:#E9DDFD;">
        Cuestionario interactivo
      </span>
      <h1 style="color:white;font-size:2.2rem;margin-bottom:1rem;">
        ¿Cuánto sabes sobre la prevención de la violencia?
      </h1>
      <p style="color:rgba(255,255,255,.75);font-size:1.05rem;margin-bottom:2rem;line-height:1.7;">
        Este cuestionario educativo te ayudará a evaluar y reforzar tus conocimientos sobre
        la violencia contra la mujer, sus formas, señales de alerta, derechos y recursos de orientación.
      </p>
      <div class="grid-3 gap-3 mb-4" style="text-align:center;">
        <div style="background:rgba(255,255,255,.08);border-radius:var(--r-md);padding:1.25rem;border:1px solid rgba(255,255,255,.1);">
          <span style="font-size:2rem;font-weight:800;color:var(--lila);display:block;"><?= QUIZ_NUM_PREGUNTAS ?></span>
          <span style="color:rgba(255,255,255,.65);font-size:.875rem;">Preguntas</span>
        </div>
        <div style="background:rgba(255,255,255,.08);border-radius:var(--r-md);padding:1.25rem;border:1px solid rgba(255,255,255,.1);">
          <span style="font-size:2rem;font-weight:800;color:var(--lila);display:block;">~5 min</span>
          <span style="color:rgba(255,255,255,.65);font-size:.875rem;">Tiempo estimado</span>
        </div>
        <div style="background:rgba(255,255,255,.08);border-radius:var(--r-md);padding:1.25rem;border:1px solid rgba(255,255,255,.1);">
          <span style="font-size:2rem;font-weight:800;color:var(--lila);display:block;"><i class="bi bi-person-check" aria-hidden="true"></i></span>
          <span style="color:rgba(255,255,255,.65);font-size:.875rem;">Sin registro</span>
        </div>
      </div>

      <?php if ($error): ?>
      <div class="alerta alerta-error mb-3" style="text-align:left;">
        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        <span><?= e($error) ?></span>
      </div>
      <?php endif; ?>

      <form method="POST" action="quiz.php">
        <input type="hidden" name="accion" value="iniciar">
        <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
        <button type="submit"
                class="btn btn-magenta btn-xl"
                id="btn-iniciar-quiz">
          <i class="bi bi-play-circle-fill" aria-hidden="true"></i>
          Comenzar cuestionario
        </button>
      </form>

      <p style="color:rgba(255,255,255,.45);font-size:.8rem;margin-top:1.5rem;">
        <i class="bi bi-shield-check" aria-hidden="true"></i>
        No necesitas crear una cuenta. Las respuestas son anónimas.
      </p>
    </div>
  </div>
</section>

<?php else: ?>
<!-- ════════════════════ CUESTIONARIO EN CURSO ═════════════════ -->
<div style="background:var(--violeta-prof);padding:1rem 0;">
  <div class="contenedor">
    <div class="flex-between">
      <span style="color:rgba(255,255,255,.75);font-size:.9rem;">
        <i class="bi bi-patch-question" aria-hidden="true"></i>
        Cuestionario VIOLETA
      </span>
      <span style="color:rgba(255,255,255,.65);font-size:.85rem;">
        <?= count($respuestas) ?>/<?= $total ?> respondidas
      </span>
    </div>
  </div>
</div>

<section class="seccion-sm">
  <div class="contenedor">

    <?php if ($error): ?>
    <div class="alerta alerta-error mb-3">
      <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
      <span><?= e($error) ?></span>
    </div>
    <?php endif; ?>

    <div class="quiz-contenedor">
      <!-- ── Área de pregunta ── -->
      <div>
        <div class="quiz-pregunta-area">
          <!-- Progreso -->
          <div class="quiz-progreso">
            <span>Pregunta <?= $actual + 1 ?> de <?= $total ?></span>
            <span class="badge badge-violeta">
              <?= e($preguntaActual['nombre_categoria'] ?? '') ?>
            </span>
          </div>
          <div class="barra-progreso-wrap" role="progressbar"
               aria-valuenow="<?= $actual + 1 ?>"
               aria-valuemin="1"
               aria-valuemax="<?= $total ?>">
            <div class="barra-progreso-fill"
                 style="width:<?= round((($actual + 1) / $total) * 100) ?>%"></div>
          </div>

          <!-- Enunciado -->
          <p class="quiz-enunciado" id="enunciado-pregunta">
            <?= e($preguntaActual['enunciado']) ?>
          </p>

          <!-- Opciones -->
          <?php
          $letras = ['A', 'B', 'C', 'D'];
          $idPregActual = (int)$preguntaActual['id_pregunta'];
          $respuestaGuardada = $respuestas[$idPregActual] ?? null;
          ?>
          <form method="POST" action="quiz.php" id="form-respuesta">
            <input type="hidden" name="accion"      value="responder">
            <input type="hidden" name="csrf_token"  value="<?= e(generarTokenCSRF()) ?>">
            <input type="hidden" name="id_pregunta" value="<?= $idPregActual ?>">
            <input type="hidden" name="ir_a"        id="ir-a-input" value="<?= $actual ?>">
            <input type="hidden" name="id_opcion"   id="opcion-seleccionada" value="">

            <div class="quiz-opciones" role="group" aria-labelledby="enunciado-pregunta">
              <?php foreach ($preguntaActual['opciones'] as $i => $op):
                $idOp = (int)$op['id_opcion'];
                $esSeleccionada = $respuestaGuardada && (int)$respuestaGuardada === $idOp;
              ?>
              <button type="button"
                      class="quiz-opcion <?= $esSeleccionada ? 'seleccionada' : '' ?>"
                      id="opcion-<?= $idOp ?>"
                      onclick="seleccionarOpcion(<?= $idOp ?>, <?= $idPregActual ?>)"
                      aria-pressed="<?= $esSeleccionada ? 'true' : 'false' ?>"
                      data-id-opcion="<?= $idOp ?>">
                <span class="quiz-opcion-letra" aria-hidden="true"><?= $letras[$i] ?? ($i+1) ?></span>
                <span><?= e($op['texto_opcion']) ?></span>
              </button>
              <?php endforeach; ?>
            </div>

            <!-- Navegación -->
            <div class="quiz-navegacion">
              <button type="button"
                      class="btn btn-contorno btn-sm"
                      id="btn-anterior"
                      onclick="navegarPregunta(<?= $actual - 1 ?>)"
                      <?= $actual === 0 ? 'disabled aria-disabled="true"' : '' ?>>
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Anterior
              </button>

              <?php if ($actual < $total - 1): ?>
              <button type="button"
                      class="btn btn-primario btn-sm"
                      id="btn-siguiente"
                      onclick="navegarPregunta(<?= $actual + 1 ?>)">
                Siguiente <i class="bi bi-arrow-right" aria-hidden="true"></i>
              </button>
              <?php else: ?>
              <!-- Botón finalizar: primero guarda la respuesta actual, luego muestra el modal -->
              <button type="button"
                      class="btn btn-magenta btn-sm"
                      id="btn-finalizar"
                      onclick="guardarYFinalizar()">
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i> Finalizar
              </button>
              <?php endif; ?>
            </div>
          </form><!-- /form-respuesta -->

        </div><!-- /quiz-pregunta-area -->

        <!-- Panel educativo contextual -->
        <div class="tarjeta mt-3" style="padding:1.25rem;background:var(--lila-claro);border:1px solid var(--lila);">
          <div style="display:flex;align-items:flex-start;gap:.75rem;">
            <i class="bi bi-lightbulb-fill t-violeta fs-sm" style="margin-top:.2rem;" aria-hidden="true"></i>
            <div>
              <h6 style="font-size:.8rem;font-weight:700;color:var(--violeta-oscuro);margin-bottom:.25rem;">
                ¿Sabías que…?
              </h6>
              <p class="fs-xs t-muted mb-0" style="line-height:1.5;">
                En Bolivia, la Ley N.° 348 reconoce 16 formas de violencia contra las mujeres.
                Aprender a reconocerlas es fundamental para prevenirlas.
                Al finalizar el cuestionario, podrás revisar las explicaciones de cada respuesta.
              </p>
            </div>
          </div>
        </div>

      </div><!-- /col izq -->

      <!-- ── Panel lateral ── -->
      <aside class="quiz-panel-lateral" aria-label="Mapa de preguntas">
        <div class="tarjeta" style="padding:1.5rem;">
          <h5 style="font-size:.8rem;text-transform:uppercase;letter-spacing:.08em;color:var(--texto-sec);margin-bottom:1rem;">
            Mapa de preguntas
          </h5>

          <div class="quiz-mapa-preguntas">
            <?php foreach ($preguntas as $i => $p): ?>
            <?php
              $idp = (int)$p['id_pregunta'];
              $clases = 'quiz-mapa-btn';
              if ($i === $actual) $clases .= ' actual';
              elseif (isset($respuestas[$idp])) $clases .= ' respondida';
            ?>
            <button class="<?= $clases ?>"
                    onclick="irAPregunta(<?= $i ?>)"
                    aria-label="Ir a pregunta <?= $i+1 ?>"
                    title="Pregunta <?= $i+1 ?> — <?= isset($respuestas[$idp]) ? 'Respondida' : 'Sin responder' ?>">
              <?= $i + 1 ?>
            </button>
            <?php endforeach; ?>
          </div>

          <!-- Leyenda -->
          <div style="font-size:.75rem;color:var(--texto-sec);margin-top:1rem;">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.3rem;">
              <div style="width:14px;height:14px;background:var(--violeta);border-radius:3px;"></div> Actual
            </div>
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.3rem;">
              <div style="width:14px;height:14px;background:var(--lila);border:1.5px solid var(--violeta);border-radius:3px;"></div> Respondida
            </div>
            <div style="display:flex;align-items:center;gap:.5rem;">
              <div style="width:14px;height:14px;background:var(--blanco);border:1.5px solid var(--borde);border-radius:3px;"></div> Sin responder
            </div>
          </div>
        </div>

        <!-- Progreso global -->
        <div class="tarjeta mt-2" style="padding:1.5rem;text-align:center;">
          <span style="font-size:2rem;font-weight:800;color:var(--violeta);"><?= count($respuestas) ?></span>
          <span style="color:var(--texto-sec);font-size:.875rem;display:block;">de <?= $total ?> respondidas</span>
          <div class="barra-progreso-wrap mt-2">
            <div class="barra-progreso-fill"
                 style="width:<?= $total > 0 ? round((count($respuestas)/$total)*100) : 0 ?>%">
            </div>
          </div>

          <?php if (count($respuestas) === $total): ?>
          <div style="margin-top:1rem;">
            <button onclick="confirmarFinalizar()"
                    class="btn btn-magenta btn-sm"
                    style="width:100%"
                    id="btn-finalizar-lateral">
              <i class="bi bi-check-circle-fill" aria-hidden="true"></i> Finalizar quiz
            </button>
          </div>
          <?php endif; ?>
        </div>
      </aside>
    </div><!-- /quiz-contenedor -->

  </div>
</section>

<!-- ══ Modal de confirmación ══════════════════════════════════ -->
<div class="modal-overlay" id="modal-finalizar" role="dialog"
     aria-modal="true" aria-labelledby="modal-titulo">
  <div class="modal-cuerpo">
    <div style="text-align:center;margin-bottom:1.5rem;">
      <div class="icono-circulo ic-violeta" style="width:70px;height:70px;font-size:1.8rem;margin:0 auto 1rem;">
        <i class="bi bi-check-circle" aria-hidden="true"></i>
      </div>
      <h3 id="modal-titulo">¿Deseas finalizar el cuestionario?</h3>
      <p class="t-muted fs-sm mt-2">
        Has respondido las <?= $total ?> preguntas. Al finalizar, se calculará tu puntuación
        y podrás revisar las respuestas correctas con sus explicaciones educativas.
      </p>
    </div>
    <div style="display:flex;gap:1rem;justify-content:center;">
      <button onclick="cerrarModal()" class="btn btn-contorno">
        Revisar respuestas
      </button>
      <form method="POST" action="quiz.php" style="display:inline;">
        <input type="hidden" name="accion"     value="finalizar">
        <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
        <button type="submit" class="btn btn-magenta" id="btn-confirmar-finalizar">
          <i class="bi bi-send-fill" aria-hidden="true"></i> Sí, enviar respuestas
        </button>
      </form>
    </div>
  </div>
</div>

<form id="form-ir-a" method="POST" action="quiz.php" style="display:none;">
  <input type="hidden" name="accion"      value="responder">
  <input type="hidden" name="csrf_token"  value="<?= e(generarTokenCSRF()) ?>">
  <input type="hidden" name="id_pregunta" id="form-ia-pregunta" value="<?= $idPregActual ?>">
  <input type="hidden" name="ir_a"        id="form-ia-ir"      value="<?= $actual ?>">
  <input type="hidden" name="id_opcion"   id="form-ia-opcion"  value="">
  <input type="hidden" name="abrir_modal" id="form-ia-modal"   value="0">
</form>

<script>
// Respuesta actualmente seleccionada en la sesión
let opcionSeleccionadaId = <?= $respuestaGuardada ? (int)$respuestaGuardada : 'null' ?>;
let preguntaActualId     = <?= $idPregActual ?>;
// ¿El servidor indica que debemos abrir el modal automáticamente?
const abrirModalAlCargar = <?= isset($_GET['confirmar']) && $_GET['confirmar'] === '1' ? 'true' : 'false' ?>;

function seleccionarOpcion(idOpcion, idPregunta) {
  opcionSeleccionadaId = idOpcion;
  // UI
  document.querySelectorAll('.quiz-opcion').forEach(btn => {
    const esEsta = parseInt(btn.dataset.idOpcion) === idOpcion;
    btn.classList.toggle('seleccionada', esEsta);
    btn.setAttribute('aria-pressed', esEsta ? 'true' : 'false');
  });
}

function navegarPregunta(indice) {
  document.getElementById('form-ia-pregunta').value = preguntaActualId;
  document.getElementById('form-ia-ir').value       = indice;
  document.getElementById('form-ia-opcion').value   = opcionSeleccionadaId || '';
  document.getElementById('form-ia-modal').value    = '0';
  document.getElementById('form-ir-a').submit();
}

function irAPregunta(indice) {
  navegarPregunta(indice);
}

/**
 * Guardar la respuesta de la última pregunta Y luego mostrar el modal.
 * Flujo: POST (accion=responder, abrir_modal=1) → servidor guarda respuesta
 *        → redirige a quiz.php?confirmar=1 → modal se abre automáticamente.
 */
function guardarYFinalizar() {
  // Si el usuario no ha seleccionado ninguna opción en esta pregunta,
  // simplemente abrimos el modal (el servidor ya verificará si faltan preguntas).
  document.getElementById('form-ia-pregunta').value = preguntaActualId;
  // Quedarse en la misma pregunta (ir_a = índice actual)
  document.getElementById('form-ia-ir').value       = <?= $actual ?>;
  document.getElementById('form-ia-opcion').value   = opcionSeleccionadaId || '';
  document.getElementById('form-ia-modal').value    = '1';
  document.getElementById('form-ir-a').submit();
}

function confirmarFinalizar() {
  document.getElementById('modal-finalizar').classList.add('visible');
}

function cerrarModal() {
  document.getElementById('modal-finalizar').classList.remove('visible');
}

// Abrir modal automáticamente si el servidor lo indica (después de guardar la última respuesta)
if (abrirModalAlCargar) {
  window.addEventListener('load', () => confirmarFinalizar());
}

// Cerrar modal con Escape
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') cerrarModal();
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
