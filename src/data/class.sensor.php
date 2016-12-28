<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';
require_once 'limit.data.php';
require_once 'limit.sensor.php';
require_once 'base.http.php';
require_once 'format.json.php';

/**
 * PHP7, PHP >= 5.2.0, PECL json >= 1.2.0
 *
 * @desc	Sensor Query
 * @author 	QZ
 * @version 1.0.281216
 * @verified 2016.12.28
 */
final class AppSensor implements ISingleton, ISensor
{
	static private $_instance = null;
	
	private $_query = null;
	private $_sensors = null;

	
	static public function getInstance()
	{
		if (! self::$_instance instanceof self) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	private function __construct()
	{
		//$this->_query = AppQuery::getInstance();
		
		$this->_sensors['sensor'][] = array(
			'vendor' => 'viready',
			'type' => 'dust',
			'url' => 'http://dustmonitor.viready.com/app/rtd/device',
			'dev' => array(
					array(
						'token' => array(
							'loginName' => 'shidian',
							'token' => '977cc6e213db3656398f3684ef2cd7b6391e346b49d9ed2f63ebf03a447r7c14',
							'mn' => '47268242000012'
						),
						'pos' => array(
							'lat' => 40.00001,
							'lng' => 115.9000009
						)
					),
					array(
						'token' => array(
							'loginName' => 'shidian',
							'token' => '977cc6e213db3656398f3684ef2cd7b6391e346b49d9ed2f63ebf03a447r7c14',
							'mn' => '47268242000018'
						),
						'pos' => array(
							'lat' => 40.000002,
							'lng' => 116.0001
						)
					)
			)	
		);
	}
	
	public function update($keyedParams = null)
	{
		$_result = array();
		
		foreach($this->_sensors['sensor'] as $sensor) {
			
			switch($sensor['vendor']) {
				case 'viready':
					$_result = $this->viready($sensor['url'], $sensor['dev']);
					break;
			}
		}
		
		return $_result;
	}
	
	
	private function viready($url, $sensors = null)
	{
		$_result = array();
		
		foreach($sensors as $sensor) {
			$key = HTTP::param($sensor['token']);
			$data = HTTP::request($url, $key);
			//$data = iconv('UTF-8', 'GB2312', $data);
			$_result['sensor']['dust'][] = array(
					'data' => $data,
					'pos' => $sensor['pos']);
		}
		
		return $_result;
	}
}
?>
