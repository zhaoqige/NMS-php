<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';
require_once 'limit.data.php';

/**
 * PHP7, PHP >= 5.2.0, PECL json >= 1.2.0
 * json_encode():	$opt need PHP >= 5.4
 * code sample: 
		if (version_compare("5.3", PHP_VERSION, ">"))
 *
 * @desc	json_encode(), json_decode()
 * @author 	QZ
 * @version 1.1.301116a/1.1.271216b
 * @verified 2016.11.30/2016.12.27
 */
final class JSON implements IDataFormat
{
	static public function decode($jsonString, $opt = true)
	{
		$_result = array();

		if ($jsonString) {
			// UTF-8 first 2 BOM bytes
			$jsonString = trim($jsonString, chr(239).chr(187).chr(191));
			if (function_exists('json_decode')) {
				$_result = @ json_decode($jsonString, $opt);
			} else {
				$_result['error'] = 'missing json_decode()';
			}
		}

		return $_result;
	}

	static public function encode($keyedArray, $opt = null)
	{
		$_result = '';

		if (function_exists('json_encode')) {
			$_result = @ json_encode($keyedArray, $opt);
		} else {
			$_result = 'missing json_encode()';
		}

		return $_result;
	}
}

?>
