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
		$data = [
			'nome'   => $this->nome ?? null,
			'email'  => $this->email ?? null,
			'origem' => $this->origem ?? null,
		];

		if (isset($this->whatsapp) && $this->whatsapp !== '') {
			$data['whatsapp'] = $this->whatsapp;
		}
		if (isset($this->curso_pretendido) && $this->curso_pretendido !== '') {
			$data['curso_pretendido'] = $this->curso_pretendido;
		}
		if (isset($this->ex_aluno) && $this->ex_aluno !== '') {
			$data['ex_aluno'] = $this->ex_aluno;
		}

		$data = array_filter($data, static function ($value) {
			return $value !== null && $value !== '';
		});

		if (empty($data['email'])) {
			return false;
		}

		$obDatabase = new Database('leads');
		$this->id = $obDatabase->insert($data);

		return true;
	} 

	//RETORNA DEPOIMENTOS
	public static function getLead($where = null,$order = null,$limit = null,$fields = '*'){

		return (new Database('leads'))->select($where,$order,$limit,$fields);
	}


}