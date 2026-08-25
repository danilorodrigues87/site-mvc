<?php

use \App\Http\Response;

// Legado B2C — redireciona para catálogo CTI (B2B)
$obRouter->get('/detalhes-curso/{id}', [
	'middlewares' => [],
	function ($request, $id) {
		$request->getRouter()->redirect('/solucoes/catalogo-cti', 301);
	},
]);
