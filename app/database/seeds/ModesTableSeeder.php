<?php 

class ModesTableSeeder extends Seeder {

	public function run() {
		$modes = array(
			'SSB',
			'CW',
			'ASCI',
			'CLO',
			'HELL',
			'JT65',
			'JT9',
			'PAC',
			'PAC2',
			'PAC3',
			'PKT',
			'PSK31',
			'PSK63',
			'PSK125',
			'Q15',
			'THRB',
			'TOR'
 			);
		foreach ($modes as $mode) {
			$t = new Mode;
			$t->name = $mode;
			$t->code = $mode;
			$t->save();
		}
	}
}