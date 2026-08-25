<?php

use \App\Http\Response;
use \App\Controller\Site\CatalogoCti;

$obRouter->get('/solucoes/catalogo-cti', [
	'middlewares' => [],
	function($request){
		return new Response(200, CatalogoCti::index($request));
	}
]);
