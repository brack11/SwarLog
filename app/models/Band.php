<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Band extends Eloquent {

	protected $table = 'bands';
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