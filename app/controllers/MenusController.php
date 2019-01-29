<?php

class MenusController extends BaseController {

	public function showWelcome()
	{
		return View::make('hello');
	}

	public static function getPageSections($page_id) {
		$sections = [];
		if($page_id > 0) {
			$p_sections = PageSection::where('page_id', $page_id)->get()->sortBy('sort_order');
			if($p_sections != null && count($p_sections)) {
				foreach($p_sections as $ps) {
					$result = [];
					if($ps->content_id > 0) {
						$result = PageContent::find($ps->content_id);
						if(isset($result)) {
							$result->type = 'content';
						}
					} elseif($ps->gallery_id > 0) {
						$result = PageImageSlider::with(['page_slider_images'])->find($ps->gallery_id);
						if(isset($result)) {
							$result->type = 'slider';
						}	
					// } elseif($ps->h2_id > 0) {
					// 	$result = H2::find($ps->h2_id);
					// 	if(isset($result)) {
					// 		$result->type = 'h2';
					// 	}	
					} elseif($ps->image_id > 0) {
						$result = Image::find($ps->image_id);
						if(isset($result)) {
							$result->type = 'image';
						}	
					} elseif($ps->youtube_id > 0) {
						$result = Youtube::find($ps->youtube_id);
						if(isset($result)) {
							$result->type = 'youtube';
						}	
					} elseif($ps->audio_id > 0) {
						$result = Audio::find($ps->audio_id);
						if(isset($result)) {
							$result->type = 'audio';
						}	
					} elseif($ps->image_grid_id > 0) {
						$result = ImageGrid::with(['grid_images'])->find($ps->image_grid_id);
						if(isset($result)) {
							$result->type = 'image_grid';
						}	
					} elseif($ps->h2_text_id > 0) {
						$result = H2text::find($ps->h2_text_id);
						if(isset($result)) {
							$result->type = 'h2text';
						}	
					}
					
					if(count($result)) {
						$result['ps_id'] = $ps->id;
						$sections[] = $result;
					}
				}
			}
		}
		// echo '<pre>'; print_r($sections); exit;

		return $sections;		
	}

	public function getMenuItem($menu_item) {
		$rdr = DB::table('redirects')->where('slug', $menu_item)->first();
		if($rdr) {
			$url = $rdr->redirect_url;
			header("Location:$url"); return;
		}

		if($menu_item == 'online-katalog') {
			return View::make('pages.external.webmill');		
		}
		if($menu_item == 'exhibitions') {
			$pages = $this->getExhibitions();

			return View::make('pages.exhibitions', ['pages' => $pages]);
		}
		$pg_links = $this->getPageLinksByTitle($menu_item);
		// echo '<pre>'; print_r($pg_links); exit;
		$page = [];		
		$pg_sections = [];
		if(is_array($pg_links) && count($pg_links)) {
			return Redirect::action('MenusController@getPage', [$menu_item, $pg_links[0]->link]);
		}

		return Redirect::action('MenusController@getStartPage');
	}
	
	public function getTestView($page_id) {
		$pg_sections = $this->getPageSections($page_id);
		$page = Page::with(['page_sections', 'page_image_sliders', 'cluster', 'page_image_sliders.page_slider_images', 'content_section', 'teaser'])->find($page_id);

		$calendar = [];
		if(isset($page->cluster_id) && is_numeric($page->cluster_id) && intval($page->cluster_id) > 0) {
			$calendar = KEventsController::getEventsCalendar(null, false, $page->cluster_id);	
		}
		// echo '<pre>'; print_r($pg_sections); exit;

		return View::make('pages.view', ['page' => $page, 'calendar' => $calendar, 'pg_sections' => $pg_sections]);
	}

	public function getExhibitions($category = 'current', $tag = null) {
		// echo '>> CAT >> '. $category; exit;
		// echo $tag; exit;
		if($tag) {
			$query = 'select id from tags where tag_de like "'. str_replace('-', ' ', $tag) . '"';
			$tag = DB::select($query);
		}
		$pages = [];
		if(isset($category) && strlen($category) > 0) {
			$pages = $this->getExbPagesByCat($category);
		} else {
			$pages = Page::with(['page_image_sliders', 'sponsor_groups', 'sponsor_groups.sponsors', 'downloads', 'cluster', 'banner', 
								 'banner.banner_text', 'page_image_sliders.page_slider_images', 'tags'])
							->where('active', 1)
							->where('page_type', 'exhibition')
							->get()->sortBy('start_date');
		}
		$results = [];
		$tags = Tag::all()->sortBy('tag_de');
		$main_exb = [];
		$use_main_exb = false;
		foreach($pages as $p) {
			if($p->is_main_teaser == 1) {
				$main_exb = $p;
				$use_main_exb = true;
			}
		}
		$results['pages'] = $pages;
		$results['category'] = strtolower($category);
		// echo count($pages); exit;
		if($category && $pages && count($pages)) {
			return View::make('pages.exhibitions', ['pages' => $pages, 'main_exb' => $main_exb, 'use_main_exb' => $use_main_exb, 
					'category' => strtolower($category), 'tags' => $tags]);
		}

		return Redirect::action('MenusController@getStartPage');
	}

	public function getExbPagesByCat($cat) {
		$pages = [];
		$today = date('Y-m-d');
		if(strtolower($cat) == 'current') {
			$eps = Page::with('teaser')->where('page_type', 'exhibition')->orderBy('start_date', 'DESC')
	                ->where('active', 1)
					->get();
			foreach($eps as $ep) {
				if($ep->start_date <= $today && $ep->end_date >= $today) {
					$pages[] = $ep;
				}
			}			
		}
		if(strtolower($cat) == 'upcoming') {
			$pages = Page::where('page_type', 'exhibition')
			               ->where('start_date', '>', $today)
			               ->where('end_date', '>', $today)
			               ->where('active', 1)
			               ->orderBy('start_date', 'ASC')
			               ->get();			
		}
		if(strtolower($cat) == 'past') {
			$pages = Page::where('page_type', 'exhibition')
			               ->where('start_date', '<', $today)
			               ->where('end_date', '<', $today)
			               ->where('active', 1)
			               ->orderBy('start_date', 'DESC')
			               ->get();			
		}

		return $pages;
	}

	public function exb_page_sort($p1, $p2) {
		$d1 = strtotime($p1['start_date']);
		$d2 = strtotime($p2['start_date']);
		if($d1 == $d2) { return 0; }
		return (($d1 < $d2) ? 1 : -1);
	}

	public function getSubPage($menu_item, $section, $page_title) {		
		$page_id = 0;
		$query = 'select p.* from pages p, content_sections cs, menu_items mi 
		          where p.content_section_id = cs.id 
		            and p.active = 1
		            and cs.menu_item_id = mi.id 
		            and lower(replace(cs.title_en, " ", "-")) = "'. $section . '" 
		            and lower(replace(mi.title_en, " ", "-")) = "'. $menu_item . '"
		            and lower(replace(p.title_en, " ", "-")) = "'. $page_title . '"
		          limit 1';
		$page = DB::select($query);
		if($page) {
			$page_id = $page[0]->id;
		}
		if($page_id > 0) {
			$pg_sections = $this->getPageSections($page_id);
			$page = Page::with(['page_contents', 'page_image_sliders', 'sponsor_groups', 'sponsor_groups.sponsors', 'downloads', 'cluster', 'banner', 'banner.banner_text', 
							'page_image_sliders.page_slider_images', 'h2text', 'teaser'])->find($page_id);
			$calendar = [];
			if(isset($page->cluster_id) && is_numeric($page->cluster_id) && intval($page->cluster_id) > 0) {
				$calendar = KEventsController::getEventsCalendar(null, false, $page->cluster_id);	
			}		
			$dl_found = count($page->downloads) > 0 ? true : false;
			/*foreach($page->downloads as $dl) {
				if(file_exists('http://cms.kunsthalle-bremen.net/files/downloads/'.$dl->filename)) { $dl_found = true; }
			}/**/
			$settings = [];		
			$set = Settings::first();
			if($set) { $settings = $set; }
			$show_membership_form = false;

			$downloads = [];
			if($page->downloads) {
				$downloads = $page->downloads->toArray();
				usort($downloads, function($a, $b) {
					if($a['sort_order'] == $b['sort_order']) { return 0; }
					return $a['sort_order'] > $b['sort_order'] ? 1 : -1;
				});
			}
			$f= fopen('logs/test_2.log', 'w+');
			fwrite($f, "downloads->>\n\n". print_r($downloads, true));
			$dl_protected = 0;
			foreach($page->downloads as $dl) {
				if($dl->protected == 1) { $dl_protected = 1; }
			}				
			$page->dl_protected = $dl_protected;

			$show_calendar = (count($calendar) > 0) ? 1 : 0;	

			$sponsors = [];
			foreach($page->sponsor_groups as $g) {
				if(!array_key_exists($g->headline, $sponsors)) {
					$sponsors[$g->headline] = [];
				}
				$sponsors[$g->headline] = $g->sponsors;
			}	
			
			return View::make('pages.sub-page', ['page' => $page, 'menu_item' => $menu_item, 'calendar' => $calendar, 
				'pg_sections' => $pg_sections, 'section' => $section, 'dl_found' => $dl_found, 'settings' => $settings, 
				'show_membership_form' => $show_membership_form, 'page_type' => 'sub_page', 'downloads' => $downloads, 
				'show_calendar' => $calendar, 'sponsors' => $sponsors ]);
		}

		return Redirect::action('MenusController@getPage', [$menu_item, $page_title]);
	}

	public function getExbPage($page_title) {		
		$page_id = 0;
		$query = 'select p.*, b.image from pages p, banners b
		          where lower(replace(p.title_en, " ", "-")) = "'. $page_title . '"
		            and p.active = 1
		            and p.page_type = "exhibition"
		            and b.page_id = p.id
		          limit 1';
		$_page = DB::select($query);
		// echo '<pre>'; print_r($page); exit;		
		if($_page) {
			$page_id = $_page[0]->id;
		}
		if($page_id > 0) {
			$pg_sections = $this->getPageSections($page_id);
			$page = Page::with(['page_contents', 'page_image_sliders', 'sponsor_groups', 'sponsor_groups.sponsors', 'downloads','cluster','banner',
				'banner.banner_text', 'page_image_sliders.page_slider_images', 'h2text', 'teaser'])->find($page_id);
			$calendar = [];
			if(isset($page->cluster_id) && is_numeric($page->cluster_id) && intval($page->cluster_id) > 0) {
				$calendar = KEventsController::getEventsCalendar(null, false, $page->cluster_id);	
			}		
			$sponsors = [];
			foreach($page->sponsor_groups as $g) {
				if(!array_key_exists($g->headline, $sponsors)) {
					$sponsors[$g->headline] = [];
				}
				$sponsors[$g->headline] = $g->sponsors;
			}	
			$settings = [];		
			$set = Settings::first();
			if($set) { $settings = $set; }
			$dl_found = count($page->downloads) > 0 ? true : false;
			// foreach($page->downloads as $dl) {
			// 	if(file_exists('http://cms.kunsthalle-bremen.net/files/downloads/'.$dl->filename)) { $dl_found = true; }
			// }
			$downloads = [];
			if($page->downloads) {
				$downloads = $page->downloads->toArray();
				usort($downloads, function($a, $b) {
					if($a['sort_order'] == $b['sort_order']) { return 0; }
					return $a['sort_order'] > $b['sort_order'] ? 1 : -1;
				});
			}
			$show_membership_form = false;
			$show_calendar = (count($calendar) > 0) ? 1 : 0;	

			return View::make('pages.exb-page', ['page' => $page, 'calendar' => $calendar, 'sponsors' => $sponsors, 'pg_sections' => $pg_sections, 
				'settings' => $settings, 'dl_found' => $dl_found, 'show_membership_form' => $show_membership_form, 'page_type'=> 'exb_page',
				'page_title' => $page_title, 'downloads' => $downloads, 'show_calendar' => $calendar ]);
		}

		return Redirect::action('MenusController@getPage', [$menu_item, $page_title]);
	}

	public function getPage($menu_item, $link, $action = null) {
		$hasMembersForm = (strtolower($menu_item) == 'jetzt-unterstützen' && strtolower($link == 'online-mitgliedsantrag')) ? true : false;

		if(strtolower($menu_item) == 'exhibitions') {
			$category = 'current';
			if($action == 'current' || $action == 'upcoming' || $action == 'past') { $category = $action; }

			return redirect()->route('exhibitions', [$category]);
		}

		$pg_links = $this->getPageLinksByTitle($menu_item, $link);

		$page = [];		
		$pg_sections = [];
		$is_page = false;
		$cs_id = 0;
		$page_id = 0;
		$pages = [];
		if(is_array($pg_links) && count($pg_links)) {
			// $page_id = $pg_links[0]->page_id;
			foreach($pg_links as $pl) {
				if($pl->current_link == 1) { 
					$pg = Page::where('content_section_id', $pl->id)
							  ->where('active', 1)
							  ->get()->sortBy('sort_order');
					if($pg) {
						$page_id = $pg[0]->id;
					}
					if($pl->type == 'page') { $is_page = true; }
					else { $cs_id = $pl->id; }
					break; 
				}
			}
			$pg_sections = $this->getPageSections($page_id);
			$page = Page::with(['page_contents', 'page_image_sliders', 'sponsor_groups', 'sponsor_groups.sponsors', 'downloads', 
								'cluster', 'banner', 'banner.banner_text', 'page_image_sliders.page_slider_images', 'h2text', 'image_grids', 
								'image_grids.grid_images', 'teaser', 'contacts', 'tags'])
						  ->where('active', 1)->find($page_id);
		}
		$calendar = [];
		if(isset($page->cluster_id) && is_numeric($page->cluster_id) && intval($page->cluster_id) > 0) {
			$calendar = KEventsController::getEventsCalendar(null, false, $page->cluster_id);	
		}
		$sponsors = [];
		if($page && $page->sponsor_groups) {
			foreach($page->sponsor_groups as $g) {
				if(strlen($g->headline) > 0) {
					if(!array_key_exists($g->headline, $sponsors)) {
						$sponsors[$g->headline] = [];
					}
					$sponsors[$g->headline] = $g->sponsors;
				}
			}	
		}			
		if($is_page) {
			$contacts = [];
			$detps = Department::all()->sortBy('sort_order');
			foreach($detps as $d) {
				$list = Contact::with(['department'])
							->where('display', 1)
							->where('department_id', $d->id)
							->get()->sortBy('sort_order');
				foreach($list as $l) {
					if(!array_key_exists($l->department->title_de, $contacts)) {
						$contacts[$l->department->title_de] = [];
					}
					$contacts[$l->department->title_de][] = $l;
				}
			}
			$settings = [];		
			$set = Settings::first();
			if($set) { $settings = $set; }
			$show_membership_form = false;
			if($link == 'online-member-form' || $link == 'online-mitgliedsantrag') {
				$show_membership_form = true;
			}
			$dl_found = count($page->downloads) > 0 ? true : false;

			// if single page
			foreach($contacts as $d => $cts) {
				$count = 0;
				foreach($cts as $c) {
					if(strlen($c->function) > 0) { ++$count; }
				}
				if($count == 0) { unset($contacts[$d]); }
			}

			$view = 'pages.page';
			if(strtolower($link) == 'team') { $view = 'pages.team'; }

			if($page) {
				$downloads = [];
				if($page->downloads) {
					$downloads = $page->downloads->toArray();
					usort($downloads, function($a, $b) {
						if($a['sort_order'] == $b['sort_order']) { return 0; }
						return $a['sort_order'] > $b['sort_order'] ? 1 : -1;
					});
				}
				return View::make($view, ['page' => $page, 'menu_item' => $menu_item, 'pg_links' => $pg_links, 'calendar' => $calendar, 
						'pg_sections' => $pg_sections, 'sponsors' => $sponsors, 'contacts' => $contacts, 'settings' => $settings, 
						'show_membership_form' => $show_membership_form, 'dl_found' => $dl_found, 'hasMembersForm' => $hasMembersForm, 'link' => $link, 
						'action' => $action, 'page_type' => 'normal', 'downloads' => $downloads]);
			}

			return Redirect::action('MenusController@getStartPage');
		} else {
			// if this is pages section
			$pages = Page::with(['teaser', 'tags'])
							->where('active', 1)
							->where('content_section_id', $cs_id)->get()->sortBy('sort_order');
			$section = ContentSection::with('contacts')->find($cs_id);
			if(!$section) {
				return Redirect::action('MenusController@getStartPage');
			}
			$section_title = strtolower(str_replace(' ', '-', $section->title_en));
			$tags = Tag::all()->sortBy('tag_de');
			$tag_ids = [];
			foreach($pages as $p) {
				if($p->tags && count($p->tags)) {
					foreach($p->tags as $t) {
						$tag_ids[] = $t->id;
					}
				}
			}
			$showFliters = (count($tag_ids)) ? true : false;
			// echo '<pre>'; print_r($pages); exit;
			// echo 'Show Filters? '. $showFliters . '<br>'. '<pre>'; print_r($tag_ids); exit;
			if($section) {
				return View::make('pages.section', ['pages' => $pages, 'menu_item' => $menu_item, 'section' => $section, 
					'section_title' =>$section_title, 'pg_links' => $pg_links, 'calendar' => $calendar, 'pg_sections' => $pg_sections, 
					'tags' => $tags, 'showFliters' => $showFliters, 'tag_ids' => $tag_ids]);
			}

			return Redirect::action('MenusController@getStartPage');
		}
	}

	public function getTeamPage() {
		$contacts = Contact::get()->sortBy('first_name');

		return View::make('pages.team', ['contacts' => $contacts]);
	}

	public function getStartPage() {
		$f = fopen('logs/test2.log', 'w+');
		fwrite($f, "getStartPage\n\n");
		$page = Page::with(['page_image_sliders', 'page_image_sliders.page_slider_images', 'page_image_sliders.page_slider_images.slide_text'])
						->where('page_type', 'start_page')->first();
		$slider = [];
		if($page->page_image_sliders && count($page->page_image_sliders)) {
			$slider = $page->page_image_sliders[0];
		}
		$slides = (array)$slider->page_slider_images;
		if(is_array($slides)) {
			usort($slides, function($a, $b) {
				if($a['sort_order'] == $b['sort_order']) return 0;
				return ($a['sort_order'] < $b['sort_order']) ? -1 :1;
			});
		}
		fwrite($f, "\nSlides\n". print_r($slides, true));
		// $slider->page_slider_images = $slides;

		return View::make('pages.start', ['page' => $page, 'slider' => $slider, 'slides' => $slides]);
	}

	public function getPageLinksByTitle($title, $link = '') {
		$sql = 'select cs.*
		        from content_sections cs, menu_items mi, pages p
		        where mi.id = cs.menu_item_id
		          and cs.active = 1
		          and mi.title_en like "'. strtolower(str_replace('-', ' ', $title). '"		        
		        group by cs.id
		        order by cs.sort_order');
		$results = DB::select($sql);
		$f = fopen('logs/test_2.log', 'a+');
		$cur_found = false;
		foreach($results as &$res) {
			$res->link = strtolower(str_replace(' ', '-', $res->title_en));
			if(strtolower(str_replace('-', ' ', $link)) == strtolower($res->title_en)) {
				$res->current_link = 1;
				$cur_found = true;
			} else {
				$res->current_link = 0;
			}
		}
		if(count($results) && !$cur_found) {
			$results[0]->current_link = 1;
		}

		return $results;
	}

	public static function getMainMenu() {
		$menu_items = MenuItem::all();

		return $menu_items;
	}

	public function getFooterPage($link) {
		$page = Page::with(['page_contents'])
					  ->where('page_type', 'footer')->where('title_en', 'like', str_replace('-', ' ', $link))->first();

		return View::make('pages.footer-page', ['page' => $page]);		
	}

	public function getOnlineKatalog() {
		return View::make('pages.external.webmill');		
	}

	public function getEventRegResponse() {
		if(Input::has('return_url')) {
			$return_url = Input::get('return_url');
			$arr = explode('_', $return_url);
			$url = '';
			if(count($arr) > 1) {
				if(strpos($arr[count($arr)-1], '_')) {
					for($i=1;$i<count($arr)-1;$i++) { $url .= '/'. $arr[$i]; }
					$url .= '_'. $arr[count($arr)-1];
				} else {
					for($i=1;$i<count($arr);$i++) { $url .= '/'. $arr[$i]; }
				}
				$return_url = $url;
			} else {
				$return_url = str_replace('_', '/', $return_url);
			}
			// echo $return_url;exit;

			return View::make('pages.event-reg-resp', ['return_url' => $return_url]);		
		}
		
		return Redirect::action('MenusController@getPage', ["calendar", "besuch-planen"]);
	}

	public function getMemberRegResponse() {
		echo 'Member reg'; exit;
		return View::make('pages.member-reg-resp');		
	}

	public function getTopMenu() {
		// echo 'top menu'; exit;
		return View::make('includes.top-menu');		
	}

	public function getDLPassword() {
		$f = fopen('logs/test_3.log', 'w+');
		fwrite($f, "getDLPassword() called at [".date('Y-m-d H:i:s')."]\n\n". print_r(Input::all(), true));
		$auth = 0;
		$set = Settings::first();
		if($set) {
			fwrite($f, "\ndl_password: ". $set->dl_password . "\nRes: ". (Input::get('pw') == $set->dl_password));
			$auth = (Input::get('pw') === $set->dl_password) ? 1 : 0;
			return $auth; // Response::json(array('error' => false, 'auth' => $auth), 200);
		}

		return $auth; // Response::json(array('error' => true, 'auth' => false, 'message' => 'Error processing request'), 422);
	}
}
