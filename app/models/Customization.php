<?php 

class Customization extends Eloquent {
	protected $table = 'customizations';
	public $timestamps = false;

	public function qso() {
		return $this->belongsTo('Qso');
	}
}