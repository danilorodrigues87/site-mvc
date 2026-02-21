<?php

use \App\Http\Response;
use \App\Controller\Site;


//ROTA CONTATO
$obRouter->get('/contato',[
	'middlewares' => [
		
	],
	function($request){
		return new Response(200,Site\Contact::index($request));
	}
]);


//ROTA CONTATO - ENVIA MENSAGEM
$obRouter->post('/contato/envia-mensagem',[
	'middlewares' => [
		
	],
	function($request){
		return new Response(200,Site\Contact::enviaMensagem($request));
	}
]);

//ROTA ASSIANTURA DE NOTICIAS E NOVIDADES
$obRouter->post('/contato/assinatura',[
	'middlewares' => [
		
	],
	function($request){
		return new Response(200,Site\Contact::assinarNews($request));
	}
]);