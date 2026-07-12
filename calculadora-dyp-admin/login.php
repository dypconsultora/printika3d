<?php
require_once __DIR__ . '/auth.php';
iniciar_sesion();

if (esta_logueado()) {
    header('Location: index.php');
    exit;
}

$error = '';
$sin_instalar = (obtener_hash_password() === null);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$sin_instalar) {
    $pass = $_POST['password'] ?? '';
    if (verificar_password($pass)) {
        session_regenerate_id(true);
        $_SESSION['auth'] = true;
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        header('Location: index.php');
        exit;
    } else {
        usleep(700000); // pequeña demora anti fuerza bruta
        $error = 'Contraseña incorrecta';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ingreso · <?php echo htmlspecialchars(APP_NOMBRE); ?></title>
<style>
  :root {
    --bg-primary:#0a0a0f; --bg-card:#1a1a26; --bg-input:#14141e;
    --border-color:#2a2a3a; --accent:#00D4FF; --accent-dim:rgba(0,212,255,.15);
    --text-primary:#e8e8f0; --text-secondary:#8888a0; --danger:#ff5252; --radius:12px;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;background:var(--bg-primary);
       color:var(--text-primary);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem}
  .login-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--radius);
       padding:2.5rem 2rem;width:100%;max-width:360px;box-shadow:0 4px 40px rgba(0,0,0,.5)}
  .login-card h1{font-size:1.3rem;font-weight:800;text-align:center;letter-spacing:-.02em;margin-bottom:.4rem;
       background:linear-gradient(135deg,#fff 30%,var(--accent));-webkit-background-clip:text;
       -webkit-text-fill-color:transparent;background-clip:text}
  .login-card p{font-size:.8rem;color:var(--text-secondary);text-align:center;margin-bottom:1.75rem}
  label{display:block;font-size:.72rem;font-weight:600;color:var(--text-secondary);
       text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem}
  input[type=password]{width:100%;background:var(--bg-input);border:1px solid var(--border-color);
       border-radius:8px;padding:.7rem .85rem;color:var(--text-primary);font-family:inherit;font-size:.9rem;outline:none}
  input[type=password]:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-dim)}
  button{width:100%;margin-top:1.25rem;background:var(--accent);color:#000;border:none;border-radius:8px;
       padding:.8rem;font-family:inherit;font-size:.9rem;font-weight:700;cursor:pointer;transition:opacity .2s}
  button:hover{opacity:.88}
  .error{background:rgba(255,82,82,.12);border:1px solid rgba(255,82,82,.3);color:var(--danger);
       font-size:.8rem;text-align:center;padding:.6rem;border-radius:8px;margin-bottom:1rem}
  .warn{background:rgba(255,171,64,.12);border:1px solid rgba(255,171,64,.35);color:#ffab40;
       font-size:.8rem;text-align:center;padding:.8rem;border-radius:8px;line-height:1.5}
  .warn a{color:#ffab40;font-weight:700}
</style>
</head>
<body>
  <form class="login-card" method="post" autocomplete="off">
    <h1><?php echo htmlspecialchars(APP_NOMBRE); ?></h1>
    <p>Ingresá la contraseña para continuar</p>

    <?php if ($sin_instalar): ?>
      <div class="warn">
        Todavía no está instalada.<br>
        Abrí <a href="install.php">install.php</a> para crear la base de datos y la contraseña.
      </div>
    <?php else: ?>
      <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <label for="password">Contraseña</label>
      <input type="password" id="password" name="password" autofocus required>
      <button type="submit">Entrar</button>
    <?php endif; ?>
  </form>
</body>
</html>
