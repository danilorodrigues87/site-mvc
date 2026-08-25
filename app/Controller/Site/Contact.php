<?php
namespace App\Controller\Site;
use \App\Utils\View;
use \App\Model\Entity\Leads;
use \App\Common\Communication\Email;
use \App\Common\Helpers\NumeroHelper;
use \App\Common\Helpers\SiteBrandingHelper;

class Contact extends Page {

	public static function index($request){
		$content = View::render('site/modules/contato', SiteBrandingHelper::viewVars());
		return parent::getPanel('Contato', $content, 'contato');
	}

	public static function enviaMensagem($request){
		$postVars = $request->getPostVars();
		$fromName = 'CONTATO SITE CTI B2B';

		$nome = filter_var($postVars['c_nome'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$escola = filter_var($postVars['c_escola'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$whatsapp = filter_var($postVars['c_whatsapp'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$email = filter_var($postVars['c_email'] ?? '', FILTER_SANITIZE_EMAIL);
		$assunto = filter_var($postVars['assunto'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$mensagem = filter_var($postVars['mensagem'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

		$leads = new Leads;
		$leads->nome = $nome;
		$leads->email = $email;
		$leads->whatsapp = $whatsapp;
		$leads->origem = 'Lead B2B Site';

		if(!$leads){
			return false;
		}

		$leads->prospectar();

		$whatsappFmt = NumeroHelper::formatarTelefone($whatsapp);
		$address = 'leads@ctieducacional.com.br';
		$subject = 'Lead B2B: '.$assunto;
		$body = '<p><b>'.$nome.'</b> enviou mensagem pelo site B2B.</p>';
		if ($escola !== '') {
			$body .= '<p><b>Escola:</b> '.$escola.'</p>';
		}
		$body .= '<p><b>Assunto:</b> '.$assunto.'</p>';
		$body .= '<p><b>Mensagem:</b><br>'.nl2br($mensagem).'</p>';
		$body .= '<p><b>WhatsApp:</b> '.$whatsappFmt.'<br><b>E-mail:</b> '.$email.'</p>';

		$obEmail = new Email;
		$res = $obEmail->sendEmail($address, $subject, $body, $fromName);
		return $res ? true : $obEmail->getError;
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
