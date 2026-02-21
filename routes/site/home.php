<?php

use \App\Http\Response;
use \App\Controller\Site;

//ROTA 
$obRouter->get('/',[
	'middlewares' => [
		
	],
	function($request){
		return new Response(200,Site\Home::index($request));
	}
]);



//ROTA 
$obRouter->post('/home-pega-lead',[
	'middlewares' => [
		
	],
	function($request){
		return new Response(200,Site\Home::getLead($request));
	}
]);
