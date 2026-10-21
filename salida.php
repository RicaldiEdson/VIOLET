<?php
/**
 * VIOLETA — Salida rápida (salida.php)
 * Redirige a un sitio neutral y muestra una nota de privacidad.
 * NO borra el historial del navegador ni garantiza privacidad total.
 */

// Redirigir inmediatamente con meta refresh como respaldo
// JavaScript realizará la redirección más rápido
$urlNeutral = 'https://www.google.com';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Saliendo del sitio...</title>
  <meta http-equiv="refresh" content="0;url=<?= htmlspecialchars($urlNeutral) ?>">
  <script>
    // Redirigir inmediatamente
    window.location.replace(<?= json_encode($urlNeutral) ?>);
  </script>
  <style>
    body {
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      background: #F8F7FC;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 2rem;
    }
    .caja {
      background: white;
      border-radius: 20px;
      padding: 3rem;
      max-width: 500px;
      text-align: center;
      box-shadow: 0 8px 24px rgba(86,54,201,.12);
    }
    h2 { color: #281653; margin-bottom: .5rem; }
    p  { color: #716A83; font-size: .9rem; line-height: 1.6; }
    .nota {
      background: #fdecd7;
      border-radius: 10px;
      padding: .75rem 1rem;
      font-size: .8rem;
      color: #A96913;
      margin-top: 1.5rem;
      text-align: left;
      border-left: 3px solid #A96913;
    }
    a.btn {
      display: inline-block;
      margin-top: 1.5rem;
      background: #5636C9;
      color: white;
      padding: .6rem 1.6rem;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      font-size: .875rem;
    }
  </style>
</head>
<body>
  <div class="caja">
    <div style="font-size:2.5rem;margin-bottom:1rem;">↗</div>
    <h2>Saliendo de VIOLETA</h2>
    <p>Estás siendo redirigido/a a un sitio neutral. Si la redirección no ocurre automáticamente, haz clic en el botón.</p>

    <a href="<?= htmlspecialchars($urlNeutral) ?>" class="btn">Salir ahora</a>

    <div class="nota">
      <strong>⚠ Nota importante sobre privacidad:</strong><br>
      La "salida rápida" te redirige a otro sitio de inmediato, pero
      <strong>no borra el historial de navegación</strong>, no elimina registros de red
      ni garantiza privacidad digital total. Si tu seguridad es una preocupación,
      considera usar el modo de navegación privada de tu navegador y borrar el historial
      manualmente después de visitar este sitio.
    </div>
  </div>
</body>
</html>
