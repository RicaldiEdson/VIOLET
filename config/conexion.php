<?php
/**
 * VIOLETA - Conexión centralizada a la base de datos
 * 
 * Compatible con:
 * 1. Entorno local (XAMPP por defecto: 127.0.0.1, puerto 3307, usuario root)
 * 2. Variables de entorno de hosting (DB_HOST, DB_USER, etc., o DATABASE_URL / MYSQL_URL)
 * 3. Archivo local protegido config/conexion.local.php o .env (no versionados en git)
 */

// 1. Cargar archivo local protegido si existe
if (file_exists(__DIR__ . '/conexion.local.php')) {
    require_once __DIR__ . '/conexion.local.php';
}

// 2. Cargar archivo .env si existe en la raíz del proyecto o en config/
$rutasEnv = [
    dirname(__DIR__) . '/.env',
    __DIR__ . '/.env',
];
foreach ($rutasEnv as $rutaEnv) {
    if (file_exists($rutaEnv) && is_readable($rutaEnv)) {
        $lineas = file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if ($linea === '' || str_starts_with($linea, '#')) {
                continue;
            }
            if (str_contains($linea, '=')) {
                [$nombre, $valor] = explode('=', $linea, 2);
                $nombre = trim($nombre);
                $valor  = trim($valor, " \t\n\r\0\x0B\"'");
                if (!array_key_exists($nombre, $_SERVER) && !array_key_exists($nombre, $_ENV)) {
                    putenv("{$nombre}={$valor}");
                    $_ENV[$nombre] = $valor;
                    $_SERVER[$nombre] = $valor;
                }
            }
        }
        break;
    }
}

// 3. Función auxiliar para leer variables de entorno con fallback
if (!function_exists('obtenerEnvVioleta')) {
    function obtenerEnvVioleta(string $clave, ?string $porDefecto = null): ?string
    {
        $val = getenv($clave);
        if ($val !== false && $val !== '') {
            return $val;
        }
        if (isset($_ENV[$clave]) && $_ENV[$clave] !== '') {
            return $_ENV[$clave];
        }
        if (isset($_SERVER[$clave]) && $_SERVER[$clave] !== '') {
            return $_SERVER[$clave];
        }
        return $porDefecto;
    }
}

// 4. Soporte para URL unificada de base de datos (DATABASE_URL / MYSQL_URL en PaaS como Render o Railway)
$dbUrl = obtenerEnvVioleta('DATABASE_URL', obtenerEnvVioleta('MYSQL_URL'));
$urlHost = null; $urlPort = null; $urlName = null; $urlUser = null; $urlPass = null;
if (!empty($dbUrl)) {
    $parsedUrl = parse_url($dbUrl);
    if ($parsedUrl) {
        $urlHost = $parsedUrl['host'] ?? null;
        $urlPort = isset($parsedUrl['port']) ? (string)$parsedUrl['port'] : null;
        $urlUser = $parsedUrl['user'] ?? null;
        $urlPass = $parsedUrl['pass'] ?? null;
        $urlName = isset($parsedUrl['path']) ? ltrim($parsedUrl['path'], '/') : null;
    }
}

// 5. Definición de constantes con prioridad:
// Constante ya definida > URL unificada > Variable de entorno > Configuración local XAMPP
if (!defined('DB_HOST')) {
    define('DB_HOST', $urlHost ?? obtenerEnvVioleta('DB_HOST', obtenerEnvVioleta('MYSQLHOST', '127.0.0.1')));
}
if (!defined('DB_PORT')) {
    define('DB_PORT', $urlPort ?? obtenerEnvVioleta('DB_PORT', obtenerEnvVioleta('MYSQLPORT', '3307')));
}
if (!defined('DB_NAME')) {
    define('DB_NAME', $urlName ?? obtenerEnvVioleta('DB_NAME', obtenerEnvVioleta('MYSQLDATABASE', 'violeta_db')));
}
if (!defined('DB_USER')) {
    define('DB_USER', $urlUser ?? obtenerEnvVioleta('DB_USER', obtenerEnvVioleta('MYSQLUSER', 'root')));
}
if (!defined('DB_PASS')) {
    define('DB_PASS', $urlPass ?? obtenerEnvVioleta('DB_PASS', obtenerEnvVioleta('MYSQLPASSWORD', '')));
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', obtenerEnvVioleta('DB_CHARSET', 'utf8mb4'));
}

/**
 * Retorna una instancia PDO reutilizable.
 */
function obtenerConexion(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND =>
            'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
    ];

    try {
        $pdo = new PDO(
            $dsn,
            DB_USER,
            DB_PASS,
            $opciones
        );
    } catch (PDOException $e) {
        error_log(
            '[VIOLETA] Error de conexión: ' . $e->getMessage()
        );

        http_response_code(500);

        exit(
            '<div style="font-family:sans-serif;padding:2rem;color:#3d1a4e;max-width:600px;margin:2rem auto;border:1px solid #e0cffc;border-radius:8px;background:#faf7ff;">'
            . '<h2 style="color:#5636C9;margin-top:0;">Error de conexión</h2>'
            . '<p>No se pudo conectar a la base de datos de <strong>VIOLETA</strong>.</p>'
            . '<p style="color:#666;font-size:0.9rem;">Verifique las credenciales en su hosting o archivo de configuración local (host, usuario, contraseña, base de datos y puerto).</p>'
            . '</div>'
        );
    }

    return $pdo;
}
