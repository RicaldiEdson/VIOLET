/**
 * VIOLETA — JavaScript Principal de la Plataforma Educativa
 * Gestión 2026 - ITNC "Federico Álvarez Plata" Nocturno
 */

document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  // ── 1. Salida Rápida (Protección y Privacidad) ──────────────────────────────
  const URL_SALIDA_DEFECTO = 'https://www.google.com';

  const ejecutarSalidaRapida = () => {
    // Reemplaza la URL en el historial para dificultar volver con el botón 'Atrás'
    try {
      window.location.replace(URL_SALIDA_DEFECTO);
    } catch (e) {
      window.location.href = URL_SALIDA_DEFECTO;
    }
  };

  // Botones con clase .btn-salida-rapida o atributo data-salida-rapida
  document.querySelectorAll('.btn-salida-rapida, [data-salida-rapida]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      ejecutarSalidaRapida();
    });
  });

  // Atajo de teclado: Pulsar 'Escape' 2 veces consecutivas o combinación 'Alt + Q'
  let escapeCount = 0;
  let escapeTimer = null;

  document.addEventListener('keydown', (e) => {
    // Alt + Q
    if (e.altKey && (e.key === 'q' || e.key === 'Q')) {
      e.preventDefault();
      ejecutarSalidaRapida();
      return;
    }

    // Doble pulsación de ESC en menos de 800ms
    if (e.key === 'Escape') {
      escapeCount++;
      if (escapeCount === 1) {
        escapeTimer = setTimeout(() => {
          escapeCount = 0;
        }, 800);
      } else if (escapeCount >= 2) {
        clearTimeout(escapeTimer);
        ejecutarSalidaRapida();
      }
    }
  });

  // ── 2. Inicialización de Tooltips de Bootstrap ───────────────────────────────
  if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
  }

  // ── 3. Copiar Números de Emergencia al Portapapeles ─────────────────────────
  document.querySelectorAll('[data-copiar-contacto]').forEach(elem => {
    elem.addEventListener('click', async (e) => {
      e.preventDefault();
      const texto = elem.getAttribute('data-copiar-contacto') || elem.innerText.trim();

      try {
        await navigator.clipboard.writeText(texto);
        const originalHtml = elem.innerHTML;
        elem.innerHTML = '<i class="bi bi-check-circle-fill me-1 text-success"></i> ¡Copiado!';
        elem.classList.add('btn-success-subtle');

        setTimeout(() => {
          elem.innerHTML = originalHtml;
          elem.classList.remove('btn-success-subtle');
        }, 2200);
      } catch (err) {
        console.error('Error al copiar al portapapeles:', err);
      }
    });
  });

  // ── 4. Filtro de Búsqueda Rápida en Directorio de Ayuda ─────────────────────
  const buscadorRecursos = document.getElementById('buscador-recursos');
  if (buscadorRecursos) {
    buscadorRecursos.addEventListener('input', (e) => {
      const termino = e.target.value.toLowerCase().trim();
      const tarjetas = document.querySelectorAll('.tarjeta-recurso-ayuda');

      tarjetas.forEach(tarjeta => {
        const texto = tarjeta.textContent.toLowerCase();
        if (texto.includes(termino)) {
          tarjeta.style.display = '';
        } else {
          tarjeta.style.display = 'none';
        }
      });
    });
  }

  // ── 5. Desplazamiento Suave para Anclas Internas ────────────────────────────
  document.querySelectorAll('a[href^="#"]:not([href="#"]):not([data-bs-toggle])').forEach(ancla => {
    ancla.addEventListener('click', function (e) {
      const objetivo = document.querySelector(this.getAttribute('href'));
      if (objetivo) {
        e.preventDefault();
        objetivo.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

  // ── 6. Cierre Automático de Alertas Informativas ─────────────────────────────
  document.querySelectorAll('.alert-auto-dismiss').forEach(alerta => {
    setTimeout(() => {
      alerta.classList.remove('show');
      setTimeout(() => alerta.remove(), 400);
    }, 4500);
  });
});
