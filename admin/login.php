<?php
/**
 * VIOLETA — Login del panel administrativo (admin/login.php)
 */
require_once __DIR__ . '/../config/configuracion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

iniciarSesionSegura();

// Si ya está autenticado, redirigir al dashboard
if (!empty($_SESSION['admin_id'])) {
    redirigir(BASE_URL . '/admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarTokenCSRF()) {
        $error = 'Token de seguridad inválido. Recarga la página.';
    } else {
        $email    = sanitizarTexto($_POST['email']    ?? '');
        $password = $_POST['password'] ?? '';

        if (estaVacio($email) || estaVacio($password)) {
            $error = 'Por favor, ingresa tu correo electrónico y contraseña.';
        } else {
            $pdo  = obtenerConexion();
            $stmt = $pdo->prepare(
                'SELECT id_admin, nombre, password_hash, activo
                   FROM administradores
                  WHERE email = :email
                  LIMIT 1'
            );
            $stmt->execute([':email' => $email]);
            $admin = $stmt->fetch();

            if ($admin && $admin['activo'] && password_verify($password, $admin['password_hash'])) {
                // Autenticación exitosa
                session_regenerate_id(true);
                $_SESSION['admin_id']     = $admin['id_admin'];
                $_SESSION['admin_nombre'] = $admin['nombre'];

                // Registrar último acceso
                $pdo->prepare(
                    'UPDATE administradores SET ultimo_acceso = NOW() WHERE id_admin = :id'
                )->execute([':id' => $admin['id_admin']]);

                redirigir(BASE_URL . '/admin/dashboard.php');
            } else {
                $error = 'Credenciales incorrectas. Verifica tu correo y contraseña.';
                // Log del intento fallido (sin exponer detalles)
                error_log('[VIOLETA Admin] Intento de acceso fallido para: ' . $email);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VIOLETA · Acceso administrativo</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/estilos.css">
</head>
<body style="background:linear-gradient(135deg,var(--violeta-prof) 0%,#3a1d6e 100%);min-height:100vh;
             display:flex;align-items:center;justify-content:center;padding:2rem;">

  <div style="width:100%;max-width:420px;">
    <div style="text-align:center;margin-bottom:2rem;">
      <a href="<?= BASE_URL ?>/index.php"
         style="display:inline-flex;align-items:center;gap:.75rem;text-decoration:none;">
        <svg width="44" height="44" viewBox="0 0 38 38" aria-hidden="true">
          <circle cx="19" cy="19" r="19" fill="rgba(255,255,255,.15)"/>
          <circle cx="19" cy="14" r="5" fill="#E9DDFD"/>
          <ellipse cx="13" cy="24" rx="4" ry="3" fill="#D94FC1" opacity=".9"/>
          <ellipse cx="25" cy="24" rx="4" ry="3" fill="#D94FC1" opacity=".9"/>
          <circle cx="19" cy="19" r="3" fill="white"/>
          <line x1="19" y1="22" x2="19" y2="30" stroke="white" stroke-width="2" stroke-linecap="round"/>
          <line x1="15" y1="27" x2="23" y2="27" stroke="white" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <div style="text-align:left;">
          <div style="font-size:1.5rem;font-weight:800;color:white;letter-spacing:.04em;">VIOLETA</div>
          <div style="font-size:.7rem;color:rgba(255,255,255,.55);text-transform:uppercase;">Panel administrativo</div>
        </div>
      </a>
    </div>

    <div class="tarjeta" style="padding:2.5rem;">
      <h2 style="font-size:1.4rem;margin-bottom:.25rem;">Acceso administrativo</h2>
      <p class="t-muted fs-sm mb-4">Ingresa tus credenciales para acceder al panel de gestión.</p>

      <?php if ($error): ?>
      <div class="alerta alerta-error mb-3">
        <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
        <span><?= e($error) ?></span>
      </div>
      <?php endif; ?>

      <form method="POST" action="login.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">

        <div class="form-grupo">
          <label for="email">Correo electrónico</label>
          <input type="email"
                 id="email"
                 name="email"
                 class="form-control"
                 placeholder="admin@violeta.edu.bo"
                 value="<?= e($_POST['email'] ?? '') ?>"
                 required
                 autocomplete="email">
        </div>

        <div class="form-grupo">
          <label for="password">Contraseña</label>
          <div style="position:relative;">
            <input type="password"
                   id="password"
                   name="password"
                   class="form-control"
                   placeholder="••••••••"
                   required
                   autocomplete="current-password">
            <button type="button"
                    onclick="togglePass()"
                    style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);
                           background:none;border:none;cursor:pointer;color:var(--texto-sec);"
                    aria-label="Mostrar u ocultar contraseña">
              <i class="bi bi-eye" id="icon-pass" aria-hidden="true"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primario" style="width:100%;margin-top:.5rem;" id="btn-login">
          <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Ingresar
        </button>
      </form>

      <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--borde);">
        <p class="fs-xs t-muted t-centro mb-0">
          <i class="bi bi-shield-lock" aria-hidden="true"></i>
          Acceso restringido. Solo personal autorizado.
        </p>
      </div>
    </div>

    <div style="text-align:center;margin-top:1.5rem;">
      <a href="<?= BASE_URL ?>/index.php" class="fs-sm" style="color:rgba(255,255,255,.6);">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Volver al sitio
      </a>
    </div>
  </div>

  <script>
  function togglePass() {
    const inp  = document.getElementById('password');
    const icon = document.getElementById('icon-pass');
    if (inp.type === 'password') {
      inp.type = 'text';
      icon.className = 'bi bi-eye-slash';
    } else {
      inp.type = 'password';
      icon.className = 'bi bi-eye';
    }
  }
  </script>
</body>
</html>
