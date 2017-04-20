<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Prefix extends Eloquent {

	protected $table = 'prefixes';
	public $timestamps = false;

	use SoftDeletingTrait;

	protected $dates = ['deleted_at'];

	public function qsos()
	{
		return $this->belongsToMany('Qso');
	}

}