<?php
/**
 * VIOLETA — Página de resultados del quiz (resultado.php)
 */
require_once __DIR__ . '/config/configuracion.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

iniciarSesionSegura();

// Validar parámetro del intento
$idIntento = isset($_GET['intento']) ? (int)$_GET['intento'] : 0;
if ($idIntento <= 0) {
    header('Location: quiz.php');
    exit;
}

// Recuperar intento de BD
$intento = obtenerIntentoPorId($idIntento);
if (!$intento || !$intento['completado']) {
    header('Location: quiz.php');
    exit;
}

// Obtener revisión de respuestas
$revision = obtenerRevisionIntento($idIntento);

$paginaActual = 'quiz';
$tituloPag    = 'Resultados del cuestionario';

// Determinar mensaje según porcentaje
$porcentaje = (float)$intento['puntuacion_porcentaje'];
if ($porcentaje >= 90) {
    $mensajeNivel  = '¡Excelente! Has demostrado un buen conocimiento sobre prevención de la violencia.';
    $iconoNivel    = 'trophy-fill';
    $colorNivel    = 'var(--verde)';
    $bgNivel       = '#d4eddf';
} elseif ($porcentaje >= 70) {
    $mensajeNivel  = '¡Buen trabajo! Puedes reforzar algunos conceptos para seguir aprendiendo.';
    $iconoNivel    = 'star-fill';
    $colorNivel    = 'var(--violeta)';
    $bgNivel       = 'var(--lila-claro)';
} else {
    $mensajeNivel  = 'Cada respuesta es una oportunidad para aprender. Explora el material educativo y vuelve a intentarlo.';
    $iconoNivel    = 'book-fill';
    $colorNivel    = 'var(--ambar)';
    $bgNivel       = '#fdecd7';
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- ════════════════ CABECERA DE RESULTADOS ═══════════════════ -->
<div style="background:linear-gradient(135deg,var(--violeta-prof),#3a1d6e);padding:3.5rem 0;text-align:center;">
  <div class="contenedor">
    <span class="badge mb-2" style="background:rgba(233,221,253,.15);color:#E9DDFD;">
      <i class="bi bi-clipboard-check" aria-hidden="true"></i> Cuestionario completado
    </span>
    <h1 style="color:white;margin-bottom:.5rem;">Tus resultados</h1>
    <p style="color:rgba(255,255,255,.65);">Aquí tienes un resumen de tu desempeño y las explicaciones educativas.</p>
  </div>
</div>

<!-- ════════════════ TARJETA DE RESULTADO ════════════════════ -->
<section class="seccion-sm">
  <div class="contenedor-sm">

    <!-- Tarjeta principal -->
    <div class="tarjeta mb-4" style="padding:3rem;text-align:center;border-top:5px solid <?= $colorNivel ?>;">
      <div style="background:<?= $bgNivel ?>;border-radius:50%;width:160px;height:160px;
                  display:flex;align-items:center;justify-content:center;flex-direction:column;
                  margin:0 auto 1.5rem;border:6px solid <?= $colorNivel ?>22;">
        <span style="font-size:3rem;font-weight:800;color:<?= $colorNivel ?>;line-height:1;">
          <?= round($porcentaje) ?>%
        </span>
        <span style="font-size:.75rem;color:var(--texto-sec);">de aciertos</span>
      </div>

      <div style="background:<?= $bgNivel ?>;border-radius:var(--r-md);padding:1.25rem;margin-bottom:2rem;display:inline-block;max-width:500px;">
        <i class="bi bi-<?= $iconoNivel ?>" style="color:<?= $colorNivel ?>;font-size:1.3rem;" aria-hidden="true"></i>
        <p style="margin:.5rem 0 0;color:var(--texto);font-size:.95rem;font-weight:600;line-height:1.5;">
          <?= e($mensajeNivel) ?>
        </p>
      </div>

      <!-- Estadísticas detalladas -->
      <div class="grid-3 gap-3" style="max-width:500px;margin:0 auto 2rem;">
        <div style="background:var(--lila-claro);border-radius:var(--r-md);padding:1.25rem;">
          <span style="font-size:2rem;font-weight:800;color:var(--verde);display:block;"><?= $intento['respuestas_correctas'] ?></span>
          <span style="font-size:.8rem;color:var(--texto-sec);">Correctas</span>
        </div>
        <div style="background:var(--lila-claro);border-radius:var(--r-md);padding:1.25rem;">
          <span style="font-size:2rem;font-weight:800;color:var(--rojo);display:block;"><?= $intento['respuestas_incorrectas'] ?></span>
          <span style="font-size:.8rem;color:var(--texto-sec);">Incorrectas</span>
        </div>
        <div style="background:var(--lila-claro);border-radius:var(--r-md);padding:1.25rem;">
          <span style="font-size:2rem;font-weight:800;color:var(--violeta);display:block;"><?= $intento['total_preguntas'] ?></span>
          <span style="font-size:.8rem;color:var(--texto-sec);">Total</span>
        </div>
      </div>

      <!-- Acciones -->
      <div class="flex-center gap-3" style="flex-wrap:wrap;">
        <a href="quiz.php" class="btn btn-magenta">
          <i class="bi bi-arrow-repeat" aria-hidden="true"></i> Volver a intentarlo
        </a>
        <a href="informacion.php" class="btn btn-contorno">
          <i class="bi bi-book" aria-hidden="true"></i> Explorar información
        </a>
        <a href="index.php" class="btn btn-contorno">
          <i class="bi bi-house" aria-hidden="true"></i> Inicio
        </a>
      </div>
    </div>

    <!-- Recomendaciones -->
    <div class="tarjeta mb-5" style="padding:1.75rem;background:var(--lila-claro);">
      <h4><i class="bi bi-lightbulb t-violeta" aria-hidden="true"></i> Para seguir aprendiendo</h4>
      <div class="grid-3 gap-3 mt-3">
        <a href="informacion.php?cat=tipos-violencia" class="btn btn-contorno btn-sm">
          <i class="bi bi-shield-exclamation" aria-hidden="true"></i> Tipos de violencia
        </a>
        <a href="informacion.php?cat=senales-alerta" class="btn btn-contorno btn-sm">
          <i class="bi bi-exclamation-triangle" aria-hidden="true"></i> Señales de alerta
        </a>
        <a href="informacion.php?cat=derechos" class="btn btn-contorno btn-sm">
          <i class="bi bi-file-earmark-text" aria-hidden="true"></i> Derechos
        </a>
      </div>
    </div>

    <!-- ════════ REVISIÓN DE RESPUESTAS ════════ -->
    <h2 style="margin-bottom:1.5rem;">
      <i class="bi bi-list-check t-violeta" aria-hidden="true"></i>
      Revisión de respuestas
    </h2>
    <p class="t-muted mb-4 fs-sm">
      Revisa cada pregunta, la respuesta que elegiste y la explicación educativa correspondiente.
    </p>

    <?php if (empty($revision)): ?>
    <div class="alerta alerta-info">
      <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
      <span>No se encontraron detalles de respuestas para este intento.</span>
    </div>
    <?php else: ?>

    <?php foreach ($revision as $i => $r): ?>
    <article class="revision-item <?= $r['era_correcta'] ? 'correcta' : 'incorrecta' ?>"
             aria-label="Pregunta <?= $i+1 ?>: <?= $r['era_correcta'] ? 'Correcta' : 'Incorrecta' ?>">

      <!-- Cabecera de la pregunta -->
      <div class="flex-between mb-2" style="flex-wrap:wrap;gap:.5rem;">
        <div style="display:flex;align-items:center;gap:.75rem;">
          <div style="width:32px;height:32px;border-radius:50%;
                      background:<?= $r['era_correcta'] ? 'var(--verde)' : 'var(--rojo)' ?>;
                      display:flex;align-items:center;justify-content:center;
                      color:white;font-size:.875rem;font-weight:700;flex-shrink:0;">
            <?= $i + 1 ?>
          </div>
          <span class="badge <?= $r['era_correcta'] ? 'badge-verde' : 'badge-rojo' ?>">
            <i class="bi bi-<?= $r['era_correcta'] ? 'check-circle-fill' : 'x-circle-fill' ?>"
               aria-hidden="true"></i>
            <?= $r['era_correcta'] ? 'Correcta' : 'Incorrecta' ?>
          </span>
          <span class="badge badge-violeta fs-xs"><?= e($r['categoria']) ?></span>
        </div>
      </div>

      <!-- Enunciado -->
      <p style="font-weight:600;margin-bottom:1rem;line-height:1.5;">
        <?= e($r['enunciado']) ?>
      </p>

      <!-- Opciones de respuesta -->
      <div class="revision-opciones">
        <!-- Respuesta seleccionada (si era incorrecta) -->
        <?php if (!$r['era_correcta']): ?>
        <div class="revision-opcion seleccionada-incorrecta">
          <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
          <span><strong>Tu respuesta:</strong> <?= e($r['texto_seleccionada']) ?></span>
        </div>
        <?php endif; ?>

        <!-- Respuesta correcta -->
        <div class="revision-opcion correcta-opc">
          <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
          <span><strong>Respuesta correcta:</strong> <?= e($r['texto_correcta']) ?></span>
        </div>
      </div>

      <!-- Explicación educativa -->
      <?php if ($r['explicacion']): ?>
      <div class="revision-explicacion">
        <strong><i class="bi bi-lightbulb-fill" aria-hidden="true"></i> Explicación:</strong>
        <?= e($r['explicacion']) ?>
      </div>
      <?php endif; ?>

    </article>
    <?php endforeach; ?>
    <?php endif; ?>

    <!-- Acciones finales -->
    <div class="flex-center gap-3 mt-5" style="flex-wrap:wrap;padding:2rem 0;">
      <a href="quiz.php" class="btn btn-magenta btn-lg">
        <i class="bi bi-arrow-repeat" aria-hidden="true"></i> Volver a intentarlo
      </a>
      <a href="informacion.php" class="btn btn-primario btn-lg">
        <i class="bi bi-book" aria-hidden="true"></i> Explorar información
      </a>
      <a href="index.php" class="btn btn-contorno btn-lg">
        <i class="bi bi-house" aria-hidden="true"></i> Inicio
      </a>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
