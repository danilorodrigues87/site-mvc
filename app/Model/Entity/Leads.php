<?php

namespace App\Model\Entity;
use App\Model\Db\Database;

class Leads{

	public $id,
	$nome,
	$email,
	$whatsapp,
    $curso_pretendido,
    $ex_aluno,
    $origem,
    $status;

	//RETORNA UM USUÁRIO COM BASE NO EMAIL
	public static function getLeadByEmail($email){
		return (new Database('leads'))->select('email = "'.$email.'"')->fetchObject(self::class);
	}


	//RETORNA UM DEPOIMENTO COM BASE NO ID
	public static function getLeadById($id){

		return self::getUser('id = '.$id)->fetchObject(self::class);

	}

	//ENVIA A MENSAGEM PARA O BANCO
	public function prospectar(){
		
		//INSERIR OS DADOS PARA O BANCO DE DADOS
		$obDatabase = new Database('leads');
		$this->id = $obDatabase->insert([
			'nome' => $this->nome,
			'email' => $this->email,
			'whatsapp' => $this->whatsapp,
			'curso_pretendido' => $this->curso_pretendido,
			'ex_aluno' => $this->ex_aluno,
			'origem' => $this->origem
		]);
		
		return true;
	} 

	//RETORNA DEPOIMENTOS
	public static function getLead($where = null,$order = null,$limit = null,$fields = '*'){

		return (new Database('leads'))->select($where,$order,$limit,$fields);
	}


}