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

		$nome = filter_var($postVars['c_nome'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$escola = filter_var($postVars['c_escola'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$whatsapp = filter_var($postVars['c_whatsapp'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$email = filter_var($postVars['c_email'] ?? '', FILTER_SANITIZE_EMAIL);
		$assunto = filter_var($postVars['assunto'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$mensagem = filter_var($postVars['mensagem'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

		if ($nome === '' || $email === '' || $assunto === '' || $mensagem === '') {
			return ['success' => false, 'message' => 'Preencha todos os campos obrigatórios.'];
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return ['success' => false, 'message' => 'E-mail inválido.'];
		}

		if (self::dbConfigured()) {
			$leads = new Leads;
			$leads->nome = $nome;
			$leads->email = $email;
			$leads->whatsapp = $whatsapp;
			$leads->origem = 'Lead B2B Site';
			@$leads->prospectar();
		}

		$whatsappFmt = NumeroHelper::formatarTelefone($whatsapp);
		$to = trim((string)Environment::get('CONTACT_TO_EMAIL', ''));
		if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
			$to = 'contato@ctieducacional.com.br';
		}
		$subject = 'Lead B2B site: '.$assunto;
		$body = '<p><b>'.$nome.'</b> enviou mensagem pelo site B2B.</p>';
		if ($escola !== '') {
			$body .= '<p><b>Escola:</b> '.$escola.'</p>';
		}
		$body .= '<p><b>Assunto:</b> '.$assunto.'</p>';
		$body .= '<p><b>Mensagem:</b><br>'.nl2br($mensagem).'</p>';
		$body .= '<p><b>WhatsApp:</b> '.$whatsappFmt.'<br><b>E-mail para resposta:</b> '.$email.'</p>';

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

		return [
			'success' => true,
			'message' => 'Mensagem enviada. Enviamos uma confirmação para '.$email.' e nossa equipe responderá em breve.',
		];
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
		$leads = new Leads;
		$leads->email = $email;
		$leads->origem = 'Newsletter Site B2B';
		return $leads->prospectar();
	}
}
