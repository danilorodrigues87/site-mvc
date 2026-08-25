<?php
namespace App\Controller\Site;

class Plataforma extends Page {

	public static function index($request){
		$html = \App\Utils\View::render('site/modules/plataforma', parent::brandingVars());
		return parent::getPanel('Plataforma CTI', $html, 'plataforma',
			'Painel CTI — Plataforma para escolas',
			'Conheça o painel de gestão, portal do aluno e ferramentas pedagógicas do ecossistema CTI.'
		);
	}
}
