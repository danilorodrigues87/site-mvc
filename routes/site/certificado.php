<?php

use \App\Http\Response;
use \App\Controller\Site;


//ROTA SOBRE
$obRouter->get('/certificado',[
	'middlewares' => [
		
	],
	function($request){
		return new Response(200,Site\Certificate::index($request));
	}
]);