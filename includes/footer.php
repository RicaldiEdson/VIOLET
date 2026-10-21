<?php
/**
 * VIOLETA — Pie de página compartido de páginas públicas
 */
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/configuracion.php';
}
?>
<!-- ═══════════════════════════════ FOOTER ═══════════════════════════════ -->
<footer class="footer-violeta" role="contentinfo">
  <div class="contenedor">
    <div class="footer-grid">

      <!-- Columna 1: Identidad -->
      <div>
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem;">
          <svg width="36" height="36" viewBox="0 0 38 38" aria-hidden="true">
            <circle cx="19" cy="19" r="19" fill="#5636C9"/>
            <circle cx="19" cy="14" r="5" fill="#E9DDFD"/>
            <ellipse cx="13" cy="24" rx="4" ry="3" fill="#D94FC1" opacity=".8"/>
            <ellipse cx="25" cy="24" rx="4" ry="3" fill="#D94FC1" opacity=".8"/>
            <circle cx="19" cy="19" r="3" fill="white"/>
            <line x1="19" y1="22" x2="19" y2="30" stroke="white" stroke-width="2" stroke-linecap="round"/>
            <line x1="15" y1="27" x2="23" y2="27" stroke="white" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <div>
            <div style="font-size:1.3rem;font-weight:800;color:#fff;letter-spacing:.04em;">VIOLETA</div>
            <div style="font-size:.65rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.08em;">Informar · Reconocer · Actuar</div>
          </div>
        </div>
        <p style="font-size:.9rem;color:rgba(255,255,255,.6);max-width:280px;line-height:1.6;">
          Plataforma educativa sobre prevención de la violencia contra la mujer.
          Contenido informativo para estudiantes, jóvenes y comunidad en general.
        </p>
        <div class="footer-nota-educativa" style="margin-top:1.25rem;">
          <i class="bi bi-info-circle" aria-hidden="true"></i>
          El contenido de este sitio es educativo y no sustituye la orientación legal ni la asistencia profesional especializada.
        </div>
      </div>

      <!-- Columna 2: Navegación -->
      <div>
        <h5>Secciones</h5>
        <ul>
          <li><a href="<?= BASE_URL ?>/index.php"><i class="bi bi-house me-1" aria-hidden="true"></i>Inicio</a></li>
          <li><a href="<?= BASE_URL ?>/informacion.php"><i class="bi bi-book me-1" aria-hidden="true"></i>Información educativa</a></li>
          <li><a href="<?= BASE_URL ?>/quiz.php"><i class="bi bi-patch-question me-1" aria-hidden="true"></i>Quiz interactivo</a></li>
          <li><a href="<?= BASE_URL ?>/desarrolladores.php"><i class="bi bi-person-badge me-1" aria-hidden="true"></i>Desarrollador</a></li>
        </ul>
      </div>

      <!-- Columna 3: Temas -->
      <div>
        <h5>Temas</h5>
        <ul>
          <li><a href="<?= BASE_URL ?>/informacion.php?cat=tipos-violencia">Tipos de violencia</a></li>
          <li><a href="<?= BASE_URL ?>/informacion.php?cat=senales-alerta">Señales de alerta</a></li>
          <li><a href="<?= BASE_URL ?>/informacion.php?cat=mitos-realidades">Mitos y realidades</a></li>
          <li><a href="<?= BASE_URL ?>/informacion.php?cat=derechos">Derechos de las mujeres</a></li>
          <li><a href="<?= BASE_URL ?>/informacion.php?cat=prevencion">Prevención y actuación</a></li>
          <li><a href="<?= BASE_URL ?>/informacion.php?cat=recursos-ayuda">Recursos de ayuda</a></li>
        </ul>
      </div>

      <!-- Columna 4: Institución -->
      <div>
        <h5>Institución</h5>
        <ul>
          <li style="color:rgba(255,255,255,.6);font-size:.9rem;margin-bottom:.5rem;">
            <i class="bi bi-building" aria-hidden="true"></i>
            Instituto Técnico Nacional de Comercio<br>
            "Federico Álvarez Plata" — Nocturno
          </li>
          <li style="color:rgba(255,255,255,.6);font-size:.9rem;margin-bottom:.5rem;">
            <i class="bi bi-mortarboard" aria-hidden="true"></i>
            Diseño y Programación Web III
          </li>
          <li style="color:rgba(255,255,255,.6);font-size:.9rem;">
            <i class="bi bi-calendar" aria-hidden="true"></i>
            Gestión <?= VIOLETA_YEAR ?>
          </li>
        </ul>
      </div>

    </div><!-- /footer-grid -->

    <hr class="footer-divider">

    <div class="footer-bottom">
      <span>© <?= VIOLETA_YEAR ?> VIOLETA · Proyecto educativo · Bolivia</span>
      <span>
        Hecho con <span style="color:#D94FC1;">♥</span> para aprender y prevenir
      </span>
    </div>

  </div>
</footer>
<!-- ══════════════════════════════════════════════════════════════════════ -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- App JS -->
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
