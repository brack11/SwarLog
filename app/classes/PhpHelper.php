<?php 
/**
* generally used functions
*/
class PhpHelper
{
	/**
	 * Check if array is assoc
	 */
	public function is_assoc($array) {
		return (bool)count(array_filter(array_keys($array), 'is_string'));
	}
}
?>