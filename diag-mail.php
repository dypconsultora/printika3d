<?php
/**
 * Diagnóstico temporal del envío SMTP (borrar después de usar).
 * Uso: /diag-mail.php?k=pk-diag-7319
 */
declare(strict_types=1);
header('Content-Type: text/plain; charset=utf-8');

if (($_GET['k'] ?? '') !== 'pk-diag-7319') {
    http_response_code(404);
    exit('not found');
}

echo "PHP: " . PHP_VERSION . "\n\n";

$envFile = __DIR__ . '/.env';
echo ".env existe:  " . (file_exists($envFile) ? 'SI' : 'NO') . "\n";
echo ".env legible: " . (is_readable($envFile) ? 'SI' : 'NO') . "\n";

$config = require __DIR__ . '/config.php';
echo "config.php devuelve: " . gettype($config) . "\n\n";

if (!is_array($config)) {
    exit(">>> PROBLEMA: config.php de la raiz no es el correcto (debe devolver un array).\n");
}

echo "smtp_host: " . ($config['smtp_host'] !== '' ? $config['smtp_host'] : '(VACIO — falta .env?)') . "\n";
echo "smtp_port: " . $config['smtp_port'] . "\n";
echo "smtp_secure: " . $config['smtp_secure'] . "\n";
echo "smtp_user: " . ($config['smtp_user'] !== '' ? $config['smtp_user'] : '(VACIO)') . "\n";
echo "smtp_pass: " . ($config['smtp_pass'] !== '' ? '(definida, ' . strlen($config['smtp_pass']) . ' chars)' : '(VACIA)') . "\n";
echo "to_email:  " . ($config['to_email'] !== '' ? $config['to_email'] : '(VACIO)') . "\n\n";

require __DIR__ . '/lib/PHPMailer/Exception.php';
require __DIR__ . '/lib/PHPMailer/PHPMailer.php';
require __DIR__ . '/lib/PHPMailer/SMTP.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = $config['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['smtp_user'];
    $mail->Password   = $config['smtp_pass'];
    $mail->SMTPSecure = $config['smtp_secure'];
    $mail->Port       = (int) $config['smtp_port'];
    $mail->Timeout    = 12;

    echo "Probando conexion y login SMTP...\n";
    $mail->smtpConnect();
    echo "SMTP OK — conexion y credenciales correctas ✓\n";
    $mail->smtpClose();
} catch (Throwable $e) {
    echo "FALLO SMTP: " . $e->getMessage() . "\n";
}
