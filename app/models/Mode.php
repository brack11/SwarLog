<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Mode extends Eloquent {

	protected $table = 'modes';
	public $timestamps = false;

	use SoftDeletingTrait;

	protected $dates = ['deleted_at'];

	public function qsos()
	{
		return $this->hasMany('Qso');
	}

	public function scopeWithName($q,$name) {
		return $q->where('name','like',$name);
	}

}