<?php

class KHEvent extends Eloquent { //} implements RemindableInterface {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'events';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array(
								// 'password', 'remember_token'
							 );

}
