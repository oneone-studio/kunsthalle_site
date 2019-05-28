<?php

class PageImageSlider extends Eloquent {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'page_image_sliders';

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

	public function page_slider_images() {
		return $this->hasMany('PageSliderImage')->orderBy('sort_order');
	}
}
