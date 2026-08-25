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
		return new Response(200, Site\Contact::enviaMensagem($request), 'application/json');
	}
]);

//ROTA ASSINATURA DE NOTÍCIAS — desativada (sem formulário no site B2B)
$obRouter->post('/contato/assinatura',[
	'middlewares' => [
		
	],
	function($request){
		return new Response(404, [
			'success' => false,
			'message' => 'Serviço indisponível.',
		], 'application/json');
	}
]);