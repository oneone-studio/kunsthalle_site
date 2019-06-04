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

	public function getMenuItem($lang = 'de', $menu_item) {
		$_lang = self::getLang();
		if(!isset($lang)) { $lang = $_lang; }
		$rdr = DB::table('redirects')->where('slug', $menu_item)->first();
		// echo $rdr->redirect_url; exit;		
		if($rdr) {
			$url = $rdr->redirect_url;
			// header("Location: ". $url);
			echo '<script type="text/javascript">location.href="'.$url.'";</script>';
			return;
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
			return Redirect::action('MenusController@getPage', [$lang, $menu_item, $pg_links[0]->link]);
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

	public function getExhibitions($lang='de', $category = 'current', $tag = null) {
		$lang = self::getLang();
		if($tag) {
			$query = 'select id from tags where tag_'.$lang.' like "'. str_replace('-', ' ', $tag) . '"';
			$tag = DB::select($query);
		}
		$pages = [];
		if(isset($category) && strlen($category) > 0) {
			$pages = $this->getExbPagesByCat($category);
		} else {
			$pages = Page::with(['page_image_sliders', 'sponsor_groups', 'sponsor_groups.sponsors', 'downloads', 'cluster', 'banner', 
								 'banner.banner_text', 'page_image_sliders.page_slider_images', 'tags'])
							->where('active_'.$lang, 1)
							->where('page_type', 'exhibition')
							->get()->sortBy('start_date');
		}
		$results = [];
		$tags = Tag::all()->sortBy('tag_'.$lang);
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
		$lang = self::getLang();
		$pages = [];
		$today = date('Y-m-d');
		if(strtolower($cat) == 'current') {
			$eps = Page::with('teaser')->where('page_type', 'exhibition')->orderBy('start_date', 'DESC')
	                ->where('active_'.$lang, 1)
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
			               ->where('active_'.$lang, 1)
			               ->orderBy('start_date', 'ASC')
			               ->get();			
		}
		if(strtolower($cat) == 'past') {
			$pages = Page::where('page_type', 'exhibition')
			               ->where('start_date', '<', $today)
			               ->where('end_date', '<', $today)
			               ->where('active_'.$lang, 1)
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

	public function getSubPage($lang = 'de', $menu_item, $section, $page_title) {
		// echo $menu_item .' / '.$section .' / '.$page_title;exit;
		$lang = self::getLang();
		$page_id = 0;
		$query = 'select p.* from pages p, content_sections cs, menu_items mi 
		          where p.content_section_id = cs.id 
		            and p.active_'.$lang.' = 1
		            and cs.menu_item_id = mi.id 
		            and cs.slug_'.$lang.' = "'. $section . '" 
		            and lower(replace(mi.title_'.$lang.', " ", "-")) = "'. $menu_item . '"
		            and p.slug_'.$lang.' = "'. $page_title . '"
		          limit 1';
		$page = DB::select($query);
		if(!isset($page) || !isset($page[0]->id)) {
			$query = 'select p.* from pages p, content_sections cs, menu_items mi 
			          where p.content_section_id = cs.id 
			            and p.active_'.$lang.' = 1
			            and cs.menu_item_id = mi.id 
			            and cs.slug_'.$lang.' = "'. $section . '" 
			            and mi.slug_'.$lang.' = "'. $menu_item . '"
			            and p.slug_'.$lang.' = "'. $page_title . '"
			          limit 1';
			$page = DB::select($query);	
		}

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
				if(!array_key_exists($g->{'headline_'.$lang}, $sponsors)) {
					$sponsors[$g->{'headline_'.$lang}] = [];
				}
				$sponsors[$g->{'headline_'.$lang}] = $g->sponsors;
			}	
			
			return View::make('pages.sub-page', ['page' => $page, 'menu_item' => $menu_item, 'calendar' => $calendar, 
				'pg_sections' => $pg_sections, 'section' => $section, 'dl_found' => $dl_found, 'settings' => $settings, 
				'show_membership_form' => $show_membership_form, 'page_type' => 'sub_page', 'downloads' => $downloads, 
				'show_calendar' => $calendar, 'sponsors' => $sponsors ]);
		}

		return Redirect::action('MenusController@getPage', [$menu_item, $page_title]);
	}

	public function getExbPage($lang = 'de', $slug) {
		$lang = self::getLang();
		$page_id = 0;
		$slug = strtolower($slug);
		// First try slug match
		$query = 'select p.*, b.image from pages p, banners b
		          where lower(replace(p.slug_'.$lang.', " ", "-")) = "'. $slug . '"
		            and p.active_'.$lang.' = 1
		            and p.page_type = "exhibition"
		            and b.page_id = p.id
		          limit 1';
		$_page = DB::select($query);
		if(!isset($_page) || !isset($_page[0]->id)) {
			$query = 'select p.*, b.image from pages p, banners b
			          where lower(replace(p.title_'.$lang.', " ", "-")) = "'. $slug . '"
			            and p.active_'.$lang.' = 1
			            and p.page_type = "exhibition"
			            and b.page_id = p.id
			          limit 1';
			$_page = DB::select($query);
		}

		// Redirect to home page if page not found in chosen language
		if(!$_page) { 
			return Redirect::action('MenusController@getStartPage'); 
		}
		$page_title = '';
		if($_page) {
			$page_id = $_page[0]->id;
			$page_title = $_page[0]->{'title_'.$lang};
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
				if(!array_key_exists($g->{'headline_'.$lang}, $sponsors)) {
					$sponsors[$g->{'headline_'.$lang}] = [];
				}
				$sponsors[$g->{'headline_'.$lang}] = $g->sponsors;
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

		return Redirect::action('MenusController@getPage', [$lang, $menu_item, $page_title]);
	}

	public function getPage($lang = 'de', $menu_item, $link, $action = null) {
		$lang = self::getLang();
		$hasMembersForm = (strtolower($menu_item) == 'jetzt-unterstützen' && strtolower($link == 'online-mitgliedsantrag')) ? true : false;
		// echo $lang.'<br>'.$menu_item.'<br>'.$link; exit;
		if(strtolower($menu_item) == 'exhibitions') {
			$category = 'current';
			if($action == 'current' || $action == 'upcoming' || $action == 'past') { $category = $action; }

			return redirect()->route('exhibitions', [$category]);
		}

		$pg_links = $this->getPageLinksByTitle($menu_item, $link);
		// echo '<pre>'; print_r($pg_links); exit;
		$page = [];		
		$pg_sections = [];
		$is_page = false;
		$all_event_dates = [];
		$cs_id = 0;
		$page_id = 0;
		$pages = [];
		$showFliters = false;
		$tags = [];
		$tag_ids = [];
		$is_cal_page = false;
		if($menu_item == '/besuch-planen/kalender' || $menu_item == '/besuch-planen/calendar' || 
				$menu_item == '/plan-your-visit/your-visit') {
			$is_cal_page = true;
		}

		if(is_array($pg_links) && count($pg_links)) {
			foreach($pg_links as $pl) {
				if($pl->current_link == 1) { 
					$pg = Page::where('content_section_id', $pl->id)
							  ->where('active_'.$lang, 1)
							  ->get()->sortBy('sort_order');
					if($pg && isset($pg[0])) {
						$page_id = $pg[0]->id;
					}
					if($pl->type == 'page') { $is_page = true; }
					else { $cs_id = $pl->id; }
					break; 
				}
			}
			// echo $page_id;exit;			
			$pg_sections = $this->getPageSections($page_id);
			$page = Page::with(['page_contents', 'page_image_sliders', 'sponsor_groups', 'sponsor_groups.sponsors', 'downloads', 
								'cluster', 'banner', 'banner.banner_text', 'page_image_sliders.page_slider_images', 'h2text', 'image_grids', 
								'image_grids.grid_images', 'teaser', 'contacts', 'tags'])
						  ->where('active_'.$lang, 1)->find($page_id);
		}
		$calendar = [];
		if($lang == 'de') {
			if(isset($page->cluster_id) && is_numeric($page->cluster_id) && intval($page->cluster_id) > 0) {
				$calendar = KEventsController::getEventsCalendar(null, false, $page->cluster_id);
			}
			if(strtolower($link) == 'kalender' || strtolower($link) == 'calendar') {
				$jsn_data = KEventsController::getEventsCalendar($menu_item, false);
				$all_event_dates = $jsn_data['all_event_dates'];
				$calendar = $jsn_data['calendar'];
				$tags = isset($jsn_data['tags']) ? $jsn_data['tags'] : [];
				$tag_ids = isset($jsn_data['tag_ids']) ? $jsn_data['tag_ids'] : [];
				// $showFliters = true;
			}
		}
		$sponsors = [];
		if($page && $page->sponsor_groups) {
			foreach($page->sponsor_groups as $g) {
				if(strlen($g->{'headline_'.$lang}) > 0) {
					if(!array_key_exists($g->{'headline_'.$lang}, $sponsors)) { $sponsors[$g->{'headline_'.$lang}] = []; }
					$sponsors[$g->{'headline_'.$lang}] = $g->sponsors;
				}
			}	
		}
		// echo ($is_page);exit;		
		if($is_page) {
			$contacts = [];
			$detps = Department::all()->sortBy('sort_order');
			foreach($detps as $d) {
				$list = Contact::with(['department'])
							->where('display', 1)
							->where('department_id', $d->id)
							->get()->sortBy('sort_order');
				foreach($list as $l) {
					if(!array_key_exists($l->department->{'title_'.$lang}, $contacts)) {
						$contacts[$l->department->{'title_'.$lang}] = [];
					}
					$contacts[$l->department->{'title_'.$lang}][] = $l;
				}
			}
			$settings = [];		
			$set = Settings::first();
			if($set) { $settings = $set; }
			$show_membership_form = false;
			if($link == 'online-member-form' || $link == 'online-mitgliedsantrag') {
				$show_membership_form = true;
			}
			$dl_found = isset($page->downloads) && count($page->downloads) > 0 ? true : false;

			// if single page
			foreach($contacts as $d => $cts) {
				$count = 0;
				foreach($cts as $c) {
					if(strlen($c->{'function_'.$lang}) > 0) { ++$count; }
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
					'all_event_dates' => $all_event_dates, 'pg_sections' => $pg_sections, 'sponsors' => $sponsors, 'contacts' => $contacts, 
					'settings' => $settings, 'show_membership_form' => $show_membership_form, 'dl_found' => $dl_found, 'hasMembersForm' => $hasMembersForm, 
					'link' => $link, 'action' => $action, 'page_type' => 'normal', 'downloads' => $downloads, 'tags' => $tags, 
					'showFliters' => $showFliters, 'tag_ids' => $tag_ids]);
			}

			return Redirect::action('MenusController@getStartPage');
		} else {
			// if this is pages section
			$pages = Page::with(['teaser', 'tags'])
							->where('active_'.$lang, 1)
							->where('content_section_id', $cs_id)->get()->sortBy('sort_order');
			$section = ContentSection::with('contacts')->find($cs_id);
			if(!$section) {
				return Redirect::action('MenusController@getStartPage');
			}
			$section_title = isset($section->{'slug_'.$lang}) ? 
				strtolower(str_replace(' ', '-', $section->{'slug_'.$lang})) : strtolower(str_replace(' ', '-', $section->{'title_'.$lang}));
			$tags = Tag::all()->sortBy('tag_'.$lang);
			$tag_ids = [];
			foreach($pages as $p) {
				if($p->tags && count($p->tags)) {
					foreach($p->tags as $t) {
						$tag_ids[] = $t->id;
					}
				}
			}
			$showFliters = (count($tag_ids)) ? true : false;
			// if($is_cal_page) { $showFliters = true; }
			if($section) {
				return View::make('pages.section', ['pages' => $pages, 'menu_item' => $menu_item, 'section' => $section, 
					'section_title' =>$section_title, 'pg_links' => $pg_links, 'calendar' => $calendar, 'pg_sections' => $pg_sections, 
					'tags' => $tags, 'showFliters' => $showFliters, 'tag_ids' => $tag_ids, 'all_event_dates' => $all_event_dates]);
			}

			return Redirect::action('MenusController@getStartPage');
		}
	}

	public function getTeamPage() {
		$contacts = Contact::get()->sortBy('first_name');

		return View::make('pages.team', ['contacts' => $contacts]);
	}

	public function getStartPage() {
		$f = fopen('logs/page.log', 'w+');
		fwrite($f, "getStartPage\n\n");
		$lang = self::getLang();
		$page = Page::with(['page_image_sliders', 'page_image_sliders.page_slider_images', 'page_image_sliders.page_slider_images.slide_text'])
						->where('page_type', 'start_page')->first();
		$slider = [];
		if($page->page_image_sliders && count($page->page_image_sliders)) {
			$slider = $page->page_image_sliders[0];
		}
		$slides = $slider->page_slider_images;
		$_slides = [];
		foreach($slides as $sl) {
			if($sl->{'active_'.$lang} == 1) { $_slides[] = $sl; }
		}
		$slides = (array)$_slides;

		if(is_array($slides)) {
			usort($slides, function($a, $b) {
				if($a['sort_order'] == $b['sort_order']) return 0;
				return ($a['sort_order'] < $b['sort_order']) ? -1 :1;
			});
		}
		$page->page_slider_images = $slides;
		fwrite($f, "\nSlides\n". print_r($slides, true));
		// $slider->page_slider_images = $slides;

		return View::make('pages.start', ['page' => $page, 'slider' => $slider, 'slides' => $slides]);
	}

	public function getPageLinksByTitle($title, $slug = '') {
		$lang = self::getLang();
		$sql = 'select cs.*, p.title_'.$lang.' as page_title, p.slug_'.$lang.' as page_slug
		        from content_sections cs, menu_items mi, pages p
		        where mi.id = cs.menu_item_id
		          and cs.active_'.$lang.' = 1
		          and p.content_section_id = cs.id
		          and mi.slug_'.$lang.' like "'. strtolower(str_replace(' ', '-', $title)). '"
		        group by cs.id
		        order by cs.sort_order';
		$_results = DB::select($sql);
		$f = fopen('logs/test_2.log', 'a+');
		$cal_found = false;
		$cur_found = false;
		$results = [];
		if($_results) {
			$results = $_results;
			foreach($results as &$res) {
				$res->link = $res->{'slug_'.$lang};
				if(strtolower(trim($res->{'title_'.$lang})) == strtolower(trim($res->page_title))) {
					$res->link = $res->page_slug;
				}

				if(trim($slug) == trim($res->{'slug_'.$lang})) {
					$res->current_link = 1;
					$cur_found = true;
				} elseif(trim($slug) == trim($res->page_slug)) {
					$res->current_link = 1;
					$cur_found = true;
				} else {
					$res->current_link = 0;
				}
				if(strtolower($title) == 'calendar' || strtolower($title) == 'kalender') { $cal_found = true; }
			}
		}
		if(count($results) && !$cur_found) {
			$results[0]->current_link = 1;
		}
		// echo '<pre>'; print_r($results);exit;		

		return $results;
	}

	public static function getMainMenu() {
		$menu_items = MenuItem::all();

		return $menu_items;
	}

	public function getFooterPage($lang = 'de', $link) {
		// echo $link;exit;		
		$lang = self::getLang();
		$page = [];

		$page = Page::with(['page_contents'])
					  ->where('page_type', 'footer')
					  ->where('slug_'.$lang, '=', $link)->first();
		if(!isset($page)) {
			$page = Page::with(['page_contents'])
						  ->where('page_type', 'footer')
						  ->where('title_'.$lang, 'like', str_replace('-', ' ', $link))->first();
		}

		return View::make('pages.footer-page', ['page' => $page]);
	}

	public function getOnlineKatalog() {
		return View::make('pages.external.webmill');		
	}

	public function getMembershipResp($lang = 'de') {
		// echo 'getMembershipResp';exit;
		return View::make('pages.membership-resp');
	}

	public function getEvtRegResp($lang = 'de') {
		return View::make('pages.event-reg-resp');
	}

	public function getEventRegResponse($return_url = null) {
		// echo 'getEventRegResponse<br>'.$return_url; print_r(Input::all());exit;
		return Redirect::action('MenusController@getPage', ["calendar", "besuch-planen"]);

		if(!isset($return_url) && Input::has('return_url')) { $return_url = Input::get('return_url'); }
		if(isset($return_url)) {
			$arr = explode('_', $return_url);
			$url = '';
			$has_event_index = false;
			if((($arr[0] == 'calendar' || $arr[0] == 'kalender') && $arr[1] == 'besuch-planen') && ((count($arr) > 2) && (strlen($arr[2]) > 8) && isset($arr[3]))) {
				$arr[2] = $arr[2].'_'.$arr[3];
				array_splice($arr, 3, 1);
				$has_event_index = true;
			}

			if(count($arr) > 1) {
				for($i=0;$i<count($arr);$i++) { $url .= '/'. $arr[$i]; }
				$return_url = $url;
			} else {
				$return_url = str_replace('_', '/', $return_url);
			}
			return View::make('pages.event-reg-resp', ['return_url' => '']);
		}
		
		return Redirect::action('MenusController@getPage', ["calendar", "besuch-planen"]);
	}

	public function getMemberRegResponse() {
		return View::make('pages.member-reg-resp');		
	}

	public function getBlog() {
		return View::make('pages.blog');
	}

	public function getTopMenu($lang = 'de') {
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

	public static function setLang($lang = 'de') {
		$lang = Input::has('lang') ? Input::get('lang') : 'de';
		// echo $lang;exit;
		Session::put('lang', $lang);
		Session::save();

		// Redirect to home page
// echo '<pre>'; print_r(get_class_methods('Response')); exit;		
		return Redirect::to('/'.$lang.'/');

		$uri = Input::get('uri');
		if($uri == '/') { $uri .= 'index'; }
		// echo $uri;exit;
		$page_type = 'normal';
		if(strpos($uri, '/exhibitions') || strpos($uri, '/exb-page')) {
			$page_type = 'exb';
		}
		if(strpos($uri, '/sb-page')) {
			$page_type = 'sp';
		}
		if(strtolower($lang) == 'en') {
			$uri = str_replace('/de/', '/', $uri);
			$uri = '/en'.$uri;
		} else {
			$uri = str_replace('/en/', '/', $uri);
			$uri = '/de'.$uri;
		}

		$params = explode('/', $uri);
		for($i=0; $i<count($params); $i++) {
			if(empty($params[$i])) { array_splice($params, $i, 1); }
		}

		if($page_type == 'exb') {
			if(($params[0] == 'de' || $params[0] == 'en') && ($params[1] == 'de' || $params[1] == 'en')) {
				array_splice($params, 0, 1);
			}
			$page_title = end($params);
			$params = ['lang' => $lang, 'page_title' => $page_title];

			return Redirect::action('MenusController@getExbPage', $params);
		}
		else if($page_type == 'sp') {
			
			if(count($params) > 0 && ($params[0] == 'de' || $params[0] == 'en') && ($params[1] == 'de' || $params[1] == 'en')) {
				array_splice($params, 0, 1);
			}
			$menu_item = (isset($params[2])) ? $params[2] : '';
			$section = (isset($params[3])) ? $params[3] : '';
			$page_title = end($params);
			
			$params = ['lang' => $lang, 'menu_item' => $menu_item, 'section' => $section, 'page_title' => $page_title];

			return Redirect::action('MenusController@getSubPage', $params);
		} else {

			if(($params[0] == 'de' || $params[0] == 'en') && ($params[1] == 'de' || $params[1] == 'en')) {
				array_splice($params, 0, 1);
			}
			$menu_item = (isset($params[1])) ? $params[1] : '';
			$page_title = end($params);
			$params = ['lang' => $lang, 'menu_item' => $menu_item, 'page_title' => $page_title];

			return Redirect::action('MenusController@getPage', $params);
		}
	}

	public static function getLang() {
		$lang = 'de';
		if(Session::has('lang')) { $lang = Session::get('lang'); }

		return $lang;
	}
}
