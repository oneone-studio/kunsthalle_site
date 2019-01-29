<?php

class Contact extends Eloquent {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'contacts';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array();
	
	public function page() {
		return $this->belongsToMany('Page');
	}

	public function content_sections() {
		return $this->belongsToMany('ContentSection');
	}
	
	public function department() {
		return $this->belongsTo('Department');
	}
}
