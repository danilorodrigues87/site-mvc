<?php

use \App\Http\Response;
use \App\Controller\Site;


//ROTA DE CONFIRMA EXCLUSAO
$obRouter->get('/detalhes-curso/{id}',[
	'middlewares' => [

	],
	function($request,$id){
		return new Response(200,Site\CoursesDetails::getDetails($request,$id));
	}
]);
