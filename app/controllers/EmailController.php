<?php
// include __DIR__. '/../config.inc.php';

class EmailController extends BaseController {
	
	public function sendMessage() {
		$f = fopen('mail.log', 'w+');
		fwrite($f, "sendMessage\n\n". print_r(Input::all(), true));
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: Kunsthalle Bremen <info@kunsthalle-bremen.de>' . "\r\n";

		if(Input::has('email') && Input::has('contact_id')) {
			$contact = Contact::find(Input::get('contact_id'));
			if($contact && !empty($contact->email)) {
				$name = Input::get('name');
				$email = Input::get('email');
				$comment = Input::get('comment');
				$rec_email = $contact->email;

				$body = 'Diese E-Mail wurde über das Online-Formular der Kunsthalle Bremen verschickt.<br><br>'.
				        'Gesendet von:<br>'.
						$name .'<br>'.
				        $email .'<br><br>'.
				        'Ihre Nachricht:<br>'. $comment;

				$rec_emails = [ Input::get('email'), $rec_email ];
				foreach($rec_emails as $r_email) {
					mail($r_email, "Ihre Nachricht an die Kunsthalle Bremen", $body, $headers);
				}
				// mail('shahidm08@gmail.com', "Ihre Nachricht an die Kunsthalle Bremen", $body, $headers);
				$resp = [];
				$resp['data'] = Input::all();
				$resp['contact'] = $contact->email;

				return Response::json(array('error' => false, 'data' => $resp), 200);
			}
		}		

		return Response::json(array('error' => true, 'msg' => 'Failed'), 401);
	}

}
