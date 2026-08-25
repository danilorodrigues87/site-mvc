<?php
namespace App\Controller\Site;
use \App\Utils\View;
use \App\Common\Helpers\SiteBrandingHelper;

class Page {

	private static $modules = [
		'home' => [
			'label' => 'Home',
			'link' => URL
		],
		'solucoes' => [
			'label' => 'Soluções',
			'link' => URL.'/solucoes'
		],
		'plataforma' => [
			'label' => 'Plataforma',
			'link' => URL.'/plataforma'
		],
		'planos' => [
			'label' => 'Planos',
			'link' => URL.'/planos'
		],
		'escola_modelo' => [
			'label' => 'Escola modelo',
			'link' => URL.'/escola-modelo'
		],
		'sobre' => [
			'label' => 'Sobre',
			'link' => URL.'/sobre'
		],
		'contato' => [
			'label' => 'Contato',
			'link' => URL.'/contato'
		]
	];

	private static function isMenuActive($hash, $currentModule){
		if ($hash === $currentModule) {
			return true;
		}
		if ($hash === 'solucoes' && strpos((string)$currentModule, 'solucoes') === 0) {
			return true;
		}
		return false;
	}

	public static function getMenu($currentModule){
		$links = '';
		$branding = SiteBrandingHelper::viewVars();

		foreach(self::$modules as $hash => $module){
			$links .= View::render('site/menu/link', [
				'label'   => $module['label'],
				'link'    => $module['link'],
				'current' => self::isMenuActive($hash, $currentModule) ? 'active' : ''
			]);
		}

		return View::render('site/menu/box', array_merge($branding, [
			'links' => $links
		]));
	}

	public static function brandingVars(){
		return SiteBrandingHelper::viewVars();
	}

	public static function getPage($title, $content, $metaTitle = null, $metaDescription = null){
		$branding = SiteBrandingHelper::viewVars();
		return View::render('site/page', array_merge($branding, [
			'title' => $title,
			'content' => $content,
			'meta_title' => $metaTitle ?: $branding['meta_title'],
			'meta_description' => $metaDescription ?: $branding['meta_description'],
		]));
	}

	public static function getPanel($title, $content, $currentModule, $metaTitle = null, $metaDescription = null){
		$contentPanel = View::render('site/panel',[
			'menu' => self::getMenu($currentModule),
			'content' => $content
		]);
		return self::getPage($title, $contentPanel, $metaTitle, $metaDescription);
	}

	public static function getCertPage($title, $content){
		$branding = SiteBrandingHelper::viewVars();
		$html = View::render('site/modules/certificado-wrap', ['content' => $content]);
		return View::render('site/page-certificado', array_merge($branding, [
			'title' => $title,
			'content' => $html,
		]));
	}

	private static function getPaginationLink($postVars, $page, $label, $reference) {
		$postVars['page'] = $page['page'];
		$filtro = isset($postVars['filtro']) ? $postVars['filtro'] : null;
		$filtroJs = $filtro !== null ? "'$filtro'" : 'null';
		$viewLink = '<li class="page-item ' . ($page['current'] ? 'active' : '') . '">
			<a class="page-link" onclick="' .$reference. '(' . $filtroJs . ',' . $postVars['page'] . ', true)" href="#">' . ($label ?? $page['page']) . '</a>
		</li>';
		return $viewLink;
	}

	public static function getPagination($request, $obPagination, $reference) {
		$pages = $obPagination->getPages();
		if (count($pages) <= 1) return '';
		$postVars = $request->getPostVars();
		$currentPage = $postVars['page'] ?? 1;
		$limit = getenv('PAGINATION_LIMIT');
		$middle = ceil($limit/2);
		$start = $middle > $currentPage ? 0 : $currentPage - $middle;
		$limit = $limit + $start;
		if ($limit > count($pages)) {
			$diff = $limit - count($pages);
			$start = $start - $diff;
		}
		$links = '';
		if ($start > 0) {
			$links .= self::getPaginationLink($postVars, reset($pages), '<<', $reference);
		}
		foreach ($pages as $page) {
			if ($page['page'] <= $start) continue;
			if ($page['page'] > $limit) {
				$links .= self::getPaginationLink($postVars, end($pages), '>>', $reference);
				break;
			}
			$links .= self::getPaginationLink($postVars, $page, null, $reference);
		}
		return '<nav><ul class="pagination">'.$links.'</ul></nav>';
	}
}
