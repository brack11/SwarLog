<?php
use MrJuliuss\Syntara\Controllers\BaseController;

class PrefixController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->layout = View::make('config.prefix');
		$this->layout->breadcrumb = array(
			array(
				'title' => trans('custom.home'),
				'icon' => 'glyphicon-home',
				'link' => url('/'),
				),
			array(
				'title' => trans('custom.config'),
				'icon' => 'glyphicon-cog',
				'link' => url('config'),
				),
			array(
				'title' => trans('custom.config.prefix'),
				'icon' => 'glyphicon-file',
				'link' => url('config/prefix'),
				),
			);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// 
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		// 
	}


	/**
	 * Generates information about prefix
	 * @param text $call $call2 $call3 prefixes between slashes
	 * 
	 * @return array Prefix
	 */
	public function callInfo($call, $call2 = '', $call3 = '') {
		$info = PrefixHelper::prefixInfo($call, $call2, $call3);
		if (!empty($info)) {
			$html = '';
			// $call = Input::get('call');
			// $pfx = new PrefixHelper;
			$html .=     '<li class="dropdown">';
			$html .=     	'<a class="dropdown-toggle" data-toggle="dropdown" href="">';
			$html .=     		trans('custom.pfx.info').'<span class="caret"></span>';
			$html .=     	'</a>';
			$html .= 		'<ul class="dropdown-menu" role="menu">';
			$html .=		'<li class="dropdown-header">'.trans('custom.pfx.info').'</li>';
			foreach ($info as $key => $value) {
				if (!is_array($value)) {
					$html .= Form::hidden('qPrefix['.$call.'][]', $value->id);
					$html .= '<li><a href="#"  onclick="popUp(\''.url('show/map').'?lat='.$value->lat.'&lon='.$value->lon.'\');">'.$value->territory.'</a></li>';
				} else {
					$html .= '<li class="divider"></li>';
					$html .= '<li class="dropdown-header">'.trans('custom.additional.info').'</li>';
					foreach ($value as $key => $val) {
						$html .= '<li class="disabled"><a href="#">'.$val->territory.'</a></li>';
					}
				}
			}
			$html .= 		'</ul>';
			$html .=     '</li>';
			$html .=     '<li><a href="#" onclick="popUp(\'https://ssl.qrzcq.com/call/'.$call.'\');">qrzcq</a></li>';
			$html .=     '<li><a href="#" onclick="popUp(\'http://www.hamqth.com/'.$call.'\');">hamqth</a></li>';
			$html .=     '<li><a href="#" onclick="popUp(\'http://www.qrz.ru/callsign.phtml?callsign='.$call.'\');">qrz.ru</a></li>';
			// dd($info);
			return $html;
		}
		return '';
	}


	/**
	 * Calls view with java script the shows live google map with information about distance
	 */
	public function showMap() {
		$lat = Input::get('lat');
		$lon = Input::get('lon');
		$this->layout = View::make('common.map')->with('lat',$lat)->with('lon',$lon);
	}


	/**
	 * Uploading file
	 */
	public function uploadFile() {
		$destinationPath = public_path().'/uploads/pfx/';
		if(Input::file('uplPfx')->move($destinationPath,'pfx'.date('YmdHis').'.lst')) {
			return Redirect::to('config/prefix');
		} else {
			return false;
		}
	}


	/**
	 * Processing prefix file
	 */
	public function processFile($fileName) {
		$destinationPath = public_path().'/uploads/pfx/';
		$destFile = $destinationPath.$fileName;
		$last_lat = $last_lon = $last_prefix = $last_cq = $last_itu = $last_continent = $last_tz = '';
		if (file_exists($destFile)) {
			$content = File::get($destFile);
			if ($rawArray = FileHelper::splitFile($destFile)) {
				Prefix::truncate();
				foreach ($rawArray as $key => $line) {
					$pfx = new Prefix;
					$pfx->InternalUse = PrefixHelper::removeLeading($line[0]);
					$pfx->lon = $last_lon = ($line[1] != '') ? PrefixHelper::toCoords($line[1]) : $last_lon;
					$pfx->lat = $last_lat = ($line[2] != '') ? PrefixHelper::toCoords($line[2]) : $last_lat;
					$pfx->territory = $line[3];
					$pfx->prefix = $last_prefix = ($line[4] != '') ? PrefixHelper::maskToPattern($line[4]) : $last_prefix;
					$pfx->cq = $last_cq = ($line[5] != '') ? $line[5] : $last_cq;
					$pfx->itu = $last_itu = ($line[6] != '') ? $line[6] : $last_itu;
					$pfx->continent = $last_continent = ($line[7] != '') ? $line[7] : $last_continent;
					$pfx->tz = $last_tz = ($line[8] != '') ? $line[8] : $last_tz;
					$pfx->adif = $line[9];
					$pfx->province = $line[10];
					$pfx->mask = ($line[13] != '') ? PrefixHelper::maskToPattern($line[13]) : $last_prefix;
					$pfx->save();
				}

				return Redirect::to('config/prefix')->with('success','1');
			}

			return Redirect::to('config/prefix')->with('success','0');
		}
	}
}
