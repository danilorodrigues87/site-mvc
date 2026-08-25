<?php 

namespace App\Http\Middleware;

use App\Common\Environment;

class Maintenance{

	//EXECUTA O MIDDLEWARE
	public function handle($request, $next){

		//VERIFICA O ESTADO DE MANUTENÇÃO DA PÁGINA
		if(Environment::get('MAINTENANCE') == 'true'){
			throw new \Exception('Página em manutenção, tem outra vez mais tarde.', 200);
		}

		//EXECUTA O PROXIMO NIVEL DO MIDDLEWARE
		return $next($request);
	}

}