<?php
namespace App\Controller\Site;

use App\Common\Helpers\SeoHelper;

class Seo {

	public static function robots($request) {
		return SeoHelper::robotsTxt();
	}

	public static function sitemap($request) {
		return SeoHelper::sitemapXml();
	}
}
