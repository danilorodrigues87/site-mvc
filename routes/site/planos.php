<?php

use \App\Http\Response;
use \App\Controller\Site\Planos;

$obRouter->get('/planos', [
	'middlewares' => [],
	function($request){
		return new Response(200, Planos::index($request));
	}
]);
