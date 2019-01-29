<?php

class KEvent extends Eloquent { //} implements RemindableInterface {

	// use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'k_events';

 	// protected $fillable = array('title', 'subtitle');

  	public static $rules = array(
    	'title' => 'required|min:2',
    	'subtitle' => 'required'
    );
	
  	protected $fillable = [
  							 'title', 'subtitle', 'event_date', 'start_time', 'end_titme', 'detail', 'guide_name', 'registration', 'max_attendance', 'members_only', 'is_free' 
  						  ];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array(
								// 'password', 'remember_token'
							 );

	public function exhibitions() {
		return $this->belongsToMany('Exhibition', 'exhibition_k_event');
	}

	// public function event_clusters() {
	// 	return $this->belongsToMany('EventCluster', 'event_cluster_k_event');
	// }

	public function clusters() {
		return $this->belongsToMany('Cluster', 'cluster_k_event');
	}

	public function tags() {
		return $this->belongsToMany('Tag');
	}

	public function event_dates() {
		return $this->hasMany('EventDate');
	}

	public function kEventCost() {
		return $this->hasOne('KEventCost');
	}

	public function entrance() {
		return $this->hasOne('Entrance');
	}

	public static function getExhibitions($id) {
		$qry = 'select * from exhibitions where k_event_id = ' . $id;
		$data = DB::select( $qry );
		$exbs = [];
		foreach($data as $d) {
			$vals = [];
			foreach($d as $k => $v) {
				$vals[$k] = $v;
			}
			$exbs[] = $vals;
		}

		return $exbs;
	}

	public static function getExhibitionIds($id) {
		$qry = 'select exhibition_id from exhibition_k_event where k_event_id = ' . $id;
		$data = DB::select( $qry );
		$exbs = [];
		foreach($data as $e) {
			$exbs[] = $e->exhibition_id;
		}

		return $exbs;
	}
}
