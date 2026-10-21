<?php
/**
 * VIOLETA - Configuración global de la aplicación
 */

// ── Entorno ─────────────────────────────────────────────────────────────────
define('VIOLETA_ENV',     'development');   // 'development' | 'production'
define('VIOLETA_VERSION', '1.0.0');
define('VIOLETA_YEAR',    '2026');

// ── Rutas ────────────────────────────────────────────────────────────────────
// Ajuste BASE_URL si el proyecto no está en la raíz de htdocs/violeta
define('BASE_URL',  '/violeta');
define('BASE_PATH', dirname(__DIR__));      // Ruta absoluta al directorio raíz

// ── Quiz ─────────────────────────────────────────────────────────────────────
define('QUIZ_NUM_PREGUNTAS', 10);           // Preguntas por intento

// ── Sesiones ─────────────────────────────────────────────────────────────────
define('SESSION_LIFETIME', 3600);           // 1 hora en segundos

// ── Mensajes de error genéricos ───────────────────────────────────────────────
define('MSG_ERROR_GENERICO', 'Ocurrió un error inesperado. Inténtelo de nuevo.');

// ── Mostrar errores PHP (solo en desarrollo) ──────────────────────────────────
if (VIOLETA_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// ── Zona horaria ─────────────────────────────────────────────────────────────
date_default_timezone_set('America/La_Paz');
