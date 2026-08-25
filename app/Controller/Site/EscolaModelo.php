<?php
namespace App\Controller\Site;

class EscolaModelo extends Page {

	public static function index($request){
		$html = \App\Utils\View::render('site/modules/escola-modelo', parent::brandingVars());
		return parent::getPanel('Escola modelo', $html, 'escola_modelo',
			'CTI Guapiara — Escola modelo',
			'Conheça como o CTI Educacional em Guapiara opera com a própria plataforma.'
		);
	}
}
