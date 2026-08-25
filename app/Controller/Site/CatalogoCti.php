<?php
namespace App\Controller\Site;
use \App\Utils\View;
use \App\Common\Helpers\SiteBrandingHelper;

class CatalogoCti extends Page {

	public static function index($request){
		$content = View::render('site/modules/catalogo-cti', SiteBrandingHelper::viewVars());
		return parent::getPanel('Catálogo CTI', $content, 'solucoes_catalogo',
			'Catálogo CTI de cursos — Informática e tecnologia',
			'Cursos padrão CTI de informática e tecnologias básicas para escolas parceiras licenciarem na plataforma.'
		);
	}
}
