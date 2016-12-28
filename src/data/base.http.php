<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';
require_once 'limit.data.php';
require_once 'format.json.php';


final class HTTP
{
	static public function request($url, $data = null, $method = 'POST')
	{
		$_result = null;
		
		if ($url) {
			$opts = array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'Accept' => 'Accept: text/plain',
					'timeout' => 3,
					//'proxy' => '',
					'content' => $data
				)
			);
			$context = stream_context_create($opts);
			$result = file_get_contents($url, false, $context, -1, 1024);

			$result = self::_utf8($result);	
			$result = JSON::decode($result);
		}
		return $result;
	}

	static public function param($keyedData)
	{
		$_result = array();
		$_results = '';
		foreach($keyedData as $key => $value) {
			$_result[] = "{$key}={$value}";
		}
		$_results = implode('&', $_result);
		return $_results;
	}
	
	static public function _utf8($data)
	{
		$cs = mb_detect_encoding($data);
		$result = iconv($cs, 'UTF-8', $data);
		
		//$data = urldecode($data);
		//$result = mb_convert_encoding($data, 'GB2312', 'UTF-8');
		
		return $result;
	}
}

?>