<?php
namespace App\Controller\Site;

class Planos extends Page {

	public static function index($request){
		$html = \App\Utils\View::render('site/modules/planos', parent::brandingVars());
		return parent::getPanel('Planos de licença', $html, 'planos',
			'Planos de licença CTI — Escolas parceiras',
			'Escolha o nível de licença do ecossistema CTI para a sua escola profissionalizante.'
		);
	}
}
