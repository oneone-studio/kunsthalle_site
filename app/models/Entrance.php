<?php

class Entrance extends Eloquent { //} implements RemindableInterface {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'entrance';

 	// protected $fillable = array('title', 'subtitle');

  	public static $rules = array(
    );
	
  	// protected $fillable = [];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array(
								// 'password', 'remember_token'
							 );

	public function k_event() {
		return $this->belongsTo('KEvent');
	}
}
