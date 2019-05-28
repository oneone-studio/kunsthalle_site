<?php

class BannerText extends Eloquent {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'banner_text';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	// protected $hidden = array();

	public function banner() {
		return $this->belongsTo('Banner');
	}

}
