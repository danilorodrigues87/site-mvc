<?php
namespace App\Controller\Site;
use \App\Utils\View;
use \App\Model\Entity\Certificados as EntityCertificate;
use \App\Model\Entity\Trilhas;
use \App\Model\Entity\User;


class Certificate extends Page{


	//RETORNA A RENDERIZAÇÃO DA PÁGINA
	public static function index($request){

		$dados = self::getCertificado($request);

		if(isset($dados['erro'])){
			$dados ['status'] = 'Dados não encontrados, contate o suporte do CTI';
			$dados ['aluno'] = 'Não encontrado';
			$dados ['curso'] = 'Não encontrado';
			$dados ['carga_h'] = 'Não encontrado';
			$dados ['modulos'] = 'Não encontrado';
			$dados ['conclusao'] = 'Não encontrado';
            $dados ['codigo'] = 'Não encontrado';
		}


		// RETORNA A BASE DA PAGINA
		$content = View::render('site/modules/certificado',[

			'aluno' => $dados['aluno'],
			'curso' => $dados['curso'],
			'carga_h' => $dados['carga_h'],
			'modulos' => $dados['modulos'],
			'conclusao' => $dados['conclusao'],
			'codigo' => $dados['codigo']

		]);

		// RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Certificado', $content, 'certificado',$request);

	}

	public static function getCertificado($request){
    $queryParams = $request->getQueryParams();
    $codigo = $queryParams['crt'] ?? '';

    // Verifica se o código foi passado
    if(empty($codigo)){
        return ['erro' => true];
    }



    // Busca o certificado no banco MySQL
    $obCertificado = EntityCertificate::getCertificadoByCode($codigo);
    if(!$obCertificado instanceof EntityCertificate){
        return ['erro' => true];
    }

    // Busca a trilha (curso)
    $obTrilhas = Trilhas::getTrilhaById($obCertificado->id_trilha);
    if(!$obTrilhas instanceof Trilhas){
        return ['erro' => true];
    }

    $obUser = User::getUser('id = ' . $obCertificado->id_aluno, null,null,'nome');
    $obUser = $obUser->fetchObject(User::class);

    if(!$obUser instanceof User){
        return ['erro' => true];		
    }
    
    // Retorna os dados para a View
    return [
        'aluno' => $obUser->nome,
        'curso' => $obTrilhas->nome,
        'carga_h' => $obCertificado->carga_h,
        'modulos' => $obCertificado->modulos,
        'conclusao' => $obCertificado->conclusao,
        'codigo' => $codigo
    ];
}



}