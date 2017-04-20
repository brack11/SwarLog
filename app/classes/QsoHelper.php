<?php 

class QsoHelper {

	/**
	 * Check if record is not already add
	 */
	public static function isUnique($recArr) {
		if(!is_array($recArr)) {
			return false;
		} 
		if (with(new PhpHelper)->is_assoc($recArr)) {
			if ($qso = Qso::where('call',$recArr['call'])->where('date',date('Y-m-d H:i:s',strtotime($recArr['qso_date'].' '.$recArr['time_on'])))->first()) {
				return $qso;
			}
		} else if ($qso = Qso::where('call',$recArr[0])->where('date',$recArr[1])->first()) {
			return $qso;
		} else {
			return true;
		}
	}

	/**
	 * Find and update confirmed record
	 */
	public static function isConfirmed($recArr) {
		if(!is_array($recArr)) return false;

		if($qso = Qso::where('call',$recArr[0])->where('date',$recArr[1])->first()) {
			$qso->qsl_rcvd = 'Y';
			$qso->save();
			return $qso;
		} else {
			return false;
		}
	}

	/**
	 * Convert frequency to band
	 * 
	 * @return int $band->id
	 */
	public static function freqConv() {
		$freq = Input::get('f') / 1000;
		foreach (Band::all() as $band) {
			if (($band->low <= $freq) && ($freq <= $band->high)) {
				return json_encode($band->id);
			}
		}

		return false;
	}


	/**
	 * Refresh prefix information in case of loss
	 */
	public static function refreshInfo() {
		$qids = Input::get('qid');
		foreach ($qids as $qid) {
			$pids = array();
			$qso = Qso::find($qid);
			$infos = PrefixHelper::prefixInfo($qso->call);
			foreach ($infos as $info) {
				if (!is_array($info)) {
					$pids[] = $info->id;
				}
			}
			$qso->customization ? $qso->customization()->delete() : '';
			$qso->prefixes()->sync($pids);
		}
	}


	/**
	 * Send data to eQsl
	 */
	public static function eQslSend($qso) {
		if (!Conf::withUser(Sentry::getUser()->id)->withName('qUser')->exists() || !Conf::withUser(Sentry::getUser()->id)->withName('qPassword')->exists()) {
			return false;
		}
		$eUser = Conf::withUser(Sentry::getUser()->id)->withName('qUser')->first()->value;
		$ePass = Conf::withUser(Sentry::getUser()->id)->withName('qPassword')->first()->value;
		// dd($qso[0]);
		if(count($qso) == 1) {
			// dd($qso[0]);
			// $qso = Qso::find($qso[]);
			$adif = AdifHelper::getAdif($qso[0]);
			$uri = "http://www.eqsl.cc/qslcard/importADIF.cfm?EQSL_USER=".urlencode($eUser)."&EQSL_PSWD=".urlencode($ePass)."&ADIFData=".urlencode($adif);
			return file_get_contents($uri);
		} else {
			$adif = '';
			foreach ($qso as $val) {
				$qso_obj = is_object($val) ? $val : Qso::find($val); 
				$adif .= AdifHelper::getAdif($qso_obj);
			}
			$data = array('EQSL_USER'=>$eUser,'EQSL_PSWD'=>$ePass,'ADIFData'=>$adif);
			$options = array(
				'http'=>array(
					'header'  => "Content-type: application/x-www-form-urlencoded",
					'method'  => 'POST',
					'content' => http_build_query($data),
					),
				);
			$context  = stream_context_create($options);
			$url = "http://www.eqsl.cc/qslcard/importADIF.cfm";
			return file_get_contents($url, false, $context);
		}
	}


	/**
	 *
	 */
	public static function eQslReceive() {
		$user = Sentry::getUser();
		if (!Conf::withUser($user->id)->withName('qUser')->exists() || !Conf::withUser($user->id)->withName('qPassword')) {
			return false;
		}
		$eUser = Conf::withUser($user->id)->withName('qUser')->first()->value;
		$ePass = Conf::withUser($user->id)->withName('qPassword')->first()->value;
		$lastSync = date('Ymd', AdifHelper::getLastSync());
		// $lastSync = 20131201;
		// dd(date('Ymd', AdifHelper::getLastSync()));
		$source = file_get_contents("http://www.eqsl.cc/qslcard/DownloadInBox.cfm?UserName=".$eUser."&Password=".$ePass."&RcvdSince=".$lastSync);
		if (!preg_match('/Your ADIF log file has been built/', $source)) return false;
			// print_r($source);
		if (preg_match('/(\w+?\.adi)/i',$source,$match)) {
			$url = 'http://www.eqsl.cc/qslcard/downloadedfiles/'.$match[0];
			$adif = file_get_contents($url);
			$result = AdifHelper::parseStr($adif);
			return $result;
		} else {
			return false;
		}
	}


	/**
	 * Set paper QSL be requested 
	 */
	public static function qslRequested() {
		$action = strtoupper(Input::get('action', 'R'));
		$qids = Input::get('qid') ?: Qso::where('qsl_rcvd','N')->get();
		// dd($qids->count());
		if($action == 'E') {
			if (QsoHelper::eQslSend($qids)) {
				foreach ($qids as $qid) {
					$qso = is_object($qid) ? $qid : Qso::find($qid);
					$qso->qsl_rcvd = 'R';
					$qso->save();
				}
			}
		} else if ($action == 'RCV') {
			$confd = array();
			if ($rcvd = QsoHelper::eQslReceive()) {
				foreach ($rcvd as $value) {
					$longDate = date('Y-m-d H:i:s', strtotime($value['qso_date'].' '.$value['time_on']));
					if($qso = Qso::where('call',$value['call'])->where('date',$longDate)->first()) {
						$qso->qsl_rcvd = 'Y';
						$qso->save();
						$confd[] = $qso;
					}
				}
			}
			return $confd;
		} else {
			foreach ($qids as $qid) {
				$qso = Qso::find($qid);
				$qso->qsl_rcvd = $action;
				$qso->save();
			}
		}
	}
}