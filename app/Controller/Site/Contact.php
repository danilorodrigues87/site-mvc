<?php
namespace App\Controller\Site;
use \App\Utils\View;
use \App\Model\Entity\Leads;
use \App\Common\Communication\Email;
use \App\Common\Helpers\NumeroHelper;


class Contact extends Page{


	//RETORNA A RENDERIZAÇÃO DA PÁGINA
	public static function index($request){

		// RETORNA A BASE DA PAGINA
		$content = View::render('site/modules/contato',[]);

		// RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Contato', $content, 'contato',$request);

	}


	public static function enviaMensagem($request){

		$postVars = $request->getPostVars(); 
		$fromName = 'CONTATO SITE CTI';

		$nome = filter_var($postVars['c_nome'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$whatsapp = filter_var($postVars['c_whatsapp'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$email = filter_var($postVars['c_email'] ?? '', FILTER_SANITIZE_EMAIL);
		$assunto = filter_var($postVars['assunto'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$mensagem = filter_var($postVars['mensagem'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);


		// SALVA AS INFORMAÇÕES DO LEAD N BANCO DE DADOS
		$leads = new Leads;	
		$leads->nome = $nome;
		$leads->email = $email;	
		$leads->whatsapp = $whatsapp;
		$leads->origem = 'Contato Site'; 

		if(!$leads){
			return false;
		}

		$leads->prospectar();

		$whatsapp = NumeroHelper::formatarTelefone($whatsapp);

		$address = 'leads@ctieducacional.com.br';
		$subject = $assunto;
		$body = '<p>'.$nome.' acabou de entrar em contato</p>';
		$body.= '<b>Whatsapp:</b> '.$whatsapp.'<br>';
		$body.= '<b>Email:</b> '.$email.'<br>';
		$body.= '<b>Mensagem:</b><br><br> '.$mensagem;

		$obEmail = new Email;		
		$res = $obEmail->sendEmail($address,$subject,$body,$fromName);
		$res = $res ? true : $obEmail->getError;
		if(!$res){

			return false;
		}

		return true;

		
	}


	//ASSINATURA PARA RECEBER NOVIDADES DO CTI
	public static function assinarNews($request){

		$postVars = $request->getPostVars(); 
		$fromName = 'ASSINATURA SITE CTI';

		$email = filter_var($postVars['email'] ?? '', FILTER_SANITIZE_EMAIL);

		// SALVA AS INFORMAÇÕES DO LEAD N BANCO DE DADOS
		$leads = new Leads;	
		$leads->email = $email;	
		$leads->origem = 'assinatura'; 

		if(!$leads){
			return false;
		}

		$leads->prospectar();
		$address = 'leads@ctieducacional.com.br';
		$subject = 'NOVA ASSINATURA';
		$body = '<p>Novo email cadastrado para receber noticias e novidade do CTI</p>';
		$body.= '<b>Email:</b> '.$email.'<br>';

		$obEmail = new Email;		
		$res = $obEmail->sendEmail($address,$subject,$body,$fromName);
		$res = $res ? true : $obEmail->getError;
		if(!$res){

			return false;
		}

		return true;

		
	}




}