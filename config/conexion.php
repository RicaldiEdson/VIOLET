<?php
/**
 * VIOLETA - Conexión centralizada a la base de datos
 *
 * Compatible con:
 * 1. InfinityFree
 * 2. XAMPP local
 * 3. Variables de entorno
 * 4. DATABASE_URL / MYSQL_URL
 * 5. Archivo protegido conexion.local.php
 */

// ============================================================
// 1. CARGAR CONFIGURACIÓN LOCAL
// ============================================================

$archivoLocal = __DIR__ . '/conexion.local.php';

if (is_file($archivoLocal) && is_readable($archivoLocal)) {
    require_once $archivoLocal;
}

// ============================================================
// 2. CARGAR VARIABLES DE ENTORNO DESDE .env
// ============================================================

$rutasEnv = [
    dirname(__DIR__) . '/.env',
    __DIR__ . '/.env',
];

foreach ($rutasEnv as $rutaEnv) {
    if (is_file($rutaEnv) && is_readable($rutaEnv)) {
        $lineas = file(
            $rutaEnv,
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );

        foreach ($lineas as $linea) {
            $linea = trim($linea);

            if (
                $linea === '' ||
                str_starts_with($linea, '#') ||
                !str_contains($linea, '=')
            ) {
                continue;
            }

            [$nombre, $valor] = explode('=', $linea, 2);

            $nombre = trim($nombre);
            $valor = trim($valor, " \t\n\r\0\x0B\"'");

            if (
                $nombre !== '' &&
                getenv($nombre) === false &&
                !isset($_ENV[$nombre]) &&
                !isset($_SERVER[$nombre])
            ) {
                putenv("{$nombre}={$valor}");
                $_ENV[$nombre] = $valor;
                $_SERVER[$nombre] = $valor;
            }
        }

        break;
    }
}

// ============================================================
// 3. FUNCIÓN PARA LEER VARIABLES DE ENTORNO
// ============================================================

if (!function_exists('obtenerEnvVioleta')) {
    function obtenerEnvVioleta(
        string $clave,
        ?string $porDefecto = null
    ): ?string {
        $valor = getenv($clave);

        if ($valor !== false && $valor !== '') {
            return $valor;
        }

        if (isset($_ENV[$clave]) && $_ENV[$clave] !== '') {
            return (string) $_ENV[$clave];
        }

        if (isset($_SERVER[$clave]) && $_SERVER[$clave] !== '') {
            return (string) $_SERVER[$clave];
        }

        return $porDefecto;
    }
}

// ============================================================
// 4. DETECTAR CONFIGURACIÓN DEL SERVIDOR
// ============================================================

$dbUrl = obtenerEnvVioleta(
    'DATABASE_URL',
    obtenerEnvVioleta('MYSQL_URL')
);

$urlHost = null;
$urlPort = null;
$urlName = null;
$urlUser = null;
$urlPass = null;

if (!empty($dbUrl)) {
    $parsedUrl = parse_url($dbUrl);

    if (is_array($parsedUrl)) {
        $urlHost = $parsedUrl['host'] ?? null;
        $urlPort = isset($parsedUrl['port'])
            ? (string) $parsedUrl['port']
            : null;

        $urlUser = isset($parsedUrl['user'])
            ? rawurldecode($parsedUrl['user'])
            : null;

        $urlPass = isset($parsedUrl['pass'])
            ? rawurldecode($parsedUrl['pass'])
            : null;

        $urlName = isset($parsedUrl['path'])
            ? rawurldecode(ltrim($parsedUrl['path'], '/'))
            : null;
    }
}

// ============================================================
// 5. CONFIGURACIÓN DE LA BASE DE DATOS
// ============================================================

// Los valores definidos en conexion.local.php tienen prioridad.
// Después se consideran las URL y variables de entorno.

// Detectar InfinityFree mediante la variable del hosting.
$esInfinityFree =
    obtenerEnvVioleta('DB_HOST') === 'sql313.infinityfree.com'
    || obtenerEnvVioleta('MYSQLHOST') === 'sql313.infinityfree.com'
    || obtenerEnvVioleta('DB_NAME') === 'if0_43236647_violeta_db'
    || obtenerEnvVioleta('MYSQLDATABASE') === 'if0_43236647_violeta_db';

// Host
if (!defined('DB_HOST')) {
    define(
        'DB_HOST',
        $urlHost
        ?? obtenerEnvVioleta(
            'DB_HOST',
            obtenerEnvVioleta(
                'MYSQLHOST',
                $esInfinityFree
                    ? 'sql313.infinityfree.com'
                    : '127.0.0.1'
            )
        )
    );
}

// Puerto
if (!defined('DB_PORT')) {
    define(
        'DB_PORT',
        $urlPort
        ?? obtenerEnvVioleta(
            'DB_PORT',
            obtenerEnvVioleta(
                'MYSQLPORT',
                $esInfinityFree ? '3306' : '3307'
            )
        )
    );
}

// Nombre de la base de datos
if (!defined('DB_NAME')) {
    define(
        'DB_NAME',
        $urlName
        ?? obtenerEnvVioleta(
            'DB_NAME',
            obtenerEnvVioleta(
                'MYSQLDATABASE',
                $esInfinityFree
                    ? 'if0_43236647_violeta_db'
                    : 'violeta_db'
            )
        )
    );
}

// Usuario
if (!defined('DB_USER')) {
    define(
        'DB_USER',
        $urlUser
        ?? obtenerEnvVioleta(
            'DB_USER',
            obtenerEnvVioleta(
                'MYSQLUSER',
                $esInfinityFree ? 'if0_43236647' : 'root'
            )
        )
    );
}

// Contraseña
if (!defined('DB_PASS')) {
    define(
        'DB_PASS',
        $urlPass
        ?? obtenerEnvVioleta(
            'DB_PASS',
            obtenerEnvVioleta(
                'MYSQLPASSWORD',
                ''
            )
        )
    );
}

// Codificación
if (!defined('DB_CHARSET')) {
    define(
        'DB_CHARSET',
        obtenerEnvVioleta('DB_CHARSET', 'utf8mb4')
    );
}

// ============================================================
// 6. FUNCIÓN CENTRALIZADA DE CONEXIÓN PDO
// ============================================================

if (!function_exists('obtenerConexion')) {
    function obtenerConexion(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
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
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            PDO::ATTR_DEFAULT_FETCH_MODE =>
                PDO::FETCH_ASSOC,

            PDO::ATTR_EMULATE_PREPARES => false,

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

            return $pdo;

        } catch (PDOException $e) {
            error_log(
                '[VIOLETA] Error de conexión: '
                . $e->getMessage()
            );

            http_response_code(500);

            exit(
                '<div style="
                    font-family:Arial,sans-serif;
                    padding:2rem;
                    color:#3d1a4e;
                    max-width:600px;
                    margin:2rem auto;
                    border:1px solid #e0cffc;
                    border-radius:8px;
                    background:#faf7ff;
                ">
                    <h2 style="color:#5636C9;margin-top:0;">
                        Error de conexión
                    </h2>

                    <p>
                        No se pudo conectar a la base de datos
                        de <strong>VIOLETA</strong>.
                    </p>

                    <p style="color:#666;font-size:0.9rem;">
                        Verifica el host, el puerto, el nombre
                        de la base de datos, el usuario y la
                        contraseña en la configuración del servidor.
                    </p>
                </div>'
            );
        }
    }
}

