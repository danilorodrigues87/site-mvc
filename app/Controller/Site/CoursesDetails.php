<?php
namespace App\Controller\Site;
use \App\Utils\View;
use \App\Model\Entity\Trilhas;
use \App\Model\Db\Pagination;
use \App\Common\Helpers\NumeroHelper;

class CoursesDetails extends Page{


	//RETORNA A RENDERIZAÇÃO DA PÁGINA
	
	public static function getDetails($request, $id) {

		$dados = self::getCoursesDetails($id);

		if(isset($dados['erro'])){
			$dados ['curso'] = 'Não encontrado';
			$dados ['carga_h'] = 'Não encontrado';
			$dados ['descricao'] = 'Não encontrado';
			$dados ['valor_mensal'] = '0,00';
			$dados ['img'] = 'header.jpg';
		}

    	// RETORNA A BASE DA PAGINA
		$content = View::render('site/modules/detalhes-curso',[
			'curso' => $dados['curso'] ?? 'Sem Informação',
			'carga_h' => $dados['carga_h'] ?? '0',
			'descricao' => $dados['descricao'] ?? 'Sem informação',
			'valor_mensal' => NumeroHelper::moedaBr($dados['valor_mensal']?? 0),
			'img' => $dados['img']?? 'header.jpg',
			'cursos' => self::getTrilhaItens()

		]);

		// RETORNA A PÁGINA COMPLETA
		return parent::getPanel('Detalhes do Curso', $content, 'detalhes-curso',$request);

	}

	private static function getCoursesDetails($id){


    // Busca a trilha (curso)
		$obTrilhas = Trilhas::getTrilhaById($id);
		if(!$obTrilhas instanceof Trilhas){
			return ['erro' => true];
		}

    // Retorna os dados para a View
		return [
			'curso' => $obTrilhas->nome,
			'carga_h' => $obTrilhas->carga_h,
			'descricao' => $obTrilhas->descricao,
			'valor_mensal' => $obTrilhas->valor_mensal,
			'img' => $obTrilhas->img
		];


	}

	private static function getTrilhaItens() {
    // DADOS DO ADMIN
		$id_admin = 1;

		$where = 'id_admin = ' . (int)$id_admin. ' AND site =1'; 

		// RESULTADOS DA PAGINA
		$results = Trilhas::getTrilha($where, 'id DESC', 5);

    // Inicializa como string vazia
		$itens = '<div class="owl-carousel related-carousel position-relative" style="padding: 0 30px;">';

    // Verifica se existem resultados para evitar erros no loop
		if (!$results) return $itens;

    // RENDERIZA O ITEM
		while ($obDados = $results->fetchObject(Trilhas::class)) {
        // Sanitização básica de saída para evitar XSS
			$nome = htmlspecialchars($obDados->nome);
			$img  = $obDados->img;

			$itens .= '
			<a class="courses-list-item position-relative d-block overflow-hidden mb-2" href="'.URL.'/detalhes-curso/'.$obDados->id.'">
			<img class="img-fluid" src="'.URL.'/resources/assets/img/'.$img.'" alt="'.$nome.'">
			<div class="courses-text">
			<h4 class="text-center text-white px-3">'.$nome.'</h4>
			<div class="border-top w-100 mt-3">
			<div class="d-flex justify-content-between p-4">
			<span class="text-white"><i class="fa fa-user mr-2"></i>Danilo Rodrigues</span>
			<span class="text-white"><i class="fa fa-star mr-2"></i>4.5
			<small>(250)</small></span>
			</div>
			</div>
			</div>
			</a> ';
		}

		$itens.='</div>';


		return $itens;
	}


	

}