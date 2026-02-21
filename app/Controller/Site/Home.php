<?php
namespace App\Controller\Site;
use \App\Utils\View;
use \App\Model\Entity\Trilhas as EntityTrilhas;
use \App\Model\Entity\Leads;
use \App\Model\Db\Pagination;
use \App\Common\Communication\Email;
use \App\Common\Helpers\NumeroHelper;

class Home extends Page{


	//RETORNA A RENDERIZAÇÃO DA PÁGINA
	public static function index($request){

		$dados = self::getInfo($request);
		$cursos = self::getCursos();

		// RETORNA A BASE DA PAGINA
		$content = View::render('site/modules/home',[

			'itens' => $dados['itens'],
			'select-cursos' => $cursos

		]);

		// RETORNA A PÁGINA COMPLETA
		return parent::getPanel('Home', $content, 'home',$request);

	}

	private static function getTrilhaItens($request, &$obPagination) {
		// DADOS DO ADMIN
		$id_admin = 1;

		$where = 'id_admin = ' . (int)$id_admin. ' AND site =1'; 

		// QUANTIDADE TOTAL DE REGISTROS
		$quantidadeTotal = EntityTrilhas::getTrilha($where, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

		// PAGINA ATUAL 
		$postVars = $request->getPostVars(); 
		$paginaAtual = $postVars['page'] ?? 1;


		// INSTANCIA DE PAGINAÇÃO
		$obPagination = new Pagination($quantidadeTotal, $paginaAtual, 6);


		// RESULTADOS DA PAGINA
		$results = EntityTrilhas::getTrilha($where, 'id DESC', $obPagination->getLimit());

			// Inicializa a variável para evitar erro de "Undefined variable"
		$itens = '';

		// RENDERIZA O ITEM
		while ($obDados = $results->fetchObject(EntityTrilhas::class)) {

			$itens .= '
			<div class="courses-item position-relative">
			<img class="img-fluid" src="'.URL.'/resources/assets/img/'.$obDados->img.'" alt="'.$obDados->nome.'">
			<div class="courses-text">
			<h4 class="text-center text-white px-3">'.$obDados->nome.'</h4>
			<div class="border-top w-100 mt-3">
			<div class="d-flex justify-content-between p-4">
			<span class="text-white"><i class="fa fa-user mr-2"></i>Danilo Rodrigues</span>
			<span class="text-white"><i class="fa fa-star mr-2"></i>4.5 <small>(250)</small></span>
			</div>
			</div>
			<div class="w-100 bg-white text-center p-4" >
			<a class="btn btn-primary" href="'.URL.'/detalhes-curso/'.$obDados->id.'">Detalhes do Curso</a>
			</div>
			</div>
			</div>';
		}


		return $itens;
	}

	public static function getInfo($request){

	//CONTEÚDO 
		$conteudo = [
			'itens' => self::getTrilhaItens($request,$obPagination),
			'pagination' => parent::getPagination($request,$obPagination,'listarCursos')
		];

		return $conteudo;

	}

	//LISTAGEM DE CURSOS PARA O SELECT DO CAPTADOR DE LEADS
	private static function getCursos(){

		// DADOS DO ADMIN
		$id_admin = 1;

		$where = 'id_admin = ' . (int)$id_admin. ' AND site =1'; 

		// RESULTADOS DA PAGINA
		$results = EntityTrilhas::getTrilha($where, 'id DESC');

			// Inicializa a variável para evitar erro de "Undefined variable"
		
		$itens = '';

		// RENDERIZA O ITEM
		while ($obDados = $results->fetchObject(EntityTrilhas::class)) {

			$itens .=' <option value="'.$obDados->id.'">'.$obDados->nome.'</option>';

		}

		return $itens;


	}


	public static function getLead($request){

		$postVars = $request->getPostVars(); 
		$fromName = 'Captador de Leads CTI';

		$nome = filter_var($postVars['nome'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$whatsapp = filter_var($postVars['whatsapp'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$email = filter_var($postVars['email'] ?? '', FILTER_SANITIZE_EMAIL);
		$curso_pretendido = filter_var($postVars['curso_pretendido'] ?? '', FILTER_SANITIZE_NUMBER_INT);
		$ex_aluno = filter_var($postVars['ex_aluno'] ?? '', FILTER_SANITIZE_NUMBER_INT);

		$dados = (array) EntityTrilhas::getTrilhaById($curso_pretendido);

		// SALVA AS INFORMAÇÕES DO LEAD N BANCO DE DADOS
		$leads = new Leads;	
		$leads->nome = $nome;
		$leads->email = $email;	
		$leads->whatsapp = $whatsapp;
		$leads->curso_pretendido = $curso_pretendido;
		$leads->ex_aluno = $ex_aluno;
		$leads->origem = 'Lead Interesse Curso'; 

		if(!$leads){
			return false;
		}

		$leads->prospectar();

		if($ex_aluno){ 
			$ex_aluno = 'Sim';
		} else {
			$ex_aluno = 'Não';
		} 

		$whatsapp = NumeroHelper::formatarTelefone($whatsapp);

		$address = 'leads@ctieducacional.com.br';
		$subject = 'Lead da pagina';
		$body = '<p>'.$nome.' está interessado(a) no curso de</p>';
		$body.= '<b>'.$dados['nome'].'</b><br><br>';
		$body.= '<b>Whatsapp:</b> '.$whatsapp.'<br>';
		$body.= '<b>Email:</b> '.$email.'<br>';
		$body.= '<b>Ex aluno:</b> '.$ex_aluno.'<br>';

		$obEmail = new Email;		
		$res = $obEmail->sendEmail($address,$subject,$body,$fromName);
		$res = $res ? true : $obEmail->getError;
		if(!$res){

			return false;
		}

		return true;

		
	}


}