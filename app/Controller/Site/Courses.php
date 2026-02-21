<?php
namespace App\Controller\Site;
use \App\Utils\View;
use \App\Model\Entity\Trilhas as EntityTrilhas;
use \App\Model\Db\Pagination;

class Courses extends Page{


	//RETORNA A RENDERIZAÇÃO DA PÁGINA
	public static function index($request){

		// RETORNA A BASE DA PAGINA
		$content = View::render('site/modules/cursos',[]);

		// RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Cursos', $content, 'cursos',$request);

	}

	private static function getTrilhaItens($request, &$obPagination) {
		// DADOS DO ADMIN
		$id_admin = 1;

		$where = 'id_admin = ' . (int)$id_admin. ' AND site =1'; 

		//PAGINA ATUAL
		$postVars = $request->getPostVars();
		$paginaAtual = $postVars['page'] ?? 1;
	
		// QUANTIDADE TOTAL DE REGISTROS
		$quantidadeTotal = EntityTrilhas::getTrilha($where, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
		
		// INSTANCIA DE PAGINAÇÃO
		$obPagination = new Pagination($quantidadeTotal, $paginaAtual, 6);


		// RESULTADOS DA PAGINA
		$results = EntityTrilhas::getTrilha($where, 'id DESC', $obPagination->getLimit());

			// Inicializa a variável para evitar erro de "Undefined variable"
		$itens = ' <div class="row">';
	
		// RENDERIZA O ITEM
		while ($obDados = $results->fetchObject(EntityTrilhas::class)) {
			
			$itens .= '
			<div class="col-lg-4 col-md-6 pb-4">
				<a class="courses-list-item position-relative d-block overflow-hidden mb-2" href="'.URL.'/detalhes-curso/'.$obDados->id.'">
					<img class="img-fluid" src="'.URL.'/resources/assets/img/'.$obDados->img.'" alt="'.$obDados->nome.'">
					<div class="courses-text">
						<h4 class="text-center text-white px-3">'.$obDados->nome.'</h4>
						<div class="border-top w-100 mt-3">
							<div class="d-flex justify-content-between p-4">
								<span class="text-white"><i class="fa fa-user mr-2"></i>Danilo Rodrigues</span>
								<span class="text-white"><i class="fa fa-star mr-2"></i>4.5 <small>(250)</small></span>
							</div>
						</div>
					</div>
				</a>
			</div>';
		}

		$itens .= '</div>';
	
		return $itens;
	}

	public static function getInfo($request){

	//CONTEÚDO 
		$conteudo = [
			'itens' => self::getTrilhaItens($request,$obPagination),
			'pagination' => parent::getPagination($request,$obPagination,'listarCursos')
		];

		return json_encode($conteudo);


	}

}