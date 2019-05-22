<?php

class MembersController extends BaseController {

	public function registerMember() {
		// $f = fopen('logs/test_1.log', 'w+');
		// fwrite($f, "registerMember()..\n\n". print_r(Input::all(), true));
		// echo '<pre>';print_r(Input::all()); exit;
		$lang = MenusController::getLang();

		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: Kunsthalle Bremen <info@kunsthalle-bremen.de>' . "\r\n";

		$body = 'Sehr geehrte/r '.Input::get('first_name').' '.Input::get('last_name').'<br><br>
				herzlich willkommen im Kunstverein in Bremen!<br><br>
				Bei Fragen oder fehlerhaften Angaben in dieser Anmeldebestätigung schreiben Sie uns bitte eine Nachricht an mitgliederpost@kunsthalle-bremen.de oder rufen Sie uns an unter +49 (0421) 32 908 638.<br><br>

				Den Mitgliedsausweis/die Mitgliedsausweise und aktuelle Informationen senden wir Ihnen innerhalb von 10 Tagen zu.<br><br>
				Mit freundlichen Grüßen<br>
				Ihr Kunstverein in Bremen
				<br><br>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br><br>
				Sie haben folgende Mitgliedschaft beantragt:<br>';
				
				$membership = Input::get('membership');

				$ms_text = '';
				if($membership == 'single') { $ms_text = "Einzelmitglied (jährlich € 70,–)"; }
				if($membership == 'couple') { $ms_text = "Ehepaar/Lebensgemeinschaft (jährlich € 105,–)"; }
				if($membership == 'family') { $ms_text = "Familie (jährlich € 105,–)"; }
				if($membership == 'pupil') { $ms_text = "Schüler/Schülerin bis zum 20. Lebensjahr (jährlich € 22,–)"; }
				if($membership == 'study') { $ms_text = "Studierende/Auszubildene bis zum 27. Lebensjahr (jährlich € 22,–)"; }
				if($membership == 'lifetime') { $ms_text = "auf Lebenszeit (1.250,- Einzelmitglied bzw. 1.750,- Paar)"; }
				// if($membership == 'gift') { $ms_text = "als Geschenkmitgliedschaft"; }
				if(Input::has('membership_as_gift')) {
					$ms_text .= '<br>Auswahl als Geschenkmitgliedschaft';
				}

				$body .= $ms_text.'<br><br>';

				// if($membership == 'family') {
				// 	$body .= 'Ihres Ehe- oder Lebenspartners:<br>'. Input::get('partner_first_name').' '.Input::get('partner_last_name').'<br>'.
				// 	         'Geburtsdatum: '. Input::get('partner_birthday').'<br><br>';
				// }

				if(strlen(Input::get('comment'))) {
					$body .= 'Bemerkungen:<br>'. Input::get('comment');
				}

				$body .= '<br><br>Ihre Daten:<br>'.
				Input::get('first_name') .' '. Input::get('last_name').'<br>'.
				Input::get('birthday').'<br>'.
				Input::get('job').'<br>'.
				Input::get('street') .' '. Input::get('streetno').'<br>'.
				Input::get('zip').' '.Input::get('city').'<br>'.
				Input::get('phone').'<br>'.
				Input::get('email').'<br>';

		if(strtolower(Input::get('membership')) == 'couple' || strtolower(Input::get('membership')) == 'family') {
			$body .= '<br>Zusätzlich angemeldete/r Partner/in<br>';
			
			if(Input::has('partner_first_name') && !empty(Input::get('partner_first_name'))) { //strtolower(Input::get('membership')) == 'couple') {
				$body .= Input::get('partner_first_name').' '.Input::get('partner_last_name').'<br>'.Input::get('partner_birthday').'<br>';
			}

			if(Input::has('family_first_name') && !empty(Input::get('family_first_name'))) {
				$body .= Input::get('family_first_name') .' '. Input::get('family_last_name').'<br>'.Input::get('family_birthday').'<br>';
			}
		}

		// if($membership == 'family' && Input::has('children_names') && count(Input::get('children_names') > 0)) {
		if((strtolower(Input::get('membership')) == 'family') && Input::has('children_names') && count(Input::get('children_names'))) {
			$body .= '<br>Name und Alter der angemeldeten Kinder:';
			$c_names = Input::get('children_names');
			$c_dobs = Input::get('children_birthdays');

			for($i=0; $i<count($c_names); $i++) {
				if(!empty($c_names[$i])) {
					$body .= '<br>- '. $c_names[$i] .' '. $c_dobs[$i];
				}
			}
		}	

		$body .= '<br><br>Sie erhalten Ihre Mitgliederpost von uns: '. (Input::get('mailing') == 'email' ? 'per E-Mail' : 'per Brief') .'<br>';
		$body .= '<br>Die von Ihnen gewählte Zahlweise ist: '. (Input::get('payment') == 'bill' ? 'per Rechnung' : 'per Bankeinzug') .'<br>';

		if(!empty(Input::get('iban')) && !empty(Input::get('depositor')) && !empty(Input::get('bank'))) {
			$body .= '<br><br>Ihre Bankverbindung:<br>'.
			         Input::get('iban'). '<br>'. Input::get('bic'). '<br>'. Input::get('depositor'). '<br>'. Input::get('bank');
		}

		if(Input::has('age') && Input::get('age') == 'on') {
			$body .= '<br><br>Einladungen für Junge Mitglieder: Ja';
		}

		if(Input::has('newsletter') && Input::get('newsletter') == 'on') {
			$body .= '<br><br>Newsletter: Ja';
		}

		// echo '<pre>'. $body; exit;		
		$rec_emails = [ Input::get('email'), 'mitgliederpost@kunsthalle-bremen.de' ];
		$email = Input::get('email');
		if($email == 'shahidm08@gmail.com' || $email == 'manzoor@oneone-studio.com') { $rec_emails = ['shahidm08@gmail.com']; }
// echo '<pre>'; print_r($body); exit;		
		foreach($rec_emails as $rec_email) {
			mail($rec_email, "Der Kunstverein in Bremen – Mitglied werden", $body, $headers);
		}
		// echo $body; exit;				
		// $menu_item = 'art-friends';
		// $link = 'online-member-form';
		// $confirmation = 'confirmation';
		$menu_item = 'jetzt-unterstuetzen';
		$link = 'online-mitgliedsantrag';
		$confirmation = 'confirmation'; //bestätigung';

		return Redirect::action('MenusController@getMembershipResp', ['lang' => $lang]); //[$menu_item, $link, $confirmation]);

	}

}

?>