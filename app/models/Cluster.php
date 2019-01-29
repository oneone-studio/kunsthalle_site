<?php

// use Illuminate\Auth\UserTrait;
// use Illuminate\Auth\UserInterface;
// use Illuminate\Auth\Reminders\RemindableTrait;
// use Illuminate\Auth\Reminders\RemindableInterface;

class Cluster extends Eloquent { //} implements RemindableInterface {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'clusters';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array(
								// 'password', 'remember_token'
							 );

	public function k_events() {
		return $this->belongsToMany('KEvent');
	}

	public function exhibition() {
		return $this->belongsTo('Exhibition');
	}

	public function tags() {
		return $this->belongsToMany('Tag', 'cluster_tag');
	}

	public function kEventCost() {
		return $this->hasOne('KEventCost');
	}

}
