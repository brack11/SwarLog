<?php

class Conf extends Eloquent {

	protected $table = 'configs';
	public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function scopeWithUser($query,$uid) {
		return $query->where('user_id','like',$uid);
	}

	public function scopeWithName($query,$name) {
		return $query->where('name','like',$name);
	}

}