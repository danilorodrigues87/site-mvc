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
