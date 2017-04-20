<?php
use MrJuliuss\Syntara\Controllers\BaseController;

class SearchController extends BaseController {


	/**
	 * Display advanced search page
	 *
	 * @return Respond
	 */
	/*public function index() 
	{
		$currentUser = Sentry::getUser();
		$qsos = Qso::withUser($currentUser->id)
				->orderBy('id','DESC')
				->paginate('20');
		$input = array();
		$modes = $bands = array(0=>'--');
		foreach (Mode::all() as $mode) {
			$modes[$mode->id] = $mode->name;
		}
		foreach (Band::all() as $band) {
			$bands[$band->id] = $band->name.' - '.$band->low.' to '.$band->high;
		}
		$this->layout = View::make('tools.search')->with('qsos',$qsos)->with('bands',$bands)->with('modes',$modes)->with('input',$input);
		$this->layout->title = trans('custom.search');

		$this->layout->breadcrumb = array(
			array(
				'title' => trans('custom.home'),
				'link'  => url('/'),
				'icon'  => 'glyphicon-home',
				),
			array(
				'title' => trans('custom.search'),
				'link'  => url('search'),
				'icon'  => 'glyphicon-search',
				),
			);
	}*/

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$currentUser = Sentry::getUser();

		if ($input = Input::all()) {
			$qBand = (isset($input['qBand'])&&($input['qBand']>0)) ? $input['qBand'] : '%';
			$qMode = (isset($input['qMode'])&&($input['qMode']>0)) ? $input['qMode'] : '%';
			$qCall = ($input['qCall'] != '') ? '%'.$input['qCall'].'%' : '%';
			$qCountry = (isset($input['qCountry'])&&($input['qCountry']>0)) ? $input['qCountry'] : '%';
			$qCountry = (isset($input['qRegion'])&&($input['qRegion']>0)) ? $input['qRegion'] : $qCountry;
			$qNote = (isset($input['qNote'])&&($input['qNote']!='')) ? '%'.$input['qNote'].'%' : '%';
			$qFreq = (isset($input['qFreq'])&&($input['qFreq']!='')) ? '%'.$input['qFreq'].'%' : '%';
			$qRst = (isset($input['qRst'])&&($input['qRst']!='')) ? '%'.$input['qRst'].'%' : '%';
			$qDate = (isset($input['qDate'])&&($input['qDate']!='')) ? '%'.$input['qDate'].'%' : '%';
			if($input['qCq'] != '') {
				// $input = array('qCq'=>$input['qCq']);
				$qsos = Qso::join('customizations','qsos.id','=','customizations.qso_id')
							->where('user_id','=',$currentUser->id)
							->where('cq','like',$input['qCq'])
							->groupBy('qsos.id')
							->orderBy('qsos.id','DESC')
							->paginate('20');
			} elseif ($input['qItu'] != '') {
				// $input = array('qItu'=>$input['qItu']);
				$qsos = Qso::join('customizations','qsos.id','=','customizations.qso_id')
							->where('user_id','=',$currentUser->id)
							->where('itu','like',$input['qItu'])
							->groupBy('qsos.id')
							->orderBy('qsos.id','DESC')
							->paginate('20');
			} else {
				$qsos = Qso::join('prefix_qso','qsos.id','=','prefix_qso.qso_id')
							->where('prefix_qso.prefix_id','like',$qCountry)
							->where('user_id','=',$currentUser->id)
							->where('band_id','like',$qBand)
							->where('mode_id','like',$qMode)
							->where('call','like',$qCall)
							->where('comment','like',$qNote)
							->where('frequency','like',$qFreq)
							->where('rst','like',$qRst)
							->where('date','like',$qDate)
							->groupBy('qsos.id')
							->orderBy('qsos.id','DESC')
							->paginate('20');
			}
		} else {
			$input = array();
			$qsos = Qso::withUser($currentUser->id)
						->orderBy('id','DESC')
						->paginate('20');
		}
		
		$modes = $bands = array(0=>'--');
		foreach (Mode::all() as $mode) {
			$modes[$mode->id] = $mode->name;
		}
		foreach (Band::all() as $band) {
			$bands[$band->id] = $band->name.' - '.$band->low.' to '.$band->high;
		}
		$this->layout = View::make('tools.search')->with('qsos',$qsos)->with('bands',$bands)->with('modes',$modes)->with('input',$input);
		$this->layout->title = trans('custom.search');

		$this->layout->breadcrumb = array(
			array(
				'title' => trans('custom.home'),
				'link'  => url('/'),
				'icon'  => 'glyphicon-home',
				),
			array(
				'title' => trans('custom.search'),
				'link'  => url('search'),
				'icon'  => 'glyphicon-search',
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


}
