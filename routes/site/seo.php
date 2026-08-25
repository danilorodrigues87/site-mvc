<?php

use \App\Http\Response;
use \App\Controller\Site\Seo;

$obRouter->get('/robots.txt', [
	'middlewares' => [],
	function ($request) {
		return new Response(200, Seo::robots($request), 'text/plain; charset=utf-8');
	},
]);

$obRouter->get('/sitemap.xml', [
	'middlewares' => [],
	function ($request) {
		return new Response(200, Seo::sitemap($request), 'application/xml; charset=utf-8');
	},
]);
