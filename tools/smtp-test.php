<?php
/**
 * Teste SMTP — acesse uma vez e remova o arquivo.
 * URL: https://seu-dominio/tools/smtp-test.php?token=SEU_SMTP_TEST_TOKEN
 */
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

require __DIR__.'/../vendor/autoload.php';

use App\Common\Environment;
use App\Common\Communication\Email;

Environment::load(__DIR__.'/../');

$token = (string)($_GET['token'] ?? '');
$expected = (string)Environment::get('SMTP_TEST_TOKEN', '');
if ($expected === '' || !hash_equals($expected, $token)) {
	http_response_code(403);
	echo "Acesso negado.\n";
	echo "Defina SMTP_TEST_TOKEN no .env e acesse ?token=...\n";
	exit;
}

$to = trim((string)($_GET['to'] ?? Environment::get('CONTACT_TO_EMAIL', '')));
if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
	echo "Informe ?to=email@valido ou CONTACT_TO_EMAIL no .env\n";
	exit;
}

echo "SMTP_HOST=".Environment::get('SMTP_HOST')."\n";
echo "SMTP_USER=".Environment::get('SMTP_USER')."\n";
echo "SMTP_FROM=".Environment::get('SMTP_FROM_EMAIL')."\n";
echo "SMTP_PORT=".Environment::get('SMTP_PORT')."\n";
echo "SMTP_ENCRYPTION=".Environment::get('SMTP_ENCRYPTION')."\n";
echo "PASS_LEN=".strlen((string)Environment::get('SMTP_PASS', ''))."\n";
echo "TO=".$to."\n\n";

$mail = new Email();
echo 'configured='.($mail->isConfigured() ? 'yes' : 'no')."\n";

$ok = $mail->sendEmail(
	$to,
	'Teste SMTP site CTI — '.date('Y-m-d H:i:s'),
	'<p>Este é um teste automático do formulário de contato.</p>'
);

echo 'result='.($ok ? 'OK' : 'FAIL')."\n";
echo 'error='.($mail->getError() ?: '(nenhum)')."\n";
echo "\nLog: storage/logs/smtp.log\n";
echo "Remova tools/smtp-test.php após o teste.\n";
