<?php
namespace App\Controller\Site;

class Solucoes extends Page {

	public static function index($request){
		$content = parent::brandingVars();
		$html = \App\Utils\View::render('site/modules/solucoes/index', $content);
		return parent::getPanel('Soluções', $html, 'solucoes',
			'Soluções CTI — Gestão, pedagógico e comercial',
			'Conheça os módulos do ecossistema CTI para escolas profissionalizantes.'
		);
	}

	public static function construtor($request){
		$html = \App\Utils\View::render('site/modules/solucoes/construtor-cursos', parent::brandingVars());
		return parent::getPanel('Construtor de cursos', $html, 'solucoes_construtor',
			'Construtor de cursos interativos — CTI Educacional',
			'Crie trilhas, aulas interativas, avaliações e certificados na sua escola.'
		);
	}

	public static function gestao($request){
		$html = \App\Utils\View::render('site/modules/solucoes/gestao', parent::brandingVars());
		return parent::getPanel('Gestão escolar', $html, 'solucoes_gestao',
			'Gestão completa para escolas — CTI Educacional',
			'Matrículas, financeiro, carnês e indicadores da operação da sua escola.'
		);
	}

	public static function comercial($request){
		$html = \App\Utils\View::render('site/modules/solucoes/comercial', parent::brandingVars());
		return parent::getPanel('Comercial e marketing', $html, 'solucoes_comercial',
			'WhatsApp, CRM e campanhas — CTI Educacional',
			'Motor comercial integrado: inbox WhatsApp, CRM, fluxos e prospecção.'
		);
	}

	public static function certificados($request){
		$html = \App\Utils\View::render('site/modules/solucoes/certificados', parent::brandingVars());
		return parent::getPanel('Certificados digitais', $html, 'solucoes_certificados',
			'Certificados com verificação QR — CTI Educacional',
			'Emissão de certificados com QR Code e validação pública online.'
		);
	}
}
