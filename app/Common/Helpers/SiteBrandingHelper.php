<?php

namespace App\Common\Helpers;

use App\Common\Environment;
use App\Model\Entity\SiteB2bBranding;

/**
 * Branding B2B do site — API do painel, fallback DB, defaults locais.
 */
class SiteBrandingHelper {

	private static ?array $cache = null;

	public static function defaults(): array {
		return [
			'heroTitulo'         => 'Ecossistema completo para escolas profissionalizantes',
			'heroSubtitulo'      => 'Gestão completa, construtor de cursos interativos e catálogo CTI de informática — tudo em uma licença.',
			'heroCtaTexto'       => 'Solicitar demonstração',
			'heroCtaLink'        => '/contato',
			'textoInstitucional' => 'Licencie o ecossistema CTI: opere sua escola com gestão completa, disponibilize cursos padrão de informática e tecnologias básicas e crie seus próprios cursos interativos.',
			'heroImageUrl'       => null,
			'telefone'           => '(15) 99846-4457',
			'email'              => 'contato@ctieducacional.com.br',
			'whatsapp'           => '5515998464457',
			'linkAlunos'         => null,
			'statEscolas'        => '10+',
			'statAnos'           => '15+',
			'statModulos'        => '30+',
			'catalogoCtiEmBreve' => true,
			'metaTitle'          => 'CTI Educacional — Plataforma para escolas',
			'metaDescription'    => 'Licencie o ecossistema CTI: gestão escolar, construtor de cursos interativos e catálogo de informática para escolas profissionalizantes.',
			'redesSociais'       => [
				'facebook'  => 'https://www.facebook.com/ctieducacional',
				'instagram' => 'https://www.instagram.com/ctieducacional',
				'linkedin'  => 'https://www.linkedin.com/in/danilorods87/',
				'youtube'   => 'https://www.youtube.com/@ctieducacional',
			],
		];
	}

	public static function get(): array {
		if (self::$cache !== null) {
			return self::$cache;
		}
		foreach ([self::fetchFromApi(), self::fetchFromDatabase()] as $data) {
			if ($data !== null) {
				self::$cache = $data;
				return self::$cache;
			}
		}
		self::$cache = self::defaults();
		return self::$cache;
	}

	public static function ensureBrandAssets(): void {
		$root = dirname(__DIR__, 3);
		$destDir = $root.'/resources/assets/img';
		$defaultsDir = $destDir.'/_defaults';
		$iconsDir = dirname($root).DIRECTORY_SEPARATOR.'painel-cti/resources/assets/img/icons';

		if (!is_dir($destDir)) {
			@mkdir($destDir, 0775, true);
		}

		$items = [
			'logo-light.png' => ['logo-light.png', 'logo-2.png'],
			'logo-dark.png'  => ['logo-dark.png', 'icone.png'],
			'favicon.png'    => ['icone.png'],
		];

		foreach ($items as $destName => $candidates) {
			self::copyFirstReadable($destDir.'/'.$destName, self::candidatePaths($candidates, $iconsDir, $defaultsDir));
		}

		$themeImages = [
			'carousel-1.jpg',
			'sobre.jpg',
			'bg-image.jpg',
			'overlay-top.png',
			'overlay-bottom.png',
		];

		foreach ($themeImages as $fileName) {
			self::copyFirstReadable(
				$destDir.'/'.$fileName,
				self::candidatePaths([$fileName], $defaultsDir, $defaultsDir)
			);
		}
	}

	/** @param list<string> $relativeNames */
	private static function candidatePaths(array $relativeNames, string ...$baseDirs): array {
		$paths = [];
		foreach ($baseDirs as $baseDir) {
			if ($baseDir === '' || !is_dir($baseDir)) {
				continue;
			}
			foreach ($relativeNames as $name) {
				$paths[] = rtrim($baseDir, '/\\').'/'.$name;
			}
		}
		return $paths;
	}

	/** @param list<string> $sources */
	private static function copyFirstReadable(string $dest, array $sources): void {
		if (is_file($dest)) {
			return;
		}
		foreach ($sources as $src) {
			if (is_readable($src)) {
				@copy($src, $dest);
				break;
			}
		}
	}

	public static function themeImageUrl(string $fileName): string {
		$root = dirname(__DIR__, 3);
		$localFs = $root.'/resources/assets/img/'.$fileName;
		if (is_file($localFs)) {
			return rtrim((string)URL, '/').'/resources/assets/img/'.$fileName;
		}
		return '';
	}

	private static function themeBackgroundCss(string $fileName, string $gradient): string {
		$url = self::themeImageUrl($fileName);
		if ($url === '') {
			return $gradient.';';
		}
		return $gradient.", url('".$url."') center center no-repeat;";
	}

	private static function resolveAssetUrl(string $localName, string $painelIcon): string {
		$root = dirname(__DIR__, 3);
		$localFs = $root.'/resources/assets/img/'.$localName;
		if (is_file($localFs)) {
			return rtrim((string)URL, '/').'/resources/assets/img/'.$localName;
		}

		$painel = rtrim((string)getenv('PAINEL_API_URL'), '/');
		if ($painel !== '') {
			return $painel.'/resources/assets/img/icons/'.$painelIcon;
		}

		return rtrim((string)URL, '/').'/resources/assets/img/'.$localName;
	}

	public static function fixedLogoLightUrl(): string {
		return self::resolveAssetUrl('logo-light.png', 'logo-2.png');
	}

	public static function fixedLogoDarkUrl(): string {
		return self::resolveAssetUrl('logo-dark.png', 'icone.png');
	}

	public static function fixedFaviconUrl(): string {
		return self::resolveAssetUrl('favicon.png', 'icone.png');
	}

	/** @deprecated use ensureBrandAssets() */
	public static function ensureLogoFiles(): void {
		self::ensureBrandAssets();
	}

	public static function assetViewVars(): array {
		return [
			'URL'            => URL,
			'favicon_url'    => htmlspecialchars(self::fixedFaviconUrl(), ENT_QUOTES, 'UTF-8'),
			'logo_light_url' => htmlspecialchars(self::fixedLogoLightUrl(), ENT_QUOTES, 'UTF-8'),
			'logo_dark_url'  => htmlspecialchars(self::fixedLogoDarkUrl(), ENT_QUOTES, 'UTF-8'),
		];
	}

	public static function viewVars(): array {
		$b = self::get();
		$base = rtrim((string)URL, '/');
		$logoLight = self::fixedLogoLightUrl();
		$logoDark = self::fixedLogoDarkUrl();
		$favicon = self::fixedFaviconUrl();
		$hero = $b['heroImageUrl'] ?: self::themeImageUrl('carousel-1.jpg');
		$ctaLink = $b['heroCtaLink'] ?? '/contato';
		if ($ctaLink !== '' && $ctaLink[0] === '/') {
			$ctaLink = $base.$ctaLink;
		}
		$wa = preg_replace('/\D/', '', (string)($b['whatsapp'] ?? ''));
		$waLink = $wa !== '' ? 'https://wa.me/'.$wa : '#';
		$redes = is_array($b['redesSociais'] ?? null) ? $b['redesSociais'] : [];
		$linkAlunos = trim((string)($b['linkAlunos'] ?? ''));
		$badgeCatalogo = !empty($b['catalogoCtiEmBreve'])
			? '<span class="badge badge-warning ml-2">Em breve</span>'
			: '';

		return [
			'hero_titulo'           => htmlspecialchars((string)$b['heroTitulo'], ENT_QUOTES, 'UTF-8'),
			'hero_subtitulo'        => htmlspecialchars((string)$b['heroSubtitulo'], ENT_QUOTES, 'UTF-8'),
			'hero_cta_texto'        => htmlspecialchars((string)$b['heroCtaTexto'], ENT_QUOTES, 'UTF-8'),
			'hero_cta_link'         => htmlspecialchars($ctaLink, ENT_QUOTES, 'UTF-8'),
			'texto_institucional'   => htmlspecialchars((string)$b['textoInstitucional'], ENT_QUOTES, 'UTF-8'),
			'logo_url'              => htmlspecialchars($logoLight, ENT_QUOTES, 'UTF-8'),
			'logo_light_url'        => htmlspecialchars($logoLight, ENT_QUOTES, 'UTF-8'),
			'logo_dark_url'         => htmlspecialchars($logoDark, ENT_QUOTES, 'UTF-8'),
			'favicon_url'           => htmlspecialchars($favicon, ENT_QUOTES, 'UTF-8'),
			'hero_image_url'        => $hero !== '' ? htmlspecialchars($hero, ENT_QUOTES, 'UTF-8') : '',
			'sobre_image_url'       => htmlspecialchars(self::themeImageUrl('sobre.jpg') ?: $hero, ENT_QUOTES, 'UTF-8'),
			'css_overlay_top'       => htmlspecialchars(
				self::themeImageUrl('overlay-top.png') !== ''
					? "background: url('".self::themeImageUrl('overlay-top.png')."') top center no-repeat; background-size: cover;"
					: 'background: transparent;',
				ENT_QUOTES,
				'UTF-8'
			),
			'css_overlay_bottom'    => htmlspecialchars(
				self::themeImageUrl('overlay-bottom.png') !== ''
					? "background: url('".self::themeImageUrl('overlay-bottom.png')."') bottom center no-repeat; background-size: cover;"
					: 'background: transparent;',
				ENT_QUOTES,
				'UTF-8'
			),
			'css_bg_image'          => htmlspecialchars(
				self::themeBackgroundCss(
					'bg-image.jpg',
					'linear-gradient(rgba(40, 120, 235, 0.05), rgba(40, 120, 235, 0.05))'
				).' background-size: cover; background-attachment: fixed;',
				ENT_QUOTES,
				'UTF-8'
			),
			'css_hero_background'   => htmlspecialchars(
				$hero !== ''
					? "linear-gradient(rgba(40, 120, 235, 0.88), rgba(40, 120, 235, 0.88)), url('".$hero."') center center; background-size: cover;"
					: 'linear-gradient(rgba(40, 120, 235, 0.88), rgba(40, 120, 235, 0.88));',
				ENT_QUOTES,
				'UTF-8'
			),
			'telefone'              => htmlspecialchars((string)$b['telefone'], ENT_QUOTES, 'UTF-8'),
			'email'                 => htmlspecialchars((string)$b['email'], ENT_QUOTES, 'UTF-8'),
			'whatsapp_link'         => htmlspecialchars($waLink, ENT_QUOTES, 'UTF-8'),
			'link_alunos'           => $linkAlunos !== '' ? htmlspecialchars($linkAlunos, ENT_QUOTES, 'UTF-8') : '',
			'link_alunos_html'      => $linkAlunos !== ''
				? '<a class="text-white-50 mb-2 d-block" href="'.htmlspecialchars($linkAlunos, ENT_QUOTES, 'UTF-8').'" target="_blank" rel="noopener"><i class="fa fa-angle-right mr-2"></i>Área alunos CTI Guapiara</a>'
				: '',
			'stat_escolas'          => htmlspecialchars((string)$b['statEscolas'], ENT_QUOTES, 'UTF-8'),
			'stat_anos'             => htmlspecialchars((string)$b['statAnos'], ENT_QUOTES, 'UTF-8'),
			'stat_modulos'          => htmlspecialchars((string)$b['statModulos'], ENT_QUOTES, 'UTF-8'),
			'catalogo_badge'        => $badgeCatalogo,
			'meta_title'            => htmlspecialchars((string)$b['metaTitle'], ENT_QUOTES, 'UTF-8'),
			'meta_description'      => htmlspecialchars((string)$b['metaDescription'], ENT_QUOTES, 'UTF-8'),
			'redes_facebook'        => htmlspecialchars((string)($redes['facebook'] ?? ''), ENT_QUOTES, 'UTF-8'),
			'redes_instagram'       => htmlspecialchars((string)($redes['instagram'] ?? ''), ENT_QUOTES, 'UTF-8'),
			'redes_linkedin'        => htmlspecialchars((string)($redes['linkedin'] ?? ''), ENT_QUOTES, 'UTF-8'),
			'redes_youtube'         => htmlspecialchars((string)($redes['youtube'] ?? ''), ENT_QUOTES, 'UTF-8'),
		];
	}

	private static function mapRowToBranding(array $row): array {
		$redes = [];
		if (!empty($row['redes_sociais_json'])) {
			$decoded = json_decode((string)$row['redes_sociais_json'], true);
			if (is_array($decoded)) {
				$redes = $decoded;
			}
		}
		$painelBase = rtrim((string)getenv('PAINEL_API_URL'), '/');
		if ($painelBase === '') {
			$painelBase = rtrim((string)getenv('URL_IMG'), '/');
		}
		$heroUrl = null;
		if (!empty($row['hero_image']) && $painelBase !== '') {
			$heroUrl = $painelBase.'/uploads/img/site-b2b/'.basename((string)$row['hero_image']);
		}

		return array_merge(self::defaults(), array_filter([
			'heroTitulo'         => trim((string)($row['hero_titulo'] ?? '')) ?: null,
			'heroSubtitulo'      => trim((string)($row['hero_subtitulo'] ?? '')) ?: null,
			'heroCtaTexto'       => trim((string)($row['hero_cta_texto'] ?? '')) ?: null,
			'heroCtaLink'        => trim((string)($row['hero_cta_link'] ?? '')) ?: null,
			'textoInstitucional' => trim((string)($row['texto_institucional'] ?? '')) ?: null,
			'heroImageUrl'       => $heroUrl,
			'telefone'           => trim((string)($row['telefone'] ?? '')) ?: null,
			'email'              => trim((string)($row['email'] ?? '')) ?: null,
			'whatsapp'           => trim((string)($row['whatsapp'] ?? '')) ?: null,
			'linkAlunos'         => trim((string)($row['link_alunos'] ?? '')) ?: null,
			'statEscolas'        => trim((string)($row['stat_escolas'] ?? '')) ?: null,
			'statAnos'           => trim((string)($row['stat_anos'] ?? '')) ?: null,
			'statModulos'        => trim((string)($row['stat_modulos'] ?? '')) ?: null,
			'catalogoCtiEmBreve' => !empty($row['catalogo_cti_em_breve']),
			'metaTitle'          => trim((string)($row['meta_title'] ?? '')) ?: null,
			'metaDescription'    => trim((string)($row['meta_description'] ?? '')) ?: null,
			'redesSociais'       => $redes ?: null,
		], function ($v) {
			return $v !== null;
		}));
	}

	private static function fetchFromDatabase(): ?array {
		$user = Environment::get('DB_USER');
		$name = Environment::get('DB_NAME');
		if (!is_string($user) || $user === '' || !is_string($name) || $name === '') {
			return null;
		}
		$row = SiteB2bBranding::getRow();
		if ($row === null) {
			return null;
		}
		return self::mapRowToBranding($row);
	}

	private static function fetchFromApi(): ?array {
		$apiBase = rtrim((string)getenv('PAINEL_API_URL'), '/');
		if ($apiBase === '') {
			return null;
		}
		$url = $apiBase.'/api/v1/site/public/branding';
		$cacheDir = (string)getenv('CACHE_DIR');
		$cacheFile = $cacheDir !== '' ? rtrim($cacheDir, '/\\').'/site_b2b_branding.json' : '';
		$ttl = max(60, (int)(getenv('CACHE_TIME') ?: 300));

		if ($cacheFile !== '' && is_file($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
			$raw = @file_get_contents($cacheFile);
			if ($raw !== false) {
				$data = json_decode($raw, true);
				if (is_array($data['branding'] ?? null)) {
					return array_merge(self::defaults(), $data['branding']);
				}
			}
		}

		$ctx = stream_context_create(['http' => ['timeout' => 4, 'ignore_errors' => true]]);
		$raw = @file_get_contents($url, false, $ctx);
		if ($raw === false) {
			return null;
		}
		$data = json_decode($raw, true);
		if (!is_array($data['branding'] ?? null)) {
			return null;
		}
		if ($cacheFile !== '') {
			@mkdir(dirname($cacheFile), 0775, true);
			@file_put_contents($cacheFile, $raw);
		}
		return array_merge(self::defaults(), $data['branding']);
	}

	/** Limpa cache (útil após salvar no Master). */
	public static function clearCache(): void {
		self::$cache = null;
		$cacheDir = (string)getenv('CACHE_DIR');
		$cacheFile = $cacheDir !== '' ? rtrim($cacheDir, '/\\').'/site_b2b_branding.json' : '';
		if ($cacheFile !== '' && is_file($cacheFile)) {
			@unlink($cacheFile);
		}
	}
}
