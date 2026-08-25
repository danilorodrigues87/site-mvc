<?php

use \App\Http\Response;
use \App\Controller\Site\EscolaModelo;

$obRouter->get('/escola-modelo', [
	'middlewares' => [],
	function($request){
		return new Response(200, EscolaModelo::index($request));
	}
]);
