<?php

class ImageGrid extends Eloquent {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'image_grid';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array(
								// 'password', 'remember_token'
							 );

	public function pages() {
		return $this->belongsTo('Page');
	}

	public function grid_images() {
		return $this->hasMany('GridImage');
	}
}
