<?php
use MrJuliuss\Syntara\Controllers\BaseController;

class ConfController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$confs = Conf::withUser(Sentry::getUser()->id)->get();
		// dd($confs);
		$conf_arr = array();
		if($confs->count()) {
			foreach ($confs as $conf) {
				$conf_arr[$conf->name] = $conf->value;
			}
		}
		$this->layout = View::make('config.index')->with('confs',$conf_arr);
		$this->layout->title = trans('custom.welcome');

		$this->layout->breadcrumb = array(
			array(
				'title' => trans('custom.home'),
				'link'  => url('/'),
				'icon'  => 'glyphicon-home',
				),
			array(
				'title' => trans('custom.config'),
				'link'  => url('config'),
				'icon'  => 'glyphicon-cog',
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
		$uid = Sentry::getUser()->id;
		$input = Input::all();
		foreach ($input as $key => $value) {
			if (Conf::withUser($uid)->withName($key)->count()) {
				if ($key != '_token') {
					$conf = Conf::withUser($uid)->withName($key)->first();
					if (!empty($value)) {
						$conf->name = $key;
						$conf->value = $value;
						$conf->save();
					} else {
						$conf->delete();
					}
				}
			} else {
				if ($key != '_token') {
					if (!empty($value)) {
						$conf = new Conf;
						$conf->name = $key;
						$conf->value = $value;
						$conf->user_id = $uid;
						$conf->save();
					}					
				}
			}
		}
		if (Conf::withUser($uid)->withName('qDynamic')->count() && !isset($input['qDynamic'])) {
			$conf = Conf::withUser($uid)->withName('qDynamic')->delete();
		}
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
