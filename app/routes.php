<?php
include_once 'config.inc.php';

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
/**/

View::composer(['pages.calendar-block', 'pages.exhibitions.exhibition'], function($view) {
	$viewdata= $view->getData();
	$calendar = [];
	if(array_key_exists('exhibition', $viewdata)) {
		$exhibition = $viewdata['exhibition'];
		$calendar = ExhibitionsController::getExhibitionCalendar($exhibition->id);
	}
	$view->with('calendar', $calendar);
});

View::composer(['pages.content.page', 'layouts.default'], function($view) {
	$viewdata= $view->getData();
	$main_menu = MenusController::getMainMenu();

	$view->with('main_menu', $main_menu);
});

View::composer(['includes.header'], function($view) {
	$viewdata= $view->getData();
	$main_menu = MenusController::getMainMenu();
	$menu = MenuItem::get()->sortBy('sort_order');

	$view->with('menu', $menu);
});

View::share('pg_links_used', false);
$lang = 'de';
if(Session::has('lang')) { $lang = Session::get('lang'); }

$back_btn_text = ($lang == 'de') ? 'ZURÜCK' : 'back';
$contact_hdr_text = ($lang == 'de') ? 'Ansprechpartner/innen:' : 'Contact us:';
$download_all_text = ($lang == 'de') ? 'Alle markieren' : 'Select all';
$download_selection_text = ($lang == 'de') ? 'Auswahl downloaden' : 'Download Selection';
$current_exbs_text = ($lang == 'de') ? 'Aktuelle Ausstellungen' : 'Current Exhibitions';
$upcoming_exbs_text = ($lang == 'de') ? 'Kommende Ausstellungen' : 'Upcoming Exhibitions';
$past_exbs_text = ($lang == 'de') ? 'Vergangene Ausstellungen' : 'Past Exhibitions';
$team_of_the_kh_text = ($lang == 'de') ? 'Das Team der Kunsthalle Bremen' : 'The Team of the Kunsthalle Bremen';
$your_msg_to_kh_text = ($lang == 'de') ? 'Ihre E-Mail an die Kunsthalle Bremen' : 'Your Message to the Kunsthalle Bremen';
$your_email_text = ($lang == 'de') ? 'Ihre E-Mail' : 'Your Email Adress';
$your_name_text = ($lang == 'de') ? 'Ihr Name, Vorname' : 'Your Full name (first and last)';
$your_msg_text = ($lang == 'de') ? 'Ihre Nachricht' : 'Your Message';
$send_now_text = ($lang == 'de') ? 'Jetzt abschicken' : 'Send Now';
$ty_for_your_msg_h4_text = ($lang == 'de') ? 'VIELEN DANK FÜR IHRE NACHRICHT' : 'Thank you for your Message';
$mail_resp_text = ($lang == 'de') ? 'Wir werden Ihre Mail so schnell wie möglich beantworten.' : 'We will answer your Email as soon as possible.';

View::share('lang', strtolower($lang));
View::share('back_btn_text', $back_btn_text);
View::share('contact_hdr_text', $contact_hdr_text);
View::share('download_all_text', $download_all_text);
View::share('download_selection_text', $download_selection_text);
View::share('current_exbs_text', $current_exbs_text);
View::share('upcoming_exbs_text', $upcoming_exbs_text);
View::share('past_exbs_text', $past_exbs_text);
View::share('team_of_the_kh_text', $team_of_the_kh_text);
View::share('your_msg_to_kh_text', $your_msg_to_kh_text);
View::share('your_email_text', $your_email_text);
View::share('your_name_text', $your_name_text);
View::share('your_msg_text', $your_msg_text);
View::share('send_now_text', $send_now_text);
View::share('ty_for_your_msg_h4_text', $ty_for_your_msg_h4_text);
View::share('mail_resp_text', $mail_resp_text);


// define('FILES_DOMAIN', 'http://kunsthalle-cms.dev');
View::composer(['pages.page','pages.sub-page','pages.section','pages.exhibitions','pages.exb-page', 'pages.calendar', 'pages.start'], function($view) {
  $view->with('DOMAIN', FILES_DOMAIN);
});
View::composer(['pages.page','pages.sub-page','pages.section','pages.exhibitions','pages.exb-page'], function($view) {
  $view->with('SITE_DOMAIN', SITE_DOMAIN);
});

// Route::get('/', function()
// {
// 	return View::make('pages.home');
// });

Route::get('/events', function()
{
	return View::make('pages.khevents');
});

Route::get('/exhibition/calendar', function()
{
	return View::make('pages.calendar');
});

// Footer
View::composer(['includes.footer'], function($view) {
	$lang = MenusController::getLang();
	$ftr_links = [];
	$list = Page::where('page_type', 'footer')->get();
	foreach($list as $l) {
		$ftr_links[strtolower(str_replace(' ', '-', $l->{'title_'.$lang}))] = $l->{'title_'.$lang};
	}	

	$view->with('ftr_links', $ftr_links);
});

Route::get('/', 'MenusController@getStartPage');
Route::get('/{lang}', 'MenusController@getStartPage');
Route::get('/view/static/page/{link}', 'MenusController@getFooterPage');
Route::get('/{lang}/view/static/page/{link}', 'MenusController@getFooterPage');
Route::get('/index', 'MenusController@getStartPage');

Route::get('get-event-data', 'KEventsController@getEventData');
Route::get('/event-data/{id}/{index?}', 'KEventsController@getEventData');
Route::post('register-for-event', 'KEventsController@registerForEvent');
Route::post('/register-for-event', 'KEventsController@registerForEvent');
Route::post('/events/register', 'KEventsController@registerForEvent');

Route::post('register-member', 'MembersController@registerMember');
Route::post('/members/register-member', 'MembersController@registerMember');

Route::get('/get-calendar-json', 'KEventsController@getCalendarJson');
Route::get('/calendar/{menu_item?}/{json?}/{page_cluster_id?}', 'KEventsController@getEventsCalendar');
Route::get('/{lang?}/calendar/{menu_item?}/{json?}/{page_cluster_id?}', 'KEventsController@getEventsCalendar');
Route::get('/kalender/{menu_item?}/{json?}/{page_cluster_id?}', 'KEventsController@getEventsCalendar');
Route::get('/{lang}/kalender/{menu_item?}/{json?}/{page_cluster_id?}', 'KEventsController@getEventsCalendar');
Route::get('get-event-cal', 'KEventsController@getEventsCalendarJson');

Route::get('/get-exb-calendar-json', 'ExhibitionsController@getExhibitionCalendarJson');
Route::get('/get-slide-detail', 'ExhibitionsController@getSlideDetail');

Route::get('get-filtered-dates', 'KEventsController@getFilteredDates');
Route::get('reg-for-event-using-log', 'KEventsController@registerForEventUsingLog');

// Search
Route::post('/view/kunsthalle/type/search', 'SearchController@handleSearch');
Route::get('/blog', 'MenusController@getBlog');

// Downloads
Route::post('/handle-downloads', 'DownloadsController@handleDownloads');

Route::get('/friends-of-art', function() {
	echo 'friends of art'; exit;
	//'MenusController@getPage');
});

// Route::get('/get-event-reg-response', 'MenusController@getEventRegResponse');
Route::get('/event-reg-resp', 'MenusController@getEventRegResponse');

Route::get('/{lang?}/{menu_item}', 'MenusController@getMenuItem');
// Route::get('/{menu_item}/{page}/{action?}', 'MenusController@getPage');
Route::get('/{lang?}/{menu_item}/{page}/{action?}', 'MenusController@getPage');
Route::post('get-dl-password', 'MenusController@getDLPassword');

Route::get('/{lang?}/sb-page/{menu_item}/{section}/{page_id}', 'MenusController@getSubPage');
// Route::get('/menus/{menu_item}/{section}/{page_title}', 'MenusController@pageWithTitle');
// Route::get('/kunsthalle-bremen/team', 'MenusController@getTeamPage');

// Exb
// Route::get('/view/exhibitions/exb-page/{page_title}', 'MenusController@getExbPage');
// Route::get('/view/exhibitions/exb-page/{page_title}/{lang?}', 'MenusController@getExbPage');
Route::get('/{lang?}/view/exhibitions/exb-page/{slug}', 'MenusController@getExbPage');

// Route::get('/exhibitions/list/{category?}', ['as' => 'exhibitions', 'uses' => 'MenusController@getExhibitions']);
Route::get('/{lang?}/view/exhibitions/list/{category?}/{tag?}', 'MenusController@getExhibitions');
Route::get('/{lang?}/exhibition/{id}', 'ExhibitionsController@exhibition');

// Header file
Route::get('/{lang?}/kh/top/main/menu', 'MenusController@getTopMenu');
Route::get('/{lang?}/kh/top/main/set-lang', 'MenusController@setLang');
Route::get('/{lang?}/kh/top/main/get-lang', 'MenusController@getLang');

/* Content / pages
*/
Route::get('/page/{menu_item_id}/{cs_id}/{page_id}', 'ContentsController@page');
Route::get('/content-section/{menu_item_id}', 'ContentsController@getContentSection');

// View for testing via CMS
Route::get('/test/{page_id}', 'MenusController@getTestView');
Route::get('/hello', function() { echo 'Welcome to Kunsthalle website!'; });

Route::post('send-message', 'EmailController@sendMessage');

// Event registration response
Route::get('/kunsthalle/event/registration/confirmation', 'MenusController@getEventRegResponse');
// Member registration response
Route::get('/kunsthalle/member/registration/confirmation', 'MenusController@getMemberRegResponse');

// External
Route::get('/online-katalog', 'MenusController@getOnlineKatalog');

/*  Authentication
*/
Route::get("/user/auth/check/login", "UserController@login");
Route::post('/user/authenticate', ['as' => 'user.authenticate', 'uses' => 'UserController@authenticate']);
Route::get('logout', array('uses' => 'UserController@doLogout'));
Route::get('/user/auth/check/logout', 'UserController@doLogout');


// General funcitons
function _t($str) {
	$lbls = [
			   'regular_price_adult' => 'Erwachsene',
			   'regular_price_child' => 'Kinder',
			   'member_price_adult'  => 'Mitglied im Kunstverein',
			   'reduced_price_adult' => 'Erm&auml;&szlig;igt',
			   'siblings_price_child' => 'Geschwisterkinder',
			   'member_price_child' => 'Kinder / Familienmitgliedschaft',
			   'inclusive_material' => 'inkl. Material',
			];
	if(array_key_exists($str, $lbls)) {
		$str = $lbls[$str];
	}

	return $str;
}

function objectToArray($d) {
if (is_object($d)) {
	$d = get_object_vars($d);
}

return $d;
}
