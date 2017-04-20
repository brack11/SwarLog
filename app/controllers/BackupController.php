<?php
use MrJuliuss\Syntara\Controllers\BaseController;

class BackupController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->layout = View::make('config.backup');
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
				'link' => url('config/import'),
				),
			);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$username = Sentry::getUser()->username;
		$destinationPath = public_path().'/uploads/backups/'.$username.'/';
		Input::file('uplPfx')->move($destinationPath,'adif'.date('YmdHis').'.adi');
		// return Redirect::('backup');
		return Redirect::route('backup.index');
	}


	/**
	 * Processing restore of backup
	 * @param string filePath
	 * @return redirect to logger or false
	 */
	public function processRestore($file) {
		if (Session::token() === Input::get('_token')) {
			set_time_limit(300);
			DB::disableQueryLog();
			if(Input::get('clear')) Qso::where('user_id',Sentry::getUser()->id)->delete();
			$username = Sentry::getUser()->username;
			$fileArr = AdifHelper::parseFile(public_path().'/uploads/backups/'.$username.'/'.$file);

			$rowsPerChunk = 50;

			$fileChunks = array_chunk($fileArr, $rowsPerChunk);
			DB::transaction(function() use ($fileChunks){
				$modes = Mode::all();
				$result = array();
				foreach ($modes as $mode) {
					$result[$mode->id] = $mode->name;
				}
				foreach ($fileChunks as $chunk) {
					foreach ($chunk as $row) {
						if(!$row['call']) print_r($row);
						// dd($row);
						if (isset($row['qsl_sent']) && ($row['qsl_sent'] == 'q')) {
							$qsl_rcvd = 'R';
						} else if (isset($row['eqsl_qsl_sent']) && ($row['eqsl_qsl_sent'] == 'c')) {
							$qsl_rcvd = 'y';
						} else {
							$qsl_rcvd = 'i';
						}
						$qid = DB::table('qsos')
						->insertGetId(array(
							'call'=>trim(strtoupper($row['call'])),
							'date'=>(isset($row['qso_date']) && isset($row['time_on'])) ? date('Y-m-d H-i-s',strtotime($row['qso_date'].' '.$row['time_on'])) : '',
							'message'=>isset($row['qslmsg']) ? $row['qslmsg'] : '',
							'rst'=>isset($row['rst_sent']) ? $row['rst_sent'] : '',
							'qsl_via'=>isset($row['qsl_sent']) ? ($row['qsl_sent'] != 'i' ? 'D' : Conf::withUser(Sentry::getUser()->id)->withName('uQslVia')->first()->value) : 'E',
							/*'qsl_rcvd'=>isset($row['qsl_sent']) ? ((($row['qsl_sent'] == 'y') || ($row['eqsl_qsl_sent'] == 'y')) ? 'Y' : 'R') : 'I',*/
							'qsl_rcvd'=>$qsl_rcvd,
							'frequency'=>isset($row['freq']) ? $row['freq'] : '',
							'user_id'=>Sentry::getUser()->id,
							'band_id'=>isset($row['band']) ? (isset(Band::withName($row['band'])->first()->id) ? Band::withName($row['band'])->first()->id : 0) : 0,
							'mode_id'=>(isset($row['mode']) && array_search(strtoupper($row['mode']), $result)) ? array_search(strtoupper($row['mode']), $result) : 0)
						);
						// $call = explode('/',(trim(strtolower($row['call']))));
						// $prefix = PrefixHelper::prefixInfo($call[0], isset($call[1])?$call[1]:'', isset($call[2])?$call[2]:'');
						/*$country = Prefix::where('territory','like',$row['country'])->where('InternalUse','like','01%')->orwhere('InternalUse','like','06%')->first()->id;*/
						if (Prefix::where('territory','like',$row['country'])->exists()) {
							$country = Prefix::where('territory','like',$row['country'])->first()->id;
						} 
						if (Prefix::where('territory','like',$row['app_swarlog_territory'])->exists()) {
							$territory = Prefix::where('territory','like',$row['app_swarlog_territory'])->first()->id;
						} else {
							$territory = $country;
						}
						$prefixes = array($country,$territory);
						foreach ($prefixes as $value) {
							if (!is_array($value)) {
								DB::table('prefix_qso')
								->insert(array(
									'qso_id'=>$qid,
									'prefix_id'=>$value)
								);
							}
						}
						$custId = DB::table('customizations')
						->insertGetId(array(
							'lat'=>isset($row['lat']) ? $row['lat'] : '',
							'lon'=>isset($row['lon']) ? $row['lon'] : '',
							'itu'=>isset($row['ituz']) ? $row['ituz'] : '',
							'cq'=>isset($row['cqz']) ? $row['cqz'] : '',
							'grid'=>isset($row['gridsquare']) ? $row['gridsquare'] : '',
							'continent'=>isset($row['cont']) ? $row['cont'] : '',
							'prefix'=>isset($row['pfx']) ? $row['pfx'] : '',
							'qso_id'=>$qid)
						);
					}
				}
			});
		}
	}


	/**
	 * Create backup file
	 *
	 * @return fileName or null
	 */
	public function processBackup() {
		set_time_limit(300);
		DB::disableQueryLog();

		$adif = '';
		$fileName = 'backup'.date('YmdHis').'.adi';
		$destinationPath = public_path().'/uploads/backups/'.Sentry::getUser()->username.'/';
		$file = fopen($destinationPath.$fileName,'w');
		// $adif .= "ADIF 2 Export from SWARLOG\n";
		// $adif .= "Generated on ".date('Y-m-d')."\n";
		// $adif .= "<PROGRAMID:7>SWARLOG\n";
		// $adif .= "<ADIF_Ver:1>2\n";
		// $adif .= "<EOH>\n";
		// file_put_contents($file, $adif, LOCK_EX);
		fwrite($file, "ADIF 2 Export from SWARLOG\n");
		fwrite($file, "Generated on ".date('Y-m-d')."\n");
		fwrite($file, "<PROGRAMID:7>SWARLOG\n");
		fwrite($file, "<ADIF_Ver:1>2\n");
		fwrite($file, "<EOH>\n");
		User::find(Sentry::getUser()->id)->qsos()->chunk(50,function($qsos) use ($file){
			// dd($qsos);
			foreach ($qsos as $qso) {
				$row = AdifHelper::getAdif($qso);
				// file_put_contents($file, $row, FILE_APPEND | LOCK_EX);
				fwrite($file, $row);
			}
		});
		// file_put_contents($destinationPath.$fileName, $adif, LOCK_EX);

		fclose($file);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($file)
	{
		if (Session::token() === Input::get('_token')) {
			if(FileHelper::deleteFile('uploads/backups/'.Sentry::getUser()->username.'/'.$file)) return Redirect::route('logger.index');
		}
	}

}
