<?php
/**
 * VIOLETA — Página de desarrolladores (desarrolladores.php)
 */
require_once __DIR__ . '/config/configuracion.php';
require_once __DIR__ . '/includes/funciones.php';

iniciarSesionSegura();

$paginaActual = 'desarrolladores';
$tituloPag    = 'Desarrolladores';

// ── Integrante del equipo ────────────────────────────────────────────────────
$integrantes = [
    [
        'nombre'     => 'Ricaldi Laura Edson',
        'rol'        => 'Desarrollador Full-Stack',
        'inicial'    => 'R',
        'descripcion'=> 'Responsable integral del proyecto: diseño de la interfaz, arquitectura de la base de datos, desarrollo del quiz interactivo, sección informativa, panel administrativo y toda la lógica de servidor.',
    ],
];

require_once __DIR__ . '/includes/header.php';
?>

<!-- ════════════════════ HERO ════════════════════════════════ -->
<section class="dev-hero" aria-labelledby="dev-titulo">
  <div class="contenedor" style="text-align:center;">
    <span class="badge mb-3" style="background:rgba(233,221,253,.15);color:#E9DDFD;">
      <i class="bi bi-people" aria-hidden="true"></i> Equipo de desarrollo
    </span>
    <h1 id="dev-titulo" style="color:white;font-size:2.5rem;margin-bottom:1rem;">
      Sobre el proyecto
      <span class="acento-magenta">VIOLETA</span>
    </h1>
    <p style="color:rgba(255,255,255,.7);max-width:600px;margin:0 auto;font-size:1.05rem;line-height:1.7;">
      Una plataforma educativa desarrollada con compromiso académico y social para
      informar, sensibilizar y contribuir a la prevención de la violencia contra la mujer.
    </p>
  </div>
</section>

<!-- ════════════════ IDENTIDAD DEL PROYECTO ══════════════════ -->
<section class="seccion-sm seccion-blanca">
  <div class="contenedor">
    <div class="grid-2 gap-4" style="align-items:center;">
      <div>
        <span class="badge badge-violeta mb-2">Información del proyecto</span>
        <h2>¿Qué es VIOLETA?</h2>
        <div class="divider-violeta" style="margin:1rem 0;"></div>
        <p style="color:var(--texto);line-height:1.7;">
          VIOLETA es una plataforma web educativa desarrollada para la asignatura
          <strong>Diseño y Programación Web III</strong>, gestión 2026, del
          Instituto Técnico Nacional de Comercio "Federico Álvarez Plata" — Nocturno, Bolivia.
        </p>
        <p style="color:var(--texto-sec);line-height:1.7;font-size:.95rem;">
          El nombre VIOLETA hace referencia al color asociado a los movimientos por los derechos
          de las mujeres, así como a la flor que simboliza dignidad y sensibilidad.
        </p>

        <div style="margin-top:1.5rem;">
          <?php
          $infoProyecto = [
            ['icono'=>'building',    'label'=>'Institución', 'valor'=>'Instituto Técnico Nacional de Comercio "Federico Álvarez Plata" — Nocturno'],
            ['icono'=>'mortarboard', 'label'=>'Asignatura',  'valor'=>'Diseño y Programación Web III'],
            ['icono'=>'calendar',    'label'=>'Gestión',     'valor'=>VIOLETA_YEAR . ' — Bolivia'],
            ['icono'=>'bullseye',    'label'=>'Objetivo',    'valor'=>'Informar, reconocer y actuar frente a la violencia contra la mujer'],
          ];
          foreach ($infoProyecto as $item): ?>
          <div style="display:flex;align-items:flex-start;gap:.75rem;padding:.6rem 0;border-bottom:1px dashed var(--borde);">
            <i class="bi bi-<?= $item['icono'] ?> t-violeta" style="margin-top:.1rem;flex-shrink:0;" aria-hidden="true"></i>
            <div>
              <span class="fs-xs fw-700 t-muted" style="text-transform:uppercase;letter-spacing:.06em;"><?= $item['label'] ?></span>
              <p style="margin:0;font-size:.9rem;color:var(--texto);"><?= $item['valor'] ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Tarjeta de eslogan -->
      <div style="text-align:center;">
        <div style="background:linear-gradient(135deg,var(--violeta-prof),var(--violeta));
                    border-radius:var(--r-xl);padding:3rem 2rem;color:white;
                    box-shadow:var(--s-lg);position:relative;overflow:hidden;">
          <div style="position:absolute;width:200px;height:200px;border-radius:50%;
                      background:rgba(255,255,255,.05);top:-60px;right:-60px;" aria-hidden="true"></div>
          <div style="position:absolute;width:120px;height:120px;border-radius:50%;
                      background:rgba(217,79,193,.15);bottom:-30px;left:-30px;" aria-hidden="true"></div>

          <div style="position:relative;z-index:1;">
            <svg width="60" height="60" viewBox="0 0 38 38" style="margin:0 auto 1.5rem;"
                 aria-hidden="true">
              <circle cx="19" cy="19" r="19" fill="rgba(255,255,255,.15)"/>
              <circle cx="19" cy="14" r="5" fill="#E9DDFD"/>
              <ellipse cx="13" cy="24" rx="4" ry="3" fill="#D94FC1" opacity=".9"/>
              <ellipse cx="25" cy="24" rx="4" ry="3" fill="#D94FC1" opacity=".9"/>
              <circle cx="19" cy="19" r="3" fill="white"/>
              <line x1="19" y1="22" x2="19" y2="30" stroke="white" stroke-width="2" stroke-linecap="round"/>
              <line x1="15" y1="27" x2="23" y2="27" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <div style="font-size:2.5rem;font-weight:800;letter-spacing:.05em;margin-bottom:.5rem;">VIOLETA</div>
            <div style="font-size:1rem;color:rgba(255,255,255,.75);font-style:italic;margin-bottom:1.5rem;">
              "Informar, reconocer y actuar"
            </div>
            <div style="border-top:1px solid rgba(255,255,255,.15);padding-top:1.25rem;font-size:.85rem;color:rgba(255,255,255,.6);">
              Plataforma educativa sobre prevención de la violencia contra la mujer
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ════════════════ EQUIPO ══════════════════════════════════ -->
<section class="seccion seccion-lila" aria-labelledby="titulo-equipo">
  <div class="contenedor">
    <div class="seccion-titulo">
      <span class="badge badge-violeta mb-2">Desarrollador</span>
      <h2 id="titulo-equipo">Desarrollador del proyecto</h2>
      <div class="divider-violeta"></div>
      <p>Estudiante comprometido con el aprendizaje y la responsabilidad social.</p>
    </div>

    <div class="flex-center">
      <?php foreach ($integrantes as $dev): ?>
      <div class="dev-card" style="max-width:420px;width:100%;">
        <div class="dev-avatar" style="width:90px;height:90px;font-size:2.2rem;">
          <?= e($dev['inicial']) ?>
        </div>
        <h3 style="margin-bottom:.25rem;font-size:1.4rem;"><?= e($dev['nombre']) ?></h3>
        <span class="badge badge-violeta mb-3"><?= e($dev['rol']) ?></span>
        <p class="t-muted fs-sm" style="line-height:1.6;"><?= e($dev['descripcion']) ?></p>
        <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--borde);">
          <div style="display:flex;align-items:center;justify-content:center;gap:.5rem;font-size:.85rem;color:var(--texto-sec);">
            <i class="bi bi-building t-violeta" aria-hidden="true"></i>
            ITNC "Federico Álvarez Plata" &mdash; Nocturno, Bolivia
          </div>
          <div style="display:flex;align-items:center;justify-content:center;gap:.5rem;font-size:.85rem;color:var(--texto-sec);margin-top:.4rem;">
            <i class="bi bi-mortarboard t-violeta" aria-hidden="true"></i>
            Diseño y Programación Web III &mdash; Gestión 2026
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ════════════════ TECNOLOGÍAS ═════════════════════════════ -->
<section class="seccion seccion-blanca" aria-labelledby="titulo-tecno">
  <div class="contenedor">
    <div class="seccion-titulo">
      <span class="badge badge-violeta mb-2">Stack tecnológico</span>
      <h2 id="titulo-tecno">Tecnologías utilizadas</h2>
      <p>Herramientas seleccionadas por su solidez, accesibilidad y compatibilidad con XAMPP.</p>
    </div>

    <?php
    $tecnologias = [
      ['nombre'=>'HTML5',         'icono'=>'filetype-html', 'color'=>'#E34F26', 'desc'=>'Estructura semántica y accesible de todas las páginas.'],
      ['nombre'=>'CSS3',          'icono'=>'filetype-css',  'color'=>'#1572B6', 'desc'=>'Sistema de diseño personalizado con variables CSS y animaciones.'],
      ['nombre'=>'Bootstrap 5',   'icono'=>'bootstrap',     'color'=>'#7952B3', 'desc'=>'Componentes de interfaz para estructura de escritorio.'],
      ['nombre'=>'JavaScript',    'icono'=>'filetype-js',   'color'=>'#F7DF1E', 'desc'=>'Interactividad del quiz, modales y navegación dinámica.'],
      ['nombre'=>'PHP 8',         'icono'=>'filetype-php',  'color'=>'#777BB4', 'desc'=>'Lógica del servidor, sesiones, cálculo de puntuaciones y CSRF.'],
      ['nombre'=>'MySQL/MariaDB', 'icono'=>'database',      'color'=>'#4479A1', 'desc'=>'Base de datos relacional para preguntas, opciones e intentos.'],
      ['nombre'=>'PDO',           'icono'=>'shield-lock',   'color'=>'#21865A', 'desc'=>'Capa de acceso a datos con consultas preparadas y seguridad.'],
      ['nombre'=>'Google Fonts',  'icono'=>'type',          'color'=>'#4285F4', 'desc'=>'Tipografía Plus Jakarta Sans para una experiencia visual moderna.'],
      ['nombre'=>'Bootstrap Icons','icono'=>'grid-3x3-gap', 'color'=>'#5636C9', 'desc'=>'Iconografía consistente en toda la interfaz.'],
      ['nombre'=>'XAMPP',         'icono'=>'server',        'color'=>'#FB7A24', 'desc'=>'Entorno de desarrollo local con Apache, MySQL y PHP.'],
    ];
    ?>
    <div class="grid-4 gap-3">
      <?php foreach ($tecnologias as $t): ?>
      <div class="tarjeta" style="padding:1.5rem;text-align:center;">
        <div style="background:<?= $t['color'] ?>18;border-radius:var(--r-md);width:52px;height:52px;
                    display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;
                    font-size:1.4rem;color:<?= $t['color'] ?>;">
          <i class="bi bi-<?= $t['icono'] ?>" aria-hidden="true"></i>
        </div>
        <h5 style="font-size:.9rem;margin-bottom:.35rem;"><?= e($t['nombre']) ?></h5>
        <p class="fs-xs t-muted mb-0" style="line-height:1.4;"><?= e($t['desc']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ════════════════ FUNCIONALIDADES ═════════════════════════ -->
<section class="seccion seccion-lila" aria-labelledby="titulo-funciones">
  <div class="contenedor">
    <div class="seccion-titulo">
      <h2 id="titulo-funciones">Funcionalidades implementadas</h2>
    </div>
    <div class="grid-3 gap-3">
      <?php
      $funciones = [
        ['icono'=>'house-fill',          'titulo'=>'Página de inicio',         'desc'=>'Portada con hero visual, tarjetas de acceso y llamada a la acción.'],
        ['icono'=>'book-fill',           'titulo'=>'Sección informativa',      'desc'=>'6 categorías educativas con contenido detallado, ejemplos y mitos interactivos.'],
        ['icono'=>'patch-question-fill', 'titulo'=>'Quiz interactivo',         'desc'=>'Cuestionario de 10 preguntas aleatorias recuperadas desde MySQL con navegación completa.'],
        ['icono'=>'graph-up-arrow',      'titulo'=>'Resultados educativos',    'desc'=>'Página de resultados con porcentaje, estadísticas, mensajes motivadores y revisión detallada.'],
        ['icono'=>'people-fill',         'titulo'=>'Página de desarrolladores','desc'=>'Presentación del equipo, tecnologías y créditos del proyecto.'],
        ['icono'=>'shield-lock-fill',    'titulo'=>'Seguridad',                'desc'=>'Consultas PDO preparadas, protección CSRF, hashing de contraseñas y validación de sesiones.'],
        ['icono'=>'person-gear',         'titulo'=>'Panel administrativo',     'desc'=>'Gestión de preguntas, categorías, recursos e intentos con autenticación segura.'],
        ['icono'=>'box-arrow-right',     'titulo'=>'Salida rápida',            'desc'=>'Botón para abandonar el sitio de inmediato con nota de privacidad.'],
        ['icono'=>'database-fill',       'titulo'=>'Base de datos MySQL',      'desc'=>'Esquema completo con 25+ preguntas, 6 categorías y recursos institucionales.'],
      ];
      foreach ($funciones as $f): ?>
      <div class="tarjeta" style="padding:1.5rem;">
        <div style="display:flex;align-items:flex-start;gap:.75rem;">
          <div class="icono-circulo ic-violeta" style="width:44px;height:44px;font-size:1.1rem;flex-shrink:0;">
            <i class="bi bi-<?= $f['icono'] ?>" aria-hidden="true"></i>
          </div>
          <div>
            <h5 style="font-size:.95rem;margin-bottom:.3rem;"><?= e($f['titulo']) ?></h5>
            <p class="fs-xs t-muted mb-0" style="line-height:1.5;"><?= e($f['desc']) ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ════════════════ FUENTES Y CRÉDITOS ══════════════════════ -->
<section class="seccion seccion-blanca" aria-labelledby="titulo-fuentes">
  <div class="contenedor-sm">
    <h2 id="titulo-fuentes" class="mb-4">
      <i class="bi bi-journal-bookmark t-violeta" aria-hidden="true"></i>
      Fuentes consultadas y créditos
    </h2>

    <div class="alerta alerta-info mb-4">
      <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
      <span>El contenido educativo de VIOLETA se basa en fuentes institucionales verificadas. Los textos son adaptaciones educativas con fines académicos, no sustitutos de orientación legal o profesional.</span>
    </div>

    <?php
    $fuentes = [
      ['titulo'=>'Legislación boliviana',
       'items'=>[
         'Constitución Política del Estado Plurinacional de Bolivia (2009)',
         'Ley N.° 348 — Ley Integral para Garantizar a las Mujeres una Vida Libre de Violencia (2013)',
         'Ley N.° 243 — Ley contra el Acoso y Violencia Política hacia las Mujeres (2012)',
       ]],
      ['titulo'=>'Marcos internacionales',
       'items'=>[
         'Convención sobre la Eliminación de Todas las Formas de Discriminación contra la Mujer — CEDAW (ONU, 1979)',
         'Convención Interamericana para Prevenir, Sancionar y Erradicar la Violencia contra la Mujer — Belém do Pará (OEA, 1994)',
         'Declaración Universal de Derechos Humanos (ONU, 1948)',
       ]],
      ['titulo'=>'Instituciones y organismos',
       'items'=>[
         'Ministerio de Justicia y Transparencia Institucional — Bolivia (justicia.gob.bo)',
         'ONU Mujeres — Entidad de las Naciones Unidas para la Igualdad de Género (unwomen.org)',
         'SEPDAVI — Servicio Plurinacional de Defensa de la Víctima (sepdavi.gob.bo)',
         'Defensoría del Pueblo del Estado Plurinacional de Bolivia (defensoria.gob.bo)',
       ]],
      ['titulo'=>'Recursos técnicos',
       'items'=>[
         'Bootstrap 5 — getbootstrap.com',
         'Bootstrap Icons — icons.getbootstrap.com',
         'Google Fonts — fonts.google.com (Plus Jakarta Sans)',
       ]],
    ];
    foreach ($fuentes as $grupo): ?>
    <div class="tarjeta mb-3" style="padding:1.5rem;">
      <h5 style="color:var(--violeta);margin-bottom:1rem;font-size:.95rem;">
        <i class="bi bi-bookmark-fill" aria-hidden="true"></i> <?= e($grupo['titulo']) ?>
      </h5>
      <ul style="list-style:none;padding:0;margin:0;">
        <?php foreach ($grupo['items'] as $it): ?>
        <li style="display:flex;align-items:flex-start;gap:.5rem;padding:.4rem 0;
                   border-bottom:1px dashed var(--borde);font-size:.875rem;color:var(--texto);">
          <i class="bi bi-dot t-violeta" aria-hidden="true"></i><?= e($it) ?>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endforeach; ?>

    <div class="tarjeta" style="padding:1.75rem;background:var(--lila-claro);text-align:center;margin-top:2rem;">
      <p class="fs-sm t-muted mb-2">
        VIOLETA es un proyecto educativo sin fines de lucro, desarrollado para el curso académico 2026.
      </p>
      <p class="fs-xs t-muted mb-0">
        © 2026 VIOLETA · Diseño y Programación Web III ·
        ITNC "Federico Álvarez Plata" Nocturno · Bolivia
      </p>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
