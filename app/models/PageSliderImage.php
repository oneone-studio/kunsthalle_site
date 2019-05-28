<?php

class PageSliderImage extends Eloquent { //} implements RemindableInterface {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'page_slider_images';

	public function page_image_slider() {
		return $this->belongsTo('PageImageSlider');
	}

	public function slide_text() {
		return $this->hasMany('SlideText');
	}
}

?>