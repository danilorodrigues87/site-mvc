<?php

use \App\Http\Response;

// Legado B2C — redireciona para catálogo CTI (B2B)
$obRouter->get('/cursos', [
	'middlewares' => [],
	function ($request) {
		$request->getRouter()->redirect('/solucoes/catalogo-cti', 301);
	},
]);

$obRouter->post('/cursos', [
	'middlewares' => [],
	function ($request) {
		return new Response(410, [
			'success' => false,
			'message' => 'Esta rota foi descontinuada. Acesse /solucoes/catalogo-cti',
		], 'application/json');
	},
]);
