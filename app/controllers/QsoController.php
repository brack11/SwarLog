<?php
use MrJuliuss\Syntara\Controllers\BaseController;

class QsoController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{	
		if (Sentry::check()) {
			DB::connection()->disableQueryLog();
			$qsoCollection = Qso::withUser(Sentry::getUser()->id)->orderBy('id','DESC');
			$qsos = $qsoCollection->paginate('20');
			$lastqso = $qsoCollection->first();
			$modes = array();
			foreach (Mode::all() as $mode) {
				$modes[$mode->id] = $mode->name;
			}
			$bands = array();
			foreach (Band::all() as $band) {
				$bands[$band->id] = $band->name.' - '.$band->low.' to '.$band->high;
			}
			$this->layout = View::make('log.index')->with('qsos',$qsos)->with('modes',$modes)->with('bands',$bands)->with('lastqso',$lastqso);
			$this->layout->title = trans('custom.welcome');

			$this->layout->breadcrumb = array(
				array(
					'title' => trans('custom.home'),
					'link'  => url('/'),
					'icon'  => 'glyphicon-home',
					),
				array(
					'title' => trans('custom.logger'),
					'link'  => url('logger'),
					'icon'  => 'glyphicon-list',
					),
				);
		}
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if (Session::token() === Input::get('_token')) {
			DB::connection()->disableQueryLog();
			$input = Input::all();
			$qsoDate = $input['qDate'];
			$now = date('H:i:s', time());
			// dd($qsoDate);
			// dd(date('Y-m-d H:i:s', strtotime($qsoDate.' '.$input['qUtc'][0] ? : $now)));
			$output = array();
			$userId = Sentry::getUser()->id;
			$eqslDyn = Conf::withUser($userId)->withName('qDynamic')->exists();
			// dd($input);
			foreach ($input['qCall'] as $key=>$qCall) {
				if (QsoHelper::isUnique(array($qCall,date('Y-m-d H:i:s', strtotime($qsoDate.' '.$input['qUtc'][$key]))))) {
					if (!empty($input['qRst'][$key]) && (substr($input['qRst'][$key],0,1) != '0') && ($qCall != '')) {
						$prefix = PrefixHelper::prefixInfo($qCall);
						$content = array();
						foreach ($prefix as $value) {
							if (!is_array($value)) {
								$content[] = $value->id;
							}
						}
						if(empty($content)) $content = array(1);
						$qso = new Qso;
						$qso->user_id = $userId;
						$qso->call = strtoupper($qCall);
						$qso->date = date('Y-m-d H:i:s', strtotime($qsoDate.' '.$input['qUtc'][$key] ? : $now));
						$qso->message = strtoupper(trans('custom.please.confirm').' '.implode(' ',PrefixHelper::notEmpty($input['qCall2'])));
						$qso->rst = $input['qRst'][$key];
						$qso->comment = $input['qNote'][$key];
						$qso->frequency = $input['qFreq'];
						$qso->qsl_via = (Conf::withUser($qso->user_id)->withName('uQslVia'))?Conf::withUser($qso->user_id)->withName('uQslVia')->first()->value:'E';
						$qso->qsl_rcvd = 'N';
						$qso->band_id = $input['qBand'];
						$qso->mode_id = $input['qMode'];
						$qso->save();

						$qso->prefixes()->sync($content);

						// $input['qPrefix'] ? $qso->prefixes()->sync($input['qPrefix'][$qCall]) : $qso->prefixes()->sync(array(1));

						if($eqslDyn) QsoHelper::eQslSend($qso);
					}
				}
			}

			foreach ($input['qCall2'] as $key2=>$qCall2) {
				if (QsoHelper::isUnique(array($qCall2,date('Y-m-d H:i:s', strtotime($qsoDate.' '.$input['qUtc'][$key]))))) {
					if (!empty($input['qRst2'][$key2]) && (substr($input['qRst2'][$key2],0,1) != '0') && ($qCall2 != '')) {
						// dd($input['qRst2'][$key2]);
						$prefix2 = PrefixHelper::prefixInfo($qCall2);
						$content2 = array();
						foreach ($prefix2 as $value2) {
							if (!is_array($value2)) {
								$content2[] = $value2->id;
							}
						}
						if(empty($content2)) $content2 = array(1);
						$qso2 = new Qso;
						$qso2->user_id = $userId;
						$qso2->call = strtoupper($qCall2);
						$qso2->date = date('Y-m-d H:i:s', strtotime($qsoDate.' '.$input['qUtc'][$key2] ? : $now));
						$qso2->message = strtoupper(trans('custom.please.confirm').' '.implode(' ',PrefixHelper::notEmpty($input['qCall'])));
						$qso2->rst = $input['qRst2'][$key2];
						$qso2->comment = $input['qNote2'][$key2];
						$qso2->frequency = $input['qFreq'];
						$qso2->qsl_via = (Conf::withUser($qso2->user_id)->withName('uQslVia'))?Conf::withUser($qso2->user_id)->withName('uQslVia')->first()->value:'E';
						$qso2->qsl_rcvd = 'N';
						$qso2->band_id = $input['qBand'];
						$qso2->mode_id = $input['qMode'];
						$qso2->save();

						$qso2->prefixes()->sync($content2);

						// $input['qPrefix'] ? $qso2->prefixes()->sync($input['qPrefix'][$qCall2]) : $qso->prefixes()->sync(array(1));

						if($eqslDyn) QsoHelper::eQslSend($qso2);
					}
				}
			}
		}
		return Redirect::to('logger');			
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
		if(Sentry::check()) {
			$qso = Qso::find($id);
			if (Sentry::getUser()->id != $qso->user_id) {
				return Redirect::to('logger');
			}
			$customization = $qso->customization;
			$modes = array();
			foreach (Mode::all() as $mode) {
				$modes[$mode->id] = $mode->name;
			}
			$bands = array();
			foreach (Band::all() as $band) {
				$bands[$band->id] = $band->name.' - '.$band->low.' to '.$band->high;
			}

			$this->layout = View::make('home.edit')->with('qso',$qso)->with('customisation',$customization)->with('modes',$modes)->with('bands',$bands);
			$this->layout->title = strtoupper($qso->call);

			$this->layout->breadcrumb = array(
				array(
					'title' => trans('custom.home'),
					'link'  => url('/'),
					'icon'  => 'glyphicon-home',
					),
				array(
					'title' => trans('custom.logger'),
					'link'  => url('logger'),
					'icon'  => 'glyphicon-list',
					),
				array(
					'title' => trans('custom.edit'),
					'link'  => URL::route('logger.edit', $id),
					'icon'  => 'glyphicon-pencil',
					),
				);
		}
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::all();
		if (Session::token() === $input['_token']) {
			$validation = Validator::make($input, Qso::$rules);
			if($validation->fails()) return Redirect::back()->withInput()->withErrors($validation->messages());
			// dd($validation->messages());

			$qso = Qso::find($id);
			// dd($qso);
			$qso->date = date('Y-m-d H:i:s', strtotime($input['eDate']));
			$qso->mode_id = $input['eMode'];
			$qso->rst = $input['eRst'];
			$qso->message = $input['eMsg'];
			$qso->qsl_via = $input['eQvia'];
			$qso->qsl_rcvd = $input['eQrcvd'];
			$qso->comment = $input['eNote'];
			$qso->address = $input['eAddress'];
			$qso->frequency = $input['eFreq'];
			$qso->band_id = $input['eBand'];
			$qso->call = $input['eCall'];
			$qso->prefixes()->sync(array($input['eRegion'],$input['eCountry']));

			if($qso->customization) {
				$qso->customization()->update(
					array(
						'lat'	=> $input['eLat'],
						'lon'	=> $input['eLon'],
						'itu'	=> $input['eItu'],
						'cq'	=> $input['eCq'],
						'grid'	=> $input['eGrid'],
						'iota'	=> $input['eIota']
					));
				// dd($customization);

				// $qso->customization->save();
			} else {
				$customization = new Customization(
					array(
						'lat'	=> $input['eLat'],
						'lon'	=> $input['eLon'],
						'itu'	=> $input['eItu'],
						'cq'	=> $input['eCq'],
						'grid'	=> $input['eGrid'],
						'iota'	=> $input['eIota']
					));
				$qso->customization()->save($customization);
			}

			$qso->save();

			return Redirect::to('logger/'.$id.'/edit');
		}
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function deleteQsos() 
	{
		$qsos = Input::all();
		foreach ($qsos as $qso) {
			Qso::destroy($qso);
		}
	}

}
