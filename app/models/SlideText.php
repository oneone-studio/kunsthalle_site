<?php

class SlideText extends Eloquent {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'slide_text';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	// protected $hidden = array();

	public function page_slider_image() {
		return $this->belongsTo('PageSliderImage');
	}

}
