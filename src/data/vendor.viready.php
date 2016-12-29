<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';
require_once 'base.http.php';
require_once 'limit.sensor.php';


class ViReady {
	static private function vireadyWebService($url, $sensor = null)
	{
		$_result = array();
	
		$key = HTTP::param($sensor['token']);
		$data = HTTP::request($url, $key);
		//$data = iconv('UTF-8', 'GB2312', $data);
		
		$_result['sensor'] = array(
				'type' => 'dust',
				'data' => $data,
				'pos' => $sensor['pos']);
	
		return $_result;
	}
	
	static public function Update($keyedSensors = null)
	{
		$_result = array();
		
		if (is_array($keyedSensors) && key_exists('vendor', $keyedSensors) && $keyedSensors['vendor'] == 'viready') {
			$url = $keyedSensors['wservice'];
			$rawData = $formattedDev = $dev = array();
			foreach($keyedSensors['dev'] as $sensor) {
				$rawData = self::vireadyWebService($url, $sensor);
				
				if ($rawData) {
					// re-organize data
					//$formattedData = $rawData;
					$dev = $rawData['sensor']['data'];
					//var_dump($dev);
					$formattedDev['name'] = $dev['siteName'];
					$formattedDev['sn'] = $dev['deviceId'];
					$formattedDev['noise'] = self::findItemVal('噪声', $dev['result']);
					$formattedDev['pm'] = self::findPm($dev['result']);
					$formattedDev['temp'] = self::findTemp($dev['result']);
					$formattedDev['wind'] = self::findWind($dev['result']);
					$formattedDev['ts'] = $dev['result'][0]['dataTime'].', '.date('H:i:s');
					$formattedDev['pos']['lat'] = $sensor['pos']['lat'];
					$formattedDev['pos']['lng'] = $sensor['pos']['lng'];
					
					$_result[] = $formattedDev;
				}
			}
		}
		
		//var_dump($_result);
		return $_result;
	}
	
	static public function Sensors()
	{
		return array(
			'vendor' => 'viready',
			'type' => 'dust',
			'wservice' => 'http://dustmonitor.viready.com/app/rtd/device',
			'dev' => array(
					array(
						'token' => array(
							'loginName' => 'shidian',
							'token' => '977cc6e213db3656398f3684ef2cd7b6391e346b49d9ed2f63ebf03a447r7c14',
							'mn' => '47268242000012'
						),
						'pos' => array(
							'lat' => 39.92575,
							'lng' => 116.5612
						)
					),
					array(
						'token' => array(
							'loginName' => 'shidian',
							'token' => '977cc6e213db3656398f3684ef2cd7b6391e346b49d9ed2f63ebf03a447r7c14',
							'mn' => '47268242000018'
						),
						'pos' => array(
							'lat' => 39.92605,
							'lng' => 116.5599
						)
					)
			)	
		);
	}
	
	static private function findTemp($resultArray = null)
	{
		$temp = self::findItemVal('温度', $resultArray);
		$humidity = self::findItemVal('湿度', $resultArray);
		return $temp.', '.$humidity;
	}
	static private function findPm($resultArray = null)
	{
		$pm2dot5 = self::findItemVal('PM2.5', $resultArray);
		$pm10 = self::findItemVal('PM10', $resultArray);
		return $pm2dot5.', '.$pm10;
	}
	
	static private function findWind($resultArray = null)
	{
		$pressure = self::findItemVal('气压', $resultArray);
		$speed = self::findItemVal('风速', $resultArray);
		$direction = self::findItemVal('风向', $resultArray);
		return $pressure.', '.$speed.', '.$direction;
	}
	static private function findItemVal($item = '噪声', $resultArray = null)
	{
		$value = '';
		if (is_array($resultArray)) {
			foreach($resultArray as $result) {
				if ($result['pollutant'] == $item) {
					$value = $result['dataValue'];
					break;
				}
			}
		}
		return $value;
	}
}

?>