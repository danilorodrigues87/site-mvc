<?php

namespace App\Model\Entity;
use App\Model\Db\Database;

class Trilhas{

	public 
    $id,
    $id_admin,
    $valor_mensal,
    $duracao,
    $descricao,
    $nome,
    $img,
    $id_categoria,
    $site,
    $carga_h;

	//RETORNA COM BASE NO ID
	public static function getTrilhaById($id){

		return self::getTrilha('id = '.$id)->fetchObject(self::class);

	}


	//RETORNA A INFORMAÇÃO
	public static function getTrilha(
    $where = null,
    $order = null,
    $limit = null,
    $fields = '*',
    $innerJoin = null,
    $group = null
){
    return (new Database('trilhas'))->select(
        $where,
        $order,
        $limit,
        $fields,
        $innerJoin,
        $group
    );
}


	//RETORNA A INFORMAÇÃO
	public static function getCustomTrilha($where = null){

		return (new Database())->customSelect($where);
	}



}