<?php 
namespace App\Controller\Site;
use \App\Utils\View;

class Page {


    // PAGINAS DISPONIVEIS
    private static $modules = [
        'home' => [
            'label' => 'Home',
            'link' => URL
        ],
        'sobre' => [
            'label' => 'Sobre',
            'link' => URL.'/sobre'
        ],
        'cursos' => [
            'label' => 'Cursos',
            'link' => URL.'/cursos'
        ],
        'contato' => [
            'label' => 'Contato',
            'link' => URL.'/contato'
        ]

    ];


    public static function getMenu($currentModule){
      // LINKS DO MENU
      $links = '';
  
      // ITERA OS MODULOS
      foreach(self::$modules as $hash => $module){
          $links .= View::render('site/menu/link', [
              'label'   => $module['label'],
              'link'    => $module['link'],
              'current' => $hash == $currentModule ? 'active' : '' 
          ]); 
      }
  
      // RETORNA A RENDERIZAÇÃO DO BOX (FORA DO LOOP)
      return View::render('site/menu/box', [
          'links' => $links
      ]);
  }


	// RETORNA O CONTEUDO (VIEW) ESTRUTURA GENERICA PAGINA
	public static function getPage($title,$content){
		return View::render('site/page',[
			'title' => $title,
			'content' => $content
		]);
	}

    //RENDERIZA A VIEW DO PANEL
  public static function getPanel($title,$content,$currentModule){

    //RENDERIZA A VIEW DO TOP - MENU
    $contentPanel = View::render('site/panel',[
      'menu' => self::getMenu($currentModule),
      'content' => $content
    ]);

    //RETONA A PAGINA RENDERIZADA
    return self::getPage($title,$contentPanel);

  }



	private static function getPaginationLink($postVars, $page, $label, $reference) {
    // ALTERA A PÁGINA
    $postVars['page'] = $page['page'];

     // Obtém o filtro, se existir
    $filtro = isset($postVars['filtro']) ? $postVars['filtro'] : null;

    // Garante que o filtro seja passado corretamente como string
    $filtroJs = $filtro !== null ? "'$filtro'" : 'null';

    // VIEW
    $viewLink = '<li class="page-item ' . ($page['current'] ? 'active' : '') . '">
        <a class="page-link" onclick="' .$reference. '(' . $filtroJs . ',' . $postVars['page'] . ', true)" href="#">' . ($label ?? $page['page']) . '</a>
    </li>';
    return $viewLink;
}



// RENDERIZA O LAYOUT DE PAGINAÇÃO
	public static function getPagination($request, $obPagination, $reference) {
    // PÁGINAS
		$pages = $obPagination->getPages();


    // VERIFICA A QUANTIDADE DE PÁGINAS
		if (count($pages) <= 1) return '';

    // POST
		$postVars = $request->getPostVars();

    // PÁGINA ATUAL
		$currentPage = $postVars['page'] ?? 1;

    // LIMITE DE PÁGINA
		$limit = getenv('PAGINATION_LIMIT');

    // MEIO DA PAGINAÇÃO
		$middle = ceil($limit/2);

    // INÍCIO DA PAGINAÇÃO
		$start = $middle > $currentPage ? 0 : $currentPage - $middle;

    // AJUSTA O FINAL DA PAGINAÇÃO
		$limit = $limit + $start;

    // AJUSTA O INÍCIO DA PAGINAÇÃO
		if ($limit > count($pages)) {
			$diff = $limit - count($pages);
			$start = $start - $diff;
		}

    // LINKS DE PAGINAÇÃO
		$links = '';

    // LINK INICIAL
		if ($start > 0) {
			$links .= self::getPaginationLink($postVars, reset($pages), '<<', $reference);
		}

    // RENDERIZA OS ITENS
		foreach ($pages as $page) {
        // VERIFICA O START DA PAGINAÇÃO
			if ($page['page'] <= $start) continue;

        // VERIFICA O LIMITE DA PAGINAÇÃO
			if ($page['page'] > $limit) {
				$links .= self::getPaginationLink($postVars, end($pages), '>>', $reference);
				break;
			}

			$links .= self::getPaginationLink($postVars, $page,null , $reference);
		}

    // RENDERIZAÇÃO BOX DE PAGINAÇÃO
		$paginacao = 
		'<nav>
		<ul class="pagination">
		' . $links . '        
		</ul>
		</nav>';

		return $paginacao;
	}


}
