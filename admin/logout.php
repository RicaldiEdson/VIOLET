<?php
/**
 * VIOLETA — Cierre de sesión administrativo (admin/logout.php)
 */
require_once __DIR__ . '/../config/configuracion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();

// Limpiar todas las variables de sesión
$_SESSION = [];

// Borrar la cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir la sesión en el servidor
session_destroy();

// Redirigir al login con confirmación
header('Location: ' . BASE_URL . '/admin/login.php?mensaje=sesion_cerrada');
exit;
