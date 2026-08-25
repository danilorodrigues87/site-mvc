<?php

namespace App\Common\Helpers;

use App\Common\Environment;

class SeoHelper {

	private static function baseUrl(): string {
		$url = rtrim((string)(Environment::get('URL') ?: URL), '/');
		return $url !== '' ? $url : '';
	}

	public static function canonicalPathForModule(string $module): string {
		$map = [
			'home'                 => '/',
			'solucoes'             => '/solucoes',
			'solucoes_construtor'  => '/solucoes/construtor-cursos',
			'solucoes_gestao'      => '/solucoes/gestao',
			'solucoes_comercial'   => '/solucoes/comercial',
			'solucoes_certificados'=> '/solucoes/certificados',
			'solucoes_catalogo'    => '/solucoes/catalogo-cti',
			'plataforma'           => '/plataforma',
			'planos'               => '/planos',
			'escola_modelo'        => '/escola-modelo',
			'sobre'                => '/sobre',
			'contato'              => '/contato',
		];
		return $map[$module] ?? '/';
	}

	/** @return list<array{path:string,priority:string,changefreq:string}> */
	public static function sitemapEntries(): array {
		return [
			['path' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
			['path' => '/solucoes', 'priority' => '0.9', 'changefreq' => 'monthly'],
			['path' => '/solucoes/construtor-cursos', 'priority' => '0.8', 'changefreq' => 'monthly'],
			['path' => '/solucoes/gestao', 'priority' => '0.8', 'changefreq' => 'monthly'],
			['path' => '/solucoes/comercial', 'priority' => '0.8', 'changefreq' => 'monthly'],
			['path' => '/solucoes/certificados', 'priority' => '0.8', 'changefreq' => 'monthly'],
			['path' => '/solucoes/catalogo-cti', 'priority' => '0.8', 'changefreq' => 'monthly'],
			['path' => '/plataforma', 'priority' => '0.9', 'changefreq' => 'monthly'],
			['path' => '/planos', 'priority' => '0.9', 'changefreq' => 'monthly'],
			['path' => '/escola-modelo', 'priority' => '0.7', 'changefreq' => 'monthly'],
			['path' => '/sobre', 'priority' => '0.7', 'changefreq' => 'monthly'],
			['path' => '/contato', 'priority' => '0.8', 'changefreq' => 'monthly'],
		];
	}

	public static function absoluteUrl(string $path = '/'): string {
		$base = self::baseUrl();
		if ($path === '' || $path === '/') {
			return $base.'/';
		}
		return $base.'/'.ltrim($path, '/');
	}

	public static function robotsTxt(): string {
		$base = self::baseUrl();
		$lines = [
			'User-agent: *',
			'Allow: /',
			'Disallow: /certificado',
			'Disallow: /contato/envia-mensagem',
			'Disallow: /contato/assinatura',
			'',
			'Sitemap: '.$base.'/sitemap.xml',
		];
		return implode("\n", $lines)."\n";
	}

	public static function sitemapXml(): string {
		$base = self::baseUrl();
		$lastmod = date('Y-m-d');
		$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		foreach (self::sitemapEntries() as $entry) {
			$loc = htmlspecialchars(self::absoluteUrl($entry['path']), ENT_XML1, 'UTF-8');
			$xml .= "  <url>\n";
			$xml .= '    <loc>'.$loc."</loc>\n";
			$xml .= '    <lastmod>'.$lastmod."</lastmod>\n";
			$xml .= '    <changefreq>'.$entry['changefreq']."</changefreq>\n";
			$xml .= '    <priority>'.$entry['priority']."</priority>\n";
			$xml .= "  </url>\n";
		}
		$xml .= '</urlset>';
		return $xml;
	}

	public static function pageVars(string $metaTitle, string $metaDescription, string $canonicalPath, ?array $rawBranding = null): array {
		$raw = $rawBranding ?? SiteBrandingHelper::get();
		$canonical = self::absoluteUrl($canonicalPath);
		$ogImage = trim((string)($raw['heroImageUrl'] ?? ''));
		if ($ogImage === '') {
			$ogImage = SiteBrandingHelper::themeImageUrl('carousel-1.jpg');
		}
		if ($ogImage === '') {
			$ogImage = SiteBrandingHelper::fixedLogoLightUrl();
		}

		return [
			'meta_title'            => htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8'),
			'meta_description'      => htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'),
			'canonical_url'         => htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8'),
			'og_title'              => htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8'),
			'og_description'        => htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'),
			'og_url'                => htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8'),
			'og_image'              => htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'),
			'og_site_name'          => 'CTI Educacional',
			'json_ld_organization'  => self::organizationJsonLd($raw),
		];
	}

	private static function organizationJsonLd(array $raw): string {
		$base = self::baseUrl();
		$redesRaw = is_array($raw['redesSociais'] ?? null) ? $raw['redesSociais'] : [];
		$redes = array_values(array_filter([
			$redesRaw['facebook'] ?? '',
			$redesRaw['instagram'] ?? '',
			$redesRaw['linkedin'] ?? '',
			$redesRaw['youtube'] ?? '',
		], static function ($url) {
			return is_string($url) && $url !== '' && $url !== '#';
		}));

		$data = array_filter([
			'@context'    => 'https://schema.org',
			'@type'       => 'Organization',
			'name'        => 'CTI Educacional',
			'url'         => $base.'/',
			'logo'        => SiteBrandingHelper::fixedLogoLightUrl(),
			'description' => trim((string)($raw['textoInstitucional'] ?? '')),
			'email'       => trim((string)($raw['email'] ?? '')),
			'telephone'   => trim((string)($raw['telefone'] ?? '')),
			'sameAs'      => $redes ?: null,
		], static function ($value) {
			return $value !== null && $value !== '' && $value !== [];
		});

		$json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
		if ($json === false) {
			return '';
		}
		return '<script type="application/ld+json">'.$json.'</script>';
	}
}
