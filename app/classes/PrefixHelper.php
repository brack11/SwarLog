<?php 

class PrefixHelper {

	/**
	 * Main function for parsing callsigns in reality parses only $call the other two properties are after slashes
	 */
	public static function prefixInfo($call, $call1 = '', $call2 = '') {
		$from = $tmpFrom = $to = $tmpTo = null;
		$info = array();
		$call = explode('/',$call);
		$call = strtoupper(trim($call[0]));

		$pref0106s = Prefix::where('InternalUse','LIKE','01%')->orwhere('InternalUse','LIKE','06%')->get(); 

		for ($i=0; $i < count($pref0106s); $i++) { 
			if (preg_match($pref0106s[$i]->mask,$call)) {
				$info[] = $pref0106s[$i];
				$from = $tmpFrom = $pref0106s[$i]->id;
				$to = $tmpTo = $pref0106s[$i+1]->id;
				$pref0201s = Prefix::where('InternalUse','LIKE','0201%')->where('id','>',$from)->where('id','<',$to)->get();
				for ($j=0; $j < count($pref0201s); $j++) { 
					if(preg_match($pref0201s[$j]->mask,$call)) {
						$info[] = $pref0201s[$j];
						$from = $pref0201s[$j]->id;
						$to = isset($pref0201s[$j+1]) ? $pref0201s[$j+1]->id : $to;
						$pref0202s = Prefix::where('InternalUse','LIKE','0202%')->where('id','>',$from)->where('id','<',$to)->get();
						$pref09s = Prefix::where('InternalUse','LIKE','09%')->where('id','>',$from)->where('id','<',$to)->get();
						for ($k=0; $k < count($pref0202s); $k++) { 
							if(preg_match($pref0202s[$k]->mask,$call)) {
								$info[] = $pref0202s[$k];
								$from = $pref0202s[$k]->id;
								$to = isset($pref0202s[$k+1]) ? $pref0202s[$k+1]->id : $to;
							}
						}
						for ($l=0; $l < count($pref09s); $l++) { 
							if(preg_match($pref09s[$l]->mask,$call)) {
								$info[] = $pref09s[$l];
								break;
							}
						}
					}
				}
				if ($call2 == '') {
					$spPrefixes = Prefix::where('InternalUse','LIKE','03%')->where('id','>',$tmpFrom)->where('id','<',$tmpTo)->get();
					$sp = array();
					foreach ($spPrefixes as $spPrefix) {
						if (preg_match($spPrefix->mask,$call)) {
						// dd($call);
							// array_push($info,$spPrefix);
							$sp[] = $spPrefix;
						}
					}
					array_push($info,$sp);
				}
			}
		}
		return array_filter($info);
	}
	

	/**
	 * Extract territories info
	 */
	public static function getTerritory($level,$id,$options = false) {
		$pfx1 = $pfx2 = array('0'=>'--');
		$html = '<option>--</option>\n';
		switch ($level) {
			case '1':
				$prefixes1 = Prefix::where('InternalUse','LIKE','01%')->orwhere('InternalUse','LIKE','06%')->get();
				foreach ($prefixes1 as $prefix1) {
					$pfx1[$prefix1->id] = $prefix1->territory;
				}
				return $pfx1;
				break;

			case '2':
				$prefixes1 = Prefix::where('InternalUse','LIKE','01%')->orwhere('InternalUse','LIKE','06%')->get();
				foreach ($prefixes1 as $key => $prefix1) {
					if ($prefix1->id == $id) {
						$currentRec = $prefixes1[$key];
						$nextRec = $prefixes1[$key+1];
						$prefixes2 = Prefix::where('InternalUse','LIKE','02%')->where('id','>',$currentRec->id)->where('id','<',$nextRec->id)->get();
						foreach ($prefixes2 as $prefix2) {
							if ($options) {
								$html .= "<option value=\"$prefix2->id\">$prefix2->territory</option>\n";
							} else {
								$pfx2[$prefix2->id] = $prefix2->territory;
							}
						}
					}
				}
				
				return ($options)?$html:$pfx2;
				break;
			case '3':
				$prefixes2 = Prefix::find($id);
				return json_encode(array('cq'=>$prefixes2->cq,'itu'=>$prefixes2->itu));
				break;
			default:
				return null;
				break;
		}
	}


	/**
	 * Extracts array elements with empty values
	 */
	public static function notEmpty($array) {
		if (is_array($array)) {
			$result = array();
			foreach ($array as $key => $value) {
				if ($value != '') {
					$result[] = $value;
				}
			}
			return $result;
		}
	}


	/**
	 * Check if record is not already add
	 */
	public static function getExisting($pfxInternal,$pfxTerritory,$pfxMask) {
		if(Prefix::where('internaluse',$pfxInternal)->where('territory',$pfxTerritory)->where('mask',$pfxMask)->count()) {
			return Prefix::where('internaluse',$pfxInternal)->where('territory',$pfxTerritory)->where('mask',$pfxMask)->first()->id;
		} else {
			return false;
		}
	}


	/*
	* Parse file pfx into array ready to upload to the database
	* @param string $file_path
	* @return array
	*/
	public static function fileToArray($file_path){
		$pfx = new self;
		$fileA = array_slice(file($file_path),3);
		$fileB = $pfx->arrayExtended($fileA);
		return $fileB;
	}


	/*
	* Exploding lines on columns and prework on a few columns
	* @param array $lines
	* @return array 
	*/
	private function arrayExtended($lines){
		$colName = array('internaluse','longitude','latitude','territory','prefix','cq','itu','continent','tz','adif','province','startdate','enddate','mask','source');
		foreach ($lines as $key=>$line) {
			$cols = explode('|', $line);
			$i = 0;
			foreach ($cols as $col) {
				if($colName[$i]=='internaluse') $cells[$key][$colName[$i]] = $this->strReplace($col);
				elseif($colName[$i]=='mask') $cells[$key][$colName[$i]] = $this->maskToPattern($col);
				elseif(($colName[$i]=='latitude')||($colName[$i]=='longitude')) $cells[$key][$colName[$i]] = $this->toCoords($col);
				else $cells[$key][$colName[$i]] = $col; 

				$i++;
			}
		}
		return $cells;
	}

	/*
	* Removing unused signs "L,M,H,-" from the first paramenter "InternalUse"
	* @param strting $str
	* @return string
	*/
	public static function removeLeading($str){
		$str = preg_replace('/^[HML-]/','',$str);
		return $str;
	}


	/*
	* Converting patterns in paramenter "Masks"
	* @param strting $str
	* @return string
	*/
	public static function maskToPattern($str){
		$str = trim($str);
		$pattern = preg_replace("/^,/","",$str);
		$pattern = preg_replace("/\//","\/",$pattern);
		$pattern = "/^(".$pattern.")/";
		$pattern = preg_replace("/\@/","[A-Z%]",$pattern);
		$pattern = preg_replace("/\#/","[0-9%]",$pattern);
		$pattern = preg_replace("/\./","$",$pattern);
		$pattern = preg_replace("/\?/",".",$pattern);
		$pattern = preg_replace("/,/",")|^(",$pattern);
		// $pattern = preg_replace("/\^\(\)\|/", "", $pattern);
		return $pattern;
	}

	/*
	* Converting latitude and longtitude to normal degrees
	*
	* @param integer $number
	* @return integer
	*/
	public static function toCoords($number){
		return $number/180;
	}
}