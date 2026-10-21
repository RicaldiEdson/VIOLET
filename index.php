<?php
/**
 * VIOLETA — Página de inicio (index.php)
 */
require_once __DIR__ . '/config/configuracion.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

iniciarSesionSegura();

$paginaActual = 'inicio';
$tituloPag    = 'Inicio';
$stats        = obtenerEstadisticasGenerales();

require_once __DIR__ . '/includes/header.php';
?>

<!-- ════════════════════ HERO PRINCIPAL ════════════════════ -->
<section class="hero-principal" aria-labelledby="hero-titulo-principal">
  <!-- Decoraciones de fondo -->
  <div class="hero-deco hero-deco-1" aria-hidden="true"></div>
  <div class="hero-deco hero-deco-2" aria-hidden="true"></div>
  <div class="hero-deco hero-deco-3" aria-hidden="true"></div>

  <div class="hero-inner">
    <!-- Columna de texto -->
    <div class="anim-fade-in-up">
      <div class="hero-etiqueta">
        <i class="bi bi-heart-fill" aria-hidden="true"></i>
        Plataforma educativa · Bolivia 2026
      </div>

      <h1 class="hero-titulo" id="hero-titulo-principal">
        La violencia también<br>
        se esconde en
        <span class="acento-magenta">lo cotidiano</span>
      </h1>

      <p class="hero-descripcion">
        Infórmate, reconoce las señales, conoce tus derechos y pon a prueba tus
        conocimientos sobre la prevención de la violencia contra la mujer.
      </p>

      <div class="hero-botones">
        <a href="<?= BASE_URL ?>/quiz.php"
           class="btn btn-magenta btn-xl"
           id="btn-hero-comenzar">
          <i class="bi bi-play-circle-fill" aria-hidden="true"></i>
          Comenzar ahora
        </a>
        <a href="<?= BASE_URL ?>/informacion.php"
           class="btn btn-contorno-blanco btn-xl"
           id="btn-hero-explorar">
          <i class="bi bi-book" aria-hidden="true"></i>
          Explorar información
        </a>
      </div>
    </div>

    <!-- Columna de ilustración -->
    <div class="hero-ilustracion anim-fade-in-up anim-delay-2">
      <svg viewBox="0 0 480 520" xmlns="http://www.w3.org/2000/svg" role="img"
           aria-label="Ilustración de una mujer empoderada rodeada de flores">
        <!-- Fondo circular -->
        <circle cx="240" cy="260" r="220" fill="rgba(86,54,201,.15)"/>
        <circle cx="240" cy="260" r="180" fill="rgba(86,54,201,.1)"/>

        <!-- Flores decorativas -->
        <g opacity=".7">
          <circle cx="80"  cy="120" r="22" fill="#D94FC1" opacity=".5"/>
          <circle cx="80"  cy="120" r="14" fill="#E9DDFD"/>
          <circle cx="400" cy="150" r="18" fill="#5636C9" opacity=".6"/>
          <circle cx="400" cy="150" r="10" fill="#E9DDFD"/>
          <circle cx="60"  cy="380" r="16" fill="#D94FC1" opacity=".4"/>
          <circle cx="420" cy="360" r="20" fill="#5636C9" opacity=".5"/>
          <circle cx="420" cy="360" r="11" fill="#E9DDFD"/>
        </g>

        <!-- Pétalos flor grande -->
        <g transform="translate(240,90)">
          <ellipse cx="0" cy="-28" rx="12" ry="22" fill="#D94FC1" opacity=".8" transform="rotate(0)"/>
          <ellipse cx="0" cy="-28" rx="12" ry="22" fill="#D94FC1" opacity=".7" transform="rotate(60)"/>
          <ellipse cx="0" cy="-28" rx="12" ry="22" fill="#D94FC1" opacity=".6" transform="rotate(120)"/>
          <ellipse cx="0" cy="-28" rx="12" ry="22" fill="#D94FC1" opacity=".7" transform="rotate(180)"/>
          <ellipse cx="0" cy="-28" rx="12" ry="22" fill="#D94FC1" opacity=".6" transform="rotate(240)"/>
          <ellipse cx="0" cy="-28" rx="12" ry="22" fill="#D94FC1" opacity=".8" transform="rotate(300)"/>
          <circle cx="0" cy="0" r="14" fill="#F4EEFF"/>
        </g>

        <!-- Cuerpo de la figura (mujer estilizada) -->
        <!-- Cabeza -->
        <circle cx="240" cy="195" r="38" fill="#F4C57A"/>
        <!-- Cabello -->
        <ellipse cx="240" cy="175" rx="40" ry="28" fill="#4A2040"/>
        <ellipse cx="210" cy="200" rx="14" ry="22" fill="#4A2040"/>
        <ellipse cx="270" cy="200" rx="14" ry="22" fill="#4A2040"/>

        <!-- Cuello y cuerpo -->
        <rect x="228" y="230" width="24" height="18" rx="4" fill="#F4C57A"/>

        <!-- Vestido / torso -->
        <path d="M190 248 Q240 235 290 248 L310 360 Q240 380 170 360 Z" fill="#5636C9"/>
        <!-- Detalle vestido -->
        <path d="M205 248 Q240 240 275 248 L268 290 Q240 300 212 290 Z" fill="#E9DDFD" opacity=".4"/>

        <!-- Brazos -->
        <path d="M193 252 Q160 280 148 320" stroke="#F4C57A" stroke-width="18"
              stroke-linecap="round" fill="none"/>
        <path d="M287 252 Q320 280 332 320" stroke="#F4C57A" stroke-width="18"
              stroke-linecap="round" fill="none"/>

        <!-- Manos -->
        <circle cx="146" cy="324" r="12" fill="#F4C57A"/>
        <circle cx="334" cy="324" r="12" fill="#F4C57A"/>

        <!-- Signo de amor / estrella en mano izquierda -->
        <text x="130" y="328" font-size="14" fill="#D94FC1" font-family="serif">♥</text>

        <!-- Falda / piernas -->
        <path d="M170 360 Q240 380 310 360 L305 430 Q280 445 260 430 L250 410 L240 430 L230 410 L220 430 Q200 445 175 430 Z"
              fill="#3d1a6e"/>

        <!-- Pies -->
        <ellipse cx="225" cy="430" rx="16" ry="8" fill="#4A2040"/>
        <ellipse cx="255" cy="430" rx="16" ry="8" fill="#4A2040"/>

        <!-- Ojos -->
        <circle cx="228" cy="196" r="5" fill="#3d1a1a"/>
        <circle cx="252" cy="196" r="5" fill="#3d1a1a"/>
        <circle cx="230" cy="194" r="2" fill="white"/>
        <circle cx="254" cy="194" r="2" fill="white"/>

        <!-- Boca (sonrisa) -->
        <path d="M230 210 Q240 218 250 210" stroke="#c0605a" stroke-width="2.5"
              fill="none" stroke-linecap="round"/>

        <!-- Estrella decorativa -->
        <text x="345" y="200" font-size="22" fill="#D94FC1" opacity=".8">✦</text>
        <text x="100" y="300" font-size="16" fill="#E9DDFD" opacity=".6">✦</text>
        <text x="370" y="350" font-size="12" fill="#E9DDFD" opacity=".5">✦</text>
      </svg>

      <!-- Burbuja flotante -->
      <div class="hero-burbuja">
        <i class="bi bi-heart-fill" style="color:#D94FC1;" aria-hidden="true"></i>
        No estás sola
      </div>
    </div>

  </div>
</section><!-- /hero-principal -->

<!-- ════════════════ SECCIÓN: ACCESOS EDUCATIVOS ════════════════ -->
<section class="seccion-sm seccion-blanca" aria-labelledby="titulo-accesos">
  <div class="contenedor">
    <div style="text-align:center;margin-bottom:2.5rem;">
      <span class="badge badge-violeta mb-2">¿Por dónde empezar?</span>
      <h2 id="titulo-accesos">Explora la plataforma</h2>
    </div>

    <div class="tarjetas-acceso">
      <!-- Aprende -->
      <a href="<?= BASE_URL ?>/informacion.php?cat=tipos-violencia"
         class="tarjeta-acceso anim-fade-in-up"
         id="acceso-aprende">
        <div class="icono-circulo ic-violeta">
          <i class="bi bi-book-half" aria-hidden="true"></i>
        </div>
        <h4>Aprende</h4>
        <p>Conoce los distintos tipos de violencia y cómo se manifiestan en la vida cotidiana.</p>
        <span class="enlace-acc">
          Leer más <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </span>
      </a>

      <!-- Reflexiona -->
      <a href="<?= BASE_URL ?>/informacion.php?cat=senales-alerta"
         class="tarjeta-acceso anim-fade-in-up anim-delay-1"
         id="acceso-reflexiona">
        <div class="icono-circulo ic-magenta">
          <i class="bi bi-eye" aria-hidden="true"></i>
        </div>
        <h4>Reflexiona</h4>
        <p>Identifica señales de alerta en relaciones y entornos que pueden resultar dañinos.</p>
        <span class="enlace-acc">
          Explorar <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </span>
      </a>

      <!-- Quiz -->
      <a href="<?= BASE_URL ?>/quiz.php"
         class="tarjeta-acceso anim-fade-in-up anim-delay-2"
         id="acceso-quiz">
        <div class="icono-circulo ic-oscuro">
          <i class="bi bi-patch-question" aria-hidden="true"></i>
        </div>
        <h4>Pon a prueba</h4>
        <p>Responde el cuestionario interactivo y evalúa cuánto sabes sobre prevención.</p>
        <span class="enlace-acc">
          Comenzar quiz <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </span>
      </a>

      <!-- Recursos -->
      <a href="<?= BASE_URL ?>/informacion.php?cat=recursos-ayuda"
         class="tarjeta-acceso anim-fade-in-up anim-delay-3"
         id="acceso-recursos">
        <div class="icono-circulo ic-verde">
          <i class="bi bi-telephone-heart" aria-hidden="true"></i>
        </div>
        <h4>Busca orientación</h4>
        <p>Consulta recursos e instituciones de orientación y acompañamiento verificados.</p>
        <span class="enlace-acc">
          Ver recursos <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </span>
      </a>
    </div>
  </div>
</section>

<!-- ═══════════════ SECCIÓN: CONCIENTIZACIÓN ════════════════════ -->
<section class="seccion seccion-lila" aria-labelledby="titulo-concientizacion">
  <div class="contenedor">
    <div class="seccion-titulo">
      <span class="badge badge-violeta mb-2">Un primer paso</span>
      <h2 id="titulo-concientizacion">
        Reconocer la violencia es un primer paso<br>
        <span class="texto-gradiente">para prevenirla</span>
      </h2>
      <div class="divider-violeta"></div>
      <p>La información y la reflexión son herramientas poderosas para protegernos y apoyar a quienes nos rodean.</p>
    </div>

    <div class="grid-3 gap-4">
      <div class="bloque-conciencia">
        <div class="icono-circulo ic-violeta" style="margin:0 auto 1rem;">
          <i class="bi bi-search-heart" aria-hidden="true"></i>
        </div>
        <span class="num">01</span>
        <h4 style="margin-bottom:.5rem;">Identificar</h4>
        <p class="t-muted fs-sm">
          Aprender a reconocer las distintas formas que puede tomar la violencia,
          incluso cuando no deja marcas visibles.
        </p>
      </div>

      <div class="bloque-conciencia">
        <div class="icono-circulo ic-magenta" style="margin:0 auto 1rem;">
          <i class="bi bi-journal-text" aria-hidden="true"></i>
        </div>
        <span class="num" style="color:var(--magenta);">02</span>
        <h4 style="margin-bottom:.5rem;">Informarse</h4>
        <p class="t-muted fs-sm">
          Conocer los derechos, los marcos legales y los recursos disponibles
          permite tomar decisiones más informadas.
        </p>
      </div>

      <div class="bloque-conciencia">
        <div class="icono-circulo ic-verde" style="margin:0 auto 1rem;">
          <i class="bi bi-people-fill" aria-hidden="true"></i>
        </div>
        <span class="num" style="color:var(--verde);">03</span>
        <h4 style="margin-bottom:.5rem;">Buscar apoyo</h4>
        <p class="t-muted fs-sm">
          Compartir información, acompañar con respeto y conectar con redes de
          orientación puede marcar una gran diferencia.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ SECCIÓN: ESTADÍSTICAS DE LA PLATAFORMA ═════ -->
<section class="seccion seccion-blanca" aria-labelledby="titulo-estadisticas">
  <div class="contenedor">
    <div class="seccion-titulo">
      <h2 id="titulo-estadisticas">Lo que encontrarás en VIOLETA</h2>
      <p>Contenido educativo organizado y en constante crecimiento.</p>
    </div>

    <div class="grid-4" style="text-align:center;">
      <div>
        <span class="estadistica-numero"><?= $stats['total_categorias'] ?></span>
        <span class="estadistica-etiqueta">
          <i class="bi bi-grid" aria-hidden="true"></i> Categorías de contenido
        </span>
      </div>
      <div>
        <span class="estadistica-numero"><?= $stats['total_preguntas'] ?></span>
        <span class="estadistica-etiqueta">
          <i class="bi bi-patch-question" aria-hidden="true"></i> Preguntas educativas
        </span>
      </div>
      <div>
        <span class="estadistica-numero"><?= $stats['total_intentos'] ?></span>
        <span class="estadistica-etiqueta">
          <i class="bi bi-clipboard-check" aria-hidden="true"></i> Cuestionarios completados
        </span>
      </div>
      <div>
        <span class="estadistica-numero">
          <?= $stats['total_intentos'] > 0 ? round($stats['promedio_puntaje']) . '%' : '—' ?>
        </span>
        <span class="estadistica-etiqueta">
          <i class="bi bi-graph-up" aria-hidden="true"></i> Promedio de aciertos
        </span>
      </div>
    </div>

    <!-- Tipos de violencia visual -->
    <div style="margin-top:4rem;">
      <h3 style="text-align:center;margin-bottom:2rem;">Categorías de contenido educativo</h3>
      <div class="grid-3 gap-3">
        <?php
        $categoriasData = [
          ['icono'=>'shield-exclamation', 'nombre'=>'Violencia física',     'color'=>'ic-violeta', 'desc'=>'Golpes, empujones y cualquier daño corporal directo.'],
          ['icono'=>'brain',              'nombre'=>'Violencia psicológica', 'color'=>'ic-magenta', 'desc'=>'Insultos, humillaciones, amenazas y manipulación emocional.'],
          ['icono'=>'currency-dollar',    'nombre'=>'Violencia económica',   'color'=>'ic-ambar',   'desc'=>'Control del acceso a recursos, dinero o bienes.'],
          ['icono'=>'lock',               'nombre'=>'Violencia sexual',      'color'=>'ic-violeta', 'desc'=>'Actos sexuales sin consentimiento libre e informado.'],
          ['icono'=>'phone-fill',         'nombre'=>'Violencia digital',     'color'=>'ic-magenta', 'desc'=>'Acoso, vigilancia y difusión no consentida por medios tecnológicos.'],
          ['icono'=>'eye-slash',          'nombre'=>'Violencia simbólica',   'color'=>'ic-verde',   'desc'=>'Estereotipos y discursos que subordinan o degradan a las mujeres.'],
        ];
        foreach ($categoriasData as $cat): ?>
        <div class="tarjeta" style="padding:1.5rem;">
          <div style="display:flex;align-items:center;gap:1rem;margin-bottom:.75rem;">
            <div class="icono-circulo <?= $cat['color'] ?>" style="width:46px;height:46px;font-size:1.2rem;">
              <i class="bi bi-<?= $cat['icono'] ?>" aria-hidden="true"></i>
            </div>
            <h4 style="margin:0;font-size:1rem;"><?= $cat['nombre'] ?></h4>
          </div>
          <p class="t-muted fs-sm" style="margin:0;"><?= $cat['desc'] ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════ SECCIÓN: DESARROLLADOR DEL PROYECTO ══════════════════ -->
<section class="seccion seccion-lila" aria-labelledby="titulo-desarrollador-index">
  <div class="contenedor">
    <div class="seccion-titulo">
      <span class="badge badge-violeta mb-2">Autor y Créditos</span>
      <h2 id="titulo-desarrollador-index">Desarrollador del proyecto</h2>
      <div class="divider-violeta"></div>
      <p>Diseño, arquitectura de software y programación integral de la plataforma educativa VIOLETA.</p>
    </div>

    <div class="grid-2 gap-4" style="align-items:center;">
      <!-- Tarjeta principal del desarrollador -->
      <div class="tarjeta" style="padding:2.5rem 2rem;text-align:center;">
        <div class="dev-avatar" style="width:100px;height:100px;font-size:2.5rem;margin:0 auto 1.25rem;">
          R
        </div>
        <h3 style="margin-bottom:.25rem;font-size:1.5rem;color:var(--texto);">Ricaldi Laura Edson</h3>
        <span class="badge badge-violeta mb-3">Desarrollador Full-Stack</span>
        <p class="t-muted fs-sm" style="line-height:1.6;margin-bottom:1.5rem;">
          Desarrollador único del proyecto. Responsable del diseño visual UI/UX, arquitectura de base de datos MySQL,
          lógica de servidor en PHP 8, quiz interactivo con evaluación segura, sección informativa y panel administrativo.
        </p>

        <div style="border-top:1px dashed var(--borde);padding-top:1.25rem;text-align:left;font-size:.875rem;">
          <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.5rem;color:var(--texto);">
            <i class="bi bi-building t-violeta" aria-hidden="true"></i>
            <span><strong>Institución:</strong> ITNC "Federico Álvarez Plata" — Nocturno</span>
          </div>
          <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.5rem;color:var(--texto);">
            <i class="bi bi-mortarboard t-violeta" aria-hidden="true"></i>
            <span><strong>Asignatura:</strong> Diseño y Programación Web III</span>
          </div>
          <div style="display:flex;align-items:center;gap:.6rem;color:var(--texto);">
            <i class="bi bi-calendar-check t-violeta" aria-hidden="true"></i>
            <span><strong>Gestión:</strong> <?= VIOLETA_YEAR ?> — Bolivia</span>
          </div>
        </div>

        <div style="margin-top:1.75rem;">
          <a href="<?= BASE_URL ?>/desarrolladores.php" class="btn btn-primario btn-sm">
            <i class="bi bi-person-lines-fill" aria-hidden="true"></i> Ver información completa del proyecto
          </a>
        </div>
      </div>

      <!-- Resumen de tecnologías y propósito -->
      <div>
        <div class="tarjeta mb-3" style="padding:1.5rem;">
          <h4 style="font-size:1.05rem;color:var(--violeta);margin-bottom:.75rem;">
            <i class="bi bi-bullseye me-2" aria-hidden="true"></i>Propósito educativo y social
          </h4>
          <p class="fs-sm t-muted mb-0" style="line-height:1.6;">
            Sensibilizar y educar a estudiantes y jóvenes para reconocer las distintas manifestaciones de violencia contra las mujeres
            (física, psicológica, económica, sexual, digital y simbólica), promoviendo la prevención, la reflexión y el respeto a los derechos en Bolivia.
          </p>
        </div>

        <div class="tarjeta mb-3" style="padding:1.5rem;">
          <h4 style="font-size:1.05rem;color:var(--violeta);margin-bottom:.75rem;">
            <i class="bi bi-cpu me-2" aria-hidden="true"></i>Stack tecnológico
          </h4>
          <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
            <span class="badge" style="background:#e8f4fd;color:#0b5ed7;">PHP 8 &amp; PDO</span>
            <span class="badge" style="background:#eef2ff;color:#4338ca;">MySQL / MariaDB</span>
            <span class="badge" style="background:#f5f3ff;color:#6d28d9;">Bootstrap 5</span>
            <span class="badge" style="background:#fefce8;color:#a16207;">JavaScript ES6</span>
            <span class="badge" style="background:#fff1f2;color:#be123c;">HTML5 &amp; CSS3</span>
            <span class="badge" style="background:#f0fdf4;color:#15803d;">XAMPP Local</span>
          </div>
        </div>

        <div class="tarjeta" style="padding:1.5rem;">
          <h4 style="font-size:1.05rem;color:var(--violeta);margin-bottom:.75rem;">
            <i class="bi bi-check2-circle me-2" aria-hidden="true"></i>Funcionalidades destacadas
          </h4>
          <ul style="list-style:none;padding:0;margin:0;font-size:.875rem;color:var(--texto-sec);">
            <li style="margin-bottom:.4rem;"><i class="bi bi-check-lg t-verde me-1" aria-hidden="true"></i> Quiz interactivo con cálculo de puntaje en servidor y revisión educativa.</li>
            <li style="margin-bottom:.4rem;"><i class="bi bi-check-lg t-verde me-1" aria-hidden="true"></i> Sección informativa interactiva con mitos, realidades y recursos de ayuda.</li>
            <li style="margin-bottom:.4rem;"><i class="bi bi-check-lg t-verde me-1" aria-hidden="true"></i> Panel administrativo protegido con hash bcrypt y CSRF.</li>
            <li><i class="bi bi-check-lg t-verde me-1" aria-hidden="true"></i> Salida rápida con atajos de teclado y resguardo de privacidad.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════ LLAMADA A LA ACCIÓN ══════════════════════ -->
<section class="seccion seccion-violeta" style="text-align:center;" aria-labelledby="cta-titulo">
  <!-- Decoración -->
  <div style="position:relative;overflow:hidden;padding:5rem 2rem;">
    <div style="position:absolute;width:400px;height:400px;border-radius:50%;background:rgba(255,255,255,.05);top:-150px;right:-100px;pointer-events:none;" aria-hidden="true"></div>
    <div style="position:absolute;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.05);bottom:-80px;left:-60px;pointer-events:none;" aria-hidden="true"></div>

    <div style="position:relative;z-index:1;max-width:700px;margin:0 auto;">
      <span class="badge badge-magenta mb-3" style="background:rgba(217,79,193,.3);color:#f0a0e0;border:1px solid rgba(217,79,193,.4);">
        <i class="bi bi-patch-question" aria-hidden="true"></i> Quiz interactivo
      </span>
      <h2 id="cta-titulo" style="color:white;font-size:2.5rem;margin-bottom:1rem;">
        ¿Cuánto sabes sobre la<br>prevención de la violencia?
      </h2>
      <p style="color:rgba(255,255,255,.75);font-size:1.1rem;margin-bottom:2.5rem;">
        Responde <?= QUIZ_NUM_PREGUNTAS ?> preguntas educativas y descubre cuánto conoces.
        Al finalizar, podrás revisar tus respuestas y seguir aprendiendo.
      </p>
      <a href="<?= BASE_URL ?>/quiz.php"
         class="btn btn-blanco btn-xl"
         id="btn-cta-quiz">
        <i class="bi bi-play-circle-fill" aria-hidden="true"></i>
        Evaluar mis conocimientos
      </a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
