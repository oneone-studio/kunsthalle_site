<?php
// include __DIR__. '/../config.inc.php';

class EmailController extends BaseController {
	
	public function sendMessage() {
		// $f = fopen('kh.log', 'w+');
		// fwrite($f, "sendMessage\n\n". print_r(Input::all(), true));
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: kunsthalle Bremen <info@kunsthalle-bremen.net>' . "\r\n";

		if(Input::has('email')) {
			$name = Input::get('name');
			$email = Input::get('email');
			$comment = Input::get('comment');
			$rec_email = Input::get('receiver_email');

			$body = 'Absender:<br>'.
					$name .'<br>'.
			        $email .'<br>'.
			        'Text:<br><br>'. $comment;

			mail($rec_email, "Kunsthalle Bremen", $body, $headers);

			return Response::json(array('error' => false, 'data' => Input::all()), 200);
		}		

		return Response::json(array('error' => true, 'msg' => 'Failed'), 401);
	}

}
