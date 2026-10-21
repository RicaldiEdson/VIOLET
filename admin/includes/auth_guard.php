<?php
/**
 * VIOLETA — Guard de autenticación administrativo
 * Incluir al inicio de cada página protegida del panel.
 */
require_once __DIR__ . '/../../config/configuracion.php';
require_once __DIR__ . '/../../includes/funciones.php';

iniciarSesionSegura();

if (empty($_SESSION['admin_id'])) {
    redirigir(BASE_URL . '/admin/login.php');
}

$adminNombre = $_SESSION['admin_nombre'] ?? 'Administrador';
