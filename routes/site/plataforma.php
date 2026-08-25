<?php

use \App\Http\Response;
use \App\Controller\Site\Plataforma;

$obRouter->get('/plataforma', [
	'middlewares' => [],
	function($request){
		return new Response(200, Plataforma::index($request));
	}
]);
