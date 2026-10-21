<?php
/**
 * VIOLETA - Funciones de utilidad compartidas
 */

require_once __DIR__ . '/../config/conexion.php';

// ── Sesión segura ─────────────────────────────────────────────────────────────
function iniciarSesionSegura(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

// ── Seguridad ─────────────────────────────────────────────────────────────────

/** Escapa texto para salida HTML segura */
function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Genera un token CSRF y lo almacena en sesión */
function generarTokenCSRF(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Valida el token CSRF enviado por POST */
function validarTokenCSRF(): bool
{
    $tokenEnviado = $_POST['csrf_token'] ?? '';
    return isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $tokenEnviado);
}

/** Genera un código aleatorio seguro de N bytes en hex */
function generarCodigo(int $bytes = 16): string
{
    return bin2hex(random_bytes($bytes));
}

// ── Redirección ───────────────────────────────────────────────────────────────
function redirigir(string $url): never
{
    header('Location: ' . $url);
    exit;
}

// ── Categorías ────────────────────────────────────────────────────────────────
function obtenerCategorias(): array
{
    $pdo  = obtenerConexion();
    $stmt = $pdo->query(
        'SELECT * FROM categorias WHERE estado = 1 ORDER BY orden ASC, nombre ASC'
    );
    return $stmt->fetchAll();
}

function obtenerCategoriaPorSlug(string $slug): array|false
{
    $pdo  = obtenerConexion();
    $stmt = $pdo->prepare(
        'SELECT * FROM categorias WHERE slug = :slug AND estado = 1 LIMIT 1'
    );
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch();
}

// ── Preguntas ─────────────────────────────────────────────────────────────────

/** Devuelve N preguntas activas aleatorias con sus opciones */
function obtenerPreguntasAleatorias(int $cantidad = 10): array
{
    $pdo  = obtenerConexion();
    $stmt = $pdo->prepare(
        'SELECT p.*, c.nombre AS nombre_categoria, c.slug AS slug_categoria
           FROM preguntas p
           JOIN categorias c ON c.id_categoria = p.id_categoria
          WHERE p.estado = 1
          ORDER BY RAND()
          LIMIT :cantidad'
    );
    $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
    $stmt->execute();
    $preguntas = $stmt->fetchAll();

    foreach ($preguntas as &$p) {
        $p['opciones'] = obtenerOpcionesPorPregunta((int)$p['id_pregunta']);
    }
    return $preguntas;
}

/** Devuelve las opciones de una pregunta (sin marcar cuál es la correcta en el cliente) */
function obtenerOpcionesPorPregunta(int $idPregunta, bool $incluirCorrecta = false): array
{
    $pdo  = obtenerConexion();
    $campos = $incluirCorrecta
        ? 'id_opcion, texto_opcion, es_correcta, orden'
        : 'id_opcion, texto_opcion, orden';
    $stmt = $pdo->prepare(
        "SELECT $campos FROM opciones WHERE id_pregunta = :id ORDER BY orden ASC"
    );
    $stmt->execute([':id' => $idPregunta]);
    return $stmt->fetchAll();
}

/** Recupera la opción correcta de una pregunta */
function obtenerOpcionCorrecta(int $idPregunta): array|false
{
    $pdo  = obtenerConexion();
    $stmt = $pdo->prepare(
        'SELECT id_opcion FROM opciones
          WHERE id_pregunta = :id AND es_correcta = 1 LIMIT 1'
    );
    $stmt->execute([':id' => $idPregunta]);
    return $stmt->fetch();
}

// ── Intentos ──────────────────────────────────────────────────────────────────

function crearIntento(string $codigoSesion, int $totalPreguntas): int
{
    $pdo  = obtenerConexion();
    $stmt = $pdo->prepare(
        'INSERT INTO intentos (codigo_sesion, total_preguntas)
         VALUES (:codigo, :total)'
    );
    $stmt->execute([':codigo' => $codigoSesion, ':total' => $totalPreguntas]);
    return (int)$pdo->lastInsertId();
}

function obtenerIntentoPorId(int $idIntento): array|false
{
    $pdo  = obtenerConexion();
    $stmt = $pdo->prepare('SELECT * FROM intentos WHERE id_intento = :id LIMIT 1');
    $stmt->execute([':id' => $idIntento]);
    return $stmt->fetch();
}

/**
 * Finaliza un intento: calcula puntuación en servidor, guarda respuestas,
 * actualiza el intento y retorna el resumen.
 *
 * @param int   $idIntento
 * @param array $respuestasUsuario  ['id_pregunta' => 'id_opcion_seleccionada']
 * @param array $preguntasDelIntento Array de preguntas con id_pregunta
 * @return array Resumen del intento
 */
function finalizarIntento(
    int   $idIntento,
    array $respuestasUsuario,
    array $preguntasDelIntento
): array {
    $pdo = obtenerConexion();

    $correctas   = 0;
    $incorrectas = 0;
    $total       = count($preguntasDelIntento);
    $detalles    = [];

    // Mapa de id_pregunta => pregunta para validar pertenencia al intento
    $mapaPreguntas = [];
    foreach ($preguntasDelIntento as $p) {
        $mapaPreguntas[(int)$p['id_pregunta']] = $p;
    }

    // Verificar y guardar cada respuesta
    $stmtInsert = $pdo->prepare(
        'INSERT INTO respuestas_usuario
            (id_intento, id_pregunta, id_opcion_seleccionada, era_correcta)
         VALUES (:intento, :pregunta, :opcion, :correcta)
         ON DUPLICATE KEY UPDATE
            id_opcion_seleccionada = VALUES(id_opcion_seleccionada),
            era_correcta           = VALUES(era_correcta)'
    );

    foreach ($preguntasDelIntento as $pregunta) {
        $idPregunta = (int)$pregunta['id_pregunta'];

        // Solo procesar si la pregunta pertenece al intento
        if (!array_key_exists($idPregunta, $mapaPreguntas)) {
            continue;
        }

        $idOpcionSeleccionada = isset($respuestasUsuario[$idPregunta])
            ? (int)$respuestasUsuario[$idPregunta]
            : null;

        if ($idOpcionSeleccionada === null) {
            $incorrectas++;
            continue;
        }

        // Validar que la opción pertenece a la pregunta
        $stmtValidar = $pdo->prepare(
            'SELECT es_correcta FROM opciones
              WHERE id_opcion = :opc AND id_pregunta = :preg LIMIT 1'
        );
        $stmtValidar->execute([':opc' => $idOpcionSeleccionada, ':preg' => $idPregunta]);
        $opcion = $stmtValidar->fetch();

        $esCorrecta = $opcion && (int)$opcion['es_correcta'] === 1 ? 1 : 0;
        $esCorrecta ? $correctas++ : $incorrectas++;

        $stmtInsert->execute([
            ':intento'  => $idIntento,
            ':pregunta' => $idPregunta,
            ':opcion'   => $idOpcionSeleccionada,
            ':correcta' => $esCorrecta,
        ]);
    }

    $porcentaje = $total > 0 ? round(($correctas / $total) * 100, 2) : 0;

    // Actualizar el intento con los resultados definitivos
    $pdo->prepare(
        'UPDATE intentos
            SET respuestas_correctas   = :correctas,
                respuestas_incorrectas = :incorrectas,
                puntuacion_porcentaje  = :porcentaje,
                completado             = 1,
                fecha_fin              = NOW()
          WHERE id_intento = :id'
    )->execute([
        ':correctas'   => $correctas,
        ':incorrectas' => $incorrectas,
        ':porcentaje'  => $porcentaje,
        ':id'          => $idIntento,
    ]);

    return [
        'id_intento'           => $idIntento,
        'total'                => $total,
        'correctas'            => $correctas,
        'incorrectas'          => $incorrectas,
        'porcentaje'           => $porcentaje,
    ];
}

/** Obtiene las respuestas detalladas de un intento para la revisión */
function obtenerRevisionIntento(int $idIntento): array
{
    $pdo  = obtenerConexion();
    $stmt = $pdo->prepare(
        'SELECT
            p.id_pregunta,
            p.enunciado,
            p.explicacion,
            ru.id_opcion_seleccionada,
            ru.era_correcta,
            os.texto_opcion AS texto_seleccionada,
            oc.id_opcion    AS id_opcion_correcta,
            oc.texto_opcion AS texto_correcta,
            c.nombre        AS categoria
           FROM respuestas_usuario ru
           JOIN preguntas p  ON p.id_pregunta = ru.id_pregunta
           JOIN opciones  os ON os.id_opcion  = ru.id_opcion_seleccionada
           JOIN opciones  oc ON oc.id_pregunta = p.id_pregunta AND oc.es_correcta = 1
           JOIN categorias c ON c.id_categoria = p.id_categoria
          WHERE ru.id_intento = :id
          ORDER BY p.id_pregunta ASC'
    );
    $stmt->execute([':id' => $idIntento]);
    return $stmt->fetchAll();
}

// ── Estadísticas ──────────────────────────────────────────────────────────────
function obtenerEstadisticasGenerales(): array
{
    $pdo = obtenerConexion();

    $stats = [];

    $stats['total_preguntas']  = (int)$pdo->query(
        'SELECT COUNT(*) FROM preguntas WHERE estado = 1'
    )->fetchColumn();

    $stats['total_categorias'] = (int)$pdo->query(
        'SELECT COUNT(*) FROM categorias WHERE estado = 1'
    )->fetchColumn();

    $stats['total_intentos']   = (int)$pdo->query(
        'SELECT COUNT(*) FROM intentos WHERE completado = 1'
    )->fetchColumn();

    $stats['promedio_puntaje'] = (float)$pdo->query(
        'SELECT COALESCE(AVG(puntuacion_porcentaje), 0) FROM intentos WHERE completado = 1'
    )->fetchColumn();

    return $stats;
}

// ── Recursos ──────────────────────────────────────────────────────────────────
function obtenerRecursos(): array
{
    $pdo  = obtenerConexion();
    $stmt = $pdo->query(
        'SELECT * FROM recursos_ayuda WHERE estado = 1 ORDER BY nombre ASC'
    );
    return $stmt->fetchAll();
}

// ── Validaciones básicas ──────────────────────────────────────────────────────
function estaVacio(string $valor): bool
{
    return trim($valor) === '';
}

function sanitizarTexto(string $valor): string
{
    return trim(strip_tags($valor));
}
