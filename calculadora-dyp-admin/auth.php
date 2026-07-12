<?php
/**
 * Autenticacion por contraseña + ayudas de sesion y CSRF.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function iniciar_sesion() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name('calc3d');
        session_start();
    }
}

function esta_logueado() {
    iniciar_sesion();
    return !empty($_SESSION['auth']) && $_SESSION['auth'] === true;
}

/** Para paginas HTML: si no esta logueado, manda al login. */
function requerir_login() {
    if (!esta_logueado()) {
        header('Location: login.php');
        exit;
    }
}

/** Para la API: si no esta logueado, responde 401 JSON. */
function requerir_login_api() {
    if (!esta_logueado()) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'No autenticado']);
        exit;
    }
}

function obtener_hash_password() {
    try {
        $stmt = db()->prepare('SELECT valor FROM app_config WHERE clave = ? LIMIT 1');
        $stmt->execute(['password_hash']);
        $row = $stmt->fetch();
        return $row ? $row['valor'] : null;
    } catch (Exception $e) {
        return null;
    }
}

function verificar_password($password) {
    $hash = obtener_hash_password();
    if (!$hash) return false;
    return password_verify($password, $hash);
}

function token_csrf() {
    iniciar_sesion();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verificar_csrf($token) {
    iniciar_sesion();
    return !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string) $token);
}
