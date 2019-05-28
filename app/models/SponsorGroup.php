<?php

class SponsorGroup extends Eloquent {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sponsor_groups';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array(
								// 'password', 'remember_token'
							 );

	public function page() {
		return $this->belongsTo('Page');
	}

	public function sponsors() {
		return $this->hasMany('Sponsor');
	}

}
