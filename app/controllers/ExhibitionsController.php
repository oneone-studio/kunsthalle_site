<?php

class ExhibitionsController extends BaseController {

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
		$exhibitions = Exhibition::all();	
		
		$months = [ '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ];
		$exb_months = [];
		$exb_data = [];
		$cnt = 0;
		foreach($exhibitions as $exb) {
			$sdate_ar = explode('-', $exb->start_date);
			if(is_array($sdate_ar)) {
				$month = $sdate_ar[1];
				if(!in_array($month, $exb_months)) {
					$exb_months[] = $month;
				}
			}
		}

		foreach($exb_months as $exb_month) {
			++$cnt;
			$exhibitions = DB::select('select e.* from exhibitions e 
				                       where e.start_date like "'.date('Y').'-'.$exb_month.'-%"');			
			foreach($exhibitions as &$exb) {
				$calendar = [];
				foreach($exb_months as $ex_month) {
					$events = DB::select('select e.*, c.* from k_events e, k_event_costs c, exhibition_k_event eke 
										  where e.start_date like "'. date('Y').'-'.$ex_month.'-%" 
										    and c.k_event_id = e.id 
										    and eke.exhibition_id = '. $exb->id . '
										    and eke.k_event_id = e.id
										  group by e.id');
					$event_data = [];
					if(count($events) > 0) {
						$m = date('M', strtotime($events[0]->start_date));
						$event_data['month'] = $m;
						$event_data['year'] = date('Y', strtotime($events[0]->start_date));
						
						$day_titles = [];
						foreach($events as $e) {
							$cluster = [];
							$sql = 'select ec.* from event_clusters ec, event_cluster_k_event ecke
							        where ecke.k_event_id = '. $e->id . '
							          and ecke.event_cluster_id = ec.id
							        group by ec.id';
							$_cluster_data = DB::select($sql);
							if(is_array($_cluster_data) && count($_cluster_data)) {
								$cluster = $_cluster_data[0];
							}   
							$e->event_cluster = $cluster; // event cluster
							$clustered_events = [];
							$clustered_dates = [];
							if(count($cluster)) {
								$sql = 'select e.start_date from k_events e, event_clusters ec, event_cluster_k_event ecke
								        where ecke.event_cluster_id = ' . $cluster->id . '
								          and ecke.k_event_id = e.id
								        group by e.id';
								// echo $sql; exit;				        
								$ce_data = DB::select($sql);
								if(is_array($ce_data) && count($ce_data)) {
									foreach($ce_data as $cd) {
										if(!in_array(date('j.n', strtotime($cd->start_date)), $clustered_dates)) {
											$clustered_dates[] = date('j.n', strtotime($cd->start_date));
										}
									}
								}
							}
							// echo '<pre>'; print_r($clustered_events); exit;
							$e->clustered_dates = $clustered_dates;        

							$day_num = date('d', strtotime($e->start_date));
							$day_title = $day_num .' '. date('l', strtotime($e->start_date));
							$event_list = [];
							foreach($events as $ne) {
								$day_num_ne = date('d', strtotime($ne->start_date));
								if($day_num == $day_num_ne) {
									$event_list[] = objectToArray($ne);
								}
							}
							$event_data['days'][$day_title]['events'] = $event_list;
						}	

						$calendar[] = $event_data;						
					}
				}

				$exb_data[] = [ 'exhibition' => $exb, 'calendar' => $calendar ];
				$exb->exb_data = $exb_data;
			}
		}
// echo '<pre>'; print_r($exhibitions); exit;
		return View::make('pages.exhibitions.index', ['exhibitions' => $exhibitions]);
	}

	public function calendar()
	{
		$exhibitions = []; //Exhibition::all();	

		return View::make('pages.calendar', ['exhibitions' => $exhibitions]);
	}

	public function exhibition($id)
	{
		// if(Request::has('id')) {
			$exhibition = Exhibition::with('gallery_images')->find($id); 
			// $ke = new KEventsController();
			// echo '<pre>'; echo $exhibition->cluster->id; exit; //print_r($exhibition); exit;
			$calendar = [];
			$months = [ '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ];
			$cnt = 0;
			foreach($months as $month) {
				++$cnt;
				$events = DB::select('select * from k_events where start_date like "'. date('Y').'-'.$month.'-%"');
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
							$day_num_ne = date('d', strtotime($ne->start_date));
							if($day_num == $day_num_ne) {
								$event_list[] = $this->objectToArray($ne);
							}
						}
						$event_data['days'][$day_title]['events'] = $event_list;
					}	

					$calendar[] = $event_data;
				}
			}

			$exhibitions = Exhibition::all();
			$view = View::make('pages.exhibitions.exhibition', ['exhibition' => $exhibition]); //, 'calendar' => $calendar]);
			
			// View::composer('calendar', function($view) {
				// $view = View::make('pages.exhibitions.exhibition')->nest('calendar');//, ['calendar' => $calendar]);//, 'pages.calendar', $calendar);
			// });
			// echo '<pre>'; print_r($exhibition); exit;

			return $view; // View::make('pages.exhibitions.exhibition', ['exhibition' => $exhibition]);
		// }	

		// return $view; //View::make('pages.exhibitions.index', ['exhibitions' => $exhibitions]);
	}

	public function objectToArray($d) {
		if (is_object($d)) {
			$d = get_object_vars($d);
		}

		return $d;
	 }

	 public function getExhibitionCalendarJson() {
		$exhibition = Exhibition::with('gallery_images')->find(Input::get('exhibition_id'));
		$calendar = ExhibitionsController::getExhibitionCalendar($exhibition->id);
		$slideHeights = [];
		foreach($calendar as $c) {
			if(array_key_exists('slideHeight', $c)) {
				$slideHeights[] = $c['slideHeight'];
			}
		}
		$calendar['slideHeights'] = $slideHeights;

		return Response::json(array('error' => false, 'calendar_json' => json_encode($calendar)), 200);		 	
	 }

	 public static function getExhibitionCalendar($exhibition_id) {
		setlocale(LC_ALL, "de_DE", 'German', 'german');
	 	$exhibition = Exhibition::with('gallery_images')->find($exhibition_id); 

		$calendar = [];
		$months = [ '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ];
		$slideNo = -1;
		$cur_month = date('m');
		// $viewdata= $view->getData();
		// $exhibition = $viewdata['exhibition'];
		$cluster = $exhibition->cluster;
		$clustered_events = [];
		$clustered_dates = [];
		$all_clustered_dates = [];
		$_clustered_dates = [];
		$event_ids = [];
		$event_count = 0;
		$slideHeights = [];
		$event_no = 0;
		$_month = '';
		$randstr = 'YuSz2p7E90L';
$cur_year = date('Y')+1;
		foreach($months as $month) {

			$eventBlockCount = 0;
			if($month >= $cur_month || ($cur_year > date('Y'))) {
				$query = 'select e.*, e.id as base_event_id							  	  
					  	  from k_events e, clusters cl, cluster_k_event ecke 
					  	  where e.start_date like "'. $cur_year .'-'.$month.'-%" 
					  		and ecke.k_event_id = e.id
					  		and ecke.cluster_id = cl.id
					  		and cl.exhibition_id = '. $exhibition->id .'
					  		and cl.cluster_type = "exhibition"		
						  group by e.id
						  order by e.start_date';
				$events = DB::select($query);

				// $events = KEvent::with([ 'clustered_dates', 'tags', 'event_dates', 'kEventCost' ])
				// 				  ->where('start_date', '=', date('Y').'-'.$month.'-%')
				// 				  ->where('exhibition_id', '=', $exhibition->id)
				// 				  ->get();						  
				// echo '<pre>'; print_r($events); exit;								  
				// echo $query; exit;
				$event_count = count($events);
				if(count($events) > 0) {
					++$slideNo;
					$m = strftime("%B", strtotime($events[0]->start_date));
					$_month = $m;
					$event_data = [];
					$calendar[$_month]['month'] = $m;
					$calendar[$_month]['year'] = date('Y', strtotime($events[0]->start_date));
					$day_titles = [];

					foreach($events as $e) {

						$e->index = $e->base_event_id . date('dmY', strtotime($e->start_date)); //$e->base_event_id. date('dmY', strtotime($e->start_date));
						if(isset($cluster)) {
							// if(!in_array(date('j.n', strtotime($e->start_date)), $_clustered_dates)) {
							// 	$clustered_dates[] = [ 'id'		 	 => $e->base_event_id,
							// 						   'index'       => $e->index = $e->base_event_id . date('dmY', strtotime($e->start_date)),
	 					// 							   'start_date'  => date('j.n', strtotime($e->start_date)),
	 					// 							   'slideNo'	 => $slideNo
							// 						 ];
							// 	$_clustered_dates[] = date('j.n', strtotime($e->start_date)); 
							// }
							$query = 'select c.cluster_id from cluster_k_event c where c.k_event_id = ' . $e->base_event_id;
							$res = DB::select($query);
							if(is_array($res) && count($res)) {
								$cluster_id = $res[0]->cluster_id;

								$sql = 'select e.*, e.id as base_eid from k_events e, clusters ec, cluster_k_event ecke
								        where ecke.cluster_id = ' . $cluster->id . '
								          and ecke.k_event_id = e.id
								        group by e.id';
								$ce_data = DB::select($sql);
								if(is_array($ce_data) && count($ce_data)) {
									foreach($ce_data as $cd) {
										if(!in_array($cd->base_eid, $event_ids)) {
											$clustered_events[] = $cd;
											$event_ids[] = $cd->base_eid;
										}
									}
								}
							}
						}

						if(!array_key_exists($e->base_event_id, $clustered_dates)) {
							$clustered_dates[$e->base_event_id] = [];
						}
						if(isset($e->event_day) && strlen($e->event_day) > 0) {
							$rep_dates = ExhibitionsController::getEventRepeatDates($e->base_event_id, $e->start_date, $e->event_day, $e->event_day_repeat, $e->repeat_month, $e->end_date);
							foreach($rep_dates as $rep_date) {
								$fdate = strftime("%A", strtotime($rep_date)) . ', '. date('j.n', strtotime($rep_date));
								if(!in_array(date('j.n', strtotime($rep_date)), $_clustered_dates)) {
									$clustered_dates[$e->base_event_id][] = [ 
																'id'         => $e->base_event_id,
																'index'      => $e->base_event_id . date('dmY', strtotime($rep_date)),
				 												'event_date' => $fdate,
				 												'full_date'  => $rep_date,
				 												'slideNo'	 => $slideNo
														    ];
									$_clustered_dates[] = date('j.n', strtotime($rep_date)); 
								}
							}
						} else {
							$query = 'select event_date from event_dates where k_event_id = ' . $e->base_event_id;
							$event_dates = DB::select($query);
							foreach($event_dates as $rep_date_obj) {
								$rep_date = $rep_date_obj->event_date;
								$fdate = strftime("%A", strtotime($rep_date)) . ', '. date('j.n', strtotime($rep_date));
								if(!in_array(date('j.n', strtotime($rep_date)), $_clustered_dates)) {
									$clustered_dates[$e->base_event_id][] = [ 
																'id'         => $e->base_event_id,
																'index'      => $e->base_event_id . date('dmY', strtotime($rep_date)),
				 												'event_date' => $fdate,
				 												'full_date'  => $rep_date,
				 												'slideNo'	 => $slideNo
														    ];
									$_clustered_dates[] = date('j.n', strtotime($rep_date)); 
								}
							}
						}

						$e->cluster = $cluster;

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
						/** /	
						foreach($events as $ne) {
							$day_num_ne = date('d', strtotime($ne->start_date));
							if($day_num == $day_num_ne) {
								if(empty($ne->event_day)) {
								   $event_list[] = objectToArray($ne);
								}
							}
						}/**/
						// if(count($event_list)) {
						// 	$calendar[$_month]['days'][$day_title]['events'] = $event_list;
						// }
						// $calendar[$_month]['slideHeight'] = (count($events) * 300);
					}					

					foreach($events as $e) {		
						// echo '<br>EID: ' . $e->base_event_id . ' - ' . $e->title_de;			
						++$eventBlockCount;
						// $e->index = $e->base_event_id . date('dmY', strtotime($e->start_date));
						$rep_count = 0;
						if(isset($e->event_day) && strlen($e->event_day) > 0) {
							$rep_dates = ExhibitionsController::getEventRepeatDates($e->base_event_id, $e->start_date, $e->event_day, $e->event_day_repeat, $e->repeat_month, $e->end_date);
							if(count($rep_dates)) {
								$eventBlockCount += count($rep_dates); 
							}
							// if($e->base_event_id == 34) { echo implode(', ', $rep_dates); exit; }
							foreach($rep_dates as $rep_date) {
									$_MONTH = strftime("%B", strtotime($rep_date));
								// echo $rep_date . ' -> '. $_MONTH; exit;
									
								// if(date('m', strtotime($rep_date)) == intval($month)) {
									$day_num_rep = date('d', strtotime($rep_date));
									$day_title_rep = $day_num_rep .' '. strftime('%A', strtotime($rep_date));
									if(!array_key_exists($_MONTH, $calendar)) {
										// echo '<h1 style="color:red;">M: '. $_MONTH . '</h1>';
										$calendar[$_MONTH] = [];
										$calendar[$_MONTH]['month'] = $_MONTH;
										$calendar[$_MONTH]['year'] = date('Y', strtotime($rep_date));
										$calendar[$_MONTH]['days'] = [];
										// $calendar[$_MONTH]['slideHeight'] = 4500;
										// $calendar[$_MONTH]['eventsCount'] = count($rep_dates);
										$calendar[$_MONTH]['daysCount'] = count($rep_dates);
									}
									if(!array_key_exists('days', $calendar[$_MONTH])) {
										$calendar[$_MONTH]['days'] = [];
									}
									if(!array_key_exists($day_title_rep, $calendar[$_MONTH]['days'])){
										$calendar[$_MONTH]['days'][$day_title_rep] = [];
										$calendar[$_MONTH]['days'][$day_title_rep]['events'] = [];
										// $calendar[$_MONTH]['days'][$day_title_rep]['day_num'] = $day_num_rep;
									}
									$e->day_title = $day_title_rep;									
									$e->index = $e->base_event_id . date('dmY', strtotime($rep_date));
									$calendar[$_MONTH]['days'][$day_title_rep]['events'][] = objectToArray($e);

									// handle clustered dates
									// echo '<p style="color:darkgreen; font-size:11px;">'. $rep_date .'<br>'; print_r($_clustered_dates); echo '</p>';
									// if(!in_array(date('j.n', strtotime($rep_date)), $_clustered_dates)) {
									// 	echo '<span style="color:red;">Adding date.. '. $e->title_de .' </span><br>';
									// 	$clustered_dates[] = [ 'id'		 	 => $e->base_event_id,
									// 						   'index'       => $e->base_event_id . date('dmY', strtotime($rep_date)),
			 					// 							   'start_date'  => date('j.n', strtotime($rep_date)),
			 					// 							   'slideNo'	 => $slideNo
									// 						 ];
									// 	$_clustered_dates[] = date('j.n', strtotime($rep_date)); 
									// }
									// $query = 'select c.cluster_id from cluster_k_event c where c.k_event_id = ' . $e->base_event_id;
									// $res = DB::select($query);
									// if(is_array($res) && count($res)) {
									// 	$cluster_id = $res[0]->cluster_id;

									// 	$sql = 'select e.*, e.id as base_eid from k_events e, clusters ec, cluster_k_event ecke
									// 	        where ecke.cluster_id = ' . $cluster->id . '
									// 	          and ecke.k_event_id = e.id
									// 	        group by e.id';
									// 	$ce_data = DB::select($sql);
									// 	if(is_array($ce_data) && count($ce_data)) {
									// 		foreach($ce_data as $cd) {
									// 			if(!in_array($cd->base_eid, $event_ids)) {
									// 				$clustered_events[] = $cd;
									// 				$event_ids[] = $cd->base_eid;
									// 			}
									// 		}
									// 	}
									// }
								// }
							}
							$rep_count += count($rep_dates);

						} else { // if event has random dates, use these instead of repeated dates

							$query = 'select event_date from event_dates where k_event_id = ' . $e->base_event_id;
							// echo $query; exit;
							$event_dates = DB::select($query);
							// $event_dates = $e->event_dates;
							foreach($event_dates as $e_date) {
								$day_num_rep = date('d', strtotime($e_date->event_date));
								$day_title_rep = $day_num_rep .' '. strftime('%A', strtotime($e_date->event_date));
								$_MONTH = strftime("%B", strtotime($e_date->event_date));
								if(!array_key_exists($_MONTH, $calendar)) {
									// echo '<h1 style="color:red;">M: '. $_MONTH . '</h1>';
									$calendar[$_MONTH] = [];
									$calendar[$_MONTH]['month'] = $_MONTH;
									$calendar[$_MONTH]['year'] = date('Y', strtotime($e_date->event_date));
									$calendar[$_MONTH]['days'] = [];
									$calendar[$_MONTH]['slideHeight'] = 4500;
									// $calendar[$_MONTH]['eventsCount'] = count($event_dates);
									$calendar[$_MONTH]['daysCount'] = count($event_dates);
								}
								$e->index = $e->base_event_id . date('dmY', strtotime($e_date->event_date));
								// $calendar[$_MONTH]['days'][$day_title_rep]['day_num'] = $day_num_rep;
								$calendar[$_MONTH]['days'][$day_title_rep]['events'][] = objectToArray($e);
							}

									// handle clustered dates
									// if(!in_array(date('j.n', strtotime($e_date->event_date)), $_clustered_dates)) {
									// 	$clustered_dates[] = [ 'id'		 	 => $e->base_event_id,
									// 						   'index'       => $e->base_event_id . date('dmY', strtotime($e_date->event_date)),
			 					// 							   'start_date'  => date('j.n', strtotime($e_date->event_date)),
			 					// 							   'slideNo'	 => $slideNo
									// 						 ];
									// 	$_clustered_dates[] = date('j.n', strtotime($e_date->event_date)); 
									// }
									// $query = 'select c.cluster_id from cluster_k_event c where c.k_event_id = ' . $e->base_event_id;
									// $res = DB::select($query);
									// echo '<h4>Q:- '. $query . '<br>Res: '; print_r($res); echo '</h4>';
									// if(is_array($res) && count($res)) {
									// 	$cluster_id = $res[0]->cluster_id;

										// $sql = 'select e.*, e.id as base_eid from k_events e, clusters ec, cluster_k_event ecke
										//         where ecke.cluster_id = ' . $cluster->id . '
										//           and ecke.k_event_id = e.id
										//         group by e.id';
										// $ce_data = DB::select($sql);
										// echo '<h5>Q2:- '. $sql . '<br>Res: '; print_r($ce_data); echo '</h5>';
										// if(is_array($ce_data) && count($ce_data)) {
										// 	foreach($ce_data as $cd) {
										// 		if(!in_array($cd->base_eid, $event_ids)) {
										// 			$clustered_events[] = $cd;
										// 			$event_ids[] = $cd->base_eid;
										// 		}
										// 	}
										// }
									// }

						}
					}	
					// $calendar[$_month]['eventsCount'] = $eventBlockCount;
					if(!isset($calendar[$_month])) {
						$calendar[$_month] = [];
						$calendar[$_month]['days'] = [];
						// $calendar[$_month]['month'] = $_month;
						// $calendar[$_month]['year'] = date('Y');
					}
					// $calendar[$_month]['daysCount'] = count(array_keys($calendar[$_month]['days']));
					$_slideHeight = (($eventBlockCount * 180) + (count(array_keys($calendar[$_month]['days'])) * 100));
					// $calendar[$_month]['slideHeight'] = $_slideHeight;
					$slideHeights[] = $_slideHeight;
					// $calendar[] = $event_data;
					// ++$slideNo;
				}
		    }	
		}

		$_event_ids = [];
		foreach($calendar as &$cal) {
			foreach($cal['days'] as $day => &$data) {
				foreach($data['events'] as &$ev) {
					if(!in_array($ev['base_event_id'], $_event_ids)) {
						usort($clustered_dates[$ev['base_event_id']], function ($item1, $item2) {
						    if (strtotime($item1['full_date']) == strtotime($item2['full_date'])) return 0;
						    return strtotime($item1['full_date']) < strtotime($item2['full_date']) ? -1 : 1;
						});
						$_event_ids[] = $ev['base_event_id'];
					}
				}
			}
		}	

		$indexes = [];
		foreach($calendar as &$cal) {
			foreach($cal['days'] as $day => &$data) {
				foreach($data['events'] as &$ev) {
					foreach($clustered_dates[$ev['base_event_id']] as $cd) {
						if(!in_array($cd['index'], $indexes)) {
							$all_clustered_dates[] = $cd;
							$indexes[] = $cd['index'];
						}
					}
				}
			}
		}

		$day_count = 0;
		foreach($calendar as &$cal) {
			$slideHeight = 500 + (count($cal['days']) * 120);
			$eventCount = 0;
			$cl_dates_height = 0;
			foreach($cal['days'] as $day => &$data) {
				++$day_count;
				// $day['day_index'] = $day_count;
				$eventCount += count($data['events']);
				// echo '<span style="color:red; font-size:10px;">eventCount: ' .$eventCount . '</span><br>';
				foreach($data['events'] as &$ev) {
					$clustered_date_list = [];
					if(array_key_exists($ev['base_event_id'], $clustered_dates)) { $clustered_date_list = $clustered_dates[$ev['base_event_id']]; }
					$ev['clustered_dates'] = $clustered_date_list;
					$cl_dates_height += count($clustered_date_list) * 10;
					$ev['cl_dates_height'] = $cl_dates_height;
					++$event_no;
					$ev['event_no'] = $event_no;
					$ev['day_index'] = $day_count;
					$ev['all_clustered_dates'] = $all_clustered_dates;
				}
			}
			$cal['dayCount'] = count($cal['days']);
			$cal['eventCount'] = $eventCount;
			$slideHeight += ($eventCount * 138);
			$cal['slideHeight'] = $slideHeight;
		}

		foreach($calendar as &$cal) {
			ksort($cal['days']);
		}

		// $calendar['slideHeights'] = $slideHeights;
		// echo '<pre>'; print_r($calendar); exit;
		return $calendar;
	}

	// Find dates for repeated event
	public static function getEventRepeatDates($id, $start_date, $event_day, $event_day_repeat, $repeat_month = 0, $end_date) {
		$rep_dates = [];
		$range = [];
		// echo $start_date . ' --- ' . $end_date; exit;
		$event_day_repeat = strtolower($event_day_repeat);
		switch($event_day_repeat) {
			case 'daily': $rep_dates = ExhibitionsController::getDailyEventRepDates($start_date, $end_date, $event_day, $repeat_month);
			              break;
			case 'every': $rep_dates = ExhibitionsController::getRepDates($start_date, $end_date, $event_day, $repeat_month);
			              break;
			case 'every first': $rep_dates = ExhibitionsController::getEventFirstDayDates($start_date, $end_date, $event_day, $repeat_month);
			              		break;
			case 'every second': $rep_dates = ExhibitionsController::getEventSecondDayDates($start_date, $end_date, $event_day, $repeat_month);
			              		break;
			case 'every third': $rep_dates = ExhibitionsController::getEventThirdDayDates($start_date, $end_date, $event_day, $repeat_month);
			              		break;
			case 'every last': $rep_dates = ExhibitionsController::getEventLastDayDates($start_date, $end_date, $event_day, $repeat_month);
			              		break;
			case 'bi-weekly': $rep_dates = ExhibitionsController::getEventBiWeeklyDates($start_date, $end_date, $event_day);
			              		break;
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
        for($i=0; $i<20; $i++) {
        	$month += $repeat_month;
        	if($month > 12) { $month -= 12; }
        	if(!in_array($month, $months)) { $months[] = intval($month); }
        }
	    while(end($dates) < $end) {
	        $date = date('Y-m-d', strtotime(end($dates).' +1 day'));
	        $dates[] = $date;
	        $_rep_dates[] = $date;
	    }
	    $rep_dates = [];
	    foreach($_rep_dates as $d) {
	    	$m = date('m', strtotime($d));
	    	if(in_array($m, $months)) { $rep_dates[] = $d; }
	    }

	    return $rep_dates;
	}

	// Find event dates based on every Tuesday..
	public static function getRepDates($start, $end, $rep_day, $repeat_month = 0) {
	    $rep_dates = [];
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
	    $dates = array($start);
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
	    $dates = array($start);
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
