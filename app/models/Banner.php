<?php

class Banner extends Eloquent {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'banners';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	// protected $hidden = array();

	public function page() {
		return $this->belongsTo('Page');
	}

	public function banner_text() {
		return $this->hasMany('BannerText')->orderBy('id');
	}
}
