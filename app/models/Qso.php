<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Qso extends Eloquent {

	protected $table = 'qsos';
	public $timestamps = false;

	use SoftDeletingTrait;

	protected $dates = ['deleted_at'];

	public static $rules = array(
		"eDate" => "required|date",
		"eFreq"	=> "required|numeric",
		"eLat"	=> "required|numeric",
		"eLon"	=> "required|numeric",
		"eCall" => "required",
		"eRst"  => "required|numeric",
		);

	public function band()
	{
		return $this->belongsTo('Band');
	}

	public function mode()
	{
		return $this->belongsTo('Mode');
	}

	public function prefixes()
	{
		return $this->belongsToMany('Prefix');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function customization() {
		return $this->hasOne('Customization');
	}

	public function scopeWithUser($q,$uid) {
		return $q->where('user_id','like',$uid);
	}

	public function scopeWithBand($q,$bid) {
		return $q->where('band_id','like',$bid);
	}

	public function scopeWithMode($q,$mid) {
		return $q->where('mode_id','like',$mid);
	}

}