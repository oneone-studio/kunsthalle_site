<?php

class ContentSection extends Eloquent {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'content_sections';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array(
								// 'password', 'remember_token'
							 );

	public function menu_item() {
		return $this->belongsTo('MenuItem');
	}

	public function pages() {
		return $this->hasMany('Page');
	}

	public function contacts() {
		return $this->belongsToMany('Contact');
	}
}
