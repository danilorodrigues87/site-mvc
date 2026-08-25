<?php
namespace App\Controller\Site;
use \App\Utils\View;
use \App\Model\Entity\Leads;
use \App\Common\Communication\Email;
use \App\Common\Helpers\NumeroHelper;
use \App\Common\Helpers\SiteBrandingHelper;
use \App\Common\Environment;

class Contact extends Page {

	public static function index($request){
		$content = View::render('site/modules/contato', SiteBrandingHelper::viewVars());
		return parent::getPanel('Contato', $content, 'contato');
	}

	public static function enviaMensagem($request){
		$postVars = $request->getPostVars();

		$nome = trim(strip_tags((string)($postVars['c_nome'] ?? '')));
		$escola = trim(strip_tags((string)($postVars['c_escola'] ?? '')));
		$whatsapp = trim(strip_tags((string)($postVars['c_whatsapp'] ?? '')));
		$email = trim((string)($postVars['c_email'] ?? ''));
		$assunto = trim(strip_tags((string)($postVars['assunto'] ?? '')));
		$mensagem = trim(strip_tags((string)($postVars['mensagem'] ?? '')));

		if ($nome === '' || $email === '' || $assunto === '' || $mensagem === '') {
			return ['success' => false, 'message' => 'Preencha todos os campos obrigatórios.'];
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return ['success' => false, 'message' => 'E-mail inválido.'];
		}

		$whatsappFmt = NumeroHelper::formatarTelefone($whatsapp);
		$to = trim((string)Environment::get('CONTACT_TO_EMAIL', ''));
		if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
			$to = 'contato@ctieducacional.com.br';
		}

		$subject = 'Lead B2B site: '.$assunto;
		$body = '<p><b>'.htmlspecialchars($nome, ENT_QUOTES, 'UTF-8').'</b> enviou mensagem pelo site B2B.</p>';
		if ($escola !== '') {
			$body .= '<p><b>Escola:</b> '.htmlspecialchars($escola, ENT_QUOTES, 'UTF-8').'</p>';
		}
		$body .= '<p><b>Assunto:</b> '.htmlspecialchars($assunto, ENT_QUOTES, 'UTF-8').'</p>';
		$body .= '<p><b>Mensagem:</b><br>'.nl2br(htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8')).'</p>';
		$body .= '<p><b>WhatsApp:</b> '.htmlspecialchars($whatsappFmt, ENT_QUOTES, 'UTF-8')
			.'<br><b>E-mail para resposta:</b> '.htmlspecialchars($email, ENT_QUOTES, 'UTF-8').'</p>';

		$obEmail = new Email;
		if (!$obEmail->isConfigured()) {
			return [
				'success' => false,
				'message' => 'Envio de e-mail não configurado no servidor. Verifique SMTP no .env (host, usuário, senha e remetente).',
			];
		}

		$res = $obEmail->sendEmail($to, $subject, $body, null, [], [], [], $email);
		if (!$res) {
			return [
				'success' => false,
				'message' => $obEmail->getError() ?: 'Falha ao enviar e-mail.',
			];
		}

		self::sendAcknowledgment($obEmail, $nome, $email, $assunto);
		self::persistLeadSafely($nome, $email, $whatsapp);

		return [
			'success' => true,
			'message' => 'Mensagem enviada. Enviamos uma confirmação para '.$email.' e nossa equipe responderá em breve.',
		];
	}

	private static function persistLeadSafely(string $nome, string $email, string $whatsapp): void {
		if (!self::dbConfigured()) {
			return;
		}

		try {
			$leads = new Leads;
			$leads->nome = $nome;
			$leads->email = $email;
			$leads->whatsapp = $whatsapp;
			$leads->origem = 'Lead B2B Site';
			$leads->prospectar();
		} catch (\Throwable $e) {
			self::logContact('WARN lead DB: '.$e->getMessage());
		}
	}

	private static function sendAcknowledgment(Email $obEmail, string $nome, string $email, string $assunto): void {
		$flag = strtolower(trim((string)Environment::get('CONTACT_SEND_ACK', 'true')));
		if (!in_array($flag, ['1', 'true', 'yes', 'on'], true)) {
			return;
		}

		$ackSubject = 'Recebemos seu contato — CTI Educacional';
		$ackBody = '<p>Olá <b>'.htmlspecialchars($nome, ENT_QUOTES, 'UTF-8').'</b>,</p>'
			.'<p>Recebemos sua mensagem sobre <b>'.htmlspecialchars($assunto, ENT_QUOTES, 'UTF-8').'</b>.</p>'
			.'<p>Nossa equipe comercial retornará em breve no e-mail que você informou.</p>'
			.'<p>Atenciosamente,<br>Equipe CTI Educacional</p>';

		$obEmail->sendEmail($email, $ackSubject, $ackBody);
	}

	private static function logContact(string $message): void {
		$dir = dirname(__DIR__, 3).'/storage/logs';
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		@file_put_contents($dir.'/contact.log', date('Y-m-d H:i:s').' '.$message.PHP_EOL, FILE_APPEND | LOCK_EX);
	}

	private static function dbConfigured(): bool {
		$user = Environment::get('DB_USER');
		$name = Environment::get('DB_NAME');
		return is_string($user) && $user !== '' && is_string($name) && $name !== '';
	}

	public static function assinarNews($request){
		$postVars = $request->getPostVars();
		$email = filter_var($postVars['email'] ?? '', FILTER_SANITIZE_EMAIL);
		if (!$email) {
			return false;
		}

		try {
			$leads = new Leads;
			$leads->email = $email;
			$leads->origem = 'Newsletter Site B2B';
			return $leads->prospectar();
		} catch (\Throwable $e) {
			return false;
		}
	}
}
