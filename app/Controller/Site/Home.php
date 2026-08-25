<?php
namespace App\Controller\Site;
use \App\Utils\View;
use \App\Common\Helpers\SiteBrandingHelper;

class Home extends Page {

	public static function index($request){
		$content = View::render('site/modules/home', SiteBrandingHelper::viewVars());
		return parent::getPanel('Home', $content, 'home');
	}
}
