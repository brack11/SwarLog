<?php

use MrJuliuss\Syntara\Controllers\BaseController;

class HomeController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if (Sentry::check()) {
			return Redirect::to('logger');
		}	

		$this->layout = View::make('home.index');
		$this->layout->title = trans('custom.welcome');

		$this->layout->breadcrumb = array(
			array(
				'title' => trans('custom.home'),
				'link'  => url('/'),
				'icon'  => 'glyphicon-home',
				),
			);
	}


	/**
	 * menu for exchange data with eQSL
	 */
	public function toolsEqsl() {
		DB::connection()->disableQueryLog();
		$currentUser = Sentry::getUser();
		$confs = User::find($currentUser->id)->confs;
		$qsos = Qso::withUser($currentUser->id)->where('qsl_rcvd','!=','Y')->orderBy('id','DESC')->paginate('20');
		$new_qsos = Qso::withUser($currentUser->id)->where('qsl_rcvd','N')->get();
		$bands = array();
		foreach (Band::all() as $band) {
			$bands[$band->id] = $band->name.' - '.$band->low.' to '.$band->high;
		}
		$this->layout = View::make('tools.eqsl')->with('confs',$confs)->with('qsos',$qsos)->with('bands',$bands)->with('new_qsos',$new_qsos);
		$this->layout->title = trans('custom.eqsl');

		$this->layout->breadcrumb = array(
			array(
				'title' => trans('custom.home'),
				'link'  => url('/'),
				'icon'  => 'glyphicon-home',
				),
			array(
				'title' => trans('custom.eqsl'),
				'link'  => url('tools/eqsl'),
				'icon'  => 'glyphicon-globe',
				),
			);
	}


	/**
	 * Delivering eQSLs
	 */
	/*public function sendEqsl($qso = '') {
		if ($qso != '') {
			return QsoHelper::eQslSend($qso);
		}
		DB::connection()->disableQueryLog();
		$currentUser = Sentry::getUser();
		$qsos = Qso::withUser($currentUser->id)
			->where('qsl_rcvd','N')
			->get();
		return QsoHelper::eQslSend($qsos);
	}*/
}
