<?php
namespace App\Controller\Site;
use \App\Utils\View;
use \App\Common\Helpers\SiteBrandingHelper;

class About extends Page {

	public static function index($request){
		$content = View::render('site/modules/sobre', SiteBrandingHelper::viewVars());
		return parent::getPanel('Sobre', $content, 'sobre');
	}
}
