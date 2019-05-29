<?php

class KEventsController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function index()
	{
		$events = KHEvent::all();	

		return View::make('pages.k_events', $events);
	}

	public function calendar() {		
		$calendar = [];
		$months = [ '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ];
		$cnt = 0;
		$month_title_ht = 150;
		$day_title_ht = 124;
		$event_block_ht = 110;
		$yr = date('Y')+1;
		foreach($months as $month) {
			++$cnt;
			$sql = 'select * from k_events where start_date like "'. $yr.'-'.$month.'-%"	';
			$events = DB::select($sql);
			// echo $sql . '<br>' . '<br>Count: '. count($events). '<br>';
			// if(count($events) > 0) { echo '<pre>'; print_r($events); exit; }
			$min_ht = 200;
			if(count($events) > 0) {
				$m = date('M', strtotime($events[0]->start_date));
				$event_data = [];
				$event_data['month'] = $m;
				$event_data['year'] = date('Y', strtotime($events[0]->start_date));
				
				$day_titles = [];
				foreach($events as $e) {
					$day_num = date('d', strtotime($e->start_date));
					$day_title = $day_num .' '. date('l', strtotime($e->start_date));
					$event_list = [];
					foreach($events as $ne) {
						// $day_title_ne = $day_num .' '. date('l', strtotime($ne->start_date));
						$ne->index = $ne->id . date('dmY', strtotime($ne->start_date));
						$day_num_ne = date('d', strtotime($ne->start_date));
						if($day_num == $day_num_ne) {
							$event_list[] = $this->objectToArray($ne);
						}
					}
					$event_data['days'][$day_title]['events'] = $event_list;
				}	
				// echo '>> '. count($events); exit;
				$ht = $min_ht + $month_title_ht + $day_title_ht + (count($events) * $event_block_ht);
				$event_data['slide_height'] = $ht;
				// $event_data['days'] = $events;
				$calendar[] = $event_data;
			}
		}

		// echo '<pre>'; print_r($calendar); exit;
		return View::make('pages.calendar', ['calendar' => $calendar]);
	}

	public function registerForEvent() {
		$f = fopen('logs/event_reg.log', 'a+');
		fwrite($f, "[".date('Y-m-d H:i')."] - registerForEvent() called\n\nUser Agent: ". $_SERVER['HTTP_USER_AGENT']."\n\n".print_r(Input::all(), true)."\n\n");

		$inp = Input::all();
		$data = Input::all();

		$ref_url = $_SERVER['HTTP_REFERER'];
		$controller_action = 'MenusController@getEventRegResponse';
		$params = [ 'menu_item' => 'calendar', 'link' => 'besuch-planen'];
		
		$pkg_price = false;
		if(array_key_exists('pay_as_package', $data) && strtolower($data['pay_as_package']) == 'on') {
			$pkg_price = true;
		}
		if(isset($data['id'])) {
			// Store inputs in DB
			$input_ser = serialize($inp);
			DB::table('registrations')->insert(['inputs' => $input_ser, 'created_at' => date('Y-m-d H:i:s')]);

			// Disallow forms with 0 participants
			$pCount = 0;
			if(array_key_exists('regular_adult_price', $inp) && is_numeric($inp['regular_adult_price'])) { $pCount += (int)$inp['regular_adult_price']; }
			if(array_key_exists('member_adult_price', $inp) && is_numeric($inp['member_adult_price'])) { $pCount += (int)$inp['member_adult_price']; }
			if(array_key_exists('member_child_price', $inp) && is_numeric($inp['member_child_price'])) { $pCount += (int)$inp['member_child_price']; }
			if(array_key_exists('regular_child_price', $inp) && is_numeric($inp['regular_child_price'])) { $pCount += (int)$inp['regular_child_price']; }
			if(array_key_exists('sibling_member_price', $inp) && is_numeric($inp['sibling_member_price'])) { $pCount += (int)$inp['sibling_member_price']; }
			if(array_key_exists('sibling_child_price', $inp) && is_numeric($inp['sibling_child_price'])) { $pCount += (int)$inp['sibling_child_price']; }
			if(array_key_exists('reduced_price', $inp) && is_numeric($inp['reduced_price'])) { $pCount += (int)$inp['reduced_price']; }

			if($pCount == 0) {
				return Redirect::action($controller_action, $params);
			}

			$event = KEvent::with(['kEventCost', 'clusters', 'clusters.k_events', 'clusters.kEventCost', 'event_dates'])->find($data['id']);

			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: Kunsthalle Bremen <info@kunsthalle-bremen.de>' . "\r\n";

			$body = "Sehr geehrte/r ".$data['first_name']. " ". $data['last_name']. ",<br><br>
					 vielen Dank für Ihre Anmeldung zu folgender Veranstaltung der Kunsthalle Bremen:<br>";
			$body .= 'Event: ID '. $data['id']; 
			$start_time = $event->start_time;		 
			$start_time = substr($start_time, 0, 5);		 
			$body .= "<br><strong>". $event->title_de .'</strong><br>';
			if(isset($event->subtitle_de) && !empty($event->subtitle_de)) {
				$body .= $event->subtitle_de .'<br>';
			}

			$ev_dates = [];
			if($event->clusters && $pkg_price) {
				foreach($event->clusters as $cl) {
					if($cl->package == 1) {
						$evs = $cl->k_events;
						if($evs) {
							$ev_dates = [];
							foreach($evs as $ev) {
								$today = strtotime(date('Y-m-d'));
								if(strtotime($ev->start_date) >= $today) {
									fwrite($f, "\n---=> ". $ev->id.' _ '.$ev->title_de . ' - ['. $ev->start_date.']');
									$ev_dates[] = $ev->start_date;
								}
							}
							if(count($ev_dates)) {
								$body .= implode(', ', $ev_dates);
							}
						}
					}
				}
			} 
			if(count($ev_dates) == 0) {
				if($event->as_series == 1) {
					$body .= date('d/m/y', strtotime($event->start_date)) . ' - ' . date('d/m/y', strtotime($event->end_date));
				} else {				
					if(Input::has('reg_event_date')) {
						$body .= date('d/m/y', strtotime(Input::get('reg_event_date')));
					} else {
						$body .= date('d/m/y', strtotime($event->start_date));
					}
				}
			}
			$body .= '<br>'. $start_time .' - '. $event->end_time .'<br><br>';
			$event_cost_obj = $event->kEventCost;

			if($event->clusters && count($event->clusters)) {
				$price_cluster = $event->clusters[0];
				foreach($event->clusters as $cl) {
					if($cl->package == 1) {
						$price_cluster = $cl;
						$event_cost_obj = $cl->kEventCost;
						break;
					}
				}
			}
			$total_price = 0;
	        // 'Gesamtbetrag für alle von Ihnen angemeldeten Teilnehmer:<br>'.
			if(array_key_exists('regular_adult_price', $data) && is_numeric($data['regular_adult_price'])) {
				$body .= $data['regular_adult_price']. ' Erwachsene(r) ';
				if($pkg_price == true && $event->clusters) {
					$body .= $event_cost_obj->regular_adult_price;
					$total_price += ($data['regular_adult_price'] * $event_cost_obj->regular_adult_price);
				} else {
					$body .= $event->kEventCost->regular_adult_price;
					$total_price += ($data['regular_adult_price'] * $event->kEventCost->regular_adult_price);
				}  
				$body .= ' Euro<br>';
			}          
			if(array_key_exists('member_adult_price', $data) && is_numeric($data['member_adult_price'])) {
				$body .= $data['member_adult_price']. ' Mitglied(er) ';
				if($pkg_price == true && $event->clusters) {
					$body .= $event_cost_obj->member_adult_price;
					$total_price += ($data['member_adult_price'] * $event_cost_obj->member_adult_price);
				} else {
					$body .= $event->kEventCost->member_adult_price;
					$total_price += ($data['member_adult_price'] * $event->kEventCost->member_adult_price);
				}  
				$body .= ' Euro<br>';
			}
			if(array_key_exists('member_child_price', $data) && is_numeric($data['member_child_price'])) {
				$body .= $data['member_child_price']. ' Kind(er) / Mitglied ';
				if($pkg_price == true && $event->clusters) {
					$body .= $event_cost_obj->member_child_price;
					$total_price += ($data['member_child_price'] * $event_cost_obj->member_child_price);
				} else {
					fwrite($f, "\n\nCheck 1: ". $event->kEventCost->member_child_price);
					$body .= $event->kEventCost->member_child_price;
					$total_price += ($data['member_child_price'] * $event->kEventCost->member_child_price);
				}  
				$body .= ' Euro<br>';
			}
			if(array_key_exists('regular_child_price', $data) && is_numeric($data['regular_child_price'])) {
				$body .= $data['regular_child_price']. ' Kind(er) ';
				if($pkg_price == true && $event->clusters) {
					$body .= $event_cost_obj->regular_child_price;
					$total_price += ($data['regular_child_price'] * $event_cost_obj->regular_child_price);
				} else {
					fwrite($f, "\n\nCheck 1: ". $event->kEventCost->regular_child_price);
					$body .= $event->kEventCost->regular_child_price;
					$total_price += ($data['regular_child_price'] * $event->kEventCost->regular_child_price);
				}  
				$body .= ' Euro<br>';
				fwrite($f, "\Kinder ");
			}
			if(array_key_exists('sibling_member_price', $data) && is_numeric($data['sibling_member_price'])) {
				$body .= $data['sibling_member_price']. ' Geschwisterkind(er) / Mitglied ';
				if($pkg_price == true && $event->clusters) {
					fwrite($f, "\ncheck:- event->clusters[0]->kEventCost->sibling_member_price: ". $event_cost_obj->sibling_member_price);
					$body .= $event_cost_obj->sibling_member_price;
					$total_price += ($data['sibling_member_price'] * $event_cost_obj->sibling_member_price);
				} else {
					fwrite($f, "\n\ncheck:- event->kEventCost->sibling_member_price: ". $event->kEventCost->sibling_member_price);
					$body .= $event->kEventCost->sibling_member_price;
					$total_price += ($data['sibling_member_price'] * $event->kEventCost->sibling_member_price);
				}  
				$body .= ' Euro<br>';
				fwrite($f, "\n\nGeschwisterkinder / Mitglied");
			}
			if(array_key_exists('sibling_child_price', $data) && is_numeric($data['sibling_child_price'])) {
				$body .= $data['sibling_child_price']. ' Geschwisterkind(er) ';
				if($pkg_price == true && $event->clusters) {
					fwrite($f, "\ncheck:- event->clusters[0]->kEventCost->sibling_child_price: ". $event_cost_obj->sibling_child_price);
					$body .= $event_cost_obj->sibling_child_price;
					$total_price += ($data['sibling_child_price'] * $event_cost_obj->sibling_child_price);
				} else {
					fwrite($f, "\n\ncheck:- event->kEventCost->sibling_child_price: ". $event->kEventCost->sibling_child_price);
					$body .= $event->kEventCost->sibling_child_price;
					$total_price += ($data['sibling_child_price'] * $event->kEventCost->sibling_child_price);
				}  
				$body .= ' Euro<br>';
			}
			if(array_key_exists('reduced_price', $data) && is_numeric($data['reduced_price'])) {
				$body .= $data['reduced_price'] . " ermäßigt ";
				if($pkg_price == true && $event->clusters) {
					$body .= $event_cost_obj->reduced_price;
					$total_price += ($data['reduced_price'] * $event_cost_obj->reduced_price);
				} else {
					$body .= $event->kEventCost->reduced_price;
					$total_price += ($data['reduced_price'] * $event->kEventCost->reduced_price);
				}  
				$body .= ' Euro<br>';
			}
			$body .= '<br>Gesamtbetrag für alle von Ihnen angemeldeten Teilnehmer: '. $total_price . ' Euro<br>';
			          
			if(strlen($event->place) > 0) {
				$body .= '<br><br>Ort der Veranstaltung:<br>'. $event->place .'';
			}
			$body .= '<p>Diese Mail ist eine automatisierte Eingangsbestätigung. 
						 Nach Prüfung Ihrer Anmeldung erhalten Sie baldmöglichst eine verbindliche Bestätigung Ihrer Teilnahme.
					  </p><br>'.
					  'Mit freundlichen Grüßen<br>Ihre Kunsthalle Bremen<br>'.
					  "Bildung und Vermittlung<br>
					   <a href='mailto:programm@kunsthalle-bremen.de'>programm@kunsthalle-bremen.de</a><br>
					   T <a href='tel:+49 (0)421-32 908 330'>+49 (0)421-32 908 330</a>".
					   '<br>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br><br>';

			$body .= "Ihre Anmeldedaten:<br>". $data['first_name'] .' '. $data['last_name'] .
					 '<br>'.$data['street'] .' '. $data['streetno'] .'<br>'.
					 $data['zip'] .' '. $data['city'] .'<br>'. $data['phone'] .'<br>'. $data['email'].'<br>';
			
			if(isset($data['member_chk']) && strlen($data['member_no']) > 0) { $body .= '<br>Mitglied im Kunstverein:<span style="margin-left:5px;">' . $data['member_no'].'</span><br>'; }

			$people_count = 1;
			$children_list = '';
			$inc_list = false;
			if(array_key_exists('children_ages', $data)) {
				$cn_arr = $data['children_names'];
				$ca_arr = $data['children_ages'];
				if(array_key_exists(0, $ca_arr)) {
					for($i=0; $i<50; $i++) {
						if(array_key_exists($i, $ca_arr) && strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
							$children_list .= '<br>Kind(er):';
							$inc_list = true;
							break;
						}
					}
					if($inc_list) {
						for($i=0; $i<50; $i++) {
							if(array_key_exists($i, $ca_arr)) {
								if(strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
									++$people_count;
									$children_list .= '<br>- '. $cn_arr[$i] .' '. $ca_arr[$i];
								}	
							}				
						}
					}
				}
			}
			$member_children_list = '';
			$inc_list = false;
			if(array_key_exists('children_member_ages', $data)) {
				$cn_arr = $data['children_member_names'];
				$ca_arr = $data['children_member_ages'];
				if(array_key_exists(0, $ca_arr)) {
					for($i=0; $i<50; $i++) {
						if(array_key_exists($i, $ca_arr) && strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
							$children_list .= '<br><br>Kind(er)/ Mitglied:';
							$inc_list = true;
							break;
						}
					}
					if($inc_list) {
						for($i=0; $i<50; $i++) {
							if(array_key_exists($i, $ca_arr)) {
								if(strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
									++$people_count;
									$children_list .= '<br>- '. $cn_arr[$i] .' '. $ca_arr[$i];
								}
							}				
						}
					}	
				}
			}
			$sibling_children_list = '';
			$inc_list = false;
			if(array_key_exists('children_sibling_ages', $data)) {
				$cn_arr = $data['children_sibling_names'];
				$ca_arr = $data['children_sibling_ages'];
				if(array_key_exists(0, $ca_arr)) {
					for($i=0; $i<50; $i++) {
						if(array_key_exists($i, $ca_arr) && strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
							$children_list .= '<br><br>Geschwisterkind(er):';
							$inc_list = true;
							break;
						}
					}
					if($inc_list) {
						for($i=0; $i<50; $i++) {
							if(array_key_exists($i, $ca_arr)) {
								if(strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
									++$people_count;
									$children_list .= '<br>- '. $cn_arr[$i] .' '. $ca_arr[$i];
								}	
							}				
						}
					}	
				}
			}
			$member_member_list = '';
			$inc_list = false;
			if(array_key_exists('member_sibling_ages', $data)) {
				$cn_arr = $data['member_sibling_names'];
				$ca_arr = $data['member_sibling_ages'];
				if(array_key_exists(0, $ca_arr)) {
					for($i=0; $i<50; $i++) {
						if(array_key_exists($i, $ca_arr) && strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
							$children_list .= '<br><br>Geschwisterkind(er) / Mitglied:';
							$inc_list = true;
							break;
						}
					}
					if($inc_list) {
						for($i=0; $i<50; $i++) {
							if(array_key_exists($i, $ca_arr)) {
								if(strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
									++$people_count;
									$children_list .= '<br>- '. $cn_arr[$i] .' '. $ca_arr[$i];
								}	
							}				
						}
					}	
				}
			}			
			// Children
			$body .= $children_list;
			// Sibling's children
			$body .= $sibling_children_list;
			// Sibling member
			$body .= $member_member_list;

			if(isset($data['siblings_children_names[0]']) && !empty($data['siblings_children_names[0]'])) {
				$body .= '<br>Geschwisterkind(er):<br>';
				for($i=0; $i<50; $i++) {
					if(isset($data["siblings_children_names[$i]"]) && !empty($data["siblings_children_names[$i]"])) {
						$body .= '<br>- '. $data["siblings_children_names[$i]"] .' '. $data["siblings_children_ages[$i]"];
					}				
				}
			}

			// Only include iban if payment applicable
			if(Input::has('iban') && strlen(trim(Input::get('iban'))) > 0) {
				$body .= '<br><br>Ihre Bankverbindung:<br>'. trim(Input::get('iban')) .
						 '<br>'. trim(Input::get('depositor')) .
						 '<br>'.trim(Input::get('bank')) . '<br>
				<p>Der Gesamtbetrag für alle von Ihnen angemeldeten Teilnehmer wird in den kommenden Tagen von oben stehendem Konto abgebucht.</p>';
			}

			if(array_key_exists('newsletter', $data) && (strtolower($data['newsletter']) == 'on')) {
				$body .= '<br>Newsletter: Ja';
			}

			$rec_emails = [ $data['email'], 'programm@kunsthalle-bremen.de' ];
			if($data['email'] == 'shahidm08@gmail.com' || $data['email'] == 'manzoor@oneone-studio.com') { $rec_emails = [ $data['email'] ]; }

			foreach($rec_emails as $rec_email) {
				mail($rec_email, "Veranstaltungs-Anmeldung", $body, $headers);		   
			}
		}

		$lang = MenusController::getLang();
		$ref_url = $_SERVER['HTTP_REFERER'];
		$ref_url = str_replace('https://', '', str_replace('http://', '', str_replace('www.', '', $ref_url)));
		$ref_url = str_replace('/', '_', str_replace('kunsthalle-bremen.de', '', $ref_url));
		$ref_url = str_replace('/err=1', '', $ref_url);

		$controller_action = 'MenusController@getEventRegResponse';
		$params = [ 'menu_item' => 'calendar', 'link' => 'besuch-planen', 'return_url' => $ref_url];

		$menu_item = Input::has('menu_item') ? Input::get('menu_item') : 'calendar';
		$link = Input::has('link') ? Input::get('link') : 'besuch-planen';
		$action = 'bestaetigung';

		$ref_url = str_replace($_SERVER['SERVER_NAME'].'_', '', $ref_url);		

		$ref_url = $_SERVER['HTTP_REFERER'];
		
		// echo 'action: '. $controller_action.'<br>ref_url: '. $ref_url; exit;
		// $arr = explode('_', $ref_url);
		// $url = '';
		// $has_event_index = false;
		// if(strpos($ref_url, '_calendar_besuch-planen') && (count($arr) > 3) && (strlen($arr[3]) > 8) && isset($arr[4])) {
		// 	$arr[3] = $arr[3].'_'.$arr[4];
		// 	array_splice($arr, 4, 1);
		// 	$has_event_index = true;
		// }

		// if(count($arr) > 1) {
		// 	for($i=1;$i<count($arr);$i++) { $url .= '/'. $arr[$i]; }
		// 	$return_url = $url;
		// } else {
		// 	$return_url = str_replace('_', '/', $return_url);
		// }
		// return Redirect::action('MenusController@getPage', ["calendar", "besuch-planen"]);
		return Redirect::action('MenusController@getEvtRegResp', ['lang' => $lang]);
	}

	public function registerForEventUsingLog() {
		echo 'Access Denied..'; exit;
		
		include_once 'logs/regs.inc.php'; // include inputs file

		$f = fopen('logs/event_reg.log', 'a+');
		fwrite($f, "[".date('Y-m-d H:i')."] - registerForEvent() called\n\nUser Agent: ". $_SERVER['HTTP_USER_AGENT']."\n\n".print_r(Input::all(), true)."\n\n");

		foreach($inputs as $inp) {
			$data = $inp;
			$pkg_price = false;
			if(array_key_exists('pay_as_package', $data) && strtolower($data['pay_as_package']) == 'on') {
				$pkg_price = true;
			}          
			if(isset($data['id'])) {
				$event = KEvent::with(['kEventCost', 'clusters', 'clusters.k_events', 'clusters.kEventCost', 'event_dates'])->find($data['id']);
				if(isset($event)) {
					// Store inputs in DB
					// $input_ser = serialize($inp);
					// DB::table('registrations')->insert(['inputs' => $input_ser, 'created_at' => date('Y-m-d H:i:s')]);

					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					$headers .= 'From: Kunsthalle Bremen <info@kunsthalle-bremen.de>' . "\r\n";

					$body = "Sehr geehrte/r ".$data['first_name']. " ". $data['last_name']. ",<br><br>
							 vielen Dank für Ihre Anmeldung zu folgender Veranstaltung der Kunsthalle Bremen:<br>";
					$body .= 'Event: ID '. $data['id']; 
					$start_time = $event->start_time;		 
					$start_time = substr($start_time, 0, 5);		 
					$body .= "<br><strong>". $event->title_de .'</strong><br>';
					if(isset($event->subtitle_de) && !empty($event->subtitle_de)) {
						$body .= $event->subtitle_de .'<br>';
					}

					$ev_dates = [];
					if($event->clusters && $pkg_price) {
						foreach($event->clusters as $cl) {
							if($cl->package == 1) {
								$evs = $cl->k_events;
								if($evs) {
									$ev_dates = [];
									foreach($evs as $ev) {
										$today = strtotime(date('Y-m-d'));
										if(strtotime($ev->start_date) >= $today) {
											fwrite($f, "\n---=> ". $ev->id.' _ '.$ev->title_de . ' - ['. $ev->start_date.']');
											$ev_dates[] = $ev->start_date;
										}
									}
									if(count($ev_dates)) {
										$body .= implode(', ', $ev_dates);
									}
								}
							}
						}
					} 
					if(count($ev_dates) == 0) {
						if($event->as_series == 1) {
							$body .= date('d/m/y', strtotime($event->start_date)) . ' - ' . date('d/m/y', strtotime($event->end_date));
						} else {				
							if(Input::has('reg_event_date')) {
								$body .= date('d/m/y', strtotime($data['reg_event_date']));
							} else {
								$body .= date('d/m/y', strtotime($event->start_date)); // . ' - ' . date('d/m/y', strtotime($event->end_date));
							}
						}
					}

					// fwrite($f, "\n\nEvent Dates:-\n". print_r($event->event_dates, true));

					$body .= '<br>'. $start_time .' - '. $event->end_time .'<br><br>';
					$event_cost_obj = $event->kEventCost;

					if($event->clusters && count($event->clusters)) {
						$price_cluster = $event->clusters[0];
						foreach($event->clusters as $cl) {
							if($cl->package == 1) {
								$price_cluster = $cl;
								$event_cost_obj = $cl->kEventCost;
								break;
							}
						}
					}
					// echo '<pre>'; print_r($event_cost_obj); exit;
					$total_price = 0;
			        // 'Gesamtbetrag für alle von Ihnen angemeldeten Teilnehmer:<br>'.
			        // str_replace('.', ',', $data['total']) .' EURO';
					if(array_key_exists('regular_adult_price', $data) && is_numeric($data['regular_adult_price'])) {
						$body .= $data['regular_adult_price']. ' Erwachsene(r) ';
						if($pkg_price == true && $event->clusters) {
							$body .= $event_cost_obj->regular_adult_price;
							$total_price += ($data['regular_adult_price'] * $event_cost_obj->regular_adult_price);
						} else {
							$body .= $event->kEventCost->regular_adult_price;
							$total_price += ($data['regular_adult_price'] * $event->kEventCost->regular_adult_price);
						}  
						$body .= ' Euro<br>';
					}          
					// if(Input::has('regular_child_price')) {
					// 	$body .= $data['regular_child_price']. ' Kinder '. $event->kEventCost->regular_child_price .' Euro<br>';
					// }
					if(array_key_exists('member_adult_price', $data) && is_numeric($data['member_adult_price'])) {
						$body .= $data['member_adult_price']. ' Mitglied(er) ';
						if($pkg_price == true && $event->clusters) {
							$body .= $event_cost_obj->member_adult_price;
							$total_price += ($data['member_adult_price'] * $event_cost_obj->member_adult_price);
						} else {
							$body .= $event->kEventCost->member_adult_price;
							$total_price += ($data['member_adult_price'] * $event->kEventCost->member_adult_price);
						}  
						$body .= ' Euro<br>';
					}
					if(array_key_exists('member_child_price', $data) && is_numeric($data['member_child_price'])) {
						$body .= $data['member_child_price']. ' Kind(er) / Mitglied ';
						if($pkg_price == true && $event->clusters) {
							$body .= $event_cost_obj->member_child_price;
							$total_price += ($data['member_child_price'] * $event_cost_obj->member_child_price);
						} else {
							fwrite($f, "\n\nCheck 1: ". $event->kEventCost->member_child_price);
							$body .= $event->kEventCost->member_child_price;
							$total_price += ($data['member_child_price'] * $event->kEventCost->member_child_price);
						}  
						$body .= ' Euro<br>';
					}
					if(array_key_exists('regular_child_price', $data) && is_numeric($data['regular_child_price'])) {
						$body .= $data['regular_child_price']. ' Kind(er) ';
						if($pkg_price == true && $event->clusters) {
							$body .= $event_cost_obj->regular_child_price;
							$total_price += ($data['regular_child_price'] * $event_cost_obj->regular_child_price);
						} else {
							fwrite($f, "\n\nCheck 1: ". $event->kEventCost->regular_child_price);
							$body .= $event->kEventCost->regular_child_price;
							$total_price += ($data['regular_child_price'] * $event->kEventCost->regular_child_price);
						}  
						$body .= ' Euro<br>';
						fwrite($f, "\Kinder ");
					}
					// if(Input::has('sibling_child_price')) {
					// 	$body .= $data['sibling_child_price']. ' Geschwisterkinder '. $event->kEventCost->sibling_child_price .' Euro<br>';
					// }
					if(array_key_exists('sibling_member_price', $data) && is_numeric($data['sibling_member_price'])) {
						$body .= $data['sibling_member_price']. ' Geschwisterkind(er) / Mitglied ';
						if($pkg_price == true && $event->clusters) {
							fwrite($f, "\ncheck:- event->clusters[0]->kEventCost->sibling_member_price: ". $event_cost_obj->sibling_member_price);
							$body .= $event_cost_obj->sibling_member_price;
							$total_price += ($data['sibling_member_price'] * $event_cost_obj->sibling_member_price);
						} else {
							fwrite($f, "\n\ncheck:- event->kEventCost->sibling_member_price: ". $event->kEventCost->sibling_member_price);
							$body .= $event->kEventCost->sibling_member_price;
							$total_price += ($data['sibling_member_price'] * $event->kEventCost->sibling_member_price);
						}  
						$body .= ' Euro<br>';
						fwrite($f, "\n\nGeschwisterkinder / Mitglied");
					}
					if(array_key_exists('sibling_child_price', $data) && is_numeric($data['sibling_child_price'])) {
						$body .= $data['sibling_child_price']. ' Geschwisterkind(er) ';
						if($pkg_price == true && $event->clusters) {
							fwrite($f, "\ncheck:- event->clusters[0]->kEventCost->sibling_child_price: ". $event_cost_obj->sibling_child_price);
							$body .= $event_cost_obj->sibling_child_price;
							$total_price += ($data['sibling_child_price'] * $event_cost_obj->sibling_child_price);
						} else {
							fwrite($f, "\n\ncheck:- event->kEventCost->sibling_child_price: ". $event->kEventCost->sibling_child_price);
							$body .= $event->kEventCost->sibling_child_price;
							$total_price += ($data['sibling_child_price'] * $event->kEventCost->sibling_child_price);
						}  
						$body .= ' Euro<br>';
					}
					if(array_key_exists('reduced_price', $data) && is_numeric($data['reduced_price'])) {
						$body .= $data['reduced_price'] . " ermäßigt ";
						if($pkg_price == true && $event->clusters) {
							$body .= $event_cost_obj->reduced_price;
							$total_price += ($data['reduced_price'] * $event_cost_obj->reduced_price);
						} else {
							$body .= $event->kEventCost->reduced_price;
							$total_price += ($data['reduced_price'] * $event->kEventCost->reduced_price);
						}  
						$body .= ' Euro<br>';
					}
					$body .= '<br>Gesamtbetrag für alle von Ihnen angemeldeten Teilnehmer: '. $total_price . ' Euro<br>';
					          
					// fwrite($f, $body);
					if(strlen($event->place) > 0) {
						$body .= '<br><br>Ort der Veranstaltung:<br>'. $event->place .'';
					}
					$body .= '<p>Diese Mail ist eine automatisierte Eingangsbestätigung. 
								 Nach Prüfung Ihrer Anmeldung erhalten Sie baldmöglichst eine verbindliche Bestätigung Ihrer Teilnahme.
							  </p><br>'.
							  'Mit freundlichen Grüßen<br>Ihre Kunsthalle Bremen<br>'.
							  "Bildung und Vermittlung<br>
							   <a href='mailto:programm@kunsthalle-bremen.de'>programm@kunsthalle-bremen.de</a><br>
							   T <a href='tel:+49 (0)421-32 908 330'>+49 (0)421-32 908 330</a>".
							   '<br>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br><br>';

					$body .= "Ihre Anmeldedaten:<br>". $data['first_name'] .' '. $data['last_name'] .
							 '<br>'.$data['street'] .' '. $data['streetno'] .'<br>'.
							 $data['zip'] .' '. $data['city'] .'<br>'. $data['phone'] .'<br>'. $data['email'].'<br>';
					
					if(isset($data['member_chk']) && strlen($data['member_no']) > 0) { $body .= '<br>Mitglied im Kunstverein:<span style="margin-left:5px;">' . $data['member_no'].'</span><br>'; }

					$people_count = 1;
					$children_list = '';
					$inc_list = false;
					if(array_key_exists('children_ages', $data)) {
						$cn_arr = $data['children_names'];
						$ca_arr = $data['children_ages'];
						if(array_key_exists(0, $ca_arr)) {
							for($i=0; $i<50; $i++) {
								if(array_key_exists($i, $ca_arr) && strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
									$children_list .= '<br>Kind(er):';
									$inc_list = true;
									break;
								}
							}
							if($inc_list) {
								for($i=0; $i<50; $i++) {
									if(array_key_exists($i, $ca_arr)) {
										if(strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
											++$people_count;
											$children_list .= '<br>- '. $cn_arr[$i] .' '. $ca_arr[$i];
										}	
									}				
								}
							}
						}
					}
					$member_children_list = '';
					$inc_list = false;
					if(array_key_exists('children_member_ages', $data)) {
						$cn_arr = $data['children_member_names'];
						$ca_arr = $data['children_member_ages'];
						if(array_key_exists(0, $ca_arr)) {
							for($i=0; $i<50; $i++) {
								if(array_key_exists($i, $ca_arr) && strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
									$children_list .= '<br><br>Kind(er)/ Mitglied:';
									$inc_list = true;
									break;
								}
							}
							if($inc_list) {
								for($i=0; $i<50; $i++) {
									if(array_key_exists($i, $ca_arr)) {
										if(strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
											++$people_count;
											$children_list .= '<br>- '. $cn_arr[$i] .' '. $ca_arr[$i];
										}
									}				
								}
							}	
						}
					}
					$sibling_children_list = '';
					$inc_list = false;
					if(array_key_exists('children_sibling_ages', $data)) {
						$cn_arr = $data['children_sibling_names'];
						$ca_arr = $data['children_sibling_ages'];
						if(array_key_exists(0, $ca_arr)) {
							for($i=0; $i<50; $i++) {
								if(array_key_exists($i, $ca_arr) && strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
									$children_list .= '<br><br>Geschwisterkind(er):';
									$inc_list = true;
									break;
								}
							}
							if($inc_list) {
								for($i=0; $i<50; $i++) {
									if(array_key_exists($i, $ca_arr)) {
										if(strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
											++$people_count;
											$children_list .= '<br>- '. $cn_arr[$i] .' '. $ca_arr[$i];
										}	
									}				
								}
							}	
						}
					}
					$member_member_list = '';
					$inc_list = false;
					if(array_key_exists('member_sibling_ages', $data)) {
						$cn_arr = $data['member_sibling_names'];
						$ca_arr = $data['member_sibling_ages'];
						if(array_key_exists(0, $ca_arr)) {
							for($i=0; $i<50; $i++) {
								if(array_key_exists($i, $ca_arr) && strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
									$children_list .= '<br><br>Geschwisterkind(er) / Mitglied:';
									$inc_list = true;
									break;
								}
							}
							if($inc_list) {
								for($i=0; $i<50; $i++) {
									if(array_key_exists($i, $ca_arr)) {
										if(strlen(trim($ca_arr[$i])) && is_numeric($ca_arr[$i])) {
											++$people_count;
											$children_list .= '<br>- '. $cn_arr[$i] .' '. $ca_arr[$i];
										}	
									}				
								}
							}	
						}
					}			
					// $body .= '<br>Weitere angemeldete Personen:<span style="margin-left:5px;">';			
					// $body .= $people_count . '</span><br>'; 
					// Children
					$body .= $children_list;
					// Sibling's children
					$body .= $sibling_children_list;
					// Sibling member
					$body .= $member_member_list;

					if(isset($data['siblings_children_names[0]']) && !empty($data['siblings_children_names[0]'])) {
						$body .= '<br>Geschwisterkind(er):<br>';
						for($i=0; $i<50; $i++) {
							if(isset($data["siblings_children_names[$i]"]) && !empty($data["siblings_children_names[$i]"])) {
								$body .= '<br>- '. $data["siblings_children_names[$i]"] .' '. $data["siblings_children_ages[$i]"];
							}				
						}
					}

					// Only include iban if payment applicable
					if(array_key_exists('iban', $data) && strlen(trim($data['iban'])) > 0) {
						$body .= '<br><br>Ihre Bankverbindung:<br>'. trim($data['iban']) .
								 '<br>'. trim($data['depositor']) .
								 '<br>'.trim($data['bank']) . '<br>
						<p>Der Gesamtbetrag für alle von Ihnen angemeldeten Teilnehmer wird in den kommenden Tagen von oben stehendem Konto abgebucht.</p>';
					}

					if(array_key_exists('newsletter', $data) && (strtolower($data['newsletter']) == 'on')) {
						$body .= '<br>Newsletter: Ja';
					}

		echo '<br>'. $body.'<br><br><hr><br>';
					// $rec_emails = [ $data['email'], 'programm@kunsthalle-bremen.de' ];
					$rec_emails = ['manzoor@oneone-studio.com'];			
					// foreach($rec_emails as $rec_email) {
					// 	mail($rec_email, "Veranstaltungs-Anmeldung", $body, $headers);		   
					// }
				}
			}
		}

		// echo '<br><br><h2 style="color:darkgreen;">D O N E</h2>';
		// return Redirect::action($controller_action, [ 'menu_item' => $menu_item, 'link' => $link, 'action' => $action ]);
		// return Redirect::action($controller_action, [ 'return_url' => $ref_url ]);
	}

	public function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}
		// print_r($d); exit;
		// if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			// return array_map(__FUNCTION__, $d);
		// }
		// else {
			// Return array
			return $d;
		// }
	 }

	 /*
	 public function getCalendarJson() {
		$calendar = [];
		$months = [ '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ];
		$slideNo = 0;
		$cur_month = date('m');
		$exhibition = Exhibition::with('gallery_images')->find(Input::get('exhibition_id'));
		$cluster = $exhibition->cluster;
		$clustered_events = [];
		$clustered_dates = [];
		$_clustered_dates = [];
		$event_ids = [];
		$event_count = 0;
		$slideHeights = [];
		$event_no = 0;
		$calendar = ExhibitionsController::getExhibitionCalendar($exhibition->id);
		$slideHeights = [];
		foreach($calendar as $c) {
			if(array_key_exists('slideHeight', $c)) {
				$slideHeights[] = $c['slideHeight'];
			}
		}
		$calendar['slideHeights'] = $slideHeights;

		return Response::json(array('error' => false, 'calendar_json' => json_encode($calendar)), 200);		 	
	 }/**/
	
	 public function getEventsCalendar2() {
		return View::make('pages.calendar2');
	 }

	 public static function getEventsCalendar($menu_item = null, $json = true, $page_cluster_id = 0) {
		setlocale(LC_ALL, "de_DE", 'German', 'german');
		$f = fopen('logs/cal.log', 'w+');
		fwrite($f, "\npage_cluster_id: ". $page_cluster_id."\n");
		date_default_timezone_set('Europe/Berlin'); 
		setlocale(LC_TIME, "de_DE");
	 	// $exhibition = Exhibition::with('gallery_images')->find($exhibition_id); 
		$calendar = [];
		$months = [ '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ];
		$slideNo = 1;
		$cur_date = date('Y-m-d');
		$cur_month = date('m');
		// $viewdata= $view->getData();
		// $exhibition = $viewdata['exhibition'];
		// $cluster = $exhibition->cluster;
		$clustered_events = [];
		$clustered_dates = [];
		$event_dates = [];
		$_event_dates = [];
		$all_clustered_dates = [];
		$all_event_dates = [];
		$_clustered_dates = [];
		$cluster_ids = [];
		$event_ids = [];
		$cal_event_ids = [];
		$_eids = [];
		$cur_evts = [];
		$event_count = 0;
		$slideHeights = [];
		$event_no = 0;
		$cal_months = [];
		$series_event_ids = [];
		$added_ser_event_ids = [];
		$_month = '';
		$month_tmp = '';
		$randstr = 'YuSz2p7E90L';
		$cur_year = date('Y');

		$cl_event_ids = [];
		if($page_cluster_id > 0) {
			$clusterObj = Cluster::with(['k_events'])->find($page_cluster_id);
			if($clusterObj && $clusterObj->k_events) {
				foreach($clusterObj->k_events as $e) {
					if(!in_array($e->id, $cl_event_ids)) {
						$cl_event_ids[] = $e->id;
					}
				}
				asort($cl_event_ids);
			}
		}

		// $events_list = [];
		// for($year=$cur_year; $year<($cur_year+10); $year++) {
		// 	foreach($months as $month) {
		// 		if($month >= $cur_month || ($year > date('Y'))) {
		// 			$query = 'select e.*, e.id as base_event_id							  	  
		// 				  	  from k_events e
		// 				  	  where e.start_date like "'. $year .'%" 
		// 				  	    and e.end_date >= "'.$year .'-'.$month.'-%"
		// 					  order by e.start_date';
		// 			$events_1 = DB::select($query);
		// 			if(count($events_1)) {
		// 				foreach($events_1 as &$e) { 
		// 					if(!in_array($e->id, $cal_event_ids)) {
								
		// 						$first_date = $e->start_date;
		// 						if((isset($e->event_day) && strlen($e->event_day) > 0) || strtolower($e->event_day_repeat) == 'daily') {
		// 							$rep_dates = KEventsController::getEventRepeatDates($e->base_event_id, $e->start_date, $e->event_day, $e->event_day_repeat, $e->repeat_month, $e->end_date);
		// 							if(count($rep_dates)) {
		// 								$first_date = $rep_dates[0];
		// 							}
		// 						} else {
		// 							$query = 'select event_date from event_dates 
		// 									  where k_event_id = ' . $e->base_event_id. '
		// 									    and event_date >= "'.date('Y-m-d').'"
		// 									  order by event_date';
		// 							$event_dates_rs = DB::select($query);
		// 							if($event_dates_rs && count($event_dates_rs)) {
		// 								$first_date = $event_dates_rs[0]->event_date;
		// 							}
		// 						}	
		// 						if(strtotime($first_date) >= strtotime(date('Y-m-d'))) {
		// 							$events_list[] = $e;
		// 							$cal_event_ids[] = $e->base_event_id; 
		// 						}
		// 					}
		// 				}
		// 			}
		// 		}
		// 	}
		// }	

		// echo '<pre>'; print_r($events_list);
		// exit;

		for($year=$cur_year; $year<($cur_year+5); $year++) {

			// if(!isset($calendar[$year])) {
			// 	$calendar[$year] = [];
			// }
			foreach($months as $month) {

				$eventBlockCount = 0;
				// if($month >= $cur_month || ($year > date('Y'))) { // if($year >= date('Y')) { // if($month >= $cur_month || ($year > date('Y'))) {
				if($month >= $cur_month || ($year > date('Y'))) {  // Feb 13, 2018  .. to include events with start older than cur month
					$events = [];
					// $query = 'select e.*, e.id as base_event_id							  	  
					// 	  	  from k_events e
					// 	  	  where e.start_date like "'. $year .'-'.$month.'-%" 
					// 	  	    and e.end_date >= "'.date('Y-m-d'). '"
					// 		  order by e.start_date';
					// following works to show events with start date older than current month					
					$query = 'select e.*, e.id as base_event_id							  	  
						  	  from k_events e
						  	  where e.end_date >= "'.date('Y-m-d'). '"
							  order by e.start_date';
							  
					$events_1 = DB::select($query);
					// echo '<br>'.$query.'<h3>events_2: '. count($events_1).'</h3>';
					if(count($events_1)) {
						foreach($events_1 as &$e) { 
							if(!in_array($e->id, $cal_event_ids)) {
								$events[] = $e;
								$cal_event_ids[] = $e->base_event_id; 
							}
						}
					}					
					// Events with 'as series' checked i.e., From -> To boxes
					$query = 'select e.*, e.id as base_event_id
						  	  from k_events e
						  	  where e.end_date >= "'.date('Y-m-d').'"
						  	    and e.as_series = 1
							  order by e.start_date';
					$events_aser = DB::select($query);
					if(count($events_aser)) {
						foreach($events_aser as &$e) { 
							if(!in_array($e->id, $cal_event_ids)) {
								if((strtotime($e->start_date) < strtotime(date('Y-m-d')) && $e->show_after_startdate == 1) || 
								   (strtotime($e->start_date) > strtotime(date('Y-m-d')))
								  ) {
									$events[] = $e;
									$cal_event_ids[] = $e->base_event_id;
								}	
							}
						}
					}	
					$query = 'select e.*, e.id as base_event_id, ed.event_date							  	  
						  	  from k_events e, event_dates ed
						  	  where e.start_date like "'. $year .'%" 
						  	    and ed.event_date >= "'.date('Y-m-d').'"
						  	    and ed.k_event_id = e.id';
					if(count($cal_event_ids)) {
						$query .= ' and e.id not in ('. implode(',', $cal_event_ids) .') ';
					}	  	    
					$query .= 'group by e.id
							   order by ed.event_date';
					$events_2 = DB::select($query);
					if(count($events_2)) {
						foreach($events_2 as &$e) { 
							if($e->as_series == 0) {
								$query = 'select event_date from event_dates 
								          where k_event_id = '.$e->base_event_id.' 
								            and event_date >= "'.date('Y-m-d').'"
								          order by event_date 
								          limit 1';
								$ed_res = DB::select($query);
								if($ed_res) {
									$e->start_date = $ed_res[0]->event_date;
								}          
							}
							if(date('m', strtotime($e->start_date)) == $month) {
								$events[] = $e;
								if(!in_array($e->id, $cal_event_ids)) {
									$cal_event_ids[] = $e->base_event_id; 
								}
							}
						}
					}					

					$event_count = count($events);
					if(count($events) > 0) {
						$m = strftime("%B", strtotime($events[0]->start_date));
						$_month = $m;
						$event_data = [];
						// $calendar[$year][$_month]['month'] = $m;
						// $calendar[$year][$_month]['month_no'] = $month;
						// $calendar[$year][$_month]['year'] = date('Y', strtotime($events[0]->start_date));
						$day_titles = [];

						foreach($events as &$e) {

							$emonth = date("m", strtotime($e->start_date));
							if(!in_array($emonth, $cal_months)) {
								$cal_months[] = $emonth;
							}
							sort($cal_months);

							// $event_dates = [];
							$_event_dates = [];
							$_evt = KEvent::with(['clusters', 'tags', 'kEventCost', 'entrance', 'event_dates'])
							          ->where('id', $e->base_event_id)
							          ->first();
							// if($_evt->id == 296) { //echo '>> '. $_evt->id .'<br>'; echo '<pre>'; print_r($_evt->event_dates); exit; }
							if($_evt->as_series == 0) {
								if($_evt->event_dates && count($_evt->event_dates)) {
									$_evt->start_date = $_evt->event_dates[0]->event_date;
								}
							}
							$e->event_cost = $_evt->kEventCost;
							$e->entrance = $_evt->entrance;
							$hasCost = false;
							if(isset($e->event_cost)) {
								$c = $e->event_cost;
								if($c->regular_adult_price >= 0 || $c->regular_child_price >= 0 || $c->member_adult_price >= 0 || $c->member_child_price >= 0
									  || $c->siblings_child_price >= 0 || $c->reduced_price >= 0) {
									$hasCost = true;
								}
							}
							if(isset($e->entrance) && $e->entrance->free == 1) {
								$hasCost = false;
							}
							$e->hasCost = $hasCost;

							$e->tags = $_evt->tags;
							$e->entrance = $_evt->entrance;
							// $e->start_date = substr($e->start_date, 0, 4);
							$e->index = $e->base_event_id . date('dmY', strtotime($e->start_date));
							$e->clusters = $_evt->clusters;
							$exb_titles = [];
							$package = 0;
							$package_prices = [];
							$cluster_events = [];
							$_cluster_dates = [];
							$show_cost_label = false;

							$price_list_str = '';
							if(count($_evt->clusters)) {
								$cl = $_evt->clusters[0];								
								if($cl->kEventCost) {
									$package = ($cl->package == 1) ? 1 : 0;
									$e->pkg_regular_adult_price = $cl->kEventCost->regular_adult_price;
									$e->pkg_regular_child_price = $cl->kEventCost->regular_child_price;
									$e->pkg_member_adult_price = $cl->kEventCost->member_adult_price;
									$e->pkg_member_child_price = $cl->kEventCost->member_child_price;
									$e->pkg_sibling_child_price = $cl->kEventCost->sibling_child_price;
									$e->pkg_sibling_member_price = $cl->kEventCost->sibling_member_price;
									$e->pkg_reduced_price = $cl->kEventCost->reduced_price;

								}
							}
							$e->package = $package;	
							foreach($_evt->clusters as $cl) {
								$cluster_id = $cl->id;

								$sql = 'select e.*, e.id as base_eid from k_events e, clusters ec, cluster_k_event ecke
								        where ecke.cluster_id = ' . $cluster_id . '
								          and ecke.k_event_id = e.id ';
								if($page_cluster_id > 0) {
									$sql .= ' and ecke.cluster_id = '. $page_cluster_id;
								}
								$sql .= ' group by e.id';								        
								// echo $sql;        
								$ce_data = DB::select($sql);
								if(is_array($ce_data) && count($ce_data)) {
									foreach($ce_data as $cd) {
										if(!in_array($cd->base_eid, $event_ids)) {
											$clustered_events[] = $cd;
											$event_ids[] = $cd->base_eid;
										}
									}
								}
								$query = 'select title_en, title_de from pages
								          where page_type = "exhibition"
								            and cluster_id = '. $cluster_id;
								$res = DB::select($query);
								if($res && count($res)) {
									if(strlen($res[0]->title_en) > 0) {
										$exb_titles[strtolower(str_replace(' ', '-', $res[0]->title_en))] = $res[0]->title_de;
									}
								}

								if($cl->package == 1 && $cl->kEventCost) {
									$package = 1;
									// $e->pkg_regular_adult_price = $cl->kEventCost->regular_adult_price;
									// $e->kEventCost->pkg_regular_adult_price = $cl->kEventCost->regular_adult_price;

									// $e->pkg_regular_child_price = $cl->kEventCost->regular_child_price;
									// $e->kEventCost->pkg_regular_child_price = $cl->kEventCost->regular_child_price;

									// $e->pkg_member_adult_price = $cl->kEventCost->member_adult_price;
									// $e->kEventCost->pkg_member_adult_price = $cl->kEventCost->member_adult_price;

									// $e->pkg_member_child_price = $cl->kEventCost->member_child_price;
									// $e->kEventCost->pkg_member_child_price = $cl->kEventCost->member_child_price;

									// $e->pkg_sibling_child_price = $cl->kEventCost->sibling_child_price;
									// $e->kEventCost->pkg_sibling_child_price = $cl->kEventCost->sibling_child_price;

									// $e->pkg_sibling_member_price = $cl->kEventCost->sibling_member_price;
									// $e->kEventCost->pkg_sibling_member_price = $cl->kEventCost->sibling_member_price;

									// $e->pkg_reduced_price = $cl->kEventCost->reduced_price;
									// $e->kEventCost->pkg_reduced_price = $cl->kEventCost->reduced_price;
								}
								if($cl->package == 1) {
									// fwrite($f, "\nEvent: ".$e->id ." __ Cluster ----> ". $cl->id);
									$clusterObj = Cluster::with(['k_events'])->find($cl->id);
									$eids = [];
									foreach($clusterObj->k_events as $ke) {
										if(strtotime($ke->start_date) > time()) {
											$ce_date = $ke->start_date;
											$fdate = date("D-d-M", strtotime($ce_date));
											sort($cal_months);
											$slideNo = 1;
											$c = 0;
											foreach($cal_months as $cm) {
												++$c;
												if(intval($cm) == intval($emonth)) {
													$slideNo = $c;
												}
											}
											if(!in_array(date('D-d-M', strtotime($ce_date)), $_cluster_dates) && in_array($ke->id, $event_ids)) {
												$cluster_events[] = [   'id'         => $ke->id,
																		'index'      => $ke->id . date('dmY', strtotime($ce_date)),
																		'title'      => $ke->title_de,
																		'event_date' => $fdate,
																		'full_date'  => $ce_date,
																		'event_date_de' => utf8_encode(strftime('%a-%d-%b', strtotime($ce_date))),
																		'slideNo'	 => $slideNo
																    ];
												$_cluster_dates[] = date('D-d-M', strtotime($ce_date)); 
											}
										}
									}								
								}
							}
							$e->cluster_events = $cluster_events;
							$e->exb_titles = $exb_titles;

							if(!array_key_exists($e->base_event_id, $clustered_dates)) {
								$clustered_dates[$e->base_event_id] = [];
							}
							if((isset($e->event_day) && strlen($e->event_day) > 0) || strtolower($e->event_day_repeat) == 'daily') {
								$rep_dates = KEventsController::getEventRepeatDates($e->base_event_id, $e->start_date, $e->event_day, $e->event_day_repeat, $e->repeat_month, $e->end_date);
								foreach($rep_dates as $rep_date) {
									if(strtotime($rep_date) >= strtotime('Y-m-d')) {
										$fdate = date("D-d-M", strtotime($rep_date));
										$emonth = date("m", strtotime($rep_date));
										if(!in_array($emonth, $cal_months)) {
											// ++$slideNo;
											$cal_months[] = $emonth;
										}
										sort($cal_months);
										$c = -1;
										foreach($cal_months as $cm) {
											++$c;
											if(intval($cm) == intval($emonth)) {
												$slideNo = $c;
											}
										}
										if(!in_array(date('D-d-M', strtotime($rep_date)), $_event_dates)) {
											// if(!in_array($e->base_event_id, $series_event_ids)) {
												$event_dates[$e->base_event_id][] = [ 
																			'id'         => $e->base_event_id,
																			'index'      => $e->base_event_id . date('dmY', strtotime($rep_date)),
																			'title'      => $e->title_de,
							 												'event_date' => $fdate,
							 												'full_date'  => $rep_date,
							 												'event_date_de' => utf8_encode(strftime('%a-%d-%b', strtotime($rep_date))),
							 												'slideNo'	 => $slideNo
																	    ];
												$_event_dates[] = date('D-d-M', strtotime($rep_date)); 
											// }
											if($e->as_series == 1 && !in_array($e->base_event_id, $series_event_ids)) {
												$series_event_ids[] = $e->base_event_id;
											}
										}
									}
								}
							} else {
								$query = 'select event_date from event_dates 
										  where k_event_id = ' . $e->base_event_id. '
										    and event_date >= "'.date('Y-m-d').'"
										  order by event_date';
								$event_dates_rs = DB::select($query);
								foreach($event_dates_rs as $rep_date_obj) {
									$rep_date = $rep_date_obj->event_date;
									$emonth = date("m", strtotime($rep_date));
									if(!in_array($emonth, $cal_months)) {
										// ++$slideNo;
										$cal_months[] = $emonth;
									}
									sort($cal_months);
									$c = -1;
									foreach($cal_months as $cm) {
										++$c;
										if(intval($cm) == intval($emonth)) {
											$slideNo = $c;
										}
									}
									$fdate = date("D-d-M", strtotime($rep_date));
									if(!in_array(date('D-d-M', strtotime($rep_date)), $_event_dates)) {
										// if(!in_array($e->base_event_id, $series_event_ids)) {
											$event_dates[$e->base_event_id][] = [ 
																		'id'         => $e->base_event_id,
																		'index'      => $e->base_event_id . date('dmY', strtotime($rep_date)),
																		'title'      => $e->title_de,
						 												'event_date' => $fdate,
						 												'event_date_de' => strftime('%a-%d-%b', strtotime($rep_date)),
						 												'full_date'  => $rep_date,
						 												'slideNo'	 => $slideNo
																    ];
											$_event_dates[] = date('D-d-M', strtotime($rep_date)); 
										// }
										if($e->as_series == 1 && !in_array($e->base_event_id, $series_event_ids)) {
											$series_event_ids[] = $e->base_event_id;
										}
									}
								}
							}
							$e->event_dates = $event_dates;

							$query = 'select c.* from k_event_costs c where c.k_event_id = '. $e->base_event_id;
							$cost_res = DB::select($query);
							$priceFieldCount = 0;
							if(is_array($cost_res) && count($cost_res)) {
								$cost = $cost_res[0];
								foreach($cost as $k => $v) {
									if(strstr($k, '_price')) {
										$e->{$k} = $v;
										if(is_numeric($v) && floatval($v) > 0) {
											++$priceFieldCount;
										}
									}
								}
							}
							$e->priceFieldCount = $priceFieldCount;

							$day_num = date('d', strtotime($e->start_date));
							$day_title = $day_num .' '. date('l', strtotime($e->start_date));
							$event_list = [];
						}					

						foreach($events as &$e) {
							++$eventBlockCount;
							$rep_count = 0;
							if((isset($e->event_day) && strlen($e->event_day) > 0) || strtolower($e->event_day_repeat) == 'daily') {
								$rep_dates = KEventsController::getEventRepeatDates($e->base_event_id, $e->start_date, $e->event_day, $e->event_day_repeat, $e->repeat_month, $e->end_date);
								if(count($rep_dates)) {
									if($e->as_series == 0) {
										$e->rep_dates = $rep_dates;
									}
									$eventBlockCount += count($rep_dates); 
								}
								foreach($rep_dates as $rep_date) {
									if(($e->as_series == 1 && !in_array($e->base_event_id, $added_ser_event_ids)) || $e->as_series == 0) {
										// Exclude event if 'show after start date' is off and start date passed already
										if($e->show_after_startdate == 0 && (strtotime($e->start_date) < strtotime($cur_date))) { continue; }

										// Force event to only appear once
										if($e->as_series == 1) {
											$added_ser_event_ids[] = $e->base_event_id;
										}
										/**/
		 								$_MONTH = strftime("%B", strtotime($rep_date));								
		 								$month_no = date('m', strtotime($rep_date));
										$day_num_rep = date('d', strtotime($rep_date));
										$day_title_rep = $day_num_rep .' '. strftime('%A', strtotime($rep_date));
										$event_year = date('Y', strtotime($rep_date));
										$mnth_events = [];
										if(!isset($calendar[$event_year])) { $calendar[$event_year] = []; }
										if(!array_key_exists($_MONTH, $calendar[$event_year])) {
											$calendar[$event_year][$_MONTH] = [];
											$calendar[$event_year][$_MONTH]['month'] = $_MONTH;
											$calendar[$event_year][$_MONTH]['month_no'] = $month_no;
											$calendar[$event_year][$_MONTH]['year'] = date('Y', strtotime($rep_date));
											$calendar[$event_year][$_MONTH]['daysCount'] = count($rep_dates);
										}
										if(!isset($calendar[$event_year][$_MONTH]['days'])) {
											$calendar[$event_year][$_MONTH]['days'] = [];
										} 
										if(!isset($calendar[$event_year][$_MONTH]['days'][$day_title_rep])) {
											$calendar[$event_year][$_MONTH]['days'][$day_title_rep] = ['events' => []];
											$mnth_events = [];
										} else {
											$mnth_events = $calendar[$event_year][$_MONTH]['days'][$day_title_rep]['events'];
										}
										$e->day_title = $day_title_rep;									
										$e->index = $e->base_event_id . date('dmY', strtotime($rep_date));
										$estr = $e->index;
										if(count($cl_event_ids)) {
											if(in_array($e->base_event_id, $cl_event_ids)) {
												// $calendar[$event_year][$_MONTH]['days'][$day_title_rep]['events'][] = objectToArray($e);
												$mnth_events[] = objectToArray($e);
											}
										} else {
											// $calendar[$event_year][$_MONTH]['days'][$day_title_rep]['events'][] = objectToArray($e);
											$mnth_events[] = objectToArray($e);
										}
										$calendar[$event_year][$_MONTH]['days'][$day_title_rep]['events'] = $mnth_events;
										if($e->first_only) { break; }
									}
								}
								$rep_count += count($rep_dates);

							} else { // if event has random dates, use these instead of repeated dates
								$query = 'select event_date, k_event_id from event_dates 
										  where k_event_id = ' . $e->base_event_id . ' 
										    and event_date >= "'.date('Y-m-d').'"
										  order by event_date';
								$__event_dates = DB::select($query);
								foreach($__event_dates as $e_date) {
									if(($e->as_series == 1 && !in_array($e->base_event_id, $added_ser_event_ids)) || $e->as_series == 0) {
										// Exclude event if 'show after start date' is off and start date passed already
										if($e->show_after_startdate == 0 && (strtotime($e->start_date) < strtotime($cur_date))) { continue; }

										if($e->as_series == 1) {
											$added_ser_event_ids[] = $e->base_event_id;
										}
										$day_num_rep = date('d', strtotime($e_date->event_date));
										$month_no = date('m', strtotime($e_date->event_date));
										$day_title_rep = $day_num_rep .' '. strftime('%A', strtotime($e_date->event_date));
										$event_year = date('Y', strtotime($e_date->event_date));
										// echo $day_title_rep . '<br>';
										$_MONTH = strftime("%B", strtotime($e_date->event_date));
										$mnth_events = [];
										if(!isset($calendar[$event_year])) { $calendar[$event_year] = []; }
										if(!array_key_exists($_MONTH, $calendar[$event_year])) {
											$calendar[$event_year][$_MONTH] = [];
											$calendar[$event_year][$_MONTH]['month'] = $_MONTH;
											$calendar[$event_year][$_MONTH]['month_no'] = $month_no;
											$calendar[$event_year][$_MONTH]['year'] = date('Y', strtotime($e_date->event_date));
											// $calendar[$event_year][$_MONTH]['days'] = [];
											$calendar[$event_year][$_MONTH]['daysCount'] = count($__event_dates);
											// $calendar[$event_year][$_MONTH]['days'][$day_title_rep] = [ 'events' => []];
											// $calendar[$event_year][$_MONTH]['days'][$day_title_rep]['events'] = [];
										}
										if(!isset($calendar[$event_year][$_MONTH]['days'])) {
											$calendar[$event_year][$_MONTH]['days'] = [];
										} 
										if(!isset($calendar[$event_year][$_MONTH]['days'][$day_title_rep])) {
											$calendar[$event_year][$_MONTH]['days'][$day_title_rep] = ['events' => []];
											$mnth_events = [];
										} else {
											$mnth_events = $calendar[$event_year][$_MONTH]['days'][$day_title_rep]['events'];
										}

										$e->index = $e->base_event_id . date('dmY', strtotime($e_date->event_date));
										$estr = $e->index;
										// echo "<br>YEAR:--> ". $e->start_date . ' => '. $event_year . " ___ ". $year;
										// if(date('Y', strtotime($e->start_date)) == $year) {
											if(count($cl_event_ids)) {
												if(in_array($e->base_event_id, $cl_event_ids)) {
													// $calendar[$event_year][$_MONTH]['days'][$day_title_rep]['events'][] = objectToArray($e);
													$mnth_events[] = objectToArray($e);
												}
											} else {
												// $calendar[$event_year][$_MONTH]['days'][$day_title_rep]['events'][] = objectToArray($e);
												$mnth_events[] = objectToArray($e);
											}
										// }	
										$calendar[$event_year][$_MONTH]['days'][$day_title_rep]['events'] = $mnth_events;	
										if($e->first_only) { break; }
									}
								}
							}
						}	
					}
			    }	
			}
		}

		foreach($calendar as $year => &$yr_cal) {
			foreach($yr_cal as $m => &$cal) {
				if(!array_key_exists('days', $cal) || (array_key_exists('days', $cal))) {
                    foreach($cal['days'] as $dy => &$events) {
                    	$event_count = 0;
                    	foreach($events as $ky => &$es) {
                    		$event_count = count($es);
                    	}
                    	if($event_count == 0) {
                    		unset($cal['days'][$dy]);
                    	}
                    }

				    if(count($cal['days']) == 0) {
						unset($yr_cal[$m]);
					} else {
						ksort($cal['days']);
					}
				}
			}
		}	

		foreach($calendar as $year => &$yr_cal) {
			usort($yr_cal, function($c1, $c2) {
				if(array_key_exists('month_no', $c1) && array_key_exists('month_no', $c2)) {
					$m1 = intval(ltrim($c1['year'].$c1['month_no']));
					$m2 = intval(ltrim($c2['year'].$c2['month_no']));
					if($m1 == $m2) return 0;
					return ($m1 < $m2) ? -1 : 1;
				}
			});
		}
			
		$slideN = 0;
		$_mnths = [];
		foreach($calendar as $year => &$yr_cal) {
			foreach($yr_cal as $m => &$cal) {
				++$slideN;
				if(array_key_exists('days', $cal) && count($cal['days'])) {
					foreach($cal['days'] as $day => &$data) {
						// Sort same day events by time
						usort($data['events'], function($a, $b) {
							$at = str_replace(':', '', $a['start_time']);
							$et = str_replace(':', '', $b['start_time']);
							if($at == $et) return 0;
							return ($at < $et) ? -1 : 1;
						});
						foreach($data['events'] as &$ev) {
							$ev['slideNo'] = $slideN;
							foreach($ev['event_dates'] as $dates) {
								foreach($dates as &$date) {
									// fwrite($f, "\ndate:---->\n".print_r($date, true)); //exit;
									$m = date('m', strtotime($date['full_date']));
									if(!in_array($m, $_mnths)) {
										$_mnths[] = $m;
									}
								}
							}
						}
					}
				}
			}
		}
		/** /
		foreach($calendar as $m => &$cal) {
			if(array_key_exists('days', $cal) && count($cal['days'])) {
				foreach($cal['days'] as $day => &$data) {
					foreach($data['events'] as &$ev) {
						$m = date('m', strtotime($ev['start_date']));
						$c = 0;
						foreach($_mnths as $_m) {
							++$c;
							if(intval($_m) == intval($m)) { $ev['slideNo'] = $c; }
						}

						foreach($ev['event_dates'] as &$dates) {
							foreach($dates as &$date) {
								$m = date('m', strtotime($date['full_date']));
								$c = 0;
								foreach($_mnths as $_m) {
									++$c;
									if(intval($_m) == intval($m)) { $date['slideNo'] = $c; }
								}
							}
						}
					}
				}
			}
		}

		foreach($event_dates as &$dates) {
			foreach($dates as &$date) {
				$m = date('m', strtotime($date['full_date']));
				$c = 0;
				foreach($_mnths as $_m) {
					++$c;
					if(intval($_m) == intval($m)) { $date['slideNo'] = $c; }
				}
			}
		}/**/

		$tags = Tag::all()->sortBy('tag_de');
		$tag_ids = [];

		$day_count = 0;
		foreach($calendar as $year => &$yr_cal) {
			foreach($yr_cal as $m => &$cal) {
				$eventCount = 0;
				$cl_dates_height = 0;
				foreach($cal['days'] as $day => &$data) {
					++$day_count;
					$eventCount += count($data['events']);
					foreach($data['events'] as &$ev) {
						$ev['event_date'] = $ev['start_date'];
						$dt_str = $cal['year'].'-'.$cal['month_no'].'-';
						$day_arr = explode(' ', $day);
						if($day_arr) { $dt_str = $cal['year'].'-'.$cal['month_no'].'-'.$day_arr[0]; }
						$query = 'select event_date from event_dates 
								  where k_event_id = '. $ev['id'] .' 
								    and event_date like "%'.$dt_str.'%"';
						$ed_res = DB::select($query);
						if($ed_res) {
							$ev['event_date'] = $ed_res[0]->event_date;
						}
						$has_cost = 0;
						if($ev['event_cost']) {
							$cost = $ev['event_cost'];
							$citems = ['regular_adult_price', 'regular_child_price', 'member_adult_price', 'member_child_price', 
							  'sibling_child_price', 'sibling_member_price', 'reduced_price'];
							foreach($citems as $ci) {
								$price = intval(trim($cost->{$ci}));
								if($price > 0.0) { $has_cost = 1; }
							}
						}
						$ev['has_cost'] = $has_cost;
						$showCostLabel = $has_cost;
						if(intval($ev['entrance']['free']) == 1 || intval($ev['entrance']['included']) == 1 || 
						   		intval($ev['entrance']['excluded']) == 1 || intval($ev['entrance']['entry_fee']) == 1) {
							$showCostLabel = 1;
						}
						$ev['show_cost_label'] = $showCostLabel;

						$event_date_list = [];
						if(array_key_exists($ev['base_event_id'], $event_dates)) { $event_date_list = $event_dates[$ev['base_event_id']]; }
						$ev['event_dates'] = $event_date_list;
						++$event_no;
						$ev['event_no'] = $event_no;
						$ev['day_index'] = $day_count;
						if($ev['tags'] && count($ev['tags'])) {
							foreach($ev['tags'] as $t) {
								if(!in_array($t['id'], $tag_ids)) {
									$tag_ids[] = $t['id'];
								}
							}
						}
					}
				}
			}
		}	
		$showFliters = (count($tag_ids)) ? true : false;

		// foreach($calendar as $year => $yr_cal) {
		// 	foreach($yr_cal as &$cal) {
		// 		ksort($cal['days']); 
		// 	}
		// }
		// fwrite($f, "\ncal->\n". print_r($calendar, true)); exit;

		$pg_links = [];
		$_menu_item = '';
		if(isset($menu_item)) {
			$menuCtrl = new MenusController();
			$pg_links = $menuCtrl->getPageLinksByTitle($menu_item);
			if($pg_links) {
				foreach($pg_links as $l) {
					if(strtolower($l->title_en) == 'calendar') { $l->current_link = 1;
					} else { $l->current_link = 0; }
				}
			}
			$_menu_item = $menu_item;
		}

		// $_cl_months = [];
		// $cal2 = [];
		// foreach($calendar as $year => $yr_cal) {
		// 	foreach($yr_cal as $key => &$cal) { 
		// 		if(array_key_exists('month_no', $cal)) { 
		// 			$cal2[$cal['month_no']] = $cal; 
		// 			if(!in_array($cal['month_no'], $_cl_months)) {
		// 				$_cl_months[] = $cal['month_no']; 
		// 			}
		// 		}
		// 	}
		// }
		// ksort($cal2);
		// fwrite($f, "\nCal:\n". print_r($calendar, true)); exit;

		// $_cal = [];
		// foreach($cal2 as $c2) { 
		// 	$c2['cl_months'] = $_cl_months;
		// 	$_cal[$c2['month']] = $c2; 
		// }
		// $calendar = $_cal;

		// If cluster is used here, return empty if not connected events
		$show_calendar = 1;
		if($page_cluster_id > 0 && count($cl_event_ids) == 0) {
			$show_calendar = 0;
			$calendar = [];
		}
		// fwrite($f, "\ncal->\n". print_r($calendar, true));	
		// exit;
		$_calendar = [];
		foreach($calendar as $year => &$yr_cal) {
			if(count($yr_cal) == 0) {
				unset($calendar[$year]);
			}
		}
		foreach($calendar as $year => &$yr_cal) {
			foreach($yr_cal as $m => &$cal) {
				$_calendar[] = $cal;
			}
		}	
		$calendar = $_calendar;
		/**/
		// fwrite($f, "\nCal:\n". print_r($calendar, true));
		// exit;		
		
		$event_dates = [];
		$rec_eids = [];
		$all_event_date_list = []; // used for date chooser on calendar section 
		$event_list = [];
		foreach($calendar as $m => &$cal) {
			foreach($cal['days'] as $day => &$data) {
				foreach($data['events'] as &$ev) {
					$event_list[] = $ev;
					if(!in_array($ev['event_date'], $event_dates)) {
						$event_dates[] = $ev['event_date'];
						if(date('Y-m-d', strtotime($ev['event_date'])) >= date('Y-m-d')) {
							$all_event_date_list[] = $ev['event_date'];
						}
					}
					// Rep event dates
					if(isset($ev['rep_dates'])) {
						$rec_eids[] = $ev['id'];
						foreach($ev['rep_dates'] as $rd) {
							if(!in_array($rd, $all_event_date_list) && date('Y-m-d', strtotime($rd)) >= date('Y-m-d')) {
								$all_event_date_list[] = $rd;
							}
						}
					}
				}
			}
		}
		Session::put('events', $event_list);
		Session::put('event_dates', $event_dates);

		usort($calendar, function($x, $y) {
			if(strtotime(date($x['year'].'-'.$x['month_no'].'-01')) == strtotime(date($y['year'].'-'.$y['month_no'].'-01'))) return 0;
			return (strtotime(date($x['year'].'-'.$x['month_no'].'-01')) < strtotime(date($y['year'].'-'.$y['month_no'].'-01'))) ? -1 : 1;
		});

		$calMonths = [];
		foreach($calendar as $c) {
			$calMonths[] = [ 'm' => $c['month_no'], 'y' => $c['year'], 'month_year' => $c['month_no'].$c['year'] ];	
		}
		Session::put('cal_months', $calMonths); // used to determine slideNo for events etc..

		if(!$json) {
			$jsn_data = ['all_event_dates' => $all_event_date_list, 'calendar' => $calendar, 'tags' => $tags, 'tag_ids' => $tag_ids, 'showFilters' => true];

			return $jsn_data; 
		}
		// fwrite($f, print_r($all_event_date_list, true));
		fwrite($f, print_r($calendar, true));

		return View::make('pages.calendar', ['calendar' => $calendar, 'pg_links' => $pg_links, 'menu_item' => $_menu_item, 'tags' => $tags, 
				'event_dates' => $event_dates, 'all_event_dates' => $all_event_date_list, 'tag_ids' => $tag_ids, 'show_filters' => $showFliters, 
				'show_calendar' => $show_calendar]);
	}	 

	public function sortCalendar($cal) {
		sort($cal);

		return $cal;
	}

	public function getEventsCalendarJson() {
		$cal = KEventsController::getEventsCalendar(null, false, null);

		return Response::json(array('error' => false, 'cal' => $cal), 200); 
	}

	/* This function handles displaying active event dates in calendar filter based on chosen filter
	*/
	public function getFilteredDates() {
		if(Input::has('filter')) {
			$f = fopen('logs/test.log', 'w+');
			fwrite($f, "getFilteredDates() called..\n\n". print_r(Input::all(), true));//. "\n\nEvents:\n".print_r(Session::get('events'), true));
			$events = Session::get('events');
			$filtered_dates = [];
			foreach($events as &$ev) {
				if(isset($ev['tags'])) {
					fwrite($f, "\nE:- ". $ev['id']. ' ___ dt: '. $ev['event_date'] ."\nTags:- ");
					$ev_tags = [];
					foreach($ev['tags'] as $t) {
						$ev_tags[] = strtolower(str_replace(' ', '-', $t->tag_en));
						fwrite($f, strtolower(str_replace(' ', '-', $t->tag_en)) .' ');
					}
					if(in_array(Input::get('filter'), $ev_tags)) {
						fwrite($f, "\n+++++++++++ Matched for event ".$ev['id']."\n");
						$dt = $ev['event_date'];
						if($ev['as_series'] == 1) {
							$dt = date('Y-m-d');
						}
						if(!in_array($ev['event_date'], $filtered_dates)) {
							$filtered_dates[] = $dt;
						}
	
						if(array_key_exists('event_dates', $ev)) {
							fwrite($f, "\nevent_dates:\n".print_r($ev['event_dates'], true));
							foreach($ev['event_dates'] as $ed) {
								if(!in_array($ed['full_date'], $filtered_dates)) {
									$filtered_dates[] = $ed['full_date'];
								}
							}
						}
					}
				}
			}
			fwrite($f, "\n\nfiltered_dates:\n".print_r($filtered_dates, true));

			return Response::json(array('error' => false, 'filtered_dates' => $filtered_dates, 'event_dates' => Session::get('event_dates'),
				'current_date' => date('Y-m-d'), 200)); 
		}
		return Response::json(array('error' => true, 'event_dates' => []), 400); 
	}

	public function getEventData2($id = null, $index = null) {
			$f = fopen('logs/event_reg.log', 'w+');
			fwrite($f, "\ngetEventData() called\n".$id."\n".$index."\n\nInputs:\n".print_r(Input::all(), true));
		return Response::json(array('error' => false, 'event' => []), 200); 
	}

	// It returns HTML for event form based on selected event
	public function getEventData($id = null, $indx = null) {
		date_default_timezone_set('Europe/Berlin'); 
		// setlocale(LC_TIME, "de_DE");
		$f = fopen('logs/event.log', 'w+');
		fwrite($f, "\ngetEventData() called\n\nInputs:\n".print_r(Input::all(), true));
		if(!isset($id) && Input::has('id')) {
			$id = Input::get('id');
		}
		$event_index = $indx;
		if(!isset($indx) && Input::has('index')) {
			$indx = Input::get('index');
			$event_index = $indx;
		}
		if(isset($indx)) {
			$indx = substr($indx, strlen($indx)-8, strlen($indx));
		}
		// $ar = explode('', $indx);
		$active_event_date = $indx[4].$indx[5].$indx[6].$indx[7].'-'.$indx[2].$indx[3].'-'.$indx[0].$indx[1];
		fwrite($f, "\n\nactive_event_date: ". $active_event_date);
		// $active_event_date = 
		//fwrite($f, "\ngetEventData()\n\n");
		// Date boxes
		$box_html = '';
		$m_en = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		$m_de = ['Jan', 'Feb', 'M&auml;r', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];
		$d_en = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
		$d_de = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];

		$event = [];
		if($id) {
			// $event_index = Input::get('index');
			$event_dates = [];
			$cal_months = [];
			$_event_dates = [];
			$showCostLabel = false;

			$event = KEvent::with(['clusters', 'tags', 'kEventCost', 'entrance', 'event_dates'])->where('id', $id)->first();	
			$event->event_cost = $event->kEventCost;
			$hasCost = false;

			if(isset($event->entrance)) {
				if(intval($event->entrance->included) == 1 || intval($event->entrance->excluded) == 1 || 
					     intval($event->entrance->free) == 1 || intval($event->entrance->entry_fee) == 1) {
					$hasCost = true;
					fwrite($f, "\n\ncheck 1 .. ". $hasCost."\n");
				}
				if(intval($event->entrance->included) == 0 && intval($event->entrance->excluded) == 0 &&
					     intval($event->entrance->free) == 0 && intval($event->entrance->entry_fee) == 0) {
					$hasCost = false;
					fwrite($f, "\n\ncheck 2 .. ". $hasCost."\n");
				}
				$showCostLabel = $hasCost;
				fwrite($f, "\nEntrance:\nentrance->included: ".$event->entrance->included."\nexcluded: ". $event->entrance->excluded.
					"\nFree: ". $event->entrance->free."\nentry_fee: ". $event->entrance->entry_fee);
			}

			$hasZeroPrice = true;
			if(isset($event->event_cost)) {
				$c = $event->event_cost;
				fwrite($f, "\n\nCost:\nregular_adult_price: ".$c->regular_adult_price."\nregular_child_price: ".$c->regular_child_price.
					"\nmember_adult_price: ".$c->member_adult_price."\nmember_child_price: ".$c->member_child_price.
					"\nsiblings_child_price: ".$c->siblings_child_price."\nreduced_price: ".$c->reduced_price );

				if((is_numeric($c->regular_adult_price) && intval($c->regular_adult_price) > -1) || 
				   (is_numeric($c->regular_child_price) && intval($c->regular_child_price) > -1) || 
				   (is_numeric($c->member_adult_price) && intval($c->member_adult_price) > -1) || 
				   (is_numeric($c->member_child_price) && intval($c->member_child_price) > -1) || 
				   (is_numeric($c->sibling_child_price) && intval($c->sibling_child_price) > -1) || 				   
				   (is_numeric($c->reduced_price) && intval($c->reduced_price) > -1) || 
				   (is_numeric($c->sibling_member_price) && intval($c->sibling_member_price) > -1)
				) {
					$hasCost = true;
					fwrite($f, "\n\ncheck 3 .. ". $hasCost."\n");
				}
				$showCostLabel = $hasCost;
				
				if(!$hasCost && (trim($c->regular_adult_price) == '' && trim($c->regular_child_price) == '' && trim($c->sibling_member_price) == '' && 
						trim($c->member_adult_price) == '' && trim($c->member_child_price) == '' &&
						trim($c->siblings_child_price) == '' && trim($c->reduced_price) == 0)) {
					$showCostLabel = false;
					fwrite($f, "\n\ncheck 4 .. ". $hasCost."\n");
				}				

				if((is_numeric($c->regular_adult_price) && intval($c->regular_adult_price) > 0) || 
				   (is_numeric($c->regular_child_price) && intval($c->regular_child_price) > 0) || 
				   (is_numeric($c->member_adult_price) && intval($c->member_adult_price) > 0) || 
				   (is_numeric($c->member_child_price) && intval($c->member_child_price) > 0) || 
				   (is_numeric($c->sibling_child_price) && intval($c->sibling_child_price) > 0) || 				   
				   (is_numeric($c->reduced_price) && intval($c->reduced_price) > 0) || 
				   (is_numeric($c->sibling_member_price) && intval($c->sibling_member_price) > 0)
				) {
					$hasZeroPrice = false;
				}
			}
			$event->hasZeroPrice = $hasZeroPrice;
			// fwrite($f, "\n\nShow Kosten: ". $showCostLabel."\nhasCost: ". $hasCost);
			$event->hasCost = $hasCost;
			$event->tags = $event->tags;
			$event->entrance = $event->entrance;
			// $event->start_date = substr($event->start_date, 0, 4);
			$event->index = $id . $indx; // $event->id . date('dmY', strtotime($event->start_date));
			// $event->clusters = $event->clusters;
			$exb_titles = [];
			$event_ids = [];
			foreach($event->clusters as $cl) {
				$cluster_id = $cl->id;

				$sql = 'select e.*, e.id as base_eid, ec.package from k_events e, clusters ec, cluster_k_event ecke
				        where ecke.cluster_id = ' . $cluster_id . '
				          and ecke.k_event_id = e.id ';
				$sql .= ' group by e.id';								        
				// echo $sql;        
				$ce_data = DB::select($sql);
				if(is_array($ce_data) && count($ce_data)) {
					foreach($ce_data as $cd) {
						if(isset($cl->package) && $cl->package == 1) {
							if(!in_array($cd->base_eid, $event_ids)) {
								$clustered_events[] = $cd;
								$clustered_dates[] = $cd->start_date;
								$event_ids[] = $cd->base_eid;
							}
						}
					}
				}
				$query = 'select title_en, title_de from pages
				          where page_type = "exhibition"
				            and cluster_id = '. $cluster_id;
				$res = DB::select($query);
				if($res && count($res)) {
					if(strlen($res[0]->title_en) > 0) {
						$exb_titles[strtolower(str_replace(' ', '-', $res[0]->title_en))] = $res[0]->title_de;
					}
				}
			}
			$event->exb_titles = $exb_titles;
			$clustered_dates = [];

			if(!array_key_exists($event->id, $clustered_dates)) {
				$clustered_dates[$event->id] = [];
			}
			$is_recurring = false;
			if((isset($event->event_day) && strlen($event->event_day) > 0) || strtolower($event->event_day_repeat) == 'daily') {
				$is_recurring = true;
				$rep_dates = KEventsController::getEventRepeatDates($event->id, $event->start_date, $event->event_day, $event->event_day_repeat, $event->repeat_month, $event->end_date);
				foreach($rep_dates as $rep_date) {
					$fdate = date("D-d-M", strtotime($rep_date));
					$emonth = date("m", strtotime($rep_date));
					if(!in_array($emonth, $cal_months)) {
						// ++$slideNo;
						$cal_months[] = $emonth;
					}
					sort($cal_months);
					$c = 0; //-1;
					foreach($cal_months as $cm) {
						++$c;
						if(intval($cm) == intval($emonth)) {
							$slideNo = $c;
						}
					}
					if(!in_array(date('D-d-M', strtotime($rep_date)), $_event_dates)) {
						$event_dates[$event->id][] = [ 
														'id'         => $event->id,
														'index'      => $event->id . date('dmY', strtotime($rep_date)),
														'title'      => $event->title_de,
														'event_date' => $fdate,
														'full_date'  => $rep_date,
														'event_date_de' => utf8_encode(strftime('%a-%d-%b', strtotime($rep_date))),
														'slideNo'	 => $slideNo+1
												    ];
						$_event_dates[] = date('D-d-M', strtotime($rep_date)); 
					}
				}
			} else {
				if($event->id) {
					$query = 'select event_date from event_dates 
							  where k_event_id = ' . $event->id. ' 
							    and event_date >= "'.date('Y-m-d').'"
							  order by event_date';
					$event_dates_rs = DB::select($query);
					foreach($event_dates_rs as $rep_date_obj) {
						$rep_date = $rep_date_obj->event_date;
						$emonth = date("m", strtotime($rep_date));
						if(!in_array($emonth, $cal_months)) {
							// ++$slideNo;
							$cal_months[] = $emonth;
						}
						sort($cal_months);
						$c = -1;
						foreach($cal_months as $cm) {
							++$c;
							if(intval($cm) == intval($emonth)) {
								$slideNo = $c;
							}
						}
						$fdate = date("D-d-M", strtotime($rep_date));
						if(!in_array(date('D-d-M', strtotime($rep_date)), $_event_dates)) {

							$event_date_de = strftime('%a-%d-%b', strtotime($rep_date));
							$arr = explode('-', $event_date_de);
							$dt_1 = $arr[0];
							for($i=0; $i<count($d_en); $i++) { $dt_1 = str_replace($d_en[$i], $d_de[$i], $dt_1); }
							$dt_3 = $arr[2];
							for($i=0; $i<count($m_en); $i++) { $dt_3 = str_replace($m_en[$i], $m_de[$i], $dt_3); }
							$dt_3 = utf8_encode($dt_3);	
							$event_date_de = $dt_1.'-'.$arr[1].'-'.$dt_3;

							$event_dates[$event->id][] = [ 
															'id'         => $event->id,
															'index'      => $event->id . date('dmY', strtotime($rep_date)),
															'title'      => $event->title_de,
															'event_date' => $fdate,
															'event_date_de' => $event_date_de,
															'full_date'  => $rep_date,
															'slideNo'	 => $slideNo+1
													    ];
							$_event_dates[] = date('D-d-M', strtotime($rep_date)); 
						}
					}
				}
			}

			// if(count($event_dates) == 1) {				
			// 	if($event_dates[$event->id][0]['full_date'] == $event->start_date && $event->start_date == $event->end_date) {
			// 		$event_dates = [];
			// 	}
			// }
			// fwrite($f, "\n\nevent_dates:-\n". print_r($event_dates, true));

			$event->event_dates = $event_dates;
			$rep_dates = [];
			foreach($event_dates as $k => $eds) { $rep_dates = $eds; }
			$event->rep_dates = $rep_dates;

			$package = 0;
			$package_prices = [];
			$cluster_events = [];
			$_cluster_dates = [];
			$price_list_str = '';
			if(count($event->clusters)) {
				foreach($event->clusters as $cl) {
					//fwrite($f, "\npkg ----> ". $cl->package);
					if($cl->package == 1 && $cl->kEventCost) {
						$package = 1;
						$event->pkg_regular_adult_price = $cl->kEventCost->regular_adult_price;
						$event->kEventCost->pkg_regular_adult_price = $cl->kEventCost->regular_adult_price;

						$event->pkg_regular_child_price = $cl->kEventCost->regular_child_price;
						$event->kEventCost->pkg_regular_child_price = $cl->kEventCost->regular_child_price;

						$event->pkg_member_adult_price = $cl->kEventCost->member_adult_price;
						$event->kEventCost->pkg_member_adult_price = $cl->kEventCost->member_adult_price;

						$event->pkg_member_child_price = $cl->kEventCost->member_child_price;
						$event->kEventCost->pkg_member_child_price = $cl->kEventCost->member_child_price;

						$event->pkg_sibling_child_price = $cl->kEventCost->sibling_child_price;
						$event->kEventCost->pkg_sibling_child_price = $cl->kEventCost->sibling_child_price;

						$event->pkg_sibling_member_price = $cl->kEventCost->sibling_member_price;
						$event->kEventCost->pkg_sibling_member_price = $cl->kEventCost->sibling_member_price;

						$event->pkg_reduced_price = $cl->kEventCost->reduced_price;
						$event->kEventCost->pkg_reduced_price = $cl->kEventCost->reduced_price;
					}
					//fwrite($f, "\nEvent: ".$event->id ." __ Cluster ----> ". $cl->id);
					$clusterObj = Cluster::with(['k_events'])->find($cl->id);
					$series_event_ids = [];
					$eids = [];
					foreach($clusterObj->k_events as $ke) {
						if(strtotime($ke->start_date) > time()) {
							$ce_date = $ke->start_date;
							$fdate = date("D-d-M", strtotime($ce_date));
							sort($cal_months);
							$slideNo = 1;
							$c = 0;
							foreach($cal_months as $cm) {
								++$c;
								if(intval($cm) == intval($emonth)) {
									$slideNo = $c;
								}
							}

							$event_date_de = utf8_encode(strftime('%a-%d-%b', strtotime($ce_date)));
							$arr = explode('-', $event_date_de);
							$dt_1 = $arr[0];
							for($i=0; $i<count($d_en); $i++) { $dt_1 = str_replace($d_en[$i], $d_de[$i], $dt_1); }
							$dt_3 = $arr[2];
							for($i=0; $i<count($m_en); $i++) { $dt_3 = str_replace($m_en[$i], $m_de[$i], $dt_3); }
							$dt_3 = utf8_encode($dt_3);	
							$event_date_de = $dt_1.'-'.$arr[1].'-'.$dt_3;

							if(!in_array(date('D-d-M', strtotime($ce_date)), $_cluster_dates) && in_array($ke->id, $event_ids)) {
								if(!in_array($ke->id, $series_event_ids)) {
									$cluster_events[] = [   'id'         => $ke->id,
															'index'      => $ke->id . date('dmY', strtotime($ce_date)),
															'title'      => $ke->title_de,
															'event_date' => $fdate,
															'full_date'  => $ce_date,
															'event_date_de' => $event_date_de,
															'slideNo'	 => $slideNo
													    ];
									$_cluster_dates[] = date('D-d-M', strtotime($ce_date)); 
								}
								if($ke->as_series == 1) {
									$series_event_ids[] = $ke->id;
								}
							}
						}
					}
				}
			}
			$event->cluster_events = $cluster_events;
			$event->package = $package;	
			$event->show_cost_label = $showCostLabel;

			// $event->cluster = $cluster;
			$query = 'select c.* from k_event_costs c where c.k_event_id = '. $event->id;
			$cost_res = DB::select($query);
			$priceFieldCount = 0;
			if(is_array($cost_res) && count($cost_res)) {
				$cost = $cost_res[0];
				foreach($cost as $k => $v) {
					if(strstr($k, '_price')) {
						$event->{$k} = $v;
						if(is_numeric($v) && floatval($v) > 0) {
							++$priceFieldCount;
						}
					}
				}
			}
			$event->priceFieldCount = $priceFieldCount;
		}

		$event->detail_de = html_entity_decode($event->detail_de);

		$months = [ '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ];
		$html = '';
		$cur_year = date('Y');
		$cur_month = date('m');
		$events = [];
		for($year=$cur_year; $year<($cur_year+10); $year++) {
			foreach($months as $month) {
				if($month >= $cur_month || ($year > date('Y'))) {
					$query = 'select e.start_date, e.id as base_event_id							  	  
						  	  from k_events e
						  	  where e.start_date like "'. $year .'-'.$month.'-%" 
						  	    and e.start_date >= "'.date('Y-m-d').'"
							  order by e.start_date';
					$res = DB::select($query);
					if($res) {
						foreach($res as $r) { $events[] = $r; }
					}
				}
			}
		}	
		//fwrite($f, "\n".$query. "\nEvents:_\n". print_r($events, true));
		$cl_months = [];
		$_events = [];
		foreach($events as $e) {
			$m = date('m', strtotime($e->start_date));
			if(!in_array($m, $cl_months)) {
				$cl_months[] = $m;
			}
		}

		$show_as_series_boxes = false;
		if($event['as_series'] == 1 && $event['show_after_startdate'] == 1 && (strtotime($event['start_date']) > strtotime(date('Y-m-d')))) {
			$show_as_series_boxes = true;
		}
        $event->show_as_series_boxes = $show_as_series_boxes;

		// Event boxes
        if($event['as_series'] == 0) {
        	if(isset($event['clusters']) && count($event['clusters']) && isset($event['cluster_events']) && count($event['cluster_events']) > 1) {
	        	// $box_html .= '<h4>Weitere Termine</h4><div>';
	        	$added_indexes = [];
	        	$event_boxes = [];
	        	// Event rep dates
	        	/*
	        	if($event['rep_dates'] && count($event['rep_dates']) > 1) {
			        foreach($event['rep_dates'] as $ed) {
			           if($ed['index'] != $event_index) {
			           	  if(!in_array($ed['index'], $added_indexes)) {
			           	  	  $event_boxes[] = $ed;
							  $added_indexes[] = $ed['index'];                  
			           	  }
			           }
			        }
	        	}/**/
	        	// Clustered events
		        foreach($event['cluster_events'] as $ed) {
		           if($ed['index'] != $event_index) {
		           	  if(!in_array($ed['index'], $added_indexes)) {
		           	  	  $event_boxes[] = $ed;
						  $added_indexes[] = $ed['index'];                  
		           	  }
		           }
		        }

		        if(count($event_boxes)) {
					usort($event_boxes, function($a, $b) {
						$dt1 = intval(str_replace('-', '', $a['full_date']));
						$dt2 = intval(str_replace('-', '', $b['full_date']));
						if($dt1 == $dt2) return 0;
						return ($dt1 < $dt2) ? -1 : 1;
					});        	
			        foreach($event_boxes as $ed) {
			        	if($ed['full_date'] != $active_event_date) {
							$arr = explode('-', $ed['event_date_de']);
							$slideNo = 1;
							$n = 0;
							foreach($cl_months as $m) {
								++$n;
								if($m == date('m', strtotime($ed['full_date']))) {
									$slideNo = $n;
								}
							}
							$ed['slideNo'] = $slideNo;

							$dt_1 = $arr[0];
							for($i=0; $i<count($d_en); $i++) { $dt_1 = str_replace($d_en[$i], $d_de[$i], $dt_1); }
							$dt_3 = $arr[2];
							for($i=0; $i<count($m_en); $i++) { $dt_3 = str_replace($m_en[$i], $m_de[$i], $dt_3); }
							$dt_3 = utf8_encode($dt_3);	

							$box_html .= '<a href="javascript:showEvent('.$ed['index'].', '.$ed['slideNo'].')">
							            <span class="calendar">'.
							                $dt_1.'<br />'.
							                $arr[1].'<br />'.
							                $dt_3.'</span></a>';
			        	}
			        }
		        }
				$box_html .= '</div>';
        	}
        }
        else if($event['as_series'] == 1 && isset($event['rep_dates'])) { //} && count($event['rep_dates']) > 1) {
			$box_html .= '<a style="cursor:text;">';
			// $arr = explode('-', $event['start_date']);
			$start_date_de = utf8_encode(strftime('%a-%d-%b', strtotime($event['start_date'])));
			$arr = explode('-', $start_date_de);
			$dt_1 = $arr[0];
			for($i=0; $i<count($d_en); $i++) { $dt_1 = str_replace($d_en[$i], $d_de[$i], $dt_1); }
			$dt_3 = $arr[2];
			for($i=0; $i<count($m_en); $i++) { $dt_3 = str_replace($m_en[$i], $m_de[$i], $dt_3); }
			$dt_3 = utf8_encode($dt_3);	

			$box_html .= '<span class="calendar">'.$dt_1.'<br />'.$arr[1].'<br />'.$dt_3.'</span>';
			// $arr = explode('-', $event['end_date']);
			$end_date_de = utf8_encode(strftime('%a-%d-%b', strtotime($event['end_date'])));
			$arr = explode('-', $end_date_de);
			$dt_1 = $arr[0];
			for($i=0; $i<count($d_en); $i++) { $dt_1 = str_replace($d_en[$i], $d_de[$i], $dt_1); }
			$dt_3 = $arr[2];
			for($i=0; $i<count($m_en); $i++) { $dt_3 = str_replace($m_en[$i], $m_de[$i], $dt_3); }
			$dt_3 = utf8_encode($dt_3);	
			$box_html .= '<span class="calendar">'.$dt_1.'<br />'.$arr[1].'<br />'.$dt_3.'</span>';
			$box_html .= '</a></div>';
        }

        $rec_evt_boxes = [];
        if($is_recurring && strlen($box_html) == 0) {
        	fwrite($f, "\nREP DATES: \n".print_r($event->rep_dates, true));
        	foreach($event->rep_dates as $dt) {
				$arr = explode('-', $dt['event_date']);
				$dt_1 = $arr[0];
				for($i=0; $i<count($d_en); $i++) { $dt_1 = str_replace($d_en[$i], $d_de[$i], $dt_1); }
				$dt_3 = $arr[2];
				for($i=0; $i<count($m_en); $i++) { $dt_3 = str_replace($m_en[$i], $m_de[$i], $dt_3); }
				$dt_3 = utf8_encode($dt_3);

				$fd_arr = explode('-', $dt['full_date']);
				$slideNo = KEventsController::getSlideNo($fd_arr[1], $fd_arr[0]);
				fwrite($f, "\n\nfd_arr: ". $dt['full_date'] .' => getSlideNo() => '. $slideNo);

				$b_html = '<a href="javascript:showEvent('.$dt['index'].', '.$slideNo.')">
					            <span class="calendar">'.
					                $dt_1.'<br />'.
					                $arr[1].'<br />'.
					                $dt_3.'</span></a>';
				$rec_evt_boxes[] = $b_html;
				$box_html .= $b_html;
        	}
        }

        $event->event_box_html = $box_html;
        $event->rec_evt_boxes = $rec_evt_boxes;

        // Repeated event dates
        $rep_dates_box_html = '';
        $event->has_rep_dates = (isset($event->rep_dates) && count($event->rep_dates) > 0) ? true : false;

		if(count($event->has_rep_dates) == 1) {				
			if($event_dates[$event->id][0]['full_date'] == $event->rep_dates[0]['full_date'] && $event->rep_dates[0]['full_date'] == $event->end_date) {
				$event->has_rep_dates = false;
			}
		}
		$event->show_event_box_html = $event->has_rep_dates;
		$event->show_rep_dates_box_html = $event->has_rep_dates;

        fwrite($f, "\nEvent date: ". print_r($event, true));

        if($event->has_rep_dates) {
	       	foreach($event->rep_dates as $ed) {		
	       		if($ed['full_date'] != $active_event_date) {
		       		fwrite($f, "\ned-->>\n". print_r($ed, true));
		       		$date = $ed['event_date'];
		       		$dar = explode('-', $date);
		       		$em = strtolower($dar[2]);
		       		$emNo = 0;
		       		if($em == 'jan') { $emNo = 1; }
		       		else if($em == 'feb') { $emNo = 2; }
		       		else if($em == 'mar') { $emNo = 3; }
		       		else if($em == 'apr') { $emNo = 4; }
		       		else if($em == 'may') { $emNo = 5; }
		       		else if($em == 'jun') { $emNo = 6; }
		       		else if($em == 'jul') { $emNo = 7; }
		       		else if($em == 'aug') { $emNo = 8; }
		       		else if($em == 'sep') { $emNo = 9; }
		       		else if($em == 'oct') { $emNo = 10; }
		       		else if($em == 'nov') { $emNo = 11; }
		       		else if($em == 'dec') { $emNo = 12; }
		       		fwrite($f, "\ned -> ".$date . ' ----- '.$em . ' -- > '. $emNo);
		       		$date_de = $ed['event_date_de'];
		       		// $date_de = utf8_encode(strftime('%a-%d-%b', strtotime($date)));
					$dt_arr = explode('-', $date_de);

					$slideNo = 1;
					$n = 0;
					foreach($cl_months as $m) {
						++$n;
						if($emNo == $m) {
							$slideNo = $n;
							break;
						}
					}
					$ed['slideNo'] = $slideNo;

					$dt_1 = $dt_arr[0];
					$dt_2 = $dt_arr[1];
					$dt_3 = utf8_encode($dt_arr[2]);	

					$rep_dates_box_html .= '<a href="javascript:showEvent('.$ed['index'].', '.$ed['slideNo'].')">
					            <span class="calendar">'.
					                $dt_1.'<br />'.
					                $dt_2.'<br />'.
					                $dt_3.'</span></a>';       		
	       		}
	       	}
	    }   	
       	$event->rep_dates_box_html = $rep_dates_box_html;
        // fwrite($f, "\nEvent:\n\n". print_r($event, true));

		return Response::json(array('error' => false, 'event' => $event), 200); 
	}


    // Get slideNo based on event date
	public static function getSlideNo($m, $y) {
		$mos = Session::get('cal_months');
		for($i=0; $i<count($mos); $i++) {
			if($mos[$i]['month_year'] == $m.$y) {
				return ($i+1);
			}
		}

		return 0;
	}

	function getEventFormHTML($evt, $event_index) {
		$DOMAIN = 'http://cms.kunsthalle-bremen.net';
		// $calendar = KEventsController::getEventsCalendar(null, false, 0);
		$f = fopen('logs/test_1.log', 'w+');
		// fwrite($f, print_r($evt, true));
		$months = [ '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ];
		$html = '';
		$cur_year = date('Y');
		$cur_month = date('m');
		$events = [];
		for($year=$cur_year; $year<($cur_year+10); $year++) {
			foreach($months as $month) {
				if($month >= $cur_month || ($year > date('Y'))) {
					$query = 'select e.start_date, e.id as base_event_id							  	  
						  	  from k_events e
						  	  where e.start_date like "'. $year .'-'.$month.'-%" 
						  	    and e.start_date >= "'.date('Y-m-d').'"
							  order by e.start_date';
					$res = DB::select($query);
					if($res) {
						foreach($res as $r) { $events[] = $r; }
					}
				}
			}
		}	
		//fwrite($f, "\n".$query. "\nEvents:_\n". print_r($events, true));
		$cl_months = [];
		$_events = [];
		foreach($events as $e) {
			$m = date('m', strtotime($e->start_date));
			if(!in_array($m, $cl_months)) {
				$cl_months[] = $m;
			}
		}
		//fwrite($f, "\n\ncl_months: ". implode(', ', $cl_months));
		
		// Using DE date
		date_default_timezone_set('Europe/Berlin'); 
		setlocale(LC_TIME, "de_DE");

   //      $html = '<h3 class="detail-header">'.$evt['guide_name'].'</h3>'. html_entity_decode($evt['detail_de']);
   //      if(isset($evt['event_image']) && strlen($evt['event_image']) > 0) {
	  //       $html .= '<figure>
			//             <img class="img-responsive" src="'.$DOMAIN.'/'.$evt['event_image'].'" alt="#" title="#" />
			//             <figcaption>'.$evt['caption_de'].'</figcaption>
			//           </figure>';
   //      }

   //      if(isset($evt['exb_titles']) && count($evt['exb_titles'])) {
   //          $html .= '<h4>Im Rahmen der Ausstellung</h4>
			//             <ul class="list-unstyled links-underlined">';
			//                foreach($evt['exb_titles'] as $link => $title) {
			// 	                $html .= '<li>
			// 			                    <span class="icon icon-arrow icon-s icon-inline"></span>
			// 			                    <a href="/view/exhibitions/exb-page/'.$link.'"
			// 			                    >'.$title.'</a>
			// 			                  </li>';
			//                }
			// $html .= '</ul>';
   //      }
   //      fwrite($f, "\nevent boxes..\n\n");
   //      if($evt['as_series'] == 0) {
   //      	if(isset($evt['clusters']) && count($evt['clusters']) && isset($evt['cluster_events']) && count($evt['cluster_events']) > 1) {
	  //       	$html .= '<h4>Weitere Termine</h4><div>';
	  //       	$added_indexes = [];
	  //       	$event_boxes = [];
	  //       	// Event rep dates
	  //       	/*
	  //       	if($evt['rep_dates'] && count($evt['rep_dates']) > 1) {
			//         foreach($evt['rep_dates'] as $ed) {
			//            if($ed['index'] != $event_index) {
			//            	  if(!in_array($ed['index'], $added_indexes)) {
			//            	  	  $event_boxes[] = $ed;
			// 				  $added_indexes[] = $ed['index'];                  
			//            	  }
			//            }
			//         }
	  //       	}/**/
	  //       	// Clustered events
		 //        foreach($evt['cluster_events'] as $ed) {
		 //           if($ed['index'] != $event_index) {
		 //           	  if(!in_array($ed['index'], $added_indexes)) {
		 //           	  	  $event_boxes[] = $ed;
			// 			  $added_indexes[] = $ed['index'];                  
		 //           	  }
		 //           }
		 //        }

		 //        if(count($event_boxes)) {
			// 		usort($event_boxes, function($a, $b) {
			// 			$dt1 = intval(str_replace('-', '', $a['full_date']));
			// 			$dt2 = intval(str_replace('-', '', $b['full_date']));
			// 			if($dt1 == $dt2) return 0;
			// 			return ($dt1 < $dt2) ? -1 : 1;
			// 		});        	
			//         foreach($event_boxes as $ed) {
			// 			$arr = explode('-', $ed['event_date_de']);
			// 			$slideNo = 1;
			// 			$n = 0;
			// 			foreach($cl_months as $m) {
			// 				++$n;
			// 				fwrite($f, "\nMonth check: ". $m .' = '. date('m', strtotime($ed['full_date'])) . ' => ');
			// 				if($m == date('m', strtotime($ed['full_date']))) {
			// 					fwrite($f, " <--> ". (date('m', strtotime($ed['full_date']))));
			// 					$slideNo = $n;
			// 				}
			// 			}
			// 			$ed['slideNo'] = $slideNo;

			// 			$html .= '<a href="javascript:showEvent('.$ed['index'].', '.$ed['slideNo'].')">
			// 			            <span class="calendar">'.
			// 			                $arr[0].'<br />'.
			// 			                $arr[1].'<br />'.
			// 			                $arr[2].'</span></a>';
			//         }
		 //        }
			// 	$html .= '</div>';
   //      	}
   //      }
   //      else if($evt['as_series'] == 1 && isset($evt['rep_dates']) && count($evt['rep_dates']) > 1) {
			// $html .= '<a style="cursor:text;">';
			// // $arr = explode('-', $evt['start_date']);
			// $start_date_de = utf8_encode(strftime('%a-%d-%b', strtotime($evt['start_date'])));
			// $arr = explode('-', $start_date_de);
			// $html .= '<span class="calendar">'.$arr[0].'<br />'.$arr[1].'<br />'.$arr[2].'</span>';
			// // $arr = explode('-', $evt['end_date']);
			// $end_date_de = utf8_encode(strftime('%a-%d-%b', strtotime($evt['end_date'])));
			// $arr = explode('-', $end_date_de);
			// $html .= '<span class="calendar">'.$arr[0].'<br />'.$arr[1].'<br />'.$arr[2].'</span>';
			// $html .= '</a></div>';
   //      }

   //      $html .= '<dl class="dl-horizontal participants_'.$evt['index'].'">';
   //      fwrite($f, "\n\nCost check: ".$evt['id'] ."\n". print_r($evt, true));
   //      if($evt['show_cost_label'] == 1) {
   //          $prefix = '';
   //          $ep = $evt['event_cost'];
   //          if($evt['package'] == 1) { $prefix = 'pkg_'; }
   //          $html .= '<dt>Kosten</dt><dd>';

   //          if(isset($ep['regular_adult_price']) && strlen($ep['regular_adult_price']) && (is_numeric($ep['regular_adult_price']) 
   //                  && $ep['regular_adult_price']) >= 0) {
   //              $html .= $ep['regular_adult_price']. ' € Erwachsene';
   //              if($evt['entrance']['excluded'] == 1) { $html .= ' (zzgl. Eintritt)'; }
   //              $html .= '<br>';
   //          }                                                    

   //          if(isset($ep['regular_child_price']) && strlen($ep['regular_child_price']) && (is_numeric($ep['regular_child_price']) && 
   //          	$ep['regular_child_price']) >= 0) {
   //              $html .= str_replace('.00', '', $ep['regular_child_price']). ' € Kinder <br>';
   //          }                                                    

   //          if(isset($ep['member_adult_price']) && strlen($ep['member_adult_price']) && (is_numeric($ep['member_adult_price']) && 
   //          	$ep['member_adult_price']) >= 0) {
   //              $html .= str_replace('.00', '', $ep['member_adult_price']). ' € Mitglieder<br>';
   //          }                                                    

   //          if(isset($ep['member_child_price']) && strlen($ep['member_child_price']) && (is_numeric($ep['member_child_price']) && 
   //          	$ep['member_child_price']) >= 0) {
   //              $html .= str_replace('.00', '', $ep['member_child_price']). ' € Kinder / Mitglied<br>';
   //          }                                                    

   //          if(isset($ep['sibling_child_price']) && strlen($ep['sibling_child_price']) && (is_numeric($ep['sibling_child_price']) && 
   //          	$ep['sibling_child_price']) >= 0) {
   //              $html .= str_replace('.00', '', $ep['sibling_child_price']). ' € Geschwisterkinder<br>';
   //          }                                                    

   //          if(isset($ep['sibling_member_price']) && strlen($ep['sibling_member_price']) && (is_numeric($ep['sibling_member_price']) 
   //          && $ep['sibling_member_price']) >= 0) {
   //              $html .= str_replace('.00', '', $ep['sibling_member_price']). ' € Geschwisterkinder / Mitglied<br>';
   //          }                                                    

   //          if(isset($ep['reduced_price']) && strlen($ep['reduced_price']) && (is_numeric($ep['reduced_price']) && $ep['reduced_price']) >= 0) {
   //              $html .= str_replace('.00', '', $ep['reduced_price']). ' € ermäßigt ';
   //              if($evt['entrance']['excluded'] == 1) { $html .= '(zzgl. Eintritt)'; } 
   //              $html .= '<br>';
   //          }
   //      }
          
   //      if($evt['entrance']['free'] == 1) { $html .= '<p>Eintritt frei </p>'; }
   //      if($evt['entrance']['included'] == 1) { $html .= '<p>inklusive Eintritt in die Kunsthalle Bremen </p>'; }
   //          // <!-- if($evt['entrance']['excluded'] == 1) { $html .= '<p>zzgl. Eintritt </p>'; } -->
   //      if($evt['entrance']['entry_fee'] == 1) { $html .= '<p>Eintritt in die Kunsthalle Bremen </p>'; }

   //      $html .= '</dd>';
   //      if(strlen($evt['registration_detail']) > 0) {
   //          $html .= '<dt>Anmeldung</dt><dd><p>'.$evt['registration_detail'].'</p></dd>';
   //      }                                                

   //      if(strlen($evt['remarks']) > 0) {
   //          $html .= '<dt>Hinweis</dt><dd><p>'.$evt['remarks'].'</p></dd>';
   //      }
                        
   //      if(strlen($evt['place']) > 0) {
   //          $html .= '<dt>Ort</dt>
			//             <dd>
			//             <p>'.$evt['place'].'</p>';
			//             if(isset($evt['google_map_url']) && strlen($evt['google_map_url']) > 0) { 
			//                 $html .= '<ul class="list-unstyled links-underlined">
			// 		                    <li><span class="icon icon-arrow icon-s icon-inline"></span><a href="'.$evt['google_map_url'].'">Google Maps</a></li>
			// 		                  </ul>';
			//             }    
   //      	$html .= '</dd>';
   //      }
        $reg_ch_inputs = false; $mem_ch_inputs = false;
   //      $html .= '</dl>';

        if($evt['registration'] == 1) {
	        // <!-- Registration start-->
	        // $html .= '<div class="registration-opener">
			      //       <a href="#" class="open-registration text-red">Jetzt anmelden</a>
			      //       <div class="opener">
			      //           <a href="#" class="opener-close-link close-registration">
			      //               <span class="icon icon-up icon-grey icon-up-'.$evt['index'].'"></span>
			      //           </a>
			      //           <a href="#" class="opener-open-link open-registration">
			      //               <span class="icon icon-down icon-red"></span>
			      //           </a>
			      //       </div>
			      //   </div>';
	        
	        // $html .= '<div class="registration-wrapper panel-collapse collapse">
			      //       <form method="POST" id="reg_form_'.$evt['index'].'" action="/register-for-event" >';
			            if($evt['hasCost']) {

			                $html .= '<fieldset class="registration-number-of-persons">
			                    <legend><h4>Anmeldung</h4></legend>
			                    <p>Hiermit melde ich folgende Anzahl Personen verbindlich zu oben stehender Veranstaltung an:</p>';		                    
			                    if($evt['package'] == 1) {
			                    	$html .= '<div class="form-group">
						                        <div class="checkbox">
						                            <label>
						                                <input type="checkbox" id="'.$evt['index'].'_package_check" name="pay_as_package" 
						                                onclick="applyPackagePrice('.$evt['index'].', '.
						                                (($evt['regular_adult_price'] > 0) ? $evt['regular_adult_price'] : 0) .','.
						                                (($evt['regular_child_price'] > 0) ? $evt['regular_child_price'] : 0) .','.
						                                (($evt['member_adult_price'] > 0) ? $evt['member_adult_price'] : 0) .','.
						                                (($evt['member_child_price'] > 0) ? $evt['member_child_price'] : 0) .','.
						                                (($evt['sibling_child_price'] > 0) ? $evt['sibling_child_price'] : 0) .','.
						                                (($evt['sibling_member_price'] > 0) ? $evt['sibling_member_price'] : 0) .','.
						                                (($evt['reduced_price'] > 0) ? $evt['reduced_price'] : 0) .','.
						                                (($evt['pkg_regular_adult_price'] > 0) ? $evt['pkg_regular_adult_price'] : 0) .','.
						                                (($evt['pkg_regular_child_price'] > 0) ? $evt['pkg_regular_child_price'] : 0) .','.
						                                (($evt['pkg_member_adult_price'] > 0) ? $evt['pkg_member_adult_price'] : 0) .','.
						                                (($evt['pkg_member_child_price'] > 0) ? $evt['pkg_member_child_price'] : 0) .','.
						                                (($evt['pkg_sibling_child_price'] > 0) ? $evt['pkg_sibling_child_price'] : 0) .','.
						                                (($evt['pkg_sibling_member_price'] > 0) ? $evt['pkg_sibling_member_price'] : 0) .','.
						                                (($evt['pkg_reduced_price'] > 0) ? $evt['pkg_reduced_price'] : 0) .'
						                                )" /> Alle Veranstaltungen als Paket buchen.
						                            </label>
						                        </div>
						                    </div>';
			                    }
			                    if(is_numeric($evt['event_cost']['regular_adult_price']) && floatval($evt['event_cost']['regular_adult_price']) >= 0) {  
			                        $html .= '<div class="row registration-count-item reg-inp">
					                            <div class="col-xs-3 col-sm-2 col1">
					                                <a href="#" class="registration-count-increment">
					                                    <span class="icon icon-button-plus icon-red"></span>
					                                </a>
					                                    <a href="#" class="registration-count-decrement">
					                                    <span class="icon icon-button-minus icon-red"></span>
					                                </a>
					                            </div>
					                            <div class="col-xs-2 col-sm-2 col2">
					                                <div class="form-group inline">
					                                    <input type="text" class="form-control" name="regular_adult_price" 
					                                    id="'.$evt['index'].'_regular_adult_price" placeholder="0" 
					                                    data-price="'.$evt['event_cost']['regular_adult_price'].'">
					                                </div>
					                            </div>
					                            <div class="col-xs-4 col-sm-6 col3">
					                                <label for="regular_adult_price">Erwachsene(r) </label>
					                            </div>
					                            <div class="col-xs-3 col-sm-2 text-right col4">
					                                <span class="price">0,00 €</span>
					                            </div>
					                        </div>';
			                    }
			                    if(is_numeric($evt['event_cost']['regular_child_price']) && floatval($evt['event_cost']['regular_child_price']) >= 0) {  
			                    	$reg_ch_inputs = true;
			                        $html .= '<div class="row registration-count-item children reg-inp">
					                            <div class="col-xs-3 col-sm-2 col1">
					                                <a href="#" class="registration-count-increment">
					                                    <span class="icon icon-button-plus icon-red"></span>
					                                </a>
					                                    <a href="#" class="registration-count-decrement">
					                                    <span class="icon icon-button-minus icon-red"></span>
					                                </a>
					                            </div>
					                            <div class="col-xs-2 col-sm-2 col2">
					                                <div class="form-group inline">
					                                    <input type="text" class="form-control" name="regular_child_price" 
					                                    id="'.$evt['index'].'_regular_child_price" placeholder="0" 
					                                    data-price="'.(isset($evt['event_cost']) ? $evt['event_cost']->regular_child_price : '').'">
					                                </div>
					                            </div>
					                            <div class="col-xs-4 col-sm-6 col3">
					                                <label for="regular_child_price">Kinder</label>
					                            </div>
					                            <div class="col-xs-3 col-sm-2 text-right col4">
					                                <span class="price">0,00 €</span>
					                            </div>
					                        </div>';
			                    }
			                    if(is_numeric($evt['event_cost']['member_adult_price']) && floatval($evt['event_cost']['member_adult_price']) >= 0) {    
			                        $html .= '<div class="row registration-count-item reg-inp">
					                            <div class="col-xs-3 col-sm-2 col1">
					                                <a href="#" class="registration-count-increment">
					                                    <span class="icon icon-button-plus icon-red"></span>
					                                </a>
					                                    <a href="#" class="registration-count-decrement">
					                                    <span class="icon icon-button-minus icon-red"></span>
					                                </a>
					                            </div>
					                            <div class="col-xs-2 col-sm-2 col2">
					                                <div class="form-group inline">
					                                    <input type="text" class="form-control" name="member_adult_price" 
					                                    id="'.$evt['index'].'_member_adult_price" placeholder="0" 
					                                    data-price="'.(isset($evt['event_cost']) ? $evt['event_cost']->member_adult_price : '').'">
					                                </div>
					                            </div>
					                            <div class="col-xs-4 col-sm-6 col3">
					                                <label for="member_adult_price">Mitglied(er)</label>
					                            </div>
					                            <div class="col-xs-3 col-sm-2 text-right col4">
					                                <span class="price">0,00 €</span>
					                            </div>
					                        </div>';
			                    }
			                    if(is_numeric($evt['event_cost']['member_child_price']) && floatval($evt['event_cost']['member_child_price']) >= 0) {  
			                    	$mem_ch_inputs = true;
			                        $html .= '<div class="row registration-count-item children reg-inp">
					                            <div class="col-xs-3 col-sm-2 col1">
					                                <a href="#" class="registration-count-increment">
					                                    <span class="icon icon-button-plus icon-red"></span>
					                                </a>
					                                    <a href="#" class="registration-count-decrement">
					                                    <span class="icon icon-button-minus icon-red"></span>
					                                </a>
					                            </div>
					                            <div class="col-xs-2 col-sm-2 col2">
					                                <div class="form-group inline">
					                                    <input type="text" class="form-control" name="member_child_price" 
					                                    id="'.$evt['index'].'_member_child_price" placeholder="0" 
					                                    data-price="'.(isset($evt['event_cost']) ? $evt['event_cost']->member_child_price : '').'">
					                                </div>
					                            </div>
					                            <div class="col-xs-4 col-sm-6 col3">
					                                <label for="member_child_price">Kind(er)/ Mitglied</label>
					                            </div>
					                            <div class="col-xs-3 col-sm-2 text-right col4">
					                                <span class="price">0,00 €</span>
					                            </div>
					                        </div>';
			                    }
			                    if(is_numeric($evt['event_cost']['sibling_child_price']) && floatval($evt['event_cost']['sibling_child_price']) >= 0) {  
			                        $html .= '<div class="row registration-count-item children reg-inp">
					                            <div class="col-xs-3 col-sm-2 col1">
					                                <a href="#" class="registration-count-increment">
					                                    <span class="icon icon-button-plus icon-red"></span>
					                                </a>
					                                    <a href="#" class="registration-count-decrement">
					                                    <span class="icon icon-button-minus icon-red"></span>
					                                </a>
					                            </div>
					                            <div class="col-xs-2 col-sm-2 col2">
					                                <div class="form-group inline">
					                                    <input type="text" class="form-control" name="sibling_child_price" 
					                                    id="'.$evt['index'].'_sibling_child_price" placeholder="0" 
					                                    data-price="'.(isset($evt['event_cost']) ? $evt['event_cost']->sibling_child_price : '').'">
					                                </div>
					                            </div>
					                            <div class="col-xs-4 col-sm-6 col3">
					                                <label for="regular_adult_rp">Geschwisterkinder</label>
					                            </div>
					                            <div class="col-xs-3 col-sm-2 text-right col4">
					                                <span class="price">0,00 €</span>
					                            </div>
					                        </div>';
			                    }
			                    if(is_numeric($evt['event_cost']['sibling_member_price']) && floatval($evt['event_cost']['sibling_member_price']) >= 0) { 
			                        $html .= '<div class="row registration-count-item reg-inp">
					                            <div class="col-xs-3 col-sm-2 col1">
					                                <a href="#" class="registration-count-increment">
					                                    <span class="icon icon-button-plus icon-red"></span>
					                                </a>
					                                    <a href="#" class="registration-count-decrement">
					                                    <span class="icon icon-button-minus icon-red"></span>
					                                </a>
					                            </div>
					                            <div class="col-xs-2 col-sm-2 col2">
					                                <div class="form-group inline">
					                                   <input type="text" class="form-control" name="sibling_member_price" 
					                                   id="'.$evt['index'].'_sibling_member_price" placeholder="0"
					                                    data-price="'.(isset($evt['event_cost']) ? $evt['event_cost']->sibling_member_price : '').'">
					                                </div>
					                            </div>
					                            <div class="col-xs-4 col-sm-6 col3">
					                                <label for="sibling_member_price">Geschwisterkind(er) / Mitglied</label>
					                            </div>
					                            <div class="col-xs-3 col-sm-2 text-right col4">
					                                <span class="price">0,00 €</span>
					                            </div>
					                        </div>';
			                    }
			                    if(is_numeric($evt['event_cost']['reduced_price']) && floatval($evt['event_cost']['reduced_price']) >= 0) { 
			                        $html .= '<div class="row registration-count-item reg-inp">
					                            <div class="col-xs-3 col-sm-2 col1">
					                                <a href="#" class="registration-count-increment">
					                                    <span class="icon icon-button-plus icon-red"></span>
					                                </a>
					                                    <a href="#" class="registration-count-decrement">
					                                    <span class="icon icon-button-minus icon-red"></span>
					                                </a>
					                            </div>
					                            <div class="col-xs-2 col-sm-2 col2">
					                                <div class="form-group inline">
					                                    <input type="text" class="form-control" name="reduced_price" 
					                                    id="'.$evt['index'].'_reduced_price" placeholder="0" 
					                                    data-price="'.(isset($evt['event_cost']) ? $evt['event_cost']->reduced_price : '').'">
					                                </div>
					                            </div>
					                            <div class="col-xs-4 col-sm-6 col3">
					                                <label for="reduced_price">ermäßigt</label>
					                            </div>
					                            <div class="col-xs-3 col-sm-2 text-right col4">
					                                <span class="price">0,00 €</span>
					                            </div>
					                        </div>';
			                    }

			                    $html .= '<div class="row registration-count-total reg-inp">
					                        <div class="col-xs-9 col-md-10 text-right">
					                            Summe:
					                        </div>
					                        <div class="col-xs-3 col-md-2 text-right">
					                            <span class="price total_price_'.$evt['index'].'">0,00 €</span>
					                        </div>
					                    </div>
					                </fieldset>';
	                }
	                $html .= '<fieldset>
			                    <legend class="reg-inp"><h4>Persönliche Angaben *</h4></legend>
			                    <div class="form-group label-placeholder is-empty">
			                        <label for="first_name" class="control-label">Vorname</label>
			                        <input type="text" class="form-control" name="first_name" id="first_name_'.$evt['index'].'" required aria-required="true"/>
			                    </div>
			                    <div class="form-group label-placeholder is-empty">
			                        <label for="last_name" class="control-label">Nachname</label>
			                        <input type="text" class="form-control" name="last_name" id="last_name_'.$evt['index'].'" required aria-required="true"/>
			                    </div>
			                    <div class="row reg-inp">
			                        <div class="col-md-8">
			                            <div class="form-group label-placeholder is-empty">
			                                <label for="street" class="control-label">Straße</label>
			                                <input type="text" class="form-control" name="street" id="street_'.$evt['index'].'" required aria-required="true"/>
			                            </div>  
			                        </div>
			                        <div class="col-md-4">
			                            <div class="form-group label-placeholder is-empty">
			                                <label for="streetno" class="control-label">Hausnummer</label>
			                                <input type="text" class="form-control" name="streetno" id="streetno_'.$evt['index'].'" required aria-required="true" />
			                            </div>  
			                        </div>
			                    </div>
			                    <div class="row reg-inp">
			                        <div class="col-md-4">
			                            <div class="form-group label-placeholder is-empty">
			                                <label for="zip" class="control-label">PLZ</label>
			                                <input type="text" class="form-control" name="zip" id="zip_'.$evt['index'].'" required aria-required="true" />
			                            </div>
			                        </div>
			                        <div class="col-md-8">
			                            <div class="form-group label-placeholder is-empty">
			                                <label for="city" class="control-label">Ort</label>
			                                <input type="text" class="form-control" name="city" id="city_'.$evt['index'].'" required aria-required="true" />
			                            </div>  
			                        </div>
			                    </div>
			                    <div class="form-group label-placeholder reg-inp is-empty">
			                        <label for="phon" class="control-label">Telefonnummer</label>
			                        <input type="text" class="form-control" name="phone" id="phone_'.$evt['index'].'" required aria-required="true" />
			                    </div>
			                    <div class="form-group label-placeholder reg-inp is-empty">
			                        <label for="email" class="control-label">E-Mail</label>
			                        <input type="email" class="form-control" name="email" id="email_'.$evt['index'].'" required aria-required="true" />
			                    </div>
			                </fieldset>
			                <fieldset>
			                    <div class="form-group">
			                        <div class="checkbox">
			                            <label>
			                                <input type="checkbox" name="member_chk" id="member_chk'.$evt['index'].'" /> Mitglied im Kunstverein in Bremen
			                            </label>
			                        </div>
			                    </div>
			                    <div class="form-group label-placeholder disabled">
			                        <label for="artclubnumber" class="control-label">Kunstverein-Mitgliedsnummer</label>
			                        <input type="text" class="form-control" name="member_no" id="member_no_'.$evt['index'].'" required aria-required="true" disabled />
			                    </div>
			                </fieldset>';

	            if($reg_ch_inputs) {
		            $html .= '<fieldset class="registration-children-info" data-for="'.$evt['index'].'_regular_child_price">
			                    <legend><h4>Angaben zu den Kind(er) *</h4></legend>
			                    <div class="row registration-children-info-dummy">
			                        <div class="col-sm-8 col-md-10">
			                            <div class="form-group label-placeholder is-empty">
			                                <label class="control-label">Name</label>
			                                <input type="text" class="form-control" name="children_names[placeholder]" />
			                            </div>
			                        </div>
			                        <div class="col-sm-4 col-md-2">
			                            <div class="form-group label-placeholder is-empty">
			                                <label class="control-label">Alter</label>
			                                <input type="number" min="0" max="21" class="form-control" name="children_ages[placeholder]" required aria-required="true" />
			                            </div>
			                        </div>
			                    </div>
			                </fieldset>';
	            }    
	            if($mem_ch_inputs) {
		            $html .= '<fieldset class="registration-children-info" data-for="'.$evt['index'].'_member_child_price">
		                    <legend><h4>Angaben zu den Kind(er)/ Mitglied *</h4></legend>
		                    <div class="row registration-children-info-dummy">
		                        <div class="col-sm-8 col-md-10">
		                            <div class="form-group label-placeholder is-empty">
		                                <label class="control-label">Name</label>
		                                <input type="text" class="form-control" name="children_member_names[placeholder]" />
		                            </div>
		                        </div>
		                        <div class="col-sm-4 col-md-2">
		                            <div class="form-group label-placeholder is-empty">
		                                <label class="control-label">Alter</label>
		                                <input type="number" min="0" max="21" class="form-control" name="children_member_ages[placeholder]" required aria-required="true" />
		                            </div>
		                        </div>
		                    </div>
		                </fieldset>';
	            }
	            $html .= '<fieldset class="reg-inp">
	                    <legend><h4>Zahlung *</h4></legend>
	                    <p>Die Zahlung erfolgt per Bankeinzug. Diese Einzugsermächtigung gilt nur für die hier aufgeführten Veranstaltungen. Danach erlischt sie. Den ggf. gesondert anfallenden Eintritt bezahle ich an der Kasse der Kunsthalle Bremen.</p>
	                    <div class="form-group label-placeholder is-empty">
	                        <label for="iban" class="control-label">IBAN</label>
	                        <input type="text" class="form-control" name="iban" id="iban_'.$evt['index'].'" required aria-required="true" />
	                    </div>
	                    <div class="form-group label-placeholder is-empty">
	                        <label for="depositor" class="control-label">Kontoinhaber</label>
	                        <input type="text" class="form-control" name="depositor" id="depositor_'.$evt['index'].'" required aria-required="true" />
	                    </div>
	                    <div class="form-group label-placeholder is-empty">
	                        <label for="bank" class="control-label">Name der Bank</label>
	                        <input type="text" class="form-control" name="bank" id="bank_'.$evt['index'].'" required aria-required="true" />
	                    </div>

	                    <div class="form-group">
	                        <div class="checkbox">
	                            <label class="links-underlined">
	                                <input type="checkbox" name="conditions_of_participation" id="conditions_of_participation_'.$evt['index'].'" required /> Mit den <a href="http://kunsthalle-bremen.de/view/static/page/anmeldebedingungen" target="_blank">Anmelde- und Teilnahmebedingungen</a> bin ich einverstanden.
	                            </label>
	                        </div>
	                    </div>
	                    <div class="form-group">
	                        <div class="checkbox" style="margin-top:25px;">
	                            <label class="links-underlined">
	                                <input type="checkbox" name="newsletter" id="newsletter_chk_'.$evt['id'].'" /> Ich m&ouml;chte den E-Mail-Newsletter der Kunsthalle Bremen erhalten. Der Erhalt des Newsletters kann jederzeit durch einen telefonischen Hinweis oder &uuml;ber eine E-Mail an <a href="mailto:info@kunsthalle-bremen.de">info@kunsthalle-bremen.de</a> widerrufen werden.
	                            </label>
	                        </div>
	                    </div>
	                </fieldset>
	                <div class="text-center mt-15 mb-15">
	                    <a href="#" class="close-registration" title="#">
	                        <span class="icon icon-close icon-red"></span>
	                    </a>
	                </div>
	                <div>
	                    <button type="submit" id="submit_'.$evt['index'].'" class="btn btn-raised btn-default"
	                    onclick="return checkParticipants('.$evt['index'].')">Jetzt zahlungspflichtig anmelden</button>
	                </div>
	                <input name="menu_item" type="hidden" value="'.((isset($menu_item)) ? $menu_item : '').'">
	                <input name="link" type="hidden" value="'.((isset($link)) ? $link : '').'">
	                <input name="page_type" type="hidden" value="'.((isset($page_type)) ? $page_type : '').'">
	                <input name="section" type="hidden" value="'.((isset($section)) ? $section : '').'">
	                <input name="page_title" type="hidden" value="'.((isset($page_title)) ? $page_title : '').'">
	                <input name="id" type="hidden" value="'.$evt['id'].'">';//
	            // </form></div>';
        }
        
        return $html;
	}

	// Find dates for repeated event
	public static function getEventRepeatDates($id, $start_date, $event_day, $event_day_repeat, $repeat_month = 0, $end_date) {
		if(strtotime($start_date) < strtotime(date('Y-m-d'))) {
			$start_date = date('Y-m-d');
		}
		$rep_dates = [];
		$range = [];
		$event_day_repeat = strtolower($event_day_repeat);
		switch($event_day_repeat) {
			case 'daily': $rep_dates = KEventsController::getDailyEventRepDates($start_date, $end_date, $event_day, $repeat_month);
							// if($id == 162) { echo 'E: '. $id .'<br><br>'; print_r($rep_dates); exit; }
			              break;
			case 'every': $rep_dates = KEventsController::getRepDates($start_date, $end_date, $event_day, $repeat_month);
			              break;
			case 'every first': $rep_dates = KEventsController::getEventFirstDayDates($start_date, $end_date, $event_day, $repeat_month);
			              		break;
			case 'every second': $rep_dates = KEventsController::getEventSecondDayDates($start_date, $end_date, $event_day, $repeat_month);
			              		break;
			case 'every third': $rep_dates = KEventsController::getEventThirdDayDates($start_date, $end_date, $event_day, $repeat_month);
			              		break;
			case 'every last': $rep_dates = KEventsController::getEventLastDayDates($start_date, $end_date, $event_day, $repeat_month);
			              		break;
			case 'bi-weekly': $rep_dates = KEventsController::getEventBiWeeklyDates($start_date, $end_date, $event_day);
			              		break;
			// default: echo '<h2>Default'. $id . ' _ ' . $start_date . ' __ '. $end_date. '</h2>'; exit;              		
		}
		// if(count($rep_dates)) { echo '<pre>'; print_r($rep_dates); exit; }	

		return $rep_dates;
	}

	// Find event dates based on Daily
	public static function getDailyEventRepDates($start, $end, $event_day, $repeat_month = 0) {
	    $rep_dates = [];
	    $_rep_dates[] = $start;
	    $dates = array($start);
	    $months = [];
        $month = date('n', strtotime($start));
        $months[] = $month;
	    while(end($dates) < $end) {
	        $date = date('Y-m-d', strtotime(end($dates).' +1 day'));
	        $dates[] = $date;
	        $_rep_dates[] = $date;
	    }
        if($repeat_month > 0) {
	        for($i=0; $i<20; $i++) {
	        	$month += $repeat_month;
	        	if($month > 12) { $month -= 12; }
	        	if(!in_array($month, $months)) { $months[] = intval($month); }
	        }
		    $rep_dates = [];
		    if(count($months)) {
			    foreach($_rep_dates as $d) {
			    	$m = date('m', strtotime($d));
			    	if(in_array($m, $months)) { $rep_dates[] = $d; }
			    }
		    }
	    } else {
	    	foreach($_rep_dates as $d) {
	    		$rep_dates[] = $d;
	    	}
	    }

	    return $rep_dates;
	}

	// Find event dates based on every Tuesday..
	public static function getRepDates($start, $end, $rep_day, $repeat_month = 0) {
	    $rep_dates = [];
	    $start = date('Y-m-d', strtotime($start.' -1 day'));
	    $dates = array($start);
	    $start_month = date('m', strtotime($start));
	    $months = [];
	    while(end($dates) < $end) {
	        $date = date('Y-m-d', strtotime(end($dates).' +1 day'));
	        $dates[] = $date;
	        $day = date('l', strtotime($date));
	        $month = date('m', strtotime($date));
	        $last_month = 0;
	        if(count($months)) { $last_month = end($months); }
        	if($month == $start_month || $repeat_month == 0 || $repeat_month == 1 || ($repeat_month == 3 && ($last_month+3 == $month))) {
		        if($day == $rep_day) {
			        $rep_dates[] = $date; //date('j.n', strtotime($date)); 
			        $months[] = $month;
		        }
		    }    
	    }
	    // echo implode(', ', $rep_dates); echo '<br><br>'; exit;

	    return $rep_dates;
	}

	// Find dates for event on e every first Tuesday/Wednesday ..
	public static function getEventFirstDayDates($start, $end, $rep_day, $repeat_month = 0) {
		// echo $start . '<br>'.$end.'<br>'.$rep_day.'<br>'.$repeat_month.'<br>';//exit;		
	    $rep_dates = [];
	    $dates = array(date('Y-m-d', strtotime($start.' -1 day')));
	    $start_month = date('m', strtotime($start));
	    $months = [];
	    while(end($dates) < $end) {
	        $date = date('Y-m-d', strtotime(end($dates).' +1 day'));
	        $dates[] = $date;
	        $day = date('l', strtotime($date));
	        $dayOfMonth = date('d', strtotime($date));
	        $month = date('m', strtotime($date));
	        $last_month = 0;
	        if(count($months)) { $last_month = end($months); }
        	if($month == $start_month || $repeat_month == 0 || $repeat_month == 1 || ($repeat_month == 3 && ($last_month+3 == $month))) {
		        if($day == $rep_day && !in_array($month, $months) && $dayOfMonth < 8) {
			        $rep_dates[] = $date;
			        $months[] = $month;
	        	} 
	        }
	    }
	    // echo implode(', ', $rep_dates); echo '<br><br>'; exit;

	    return $rep_dates;
	}

	// Find dates for event on e every second Tuesday/Wednesday ..
	public static function getEventSecondDayDates($start, $end, $rep_day, $repeat_month = 0) {
	    $rep_dates = [];
	    $dates = array(date('Y-m-d', strtotime($start.' -1 day')));
	    $start_month = date('m', strtotime($start));
		$date = date('Y-m-d', strtotime($start));
	    $months = [];
	    $dayCount = 0;
	    while(end($dates) < $end) {
	        $dates[] = $date;
	        $day = date('l', strtotime($date));
	        $dayOfMonth = date('d', strtotime($date));
	        $month = date('m', strtotime($date));
	        // echo $date . ', ';
	        // if($month == $startMonth && ($dayOfMonth > 7)) { $dayCount = 1; }
	        if($day == $rep_day && ($dayOfMonth > 7 && $dayOfMonth <= 14)) {
	        	if($month == $start_month || $repeat_month == 0 || $repeat_month == 1 || ($repeat_month == 3 && ($last_month+3 == $month))) {
		        	if(!in_array($month, $months)) {
				        $rep_dates[] = $date;
				        $months[] = $month;
				        $dayCount = 0;
		        	}
	        	} 
	        }
	        $date = date('Y-m-d', strtotime(end($dates).' +1 day'));
	    }
	    // echo implode(', ', $rep_dates); echo '<br><br>'; exit;

	    return $rep_dates;
	}

	// Find event dates based on third event
	public static function getEventThirdDayDates($start, $end, $rep_day, $repeat_month = 0) {
	    $rep_dates = [];
	    $dates = array(date('Y-m-d', strtotime($start.' -1 day')));
	    $start_month = date('m', strtotime($start));
		$date = date('Y-m-d', strtotime($start));
	    $months = [];
	    $dayCount = 0;
	    while(end($dates) < $end) {
	        $dates[] = $date;
	        $month = date('m', strtotime($date));
	        $day = date('l', strtotime($date));
	        $dayOfMonth = date('d', strtotime($date));
	        // echo $date . ', ';
	        // if($month == $startMonth && ($dayOfMonth > 7)) { $dayCount = 1; }
	        $last_month = 0;
	        if(count($months)) { $last_month = end($months); }        	
		    if($day == $rep_day && ($dayOfMonth > 14 && $dayOfMonth <= 21)) {
		    	if($month == $start_month || $repeat_month == 0 || $repeat_month == 1 || ($repeat_month == 3 && ($last_month+3 == $month))) {
			        $rep_dates[] = $date;
			        $dayCount = 0;
		        	if(!in_array($month, $months)) {
				        $months[] = $month;
		        	}
		        }	
	        }
	        $date = date('Y-m-d', strtotime(end($dates).' +1 day'));
	    }
	    // echo 'Dates: '. implode(', ', $rep_dates); echo '<br><br>'; exit;

	    return $rep_dates;
	}

	// Find dates for event on e every last Tuesday/Wednesday ..
	public static function getEventLastDayDates($start, $end, $rep_day, $repeat_month = 0) {
	    $rep_dates = [];
	    $dates = array($start);
		$start_month = date('m', strtotime($start));	    
		$date = date('Y-m-d', strtotime($start));
	    $months = [];
	    $dayCount = 0;
	    while(end($dates) < $end) {
	        $dates[] = $date;
	        $day = date('l', strtotime($date));
	        $dayOfMonth = date('d', strtotime($date));
	        $month = date('m', strtotime($date));
	        $last_month = 0;
	        if(count($months)) { $last_month = end($months); }
        	if($month == $start_month || $repeat_month == 0 || $repeat_month == 1 || ($repeat_month == 3 && ($last_month+3 == $month))) {
		        if($day == $rep_day && ($dayOfMonth >= 23)) {
		        	$forceEntry = false;
		        	if($day == $rep_day && ($dayOfMonth >= 23)) {
		        		$forceEntry = true;
		        	}
		        	if(!in_array($month, $months) || $forceEntry) {
		        		foreach($rep_dates as $k => $dt) {
		        			$ar = explode('-', $dt);
		        			if($ar[1] == $month) {
		        				array_splice($rep_dates, $k, 1);
		        			}
		        		}
				        $rep_dates[] = $date;
				        if(!in_array($month, $months)) {
					        $months[] = $month;
				        }
				        $dayCount = 0;
		        	}
		        }
		    }  
	        $date = date('Y-m-d', strtotime(end($dates).' +1 day'));
	    }
	    // echo implode(', ', $rep_dates); echo '<br><br>'; exit;

	    return $rep_dates;
	}
	
	// Find dates for event on bi-weekly basis .. Tuesday/Wednesday ..
	public static function getEventBiWeeklyDates($start, $end, $rep_day) {
	    $rep_dates = [];
	    $dates = array($start);
		$startMonth = date('m', strtotime($start));	    
		$date = date('Y-m-d', strtotime($start));
	    $months = [];
	    $matchCount = 0;
	    while(end($dates) < $end) {
	        $dates[] = $date;
	        $day = date('l', strtotime($date));
	        $dayOfMonth = date('d', strtotime($date));
	        $month = date('m', strtotime($date));
	        if($day == $rep_day) {
	        	++$matchCount;
	        }
	        if($day == $rep_day && $matchCount == 1) {
		        $rep_dates[] = $date;
	        }
	        if($matchCount > 1) {
	        	$matchCount = 0;
	        }
	        $date = date('Y-m-d', strtotime(end($dates).' +1 day'));
	    }
	    // echo implode(', ', $rep_dates); echo '<br><br>'; exit;

	    return $rep_dates;
	}

	public function getSlideDetail() {
		if(Request::get('id')) {
			$id = Input::get('id');
			$query = 'select * from gallery_images where id = '. $id;
			$result = DB::select($query);
			$slide = [];
			if(is_array($result) && count($result)) {
				$slide = $result[0];
	
				return Response::json(array('error' => false, 'slide' => $slide), 200);		 	
			}	
			// $slide->detail = html_entity_decode(utf8_decode($slide->detail));
		}

		return Response::json(array('error' => true, 'msg' => 'Failed to fetch slide data'), 422);		 	
	}
}
