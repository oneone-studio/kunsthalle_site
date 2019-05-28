<?php

class Tag extends Eloquent {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tags';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array(
								// 'password', 'remember_token'
							 );

	public function k_events() {
		return $this->belongsToMany('KEvent');//, 'event_cluster_k_event');
	}

	public function pages() {
		return $this->belongsToMany('Page');
	}

	public function clusters() {
		return $this->belongsToMany('Cluster');
	}

}
