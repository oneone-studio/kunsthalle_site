<?php

class SearchController extends BaseController {

	var $calendar = [];

	public function __construct() {
		$this->calendar = KEventsController::getEventsCalendar(null, false, 0);
	}
	
	public function handleSearch() {
		$lang = MenusController::getLang();
		$domain = Config::get('vars.domain');
		$f = fopen('logs/search.log', 'w+');
		fwrite($f, "sendMessage\n\n". print_r(Input::all(), true));
		$data = [];
		$page_ids = [];
		$urls = [];
		if(Input::has('search_term')) {
			$term = trim(Input::get('search_term'));
			$enc_term = htmlentities($term);
			$uc_term = ucwords($term);
			$ucf_term = ucfirst($term);
			$lc_term = strtolower($term);
			$upr_term = strtoupper($term);
			$terms = [$uc_term, $ucf_term, $lc_term, $upr_term];
			// Non exhibition pages
			$query = 'select p.id, 
					    p.title_'.$lang.' as page_title_'.$lang.', 
					    p.title_'.$lang.' as page_title_'.$lang.', p.slug_'.$lang.' as page_slug,
					    cs.title_'.$lang.' as cs_title_'.$lang.', mi.title_'.$lang.' as menu_title_'.$lang.', 
					    mi.slug_'.$lang.' as menu_item_slug,
					    cs.slug_'.$lang.' as cs_item_slug, cs.type as page_type
					  from pages p, page_contents pc, content_sections cs, menu_items mi
			          where ( lower(pc.content_'.$lang.') like "%'. $lc_term . '%"';
				        // foreach($terms as $trm) { $query .= ' or pc.content_'.$lang.' like "%'. $trm . '%"'; }
		    $query .= ') 
						and pc.page_id = p.id
			            and p.page_type != "exhibition"
			            and lower(mi.title_'.$lang.') != "exhibitions"
			            and cs.id = p.content_section_id 
			            and mi.id = cs.menu_item_id ';
			$results = DB::select($query);
			// echo '<pre>'.$query;exit;
			if($results) {
				foreach($results as $res) {
					if($res->page_slug && $res->menu_item_slug) {
						$url = $domain.$lang.'/';
						if($res->page_type == 'page_section') {
							$url .= 'sb-page/'.$res->menu_item_slug .'/'. $res->cs_item_slug.'/';
						} elseif($res->page_type == 'footer') {
							$url .= 'view/static/page/';
						} else {
							$url .= $res->menu_item_slug.'/';
						}
						$url .= $res->page_slug;
						// if(!in_array($url, $urls) && !in_array($res->id, $page_ids)) {
						if(!in_array($url, $urls)) {
							$page_ids[] = $res->id;
							$res->url = $url;
							$data[] = (array)$res;
							$urls[] = $url;
						}
					}
				}
			}
			// Pages
			$_url = $domain;
			$page_url = $_url;
			$exb_url = $domain.$lang.'/'.'view/exhibitions/exb-page/';

			$pgs = Page::with(['page_contents', 'content_section', 'h2', 'h2text'])->get();
			foreach($pgs as $pg) {
				$query = 'select p.id, p.content_section_id, mi.slug_'.$lang.' as menu_item_slug, 
						    p.title_'.$lang.' as page_title_'.$lang.',
						    mi.title_'.$lang.' as menu_title_'.$lang.',
						    p.slug_'.$lang.' as page_slug,
						    cs.slug_'.$lang.' as cs_item_slug, p.page_type					    
				 		  from pages p, page_contents pc, content_sections cs, menu_items mi
				 		  where p.id = '.$pg->id.'
				 		  and pc.page_id = p.id
			              and cs.id = p.content_section_id 
			              and mi.id = cs.menu_item_id 
			              group by p.id';
			    $results = DB::select($query);
			    if($results && count($results)) {
			    	$pres = $results[0];
					$page_slug = strtolower(str_replace(" ", "-", $pg->{'title_'.$lang}));
			    	$url = self::getSearchResUrl($pg->page_type, $results[0]->menu_item_slug, $results[0]->cs_item_slug, $page_slug);
					$match = false;
					if($pg->h2text) {
						foreach($pg->h2text as $h2t) {
							// Headline
							if(stripos(strtolower($h2t->{'headline_'.$lang}), $lc_term) || 
									stripos($h2t->{'headline_'.$lang}, $enc_term)) { $match = true; 
							}
							// Intro
							if(stripos(strtolower($h2t->{'intro_'.$lang}), $lc_term) || stripos($h2t->{'intro_'.$lang}, $enc_term)) { $match = true; }
						}
					}
					if($match) {											
						// if(!in_array($url, $urls) && !in_array($pg->id, $page_ids)) {
						if(!in_array($url, $urls)) {
							$page_ids[] = $pg->id;
							$results[0]->url = $url;
							$data[] = (array)$results[0];
							$urls[] = $url;
						}
					}
			    }			    

				// Content Section
				$cs_match = false;
				if(isset($pg->content_section)) {
					if(strcasecmp($lc_term, strtolower($pg->content_section->{'title_'.$lang})) == 0) { $cs_match = true; }
					if(stripos(strtolower($pg->content_section->{'headline_'.$lang}), $lc_term)) { 
						$cs_match = true;
					}
					if(stripos(strtolower($pg->content_section->{'detail_'.$lang}), $lc_term)) { $cs_match = true; }
					if($results && count($results) && $cs_match) {
						$url = $_url.$results[0]->menu_item_slug.'/'. strtolower(str_replace(' ', '-', $pg->content_section->{'title_'.$lang}));
						if(!in_array($url, $urls) && !in_array($pg->id, $page_ids)) {
							$res_item = self::getResItem($pg->content_section->id, 'content_section', $pg->content_section->{'title_'.$lang});
							$res_item['page_type'] = $pg->page_type;
							$data[] = $res_item;
							$page_ids[] = $pg->id;
							$urls[] = $url;
						}
					}
				}
				// Page content
				fwrite($f, "\npage_content-check: ".$pg->id);
				if($pg->page_contents) {
					$contents = $pg->page_contents;
					$match = false;

					foreach($contents as $c) {
						$content = $c->{'content_'.$lang};
						$content = html_entity_decode($content);
						if(strpos(strtolower($content), $lc_term) !== false || strpos($content, htmlentities($term)) !== false) {
							$match = true;
						}
					}
					if($match) {
						$res_item = self::getResItem($pg->id, 'page_content');
						// if(is_array($res_item) && array_key_exists('url', $res_item) && !in_array($res_item['url'], $urls) 
						// 		&& !in_array($pg->id, $page_ids)) {
						if(is_array($res_item) && array_key_exists('url', $res_item) && !in_array($res_item['url'], $urls)) {
							$res_item['page_type'] = $pg->page_type;
							$data[] = $res_item;
							$page_ids[] = $pg->id;
							$urls[] = $res_item['url'];
						}
					}
				}
				// H2Text
				$match = false;
				if($pg->h2text) {

					foreach($pg->h2text as $h2t) {
						// Headline
						if(stripos(strtolower($h2t->{'headline_'.$lang}), $lc_term) || stripos($h2t->{'headline_'.$lang}, $enc_term)) {
							$match = true; 
							fwrite($f, "\n\n-->> Found match for page_id: ".$pg->id." and h2text_id: ".$h2t->id."\n\n");
						}
						// Intro
						if(stripos(strtolower($h2t->{'intro_'.$lang}), $lc_term) || stripos($h2t->{'intro_'.$lang}, $enc_term)) {
							$match = true; 
							fwrite($f, "\n\n-->> Found match for page_id: ".$pg->id." and h2text_id: ".$h2t->id."\n\n");
						}
					}
					if($match) {
						$res_item = self::getResItem($pg->id, 'h2_text');
						// if(is_array($res_item) && array_key_exists('url', $res_item) && !in_array($res_item['url'], $urls) 
						// 		&& !in_array($pg->id, $page_ids)) {
						if(is_array($res_item) && array_key_exists('url', $res_item) && !in_array($res_item['url'], $urls)) {
							$res_item['page_type'] = $pg->page_type;
							$data[] = $res_item;
							$page_ids[] = $pg->id;
							$urls[] = $res_item['url'];
						}
					}
				}
			}
			// Check exhibition pages
			$url = Config::get('vars.domain').$lang.'/'.'view/exhibitions/exb-page/';

			$query = 'select p.id, p.content_section_id, p.title_'.$lang.' as page_title_'.$lang.', p.page_type, 
					  	p.slug_'.$lang.' as page_slug 
					  from pages p, page_contents pc
			          where ( lower(pc.content_'.$lang.') like "%'. $lc_term . '%"';
		    foreach($terms as $trm) {
		    	$query .= ' or pc.content_'.$lang.' like "%'. $trm . '%"';
		    }      
			$query .= ') and pc.page_id = p.id
			            and p.active_'.$lang.' = 1
			            and p.page_type = "exhibition"';
			$results = DB::select($query);
			if($results) {
				foreach($results as $res) {
					$res->url = $url. $res->page_slug;
					// if(!in_array($res->url, $urls) && !in_array($res->id, $page_ids)) {
					if(!in_array($res->url, $urls)) {
						$data[] = (array)$res;
						$page_ids[] = $res->id;
						$urls[] = $res->url;
					}
				}
			} else {
				$eps = Page::where('page_type', 'exhibition')->where('active_'.$lang, 1)->get();
				if($eps) {
					foreach($eps as $ep) {
						$do_add = false;
						if(strpos(strtolower($ep->{'title_'.$lang}), $lc_term)) {
							$do_add = true;
						}
						if($ep->teaser && strpos(strtolower($ep->teaser->{'caption_'.$lang}), $lc_term)) {
							$do_add = true;
						}
						if($ep->teaser && (strpos(strtolower($ep->teaser->{'line_1_'.$lang}), $lc_term) || 
							strpos(strtolower($ep->teaser->{'line_2_'.$lang}), $lc_term))) {
							$do_add = true;
						}
						if($do_add) {
							$ep_url = $url.$ep->{'slug_'.$lang};
							// if(!in_array($ep_url, $urls) && !in_array($ep->id, $page_ids)) { $page_ids[] = $ep->id; $urls[] = $ep_url; }
							if(!in_array($ep_url, $urls)) { $page_ids[] = $ep->id; $urls[] = $ep_url; }
						}
					}
				}
			}
			// echo '<pre>';print_r($urls);exit;			

			// Find events
			$srch_term = strtoupper($term); // ucwords($term);//strtolower($term);
			$query = 'select id, title_'.$lang.' as page_title_'.$lang.', start_date, event_day, event_day_repeat, repeat_month, end_date 
					  from k_events 
					  where upper(title_'.$lang.') like "%'.$srch_term.'%" or upper(subtitle_'.$lang.') like "%'.$srch_term.'%" 
					    and start_date >= "'. date('Y-m-d').'"';
			$results = DB::select($query);
			fwrite($f, "\nquery:\n".$query);
			$start_date = '';
			$list = [];
			$evt_list = [];
			$url = $domain.'calendar/besuch-planen/';
			if($results) {
				fwrite($f, "\n\nResults:-\n". print_r($results, true));
				foreach($results as $evt) {
					// repeated dates case
					if((isset($evt->event_day) && strlen($evt->event_day) > 0) || strtolower($evt->event_day_repeat) == 'daily') {
						$rep_dates = KEventsController::getEventRepeatDates($evt->id, $evt->start_date, $evt->event_day, $evt->event_day_repeat, $evt->repeat_month, $evt->end_date);
						fwrite($f, "\nRep dates-1:\n". print_r($rep_dates, true));
						foreach($rep_dates as $rep_dt) {
							if(strtotime($rep_dt) >= strtotime(date('Y-m-d'))) {
								$start_date = $rep_dt;
								if(!empty($start_date)) {
									foreach($results as $res) {
										$index = $res->id . date('dmY', strtotime($start_date));
										fwrite($f, "\nindex:--- ". $index);
										if(!in_array($index, $list)) {
											$list[] = $index;
											$res->page_type = 'event';
											$evt_data = $this->getSlideNo($index, $start_date);
											if($evt_data) {
												$res->slideNo = $evt_data['slideNo'];
												$res->url = $url. $index .'_'. $evt_data['slideNo'];
												if(!in_array($res->url, $urls)) {
													$res->date_info = $evt_data['date_info'];
													$evt_list[] = (array)$res;
												}
											}
										}
									}
								}	
							}
						}					
					} else {
						$query = 'select event_date from event_dates 
						          where k_event_id = '. $evt->id . ' order by event_date';
						$dt_res = DB::select($query);
						fwrite($f, "\nRep dates-2:\n". print_r($dt_res, true));
						foreach($dt_res as $dtr) {
							if(strtotime($dtr->event_date) >= strtotime(date('Y-m-d'))) {
								$start_date = $dtr->event_date;
								if(!empty($start_date)) {
									foreach($results as $res) {
										$index = $res->id . date('dmY', strtotime($start_date));
										fwrite($f, "\nindex:--- ". $index);
										if(!in_array($index, $list)) {
											$list[] = $index;
											$res->page_type = 'event';
											$evt_data = $this->getSlideNo($index, $start_date);
											if($evt_data) {
												$res->slideNo = $evt_data['slideNo'];
												$res->url = $url. $index .'_'. $evt_data['slideNo'];
												if(!in_array($res->url, $urls)) {
													$res->date_info = $evt_data['date_info'];
													$evt_list[] = (array)$res;
												}
											}
										}
									}
								}	
							}
						}
					}	
				}
			}
			if(count($evt_list) > 0) {
				usort($evt_list, function($x, $y) {
					$dt_x = substr($x['date_info'], strpos($x['date_info'], ', ')+2, strlen($x['date_info']));
					$dt_int_x = (int)strtotime($dt_x);
					$dt_y = substr($y['date_info'], strpos($y['date_info'], ', ')+2, strlen($y['date_info']));
					$dt_int_y = (int)strtotime($dt_y);
					if($dt_int_x == $dt_int_y) return 0;
					return ($dt_int_x < $dt_int_y) ? -1 : 1;
				});
				// Add events to results data
				foreach($evt_list as $evt) {
					$data[] = $evt;
				}
			}

			// Refine final results
			$besuch_planen_url = $domain.'besuch-planen/';
			// foreach($data as &$dt) {
			for($i=0; $i<count($data); $i++) {
				$dt = $data[$i];
				// Update title if url is /besuch-planen
				if(array_key_exists('url', $dt) && strcmp($dt['url'], $besuch_planen_url) ==0) {
					$dt['page_title'] = $dt['page_title_'.$lang] = 'Angebote und Programm';
				}
				// Remove invalid result
				if($dt['page_type'] == 'normal' && !array_key_exists('menu_title_'.$lang, $dt)) {
					unset($data[$i]);
				}
				$data[$i] = $dt;
			}
			// echo '<pre>'.$term.'<br><br>'; print_r($data); exit;
			// fwrite($f, "\n\n\nFinal Results:\n\n".print_r($data, true));

			return View::make('pages.search', ['results' => $data, 'search_term' => $term]);
		}
	}	

	public static function getResItem($pgid, $type, $_page_title = '') {
		$lang = MenusController::getLang();
		$f = fopen('logs/search.log', 'a+');
		if($pgid == 190) { $f = fopen('logs/search_190.log', 'w+'); }
		fwrite($f, "\ngetResItem($pgid) called..");
		$res = [];
		$domain = Config::get('vars.domain');
		$query = 'select p.title_'.$lang.', p.content_section_id, mi.slug_'.$lang.' as menu_item_slug, 
				    p.title_'.$lang.' as page_title_'.$lang.',
				    cs.title_'.$lang.' as cs_title_'.$lang.',
				    mi.title_'.$lang.' as menu_title_'.$lang.',
				    p.slug_'.$lang.' as page_slug,
				    cs.slug_'.$lang.' as cs_item_slug, p.page_type
				  from pages p, content_sections cs, menu_items mi
					where p.id = '.$pgid.'
		            and cs.id = p.content_section_id 
		            and mi.id = cs.menu_item_id 
		            and p.active_'.$lang.' = 1
		          group by p.id';
		$m_res = DB::select($query);
		if(!$m_res || (isset($m_res) && $m_res[0]->page_type == 'exhibition')) {
			$query = 'select p.title_'.$lang.' as page_title_'.$lang.', p.title_'.$lang.' as page_title_'.$lang.', p.page_type, 
						p.slug_'.$lang.' as page_slug
					  from pages p, content_sections cs
						where p.id = '.$pgid.'
			          group by p.id';
			$m_res = DB::select($query);
		}
		if($m_res) {
			$mr = $m_res[0];
			$_url = $domain.$lang.'/';
			$url = $_url;
			$exb_url = $domain.$lang.'/'.'view/exhibitions/exb-page/';
			$static_pg_url = $domain.'view/static/page/';

			if(((isset($mr->content_section_id) && $pgid != $mr->content_section_id) || $mr->page_type == 'page_section') && isset($mr->menu_item_slug) && isset($mr->cs_item_slug)) {
				if(strtolower($mr->{'title_'.$lang}) != strtolower($mr->{'cs_title_'.$lang})) {
					if($mr->page_type != 'content_section') {
						$url = $_url. 'sb-page/'.$mr->menu_item_slug .'/'. $mr->cs_item_slug.'/';
					}
				} else {
					$url = $_url. $mr->menu_item_slug .'/'. $mr->cs_item_slug.'/';
				}
			} elseif($mr->page_type == 'exhibition') {
				$url = $exb_url;
			} elseif($mr->page_type == 'footer') {
				$url = $static_pg_url;
			} else {
				if(isset($mr->menu_item_slug)) {
					$url = $_url. $mr->menu_item_slug.'/';
				}
			}
			$page_slug = ($_page_title == '') ? $mr->page_slug : strtolower(str_replace(' ', '-', $_page_title));
			if(isset($mr->menu_item_slug)) {
				$url .= $page_slug;
				$page_title = ($_page_title == '') ? $mr->{'page_title_'.$lang} : $_page_title;
				$res['page_id'] = $pgid;
				$res['page_title_'.$lang] = $page_title;
				$res['page_type'] = $mr->page_type;
				$res['res_type'] = $type;
				if(isset($mr->{'menu_title_'.$lang})) { 
					$res['menu_title_'.$lang] = $mr->{'menu_title_'.$lang};
					if($type == 'page_content') {
						$res['menu_title_'.$lang] = $mr->{'cs_title_'.$lang}. ' > '.$mr->{'menu_title_'.$lang};
					}
				}			
				$res['url'] = $url;
			}
		}	

		return $res;
	}

	public static function getSearchResUrl($page_type = 'normal', $menu_item_slug = '', $cs_item_slug = '', $page_slug = '') {
		$lang = MenusController::getLang();
		$domain = Config::get('vars.domain');
		$_url = $domain.$lang.'/';
		$url = $_url;
		$exb_url = $domain.$lang.'/'.'view/exhibitions/exb-page/';

		if($page_type == 'page_section') {							
			$url = $_url. 'sb-page/'.$menu_item_slug .'/'. $cs_item_slug.'/';
		} elseif($page_type == 'exhibition') {
			$url = $exb_url;
		} else {
			$url = $_url. $menu_item_slug.'/';
		}

		return $url;
	}

	public static function getSearchResItem($pid, $page_title = '') {
		$lang = MenusController::getLang();
		$item = [];
		$query = 'select mi.title_'.$lang.' as menu_title_'.$lang.'
				  from pages p, content_sections cs, menu_items mi
					where p.id = '.$pid.'
		            and cs.id = p.content_section_id 
		            and mi.id = cs.menu_item_id 
		          group by p.id';
		$m_res = DB::select($query);
		if($m_res) {
			$item = [];
			$item['page_title_'.$lang] = $page_title;
			$item['page_type'] = 'content_section';
			$item['menu_title_'.$lang] = $m_res[0]->{'menu_title_'.$lang};

			return $item;
		}

		return $item;
	}

	function getSlideNo($index, $start_date) {
		$evt_data = [];
		foreach($this->calendar as $m => $data) {
			foreach($data['days'] as $day => $event_data) {
				foreach($event_data['events'] as $ev) {
					if($ev['index'] == $index) {
						$day_arr = explode(' ', $day);
						$dayStr = $day_arr[count($day_arr)-1];
						$evt_data = [ 'slideNo' => $ev['slideNo'],
								  'date_info' => $dayStr .', '. date('d.m.Y', strtotime($start_date))
								];
						break;		
					}
				}
			}
		}

		return $evt_data;
	}

}




