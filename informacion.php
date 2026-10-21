<?php
/**
 * VIOLETA — Sección informativa educativa (informacion.php)
 */
require_once __DIR__ . '/config/configuracion.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

iniciarSesionSegura();

// Categoría activa
$slugsValidos = [
    'tipos-violencia', 'senales-alerta', 'mitos-realidades',
    'derechos', 'prevencion', 'recursos-ayuda'
];
$catParam = isset($_GET['cat']) ? sanitizarTexto($_GET['cat']) : 'tipos-violencia';
if (!in_array($catParam, $slugsValidos)) {
    $catParam = 'tipos-violencia';
}

$categorias = obtenerCategorias();
$paginaActual = 'informacion';
$tituloPag    = 'Información educativa';

require_once __DIR__ . '/includes/header.php';
?>

<!-- ════════════════ CABECERA DE SECCIÓN ════════════════════════ -->
<div style="background:linear-gradient(135deg,var(--violeta-prof),#3a1d6e);padding:3rem 0;">
  <div class="contenedor">
    <span class="badge badge-violeta mb-2" style="background:rgba(233,221,253,.15);color:#E9DDFD;">
      <i class="bi bi-book" aria-hidden="true"></i> Contenido educativo
    </span>
    <h1 style="color:white;margin-bottom:.5rem;">Centro de información</h1>
    <p style="color:rgba(255,255,255,.7);max-width:550px;">
      Aprende a reconocer, comprender y prevenir la violencia contra la mujer
      a través de contenidos claros, respetuosos y basados en fuentes verificadas.
    </p>
  </div>
</div>

<!-- ════════════════ CUERPO PRINCIPAL ═══════════════════════════ -->
<section class="seccion-sm">
  <div class="contenedor">
    <div class="info-layout" style="gap:2rem;">

      <!-- ── Menú lateral ── -->
      <aside class="info-sidebar" aria-label="Categorías de información">
        <h4><i class="bi bi-grid" aria-hidden="true"></i> Categorías</h4>

        <?php foreach ($categorias as $cat): ?>
        <a href="informacion.php?cat=<?= e($cat['slug']) ?>"
           class="info-nav-item <?= $catParam === $cat['slug'] ? 'activo' : '' ?>"
           aria-current="<?= $catParam === $cat['slug'] ? 'page' : 'false' ?>">
          <i class="bi bi-<?= e($cat['icono']) ?>" aria-hidden="true"></i>
          <?= e($cat['nombre']) ?>
        </a>
        <?php endforeach; ?>

        <!-- Panel de ayuda rápida -->
        <div style="margin-top:2rem;background:var(--lila-claro);border-radius:var(--r-md);padding:1.1rem;">
          <h5 style="font-size:.8rem;color:var(--violeta-oscuro);margin-bottom:.5rem;">
            <i class="bi bi-lightbulb" aria-hidden="true"></i> Recuerda
          </h5>
          <p style="font-size:.8rem;color:var(--texto-sec);margin:0;line-height:1.5;">
            Este contenido es educativo. Si necesitas orientación específica, consulta
            a profesionales especializados o a instituciones de apoyo.
          </p>
        </div>
      </aside>

      <!-- ── Área de contenido principal ── -->
      <main id="contenido-principal" tabindex="-1">

        <?php if ($catParam === 'tipos-violencia'): ?>
        <!-- ═══════════════ TIPOS DE VIOLENCIA ══════════════════ -->
        <div class="flex-between mb-4" style="flex-wrap:wrap;gap:1rem;">
          <div>
            <span class="badge badge-violeta mb-1">Categoría</span>
            <h2>Tipos de violencia</h2>
            <p class="t-muted">La violencia contra la mujer puede manifestarse de muchas formas. Conocerlas es el primer paso para reconocerlas.</p>
          </div>
        </div>

        <div class="alerta alerta-info mb-4">
          <i class="bi bi-info-circle-fill fs-sm" aria-hidden="true"></i>
          <span>Las distintas formas de violencia pueden presentarse simultáneamente. Los ejemplos aquí descritos son orientativos y no constituyen diagnósticos ni definiciones legales vinculantes.</span>
        </div>

        <div class="violencia-grid">
          <?php
          $tipos = [
            [
              'icono' => 'shield-exclamation',
              'nombre' => 'Violencia física',
              'color'  => '#5636C9',
              'def'    => 'Cualquier acción que cause daño corporal directo: golpes, empujones, bofetadas, patadas, quemaduras u otras formas de agresión física.',
              'ejemplos' => [
                'Golpes, empujones o bofetadas',
                'Arrojar objetos con intención de herir',
                'Pellizcos, jalones de cabello, mordeduras',
                'Quemaduras o lesiones provocadas intencionalmente',
              ],
              'alertas' => 'Marcas físicas inexplicables, miedo a movimientos bruscos, excusas repetidas sobre lesiones.',
              'por_que' => 'Es la forma más visible de violencia, pero su visibilidad no la hace más grave que otras; todas causan daño real.',
            ],
            [
              'icono' => 'brain',
              'nombre' => 'Violencia psicológica',
              'color'  => '#D94FC1',
              'def'    => 'Conductas que dañan la salud mental, la autoestima y la autonomía de la persona: insultos, humillaciones, amenazas, manipulación y control emocional.',
              'ejemplos' => [
                'Insultos y humillaciones frecuentes',
                'Amenazas hacia la persona o sus seres queridos',
                'Manipulación y gaslighting',
                'Críticas constantes que erosionan la autoestima',
              ],
              'alertas' => 'Baja autoestima sin causa aparente, miedo constante, cambios en la personalidad, aislamiento progresivo.',
              'por_que' => 'Aunque no deja marcas visibles, puede causar daños duraderos en la salud mental y dificultar la toma de decisiones.',
            ],
            [
              'icono' => 'currency-dollar',
              'nombre' => 'Violencia económica y patrimonial',
              'color'  => '#A96913',
              'def'    => 'Control, negación o restricción del acceso a recursos económicos, bienes o trabajo con el objetivo de generar dependencia.',
              'ejemplos' => [
                'Negar acceso a dinero para necesidades básicas',
                'Impedir que la mujer trabaje o estudie',
                'Apropiarse de bienes, documentos o ingresos',
                'Generar deudas a nombre de la víctima sin su consentimiento',
              ],
              'alertas' => 'Dependencia económica total, no tener acceso a documentos propios, no poder tomar decisiones de gasto.',
              'por_que' => 'Genera dependencia que dificulta salir de una situación de violencia y afecta la autonomía a largo plazo.',
            ],
            [
              'icono' => 'lock',
              'nombre' => 'Violencia sexual',
              'color'  => '#5636C9',
              'def'    => 'Cualquier acto sexual realizado sin consentimiento libre, voluntario e informado, incluso dentro de relaciones de pareja o matrimoniales.',
              'ejemplos' => [
                'Actos sexuales sin consentimiento',
                'Presión o coerción para actos sexuales no deseados',
                'Difusión de contenido íntimo sin autorización',
                'Acoso sexual en cualquier entorno',
              ],
              'alertas' => 'El consentimiento puede retirarse en cualquier momento. "No" siempre significa "no", incluso en una relación de pareja.',
              'por_que' => 'Afecta la integridad física y emocional. Su invisibilización contribuye a la normalización de relaciones sin respeto mutuo.',
            ],
            [
              'icono' => 'phone-fill',
              'nombre' => 'Violencia digital',
              'color'  => '#D94FC1',
              'def'    => 'Uso de tecnologías digitales para acosar, controlar, amenazar, vigilar o difundir contenido sin consentimiento.',
              'ejemplos' => [
                'Publicar imágenes íntimas sin autorización',
                'Hackear cuentas o dispositivos para vigilar',
                'Amenazas y acoso por mensajes o redes sociales',
                'Control obsesivo de la actividad digital de la pareja',
              ],
              'alertas' => 'Verificación constante del teléfono, demanda de acceso a contraseñas, mensajes intimidatorios frecuentes.',
              'por_que' => 'La violencia digital puede tener consecuencias en la vida real y suele acompañar otras formas de violencia.',
            ],
            [
              'icono' => 'eye-slash',
              'nombre' => 'Violencia simbólica',
              'color'  => '#21865A',
              'def'    => 'Se reproduce mediante estereotipos, imágenes, discursos y normas culturales que subordinan, degradan o invisibilizan a las mujeres.',
              'ejemplos' => [
                'Publicidad que utiliza el cuerpo de la mujer de forma degradante',
                'Chistes o expresiones que minimizan la violencia',
                'Estereotipos que limitan los roles de la mujer',
                'Normalización de conductas de control como "señales de amor"',
              ],
              'alertas' => 'Aunque parece invisible, refuerza actitudes que facilitan otras formas de violencia.',
              'por_que' => 'Cuestionar los mensajes culturales que normalizan la desigualdad contribuye a construir entornos más respetuosos.',
            ],
          ];
          foreach ($tipos as $t): ?>
          <div class="violencia-tarjeta" style="border-top-color:<?= $t['color'] ?>;">
            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
              <div class="icono-circulo" style="background:<?= $t['color'] ?>22;color:<?= $t['color'] ?>;width:44px;height:44px;font-size:1.2rem;">
                <i class="bi bi-<?= $t['icono'] ?>" aria-hidden="true"></i>
              </div>
              <h4 style="color:<?= $t['color'] ?>;margin:0;"><?= $t['nombre'] ?></h4>
            </div>
            <p class="fs-sm" style="color:var(--texto);margin-bottom:.75rem;"><?= $t['def'] ?></p>

            <details style="margin-bottom:.75rem;">
              <summary style="font-size:.85rem;font-weight:600;color:var(--violeta);cursor:pointer;list-style:none;display:flex;align-items:center;gap:.4rem;">
                <i class="bi bi-chevron-right" aria-hidden="true"></i> Ejemplos cotidianos
              </summary>
              <ul class="violencia-ejemplos mt-2">
                <?php foreach ($t['ejemplos'] as $ej): ?>
                <li><?= $ej ?></li>
                <?php endforeach; ?>
              </ul>
            </details>

            <div style="background:var(--lila-claro);border-radius:var(--r-sm);padding:.75rem;font-size:.8rem;color:var(--violeta-oscuro);border-left:3px solid <?= $t['color'] ?>;">
              <strong><i class="bi bi-lightbulb" aria-hidden="true"></i> ¿Por qué es importante reconocerla?</strong><br>
              <?= $t['por_que'] ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div><!-- /violencia-grid -->

        <?php elseif ($catParam === 'senales-alerta'): ?>
        <!-- ═══════════════ SEÑALES DE ALERTA ═══════════════════ -->
        <h2 class="mb-2">Señales de alerta</h2>
        <p class="t-muted mb-4">
          Las siguientes situaciones pueden indicar un entorno o relación problemática.
          Ninguna señal aislada permite "diagnosticar" automáticamente una situación; lo importante
          es prestar atención a patrones sostenidos en el tiempo.
        </p>

        <div class="alerta alerta-aviso mb-4">
          <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
          <span>Esta información es orientativa. Si reconoces varias de estas conductas de forma frecuente, considera buscar orientación con una persona de confianza o un servicio especializado.</span>
        </div>

        <?php
        $senales = [
          ['icono'=>'person-lock',        'color'=>'ic-violeta', 'titulo'=>'Control excesivo de actividades',
           'desc'=>'Controlar dónde va la persona, con quién se reúne, cómo viste o cómo usa su tiempo libre. La autonomía es un derecho.'],
          ['icono'=>'people-fill',        'color'=>'ic-magenta', 'titulo'=>'Aislamiento de familiares y amistades',
           'desc'=>'Separar progresivamente a la persona de su red de apoyo para aumentar su dependencia y reducir sus opciones.'],
          ['icono'=>'chat-quote-fill',    'color'=>'ic-ambar',   'titulo'=>'Humillaciones frecuentes',
           'desc'=>'Insultos, críticas constantes o ridiculizaciones, especialmente en presencia de otras personas, que dañan la autoestima.'],
          ['icono'=>'exclamation-octagon','color'=>'ic-violeta', 'titulo'=>'Amenazas e intimidación',
           'desc'=>'Amenazas directas o veladas hacia la persona, su familia o sus posesiones como forma de control y coerción.'],
          ['icono'=>'cash-coin',          'color'=>'ic-verde',   'titulo'=>'Control económico',
           'desc'=>'Negar o limitar el acceso al dinero, impedir trabajar o estudiar, o tomar decisiones económicas unilateralmente.'],
          ['icono'=>'phone-landscape',    'color'=>'ic-magenta', 'titulo'=>'Vigilancia de comunicaciones',
           'desc'=>'Revisar mensajes, redes sociales o correos sin consentimiento; exigir acceso a contraseñas o dispositivos.'],
          ['icono'=>'lock-fill',          'color'=>'ic-violeta', 'titulo'=>'Presión o coerción sexual',
           'desc'=>'Presionar para actos sexuales no deseados, ignorar la negativa o utilizar la culpa para obtener sexo.'],
          ['icono'=>'funnel-fill',        'color'=>'ic-ambar',   'titulo'=>'Manipulación emocional',
           'desc'=>'Negar hechos ocurridos, hacer sentir a la persona "exagerada" o "loca", o usar el afecto de forma condicional.'],
        ];
        ?>
        <div class="grid-2 gap-3">
          <?php foreach ($senales as $s): ?>
          <div class="tarjeta tarjeta-educativa" style="padding:1.5rem;">
            <div style="display:flex;align-items:flex-start;gap:1rem;">
              <div class="icono-circulo <?= $s['color'] ?>">
                <i class="bi bi-<?= $s['icono'] ?>" aria-hidden="true"></i>
              </div>
              <div>
                <h4 style="font-size:1rem;margin-bottom:.4rem;"><?= $s['titulo'] ?></h4>
                <p class="fs-sm t-muted" style="margin:0;line-height:1.55;"><?= $s['desc'] ?></p>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="tarjeta" style="padding:1.75rem;margin-top:2rem;background:var(--lila-claro);">
          <h4><i class="bi bi-info-circle t-violeta" aria-hidden="true"></i> Importante</h4>
          <p class="fs-sm t-muted mb-0" style="line-height:1.6;">
            Una señal aislada no define una relación. Sin embargo, la presencia de varias de estas conductas de forma
            repetida y sostenida puede indicar una dinámica dañina. Hablar con alguien de confianza o con un
            profesional especializado puede ayudar a clarificar la situación.
          </p>
        </div>

        <?php elseif ($catParam === 'mitos-realidades'): ?>
        <!-- ═══════════════ MITOS Y REALIDADES ══════════════════ -->
        <h2 class="mb-2">Mitos y realidades</h2>
        <p class="t-muted mb-4">
          Muchas ideas erróneas circulan sobre la violencia contra la mujer. Desmontarlas
          es fundamental para construir una comprensión más justa y precisa.
          Haz clic en cada mito para conocer la realidad.
        </p>
        <?php
        $mitos = [
          ['mito'=>'"Si no hay golpes, no hay violencia."',
           'realidad'=>'La violencia puede ser psicológica, económica, sexual, digital o simbólica. No requiere daño físico para ser real y causar un impacto profundo en la persona afectada.'],
          ['mito'=>'"Los celos son una prueba de amor."',
           'realidad'=>'Los celos patológicos son una forma de control, no de amor. Una relación saludable se construye sobre la confianza, el respeto y la autonomía de cada persona.'],
          ['mito'=>'"La víctima provoca o es responsable de lo que ocurre."',
           'realidad'=>'La responsabilidad de ejercer violencia corresponde siempre y únicamente a quien la ejerce. Ninguna conducta, forma de vestir o decisión de la víctima justifica ni provoca la violencia.'],
          ['mito'=>'"Pedir ayuda es señal de debilidad."',
           'realidad'=>'Buscar orientación y apoyo requiere valentía y capacidad de reconocer una situación difícil. Es un paso que puede contribuir a protegerse y acceder a recursos.'],
          ['mito'=>'"La violencia solo ocurre en familias de bajos ingresos."',
           'realidad'=>'La violencia contra la mujer se presenta en todos los contextos sociales, económicos, culturales y educativos. Limitarla a ciertos grupos invisibiliza muchos casos.'],
          ['mito'=>'"Si se queda en la relación, es porque quiere."',
           'realidad'=>'Salir de una relación violenta puede ser muy difícil por múltiples razones: miedo, dependencia económica, aislamiento, vínculos afectivos o falta de información sobre recursos disponibles.'],
          ['mito'=>'"Lo que pasa entre una pareja es un asunto privado."',
           'realidad'=>'La violencia no es un asunto privado cuando afecta los derechos fundamentales de una persona. Las leyes y los servicios de protección existen precisamente para garantizar esos derechos.'],
          ['mito'=>'"Las mujeres que denuncian mienten o exageran."',
           'realidad'=>'Las denuncias falsas son minoritarias y los sistemas institucionales existen para verificarlas. La desconfianza sistemática hacia las víctimas dificulta el acceso a la protección y la justicia.'],
        ];
        foreach ($mitos as $i => $m):
          $id = 'mito-' . $i;
        ?>
        <div class="mito-tarjeta">
          <div class="mito-cabecera"
               onclick="toggleMito('<?= $id ?>')"
               aria-expanded="false"
               aria-controls="<?= $id ?>-cuerpo"
               role="button"
               tabindex="0"
               onkeypress="if(event.key==='Enter')toggleMito('<?= $id ?>')">
            <div style="display:flex;align-items:center;gap:.75rem;">
              <span class="badge badge-ambar">MITO</span>
              <h5><?= $m['mito'] ?></h5>
            </div>
            <i class="bi bi-chevron-down t-violeta" id="icon-<?= $id ?>" aria-hidden="true"></i>
          </div>
          <div class="mito-cuerpo" id="<?= $id ?>-cuerpo">
            <div style="display:flex;align-items:flex-start;gap:.75rem;">
              <span class="badge badge-verde" style="flex-shrink:0;">REALIDAD</span>
              <p style="margin:0;color:var(--texto);line-height:1.6;"><?= $m['realidad'] ?></p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

        <?php elseif ($catParam === 'derechos'): ?>
        <!-- ═══════════════ DERECHOS ════════════════════════════ -->
        <h2 class="mb-2">Derechos de las mujeres</h2>
        <p class="t-muted mb-4">
          Las mujeres tienen derechos reconocidos a nivel nacional e internacional.
          Conocerlos es fundamental para ejercerlos y defenderlos.
        </p>

        <div class="alerta alerta-info mb-4">
          <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
          <span>La información que se presenta es de carácter educativo. Para orientación legal específica, consulte a una persona profesional del derecho o a las instituciones competentes.</span>
        </div>

        <!-- Marco boliviano -->
        <div class="tarjeta mb-4" style="padding:2rem;">
          <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;">
            <div class="icono-circulo ic-violeta">
              <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
            </div>
            <div>
              <h3 style="margin:0;">Marco legal en Bolivia</h3>
              <span class="badge badge-verde">Legislación nacional</span>
            </div>
          </div>
          <?php
          $leyes = [
            ['nombre'=>'Constitución Política del Estado (2009)',
             'desc'=>'Reconoce el derecho a la vida, a la integridad personal y a vivir libres de violencia como derechos fundamentales de las mujeres bolivianas.'],
            ['nombre'=>'Ley N.° 348 — Ley Integral para Garantizar a las Mujeres una Vida Libre de Violencia (2013)',
             'desc'=>'Define 16 formas de violencia contra las mujeres, establece mecanismos de atención, protección y sanción, y crea servicios especializados. Es la principal norma en la materia en Bolivia.'],
            ['nombre'=>'Ley N.° 243 — Ley contra el Acoso y Violencia Política hacia las Mujeres (2012)',
             'desc'=>'Protege los derechos políticos de las mujeres y sanciona conductas de acoso y violencia en el ámbito político.'],
            ['nombre'=>'Ley N.° 807 — Ley de Identidad de Género (2016)',
             'desc'=>'Reconoce el derecho a la identidad de género y la protección de las personas en el ejercicio de sus derechos.'],
          ];
          foreach ($leyes as $l): ?>
          <div style="padding:1rem;border-left:3px solid var(--violeta);background:var(--lila-claro);border-radius:0 var(--r-sm) var(--r-sm) 0;margin-bottom:1rem;">
            <h5 style="font-size:.95rem;margin-bottom:.3rem;color:var(--violeta-oscuro);"><?= $l['nombre'] ?></h5>
            <p class="fs-sm t-muted mb-0"><?= $l['desc'] ?></p>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Marcos internacionales -->
        <div class="tarjeta mb-4" style="padding:2rem;">
          <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;">
            <div class="icono-circulo ic-verde">
              <i class="bi bi-globe2" aria-hidden="true"></i>
            </div>
            <div>
              <h3 style="margin:0;">Marcos internacionales</h3>
              <span class="badge badge-verde">Instrumentos ratificados</span>
            </div>
          </div>
          <?php
          $internacionales = [
            ['nombre'=>'Convención sobre la Eliminación de Todas las Formas de Discriminación contra la Mujer (CEDAW)',
             'desc'=>'Adoptada por la ONU en 1979. Establece que los Estados deben garantizar la igualdad de derechos entre mujeres y hombres y eliminar toda forma de discriminación.'],
            ['nombre'=>'Convención de Belém do Pará (1994)',
             'desc'=>'Instrumento interamericano que define la violencia contra la mujer, reconoce el derecho a vivir libre de violencia y establece obligaciones para los Estados miembros, entre ellos Bolivia.'],
            ['nombre'=>'Declaración Universal de Derechos Humanos (1948)',
             'desc'=>'Proclama derechos fundamentales que corresponden a todas las personas sin distinción, incluidos el derecho a la vida, la seguridad y la dignidad.'],
          ];
          foreach ($internacionales as $i): ?>
          <div style="padding:1rem;border-left:3px solid var(--verde);background:#f0faf5;border-radius:0 var(--r-sm) var(--r-sm) 0;margin-bottom:1rem;">
            <h5 style="font-size:.95rem;margin-bottom:.3rem;color:#145a38;"><?= $i['nombre'] ?></h5>
            <p class="fs-sm t-muted mb-0"><?= $i['desc'] ?></p>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="alerta alerta-aviso">
          <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
          <span>Para consultar los textos legales completos y actualizados, acceda al Servicio de Gestión de la Información Normativa del Estado Plurinacional de Bolivia o consulte a una persona profesional del derecho.</span>
        </div>

        <?php elseif ($catParam === 'prevencion'): ?>
        <!-- ═══════════════ PREVENCIÓN Y ACTUACIÓN ═══════════════ -->
        <h2 class="mb-2">Prevención y actuación</h2>
        <p class="t-muted mb-4">
          La prevención de la violencia es responsabilidad de toda la comunidad.
          Aquí encontrarás orientación sobre cómo actuar en distintas situaciones.
        </p>
        <?php
        $bloques = [
          ['icono'=>'ear-fill','color'=>'#5636C9','titulo'=>'Si alguien te cuenta su situación',
           'puntos'=>[
             'Escucha sin interrumpir ni juzgar.',
             'Valida sus sentimientos: "Entiendo que esto es muy difícil para ti".',
             'No minimices la situación ni le digas qué hacer de inmediato.',
             'Ofrece acompañamiento respetando su ritmo y sus decisiones.',
             'Comparte información sobre recursos de orientación disponibles.',
             'No divulgues su situación sin su consentimiento.',
           ]],
          ['icono'=>'person-fill','color'=>'#D94FC1','titulo'=>'Si tú estás viviendo una situación difícil',
           'puntos'=>[
             'Habla con alguien de confianza si te sientes segura para hacerlo.',
             'Anota situaciones importantes (fechas, hechos, testigos) si puedes hacerlo con seguridad.',
             'Busca información sobre servicios de orientación disponibles en tu municipio.',
             'Recuerda que lo que ocurre no es tu culpa.',
             'Considera comunicarte con el SLIM de tu municipio o la Fiscalía.',
             'Tu seguridad es prioritaria; confía en tu percepción de la situación.',
           ]],
          ['icono'=>'people-fill','color'=>'#21865A','titulo'=>'Si presencias una situación de violencia',
           'puntos'=>[
             'En situaciones de riesgo inmediato, llama a los servicios de emergencia.',
             'Si es seguro, acércate discretamente a la persona afectada y pregunta cómo puede ayudarla.',
             'No confrontes directamente al agresor si esto puede aumentar el riesgo.',
             'No grabes ni publiques la situación sin el consentimiento de la persona afectada.',
             'Ofrece acompañamiento sin presionar.',
             'Respeta la decisión de la persona sobre cómo quiere manejar la situación.',
           ]],
        ];
        foreach ($bloques as $b): ?>
        <div class="tarjeta mb-3" style="padding:2rem;">
          <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;">
            <div class="icono-circulo" style="background:<?= $b['color'] ?>22;color:<?= $b['color'] ?>;font-size:1.3rem;">
              <i class="bi bi-<?= $b['icono'] ?>" aria-hidden="true"></i>
            </div>
            <h4 style="margin:0;color:<?= $b['color'] ?>;"><?= $b['titulo'] ?></h4>
          </div>
          <ul style="list-style:none;padding:0;margin:0;">
            <?php foreach ($b['puntos'] as $p): ?>
            <li style="display:flex;align-items:flex-start;gap:.75rem;padding:.5rem 0;border-bottom:1px dashed var(--borde);font-size:.9rem;color:var(--texto);">
              <i class="bi bi-check-circle-fill" style="color:<?= $b['color'] ?>;margin-top:.1rem;flex-shrink:0;" aria-hidden="true"></i>
              <?= $p ?>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endforeach; ?>

        <div class="tarjeta" style="padding:1.75rem;background:var(--lila-claro);margin-top:1rem;">
          <h4><i class="bi bi-heart-fill t-violeta" aria-hidden="true"></i> Una nota importante</h4>
          <p class="fs-sm t-muted mb-0" style="line-height:1.6;">
            No existe una respuesta única o correcta para todas las situaciones. Cada caso es diferente y las decisiones
            deben tomarse considerando la seguridad de la persona involucrada. Esta información es orientativa
            y no sustituye la evaluación de un profesional especializado.
          </p>
        </div>

        <?php elseif ($catParam === 'recursos-ayuda'): ?>
        <!-- ═══════════════ RECURSOS DE AYUDA ════════════════════ -->
        <h2 class="mb-2">Recursos de orientación</h2>
        <p class="t-muted mb-4">
          A continuación encontrarás instituciones y servicios de orientación disponibles en Bolivia.
          La información ha sido verificada a nivel institucional; los datos de contacto específicos
          pueden variar según el municipio o departamento.
        </p>

        <div class="alerta alerta-aviso mb-4">
          <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
          <span>Los datos de contacto detallados (teléfonos, direcciones) varían por municipio y pueden cambiar. Consulte directamente a la institución o a su gobierno municipal para obtener información actualizada.</span>
        </div>

        <?php
        $recursos = obtenerRecursos();
        foreach ($recursos as $r): ?>
        <div class="tarjeta mb-3" style="padding:1.75rem;">
          <div class="flex-between" style="flex-wrap:wrap;gap:.75rem;margin-bottom:1rem;">
            <div style="display:flex;align-items:center;gap:.75rem;">
              <div class="icono-circulo ic-violeta">
                <i class="bi bi-building" aria-hidden="true"></i>
              </div>
              <div>
                <h4 style="margin:0;font-size:1.05rem;"><?= e($r['nombre']) ?></h4>
                <span class="badge badge-violeta fs-xs"><?= e($r['tipo_recurso']) ?></span>
              </div>
            </div>
            <?php if ($r['url_oficial']): ?>
            <a href="<?= e($r['url_oficial']) ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="btn btn-contorno btn-sm">
              <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Sitio oficial
            </a>
            <?php endif; ?>
          </div>
          <p class="fs-sm" style="margin-bottom:.75rem;"><?= e($r['descripcion']) ?></p>
          <?php if ($r['contacto']): ?>
          <div style="background:var(--lila-claro);border-radius:var(--r-sm);padding:.75rem;font-size:.85rem;color:var(--violeta-oscuro);">
            <i class="bi bi-telephone" aria-hidden="true"></i> <?= e($r['contacto']) ?>
          </div>
          <?php endif; ?>
          <?php if ($r['nota_verificacion']): ?>
          <p class="fs-xs t-muted mt-2 mb-0">
            <i class="bi bi-info-circle" aria-hidden="true"></i> <?= e($r['nota_verificacion']) ?>
          </p>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <div class="tarjeta" style="padding:1.75rem;background:var(--lila-claro);margin-top:1rem;">
          <h4><i class="bi bi-telephone-heart t-violeta" aria-hidden="true"></i> Recomendación general</h4>
          <p class="fs-sm t-muted mb-0" style="line-height:1.6;">
            En Bolivia, puede acudir al <strong>SLIM (Servicio Legal Integral Municipal)</strong> de su municipio,
            disponible de forma gratuita, o a la <strong>Fiscalía</strong> más cercana.
            En situaciones de riesgo inmediato para la vida, contacte a los servicios de emergencia disponibles en su área.
          </p>
        </div>

        <?php endif; ?>
        <!-- FIN del bloque de categorías -->

      </main><!-- /contenido-principal -->
    </div><!-- /info-layout -->
  </div><!-- /contenedor -->
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
