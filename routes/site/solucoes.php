<?php

use \App\Http\Response;
use \App\Controller\Site\Solucoes;

$obRouter->get('/solucoes', [
	'middlewares' => [],
	function($request){
		return new Response(200, Solucoes::index($request));
	}
]);

$obRouter->get('/solucoes/construtor-cursos', [
	'middlewares' => [],
	function($request){
		return new Response(200, Solucoes::construtor($request));
	}
]);

$obRouter->get('/solucoes/gestao', [
	'middlewares' => [],
	function($request){
		return new Response(200, Solucoes::gestao($request));
	}
]);

$obRouter->get('/solucoes/comercial', [
	'middlewares' => [],
	function($request){
		return new Response(200, Solucoes::comercial($request));
	}
]);

$obRouter->get('/solucoes/certificados', [
	'middlewares' => [],
	function($request){
		return new Response(200, Solucoes::certificados($request));
	}
]);
