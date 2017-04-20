<?php 

class AdifHelper {

	/**
	 * ADIF Text Parser importer
	 * @param string in adif format
	 * @return multidimentional array of records
	 */
	public static function parseStr($adifStr) {
		$result = array();
		$arr1 = explode('<EOH>', $adifStr);
		if (count($arr1) == 1) {
			$records= explode('<EOR>',$arr1[0]);
		} else {
			$records = explode('<EOR>',$arr1[1]);
		}
		$records = array_filter($records);

		for ($i=0; $i < count($records)-1; $i++) { 
			$cells = explode('<',$records[$i]);
			// $cells = array_filter($cells);
			foreach ($cells as $cell) {
				list($name,$length,$value) = self::scanAdif($cell);
				if(($value != '') && ($name != '')) {
					$result[$i][strtolower($name)] = substr(strtolower($value),0,$length);
				}
			}
		}
		// var_dump($records);
		return $result;
	}

	protected static function scanAdif($str) {
		list($name,$length) = sscanf($str,'%[^:]:%d');
		$value = substr($str, -1*abs($length));
		return array($name,$length,$value);
	}

	public static function parseFile($adifFile) {
		// if (Session::token() === Input::get('_token')) {
			// $username = Sentry::getUser()->username;
			// $destinationFile = $adifFile;
			$contents = File::get($adifFile);
			$result = AdifHelper::parseStr($contents);
		    return $result;
		// }

	}

	public static function getLastSync() {
		if (($qName = Conf::withUser(Sentry::getUser()->id)->withName('qUser')) && ($qPass = Conf::withUser(Sentry::getUser()->id)->withName('qPassword'))) {
			$username = $qName->first()->value;
			$password = $qPass->first()->value;

			$url = "http://www.eqsl.cc/qslcard/DisplayLastUploadDate.cfm?username=$username&password=$password";
			$data = file_get_contents($url);
			if(preg_match('/([0-9]+\-[A-z]+\-[0-9]+)([\sat]+)([:0-9]+)/i', $data, $matches)) {
				// dd(date('Ymd', strtotime($matches[1].' '.$matches[3])));
				return strtotime($matches[1].' '.$matches[3]);
			}
		}

		return false;
	}


	public static function getAdif(Qso $qso) {
		$cont = isset($qso->customization->continent) ? $qso->customization->continent : $qso->prefixes->first()->continent;
		$cq = isset($qso->customization->cq) ? $qso->customization->cq : $qso->prefixes->last()->cq;
		$itu = isset($qso->customization->itu) ? $qso->customization->itu : $qso->prefixes->last()->itu;
		$lat = isset($qso->customization->lat) ? $qso->customization->lat : $qso->prefixes->last()->lat;
		$lon = isset($qso->customization->lon) ? $qso->customization->lon : $qso->prefixes->last()->lon;
		$grid = isset($qso->customization->grid) ? $qso->customization->grid : GridHelper::getStr($lat,$lon);
		$band = (isset($qso->band_id) && ($qso->band_id != 0)) ? $qso->band->name : '';
		$mode = (isset($qso->mode_id) && ($qso->mode_id != 0)) ? $qso->mode->name : '';
		$iota = isset($qso->customization->iota) ? $qso->customization->iota : '';
		if (isset($qso->customization->prefix)) {
			$pref = $qso->customization->prefix;
		} else {
			$pre = sscanf($qso->prefixes->last()->prefix,"/^(%[^)/])/");
			$pref = $pre[0];
		}
		// $prefixes1 = $qso->prefixes ? $qso->prefixes->first()->: (PrefixHelper::prefixInfo($qso->call) ? array('',''));
		

		$row = "<CALL:".strlen($qso->call).">".strtoupper($qso->call).
			"<BAND:".strlen($band).">".strtoupper($band).
			"<FREQ:".strlen($qso->frequency).">".strtoupper($qso->frequency).
			"<CONT:".strlen($cont).">".strtoupper($cont).
			"<COUNTRY:".strlen($qso->prefixes->first()->territory).">".strtoupper($qso->prefixes->first()->territory).
			"<CQZ:".strlen($cq).">".$cq.
			"<DXCC:".strlen($qso->prefixes->first()->adif).">".$qso->prefixes->first()->adif.
			"<GRIDSQUARE:".strlen($grid).">".strtoupper($grid).
			"<IOTA:".strlen($iota).">".strtoupper($iota).
			"<ITUZ:".strlen($itu).">".strtoupper($itu).
			"<LAT:".strlen($lat).">".strtoupper($lat).
			"<LON:".strlen($lon).">".strtoupper($lon).
			"<MODE:".strlen($mode).">".strtoupper($mode).
			"<NOTES:".strlen($qso->comment).">".strtoupper($qso->comment).
			"<PFX:".strlen($pref).">".strtoupper($pref).
			"<QSLMSG:".strlen($qso->message).">".strtoupper($qso->message).
			"<QSL_RCVD:".strlen($qso->qsl_rcvd).">".strtoupper($qso->qsl_rcvd).
			"<QSL_SENT_VIA:".strlen($qso->qsl_via).">".strtoupper($qso->qsl_via).
			"<QSO_DATE:".strlen(date('Ymd',strtotime($qso->date))).":D>".date('Ymd',strtotime($qso->date)).
			"<RST_SENT:".strlen($qso->rst).">".$qso->rst.
			"<TIME_ON:".strlen(date('Hi',strtotime($qso->date))).">".date('Hi',strtotime($qso->date)).
			"<LOG_PGM:".strlen('SWARLOG').">SWARLOG
			<APP_SWARLOG_TERRITORY:".strlen($qso->prefixes->last()->territory).">".strtoupper($qso->prefixes->last()->territory).
			"<APP_SWARLOG_TZ:".strlen($qso->prefixes->last()->tz).">".strtoupper($qso->prefixes->last()->tz).
			"<EOR>\n";	
			return (string)$row;
	}

}