<?php

namespace App\Model\Entity;

use App\Common\Environment;
use App\Model\Db\Database;
use PDO;

/** Leitura do branding B2B (tabela do painel — mesmo banco). */
class SiteB2bBranding {

	private static function dbConfigured(): bool {
		$user = Environment::get('DB_USER');
		$name = Environment::get('DB_NAME');
		return is_string($user) && $user !== '' && is_string($name) && $name !== '';
	}

	public static function tabelasExistem(): bool {
		static $cache = null;
		if ($cache !== null) {
			return $cache;
		}
		if (!self::dbConfigured()) {
			$cache = false;
			return false;
		}
		try {
			$st = (new Database())->execute("SHOW TABLES LIKE 'site_b2b_branding'");
			$cache = (bool)$st->fetch(PDO::FETCH_NUM);
		} catch (\Throwable $e) {
			$cache = false;
		}
		return $cache;
	}

	public static function getRow(): ?array {
		if (!self::tabelasExistem()) {
			return null;
		}
		try {
			$row = (new Database('site_b2b_branding'))
				->select('id = 1', null, '1')
				->fetch(PDO::FETCH_ASSOC);
			return is_array($row) ? $row : null;
		} catch (\Throwable $e) {
			return null;
		}
	}
}
