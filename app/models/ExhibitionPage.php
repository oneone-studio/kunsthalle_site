<?php

class 	ExhibitionPage extends Eloquent {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'pages';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array(
								// 'password', 'remember_token'
							 );

	public function content_section() {
		return $this->belongsTo('ContentSection');
	}

	public function page_sections() {
		return $this->hasMany('ExhibitionPageSection')->orderBy('sort_order');
	}

	public function page_contents() {
		return $this->hasMany('ExhibitionPageContent')->orderBy('sort_order');
	}

	public function page_image_sliders() {
		return $this->hasMany('ExhibitionPageImageSlider')->orderBy('sort_order');
	}

	public function cluster() {
		return $this->hasOne('Cluster');
	}

	public function banner() {
		return $this->hasOne('Banner');
	}

	public function h2() {
		return $this->hasMany('H2');
	}

	public function h2text() {
		return $this->hasMany('H2text');
	}

	public function images() {
		return $this->hasMany('Image');
	}

	public function contacts() {
		return $this->belongsToMany('Contact');
	}

	public function sponsor_groups() {
		return $this->hasMany('SponsorGroup');
	}

	public function downloads() {
		return $this->hasMany('Download');
	}

	public function teaser() {
		return $this->hasOne('Teaser');
	}
}
