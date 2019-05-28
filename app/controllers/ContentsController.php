<?php

class ContentsController extends BaseController {

	public static function getPageContent($menu_item_id, $cs_id, $id) {
		$sections = PagesController::getPageSections($id);

		return View::make('pages.page', ['sections' => $sections, 'menu_item_id' => $menu_item_id, 'cs_id' => $cs_id, 'page_id' => $id]);
	}

	public function page($menu_item_id, $cs_id, $page_id) {
		$sections = MenusController::getPageSections($menu_item_id, $cs_id, $page_id);		
		// echo '<pre>'; print_r($sections); exit;

		return View::make('pages.content.page', ['sections' => $sections, 'menu_item_id' => $menu_item_id, 'cs_id' => $cs_id, 'page_id' => $page_id]);
	}

	public function getContentSection($menu_item_id) {
		$menu_item = MenuItem::find($menu_item_id);
		$content_sections = ContentSection::where('menu_item_id', $menu_item_id)->get();
		$pages = [];
		foreach($content_sections as $cs) {
			if($cs->type == 'page_section' || $cs->type == 'page') {
				if(count($cs->pages)) {
					$pages[] = $cs->pages[0];
				}
			}
		}

		// echo '<pre>'; print_r($content_sections); exit;
		return View::make('pages.content-sections.content-section', ['pages' => $pages, 'menu_item' => $menu_item, 'menu_item_id' => $menu_item_id]);
	}
}
