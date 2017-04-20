<?php

class GridHelper extends PrefixHelper{

	public static function getGrids($callSign) {
		$prefixes = PrefixHelper::prefixInfo(trim($callSign));
		foreach ($prefixes as $prefix) {
			if(!is_array($prefix)) {
				$lat = $prefix->lat;
				$lon = $prefix->lon;
			}
		}

		return self::getStr($lat,$lon);
	}
	
	public static function getStr($lat, $lon, $length='4') {
		if (empty($lat) || empty($lon)) {
			return "set lan lon first";
		}
		$field = range('A','R');
		$square = range(0,9);
		$subsquare = range('a','x');
		$lat += 90;
		$lon += 180;
		$v = $lon/20; 
		$lon -= floor($v)*20;
		$p1 = $field[$v];
		$v = $lat/10;			   
		$lat -= floor($v)*10;
		$p2 = $field[$v];
		$p3 = $lon/2;
		$p4 = $lat;
		$lon -= floor($p3)*2;
		$lat -= floor($p4);
		//~ echo ($p4)."<br>";
		$p3 = floor($p3);
		$p4 = floor($p4);
		$p5 = 12 * $lon;
		$p6 = 24 * $lat;
		$p5 = $subsquare[$p5];
		$p6 = $subsquare[$p6];
		$result = $p1.$p2.$p3.$p4.$p5.$p6;
		return substr($result,0,$length);
	}

	public static function getReverse($grid){
		if(!empty($grid)) {
			$grid = str_split(strtoupper($grid));
			for($i=0;$i<count($grid);$i++){
				if(!isset($grid[$i])){
					if($i>1 && $i<4){
						$qra[$i] = '0';
					}else{
						$qra[$i] = 'A';
					}
				}else{
					$qra[$i] = $grid[$i];
				}
			}
			list($p1, $p2, $p3, $p4, $p5, $p6) = $qra;
			list($p1, $p2, $p3, $p4, $p5, $p6) = array(ord($p1)-ord('A'), ord($p2)-ord('A'), ord($p3)-ord('0'), ord($p4)-ord('0'), ord($p5)-ord('A'), ord($p6)-ord('A') );
			
			$lon = ($p1*20) + ($p3*2) + (($p5+0.5)/12) - 180;
			$lat = ($p2*10) + $p4 + (($p6+0.5)/24) - 90;
			return array('lat'=>$lat, 'lon'=>$lon);
		}
	}
}