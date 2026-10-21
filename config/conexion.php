
<?php
/**
 * VIOLETA - Conexión centralizada a la base de datos
 * Conexión local mediante PDO y MySQL en el puerto 3307.
 */

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3307');
define('DB_NAME', 'violeta_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

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
            '<div style="font-family:sans-serif;padding:2rem;color:#3d1a4e">'
            . '<h2>Error de conexión</h2>'
            . '<p>No se pudo conectar a la base de datos. '
            . 'Verifique la configuración del servidor.</p>'
            . '</div>'
        );
    }

    return $pdo;
}
