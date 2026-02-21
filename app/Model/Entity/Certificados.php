<?php

namespace App\Model\Entity;
use App\Model\Db\Database;

class Certificados{

	public 
	$id,
	$id_aluno,
	$id_trilha,
	$id_admin,
	$modulos,
	$carga_h,
	$codigo,
	$conclusao;


	//RETORNA O CERTIFICADO COM BASE NO CODIGO
	public static function getCertificadoByCode($codigo){
		return (new Database('certificados'))->select('codigo = "'.$codigo.'"')->fetchObject(self::class);
	}


}