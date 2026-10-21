<?php
/**
 * VIOLETA - Configuración global de la aplicación
 * Adaptada tanto para entornos locales (XAMPP) como para producción / hosting web.
 */

// ── Entorno ─────────────────────────────────────────────────────────────────
$entorno = getenv('VIOLETA_ENV') ?: ($_ENV['VIOLETA_ENV'] ?? 'development');
define('VIOLETA_ENV',     $entorno);   // 'development' | 'production'
define('VIOLETA_VERSION', '1.0.0');
define('VIOLETA_YEAR',    '2026');

// ── Rutas ────────────────────────────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));      // Ruta absoluta en el sistema de archivos del servidor

if (!defined('BASE_URL')) {
    $envBaseUrl = getenv('BASE_URL') ?: ($_ENV['BASE_URL'] ?? null);
    if ($envBaseUrl !== null) {
        define('BASE_URL', rtrim($envBaseUrl, '/'));
    } else {
        // Detección automática de la ruta base web respecto al DocumentRoot del servidor
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== ''
            ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']) ?: $_SERVER['DOCUMENT_ROOT'])
            : '';
        $appRoot = str_replace('\\', '/', realpath(BASE_PATH) ?: BASE_PATH);

        if (!empty($docRoot) && !empty($appRoot) && str_starts_with($appRoot, $docRoot)) {
            $subPath = substr($appRoot, strlen($docRoot));
            define('BASE_URL', rtrim(str_replace('\\', '/', $subPath), '/'));
        } else {
            // Fallback para entornos donde no se exponga DOCUMENT_ROOT
            define('BASE_URL', '/violeta');
        }
    }
}

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
