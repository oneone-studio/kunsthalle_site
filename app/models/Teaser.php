<?php

class Teaser extends Eloquent {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'teasers';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	// protected $hidden = array();

	public function page() {
		return $this->belongsTo('Page');
	}
}
