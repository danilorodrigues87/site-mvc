<?php

use \App\Http\Response;
use \App\Controller\Site;


//ROTA SOBRE
$obRouter->get('/cursos',[
	'middlewares' => [
		
	],
	function($request){
		return new Response(200,Site\Courses::index($request));
	}
]);

//ROTA SOBRE
$obRouter->post('/cursos',[
	'middlewares' => [
		
	],
	function($request){
		return new Response(200,Site\Courses::getInfo($request));
	}
]);

