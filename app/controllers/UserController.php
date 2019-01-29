<?php
 
class UserController extends Controller
{
  public function login()
  {
// echo 'login..';exit;
  	// echo 'login'; exit;
    return View::make("pages.user.login");
  }

  public function authenticate() {
 	$rules = array(
	    'key'    => 'required|alphaNum', // make sure the email is an actual email
	);
	$validator = Validator::make(Input::all(), $rules);
	// run the validation rules on the inputs from the form
	// if(Session::get('auth') == false) {
	// if(isset($_POST)) { echo 'post'; exit; }
		if(strtolower(Input::get('key')) == 'khdev') {

	    	Session::put('auth', true);
    		Session::put('is_admin', false);

	    	return Redirect::to('/');
	    } else {        
	    	$validator->getMessageBag()->add('key', 'Incorrect key entered');
	        return Redirect::to('/user/auth/check/login')->withErrors($validator)->withInput();
	    }
    // } else {
    // 	return Redirect::to('/');
    }  	

    public function doLogout() {
		// echo 'Logout'; exit;
		 Auth::logout();
		 // Session::put('auth', false);
		 Session::forget('auth');
		 return Redirect::action('UserController@login'); //('/user/auth/check/login');
		 // return Redirect::to('/user/auth/check/login');
    }  

}