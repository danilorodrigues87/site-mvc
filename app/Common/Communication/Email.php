<?php

namespace App\Common\Communication;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Common\Environment;

class Email {

	private array $config;
	private ?string $error = null;

	public function __construct() {
		$this->config = [
			'host'       => (string)Environment::get('SMTP_HOST', ''),
			'user'       => (string)Environment::get('SMTP_USER', ''),
			'pass'       => (string)Environment::get('SMTP_PASS', ''),
			'port'       => (int)Environment::get('SMTP_PORT', 587),
			'charset'    => (string)Environment::get('SMTP_CHARSET', 'UTF-8'),
			'from_email' => (string)Environment::get('SMTP_FROM_EMAIL', ''),
			'from_name'  => (string)Environment::get('SMTP_FROM_NAME', 'CTI Educacional'),
			'encryption' => (string)Environment::get('SMTP_ENCRYPTION', 'tls'),
		];
	}

	public function getError(): ?string {
		return $this->error;
	}

	public function isConfigured(): bool {
		return $this->config['host'] !== ''
			&& $this->config['user'] !== ''
			&& $this->config['from_email'] !== '';
	}

	private function resolveEncryption(string $encryption) {
		$encryption = strtolower(trim($encryption));
		if ($encryption === 'ssl') {
			return PHPMailer::ENCRYPTION_SMTPS;
		}
		if ($encryption === 'none' || $encryption === '') {
			return false;
		}
		return PHPMailer::ENCRYPTION_STARTTLS;
	}

	/**
	 * @param string|array $addresses
	 * @param string|null $replyTo E-mail para Responder (ex.: visitante do formulário)
	 */
	public function sendEmail(
		$addresses,
		$subject,
		$body,
		$fromName = null,
		$attachments = [],
		$ccs = [],
		$bccs = [],
		$replyTo = null
	) {
		$this->error = null;

		if (!$this->isConfigured()) {
			$this->error = 'Configuração SMTP incompleta (SMTP_HOST, SMTP_USER, SMTP_FROM_EMAIL).';
			return false;
		}

		$mail = new PHPMailer(true);

		try {
			$mail->isSMTP();
			$mail->Host       = $this->config['host'];
			$mail->SMTPAuth   = true;
			$mail->Username   = $this->config['user'];
			$mail->Password   = $this->config['pass'];
			$mail->SMTPSecure = $this->resolveEncryption($this->config['encryption']);
			$mail->Port       = $this->config['port'];
			$mail->CharSet    = $this->config['charset'];
			$mail->Encoding   = 'base64';

			$senderName = $fromName !== null && trim((string)$fromName) !== ''
				? (string)$fromName
				: $this->config['from_name'];

			$mail->setFrom($this->config['from_email'], $senderName);

			if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
				$mail->addReplyTo($replyTo);
			}

			foreach ((array)$addresses as $address) {
				if (!empty($address) && filter_var($address, FILTER_VALIDATE_EMAIL)) {
					$mail->addAddress($address);
				}
			}

			if (count($mail->getToAddresses()) === 0) {
				$this->error = 'Nenhum destinatário válido.';
				return false;
			}

			foreach ((array)$attachments as $attachment) {
				if (!empty($attachment)) {
					$mail->addAttachment($attachment);
				}
			}

			foreach ((array)$ccs as $cc) {
				if (!empty($cc) && filter_var($cc, FILTER_VALIDATE_EMAIL)) {
					$mail->addCC($cc);
				}
			}

			foreach ((array)$bccs as $bcc) {
				if (!empty($bcc) && filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
					$mail->addBCC($bcc);
				}
			}

			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body    = $body;
			$mail->AltBody = strip_tags($body);

			return $mail->send();
		} catch (Exception $e) {
			$this->error = $e->getMessage();
			return false;
		}
	}
}
