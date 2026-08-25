<?php 

require __DIR__.'/../vendor/autoload.php';

use \App\Utils\View;
use \App\Common\Environment;
use \App\Http\Middleware\Queue as MiddlewareQueue;

//CARREGA VARIAVEIS DE AMBIENTE (antes de qualquer classe que use Database)
Environment::load(__DIR__.'/../');

$appUrl = (string)Environment::get('URL', '');
$appDebug = strtolower(trim((string)Environment::get('APP_DEBUG', '')));
$isLocal = $appDebug === 'true'
	|| preg_match('/localhost|127\.0\.0\.1/i', $appUrl) === 1;

if ($isLocal) {
	ini_set('display_errors', '1');
	error_reporting(E_ALL);
} else {
	ini_set('display_errors', '0');
	error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

//DEFINE A CONSTANTE DE URL
define('URL', Environment::get('URL'));
define('URL_IMG', Environment::get('URL_IMG'));
define('SITE', Environment::get('SITE'));
define('TIMEZONE', Environment::get('TIMEZONE'));
date_default_timezone_set(TIMEZONE ?: 'America/Sao_Paulo');
header('Content-Type: text/html; charset=utf-8');

\App\Common\Helpers\SiteBrandingHelper::ensureBrandAssets();

//TOKEN DE SISTEMA
define('SYSTEM_TOKEN', Environment::get('SYSTEM_TOKEN'));

//DEFINE O VALOR PADRÃO DAS VARIAVEIS
View::init([
'URL' => URL,
'URL_IMG' => URL_IMG
]);

//DEFINE O MAPEAMENTO DE MIDDLEWARES
MiddlewareQueue::setMap([
	'maintenance' => \App\Http\Middleware\Maintenance::class,
	'api' => \App\Http\Middleware\Api::class,
	'user-basic-auth' => \App\Http\Middleware\UserBasicAuth::class,
	'jwt-auth' => \App\Http\Middleware\JWTAuth::class,
	'cache' => \App\Http\Middleware\Cache::class
]);

//DEFINE O MAPEAMENTO DE MIDDLEWARES PADRÕES (EXECUTA EM TODAS AS ROTAS)
MiddlewareQueue::setDefault([
	'maintenance'
]);