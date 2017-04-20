<?php 

class FileHelper {

	/**
	 * Lists files in directory
	 */
	public static function listFiles($dir) {
		File::isDirectory($dir) ?: File::makeDirectory($dir);
		$files = array_map('basename',File::allFiles($dir));
		$html = '<table class="table table-default" id="file-list">';
		foreach ($files as $key => $file) {
			$html .= '<tr>';
			$html .= '<td>';
			$html .= (string)$file;
			$html .= '</td>';
			$html .= '<td>';
			$html .= '<span class="mapShower fileDelete" data-file="'.(string)$file.'">'.trans('custom.delete').'</span>';
			$html .= '</td>';
			$html .= '<td>';
			$html .= '<span class="mapShower fileProcess" data-file="'.(string)$file.'">'.trans('custom.process').'</span>';
			$html .= '</td>';
			$html .= '<td>';
			$html .= Form::checkbox('clear', '1','',array('data-toggle'=>'tooltip','data-placement'=>'right','title'=>trans('custom.clear.data'),'data-container'=>'body'));
			$html .= '</td>';
			$html .= '</tr>';
		}
		$html .= '</table>';
		return $html;
	}

	public static function deleteFile($filename) {
		$destinationFile = $filename;
		File::delete($destinationFile);
		return true;
	}


	/*
	* Parse file pfx into array ready to upload to the database
	* @param string $file_path
	* @return array or false
	*/
	public static function splitFile($filePath) {
		$file = file($filePath);
		$return = array();
		foreach ($file as $key => $line) {
			if (!preg_match('/^#/',$line)) {
				$return[] = explode('|',$line);
			}

		}
		return count($return) ? $return : false;
	}
}