<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
/**
 * logger main page
 */
Route::get('call/{call}/{call2?}/{call3?}', 'PrefixController@callInfo');
Route::get('show/map', 'PrefixController@showMap');
Route::get('user/new', array(
    'as' => 'newUser',
    'uses' => 'MrJuliuss\Syntara\Controllers\UserController@getCreate')
);

Route::resource('','HomeController');

Route::resource('user', 'UserController');

/**
 * pages requiring authentication
 */
Route::group(array('before'=>'basicAuth|hasPermissions:create-log'), function(){
	/**
	 * configurations
	 */
	Route::get('territory/{level}/{id}', function($level,$id){
		return PrefixHelper::getTerritory($level,$id,true);
	});
	Route::post('refresh', function(){
		return QsoHelper::refreshInfo();
	});
	Route::post('request', function(){
		return QsoHelper::qslRequested();
	});
	Route::post('delete', 'QsoController@deleteQsos');
	Route::post('freq/band', function(){
		return QsoHelper::freqConv();
	});
	Route::get('get/grid/{lat}/{lon}', function($lat,$lon){
		return GridHelper::getStr($lat,$lon);
	});

	/**
	 * backup page
	 */
	Route::get('backup/{file}/delete', 'BackupController@destroy');
	Route::get('backup/{file}/process', 'BackupController@processRestore');
	Route::post('backup/backup', 'BackupController@processBackup');
	Route::resource('backup', 'BackupController');

	/**
	 * tools pages
	 */
	Route::get('tools/eqsl', 'HomeController@toolsEqsl');
	Route::get('search', 'SearchController@index');
	// Route::post('search', 'SearchController@toolsSearch');

	/**
	 * pages require administration permissions
	 */
	Route::group(array('before'=>'hasPermissions:permissions-management'), function() {
		Route::post('config/upload', 'PrefixController@uploadFile');
		Route::get('process/{file}', 'PrefixController@processFile');
		Route::get('delete/{file}',function($file){
			if(FileHelper::deleteFile('pfx/'.$file)) return Redirect::to('config/prefix');
		});
		Route::resource('config/prefix', 'PrefixController');
	});

	/**
	 * other resource controllers
	 */
	Route::resource('config', 'ConfController');
	Route::resource('logger', 'QsoController');
	

});


Route::get('test', function(){
	// $qsos = Qso::withUser(1)->get();
	// $qso = User::find(1)->qsos()->where('id','>','20')->get();
	// $qso = date('YmdHi', AdifHelper::getLastSync());
	// dd($qso);
	// $filename = "test.adi";
	// header("Cache-Control: public");
	// header("Content-Description: File Transfer");
	// // header("Content-Length: ". filesize("$filename").";");
	// header("Content-Disposition: attachment; filename=$filename");
	// header("Content-Type: application/octet-stream; "); 
	// header("Content-Transfer-Encoding: binary");

	// echo "filename";
	// $url = "http://www.eqsl.cc/qslcard/DownloadInBox.cfm?Username=sm6011swl&Password=ghjcnj&RcvdSince=201409052025";
	// $content = file_get_contents($url);
	// if (preg_match('/[a-zA-Z0-9\/]+\.adi/i', $content, $matches)) {
	// 	$adif = file_get_contents("http://www.eqsl.cc/qslcard/".$matches[0]);
	// 	$adif_final = AdifHelper::parseStr($adif);
	// 	print_r($adif_final[0]);
	// }
	$prefix = PrefixHelper::prefixInfo('f/ra6lo');
	$content = array();
	foreach ($prefix as $key => $value) {
		if (!is_array($value)) {
			$content[] = $value->id;
		}
	}
	// $date = '20070920';
	// $time = '191500';

	// $content = date('Ymd', AdifHelper::getLastSync());

	// $content = Request::segment(3);
	print_r($content);
});
