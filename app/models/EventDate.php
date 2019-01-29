<?php

class EventDate extends Eloquent {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'event_dates';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array(
								// 'password', 'remember_token'
							 );

	public function k_event() {
		return $this->belongsTo('KEvent');//, 'event_cluster_k_event');
	}

	// public function event_clusters() {
	// 	return $this->belongsToMany('EventCluster', 'event_cluster_tag');
	// }
	public function clusters() {
		return $this->belongsToMany('Cluster');
	}

}
