<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';
require_once 'limit.data.php';
require_once 'limit.sensor.php';
require_once 'format.json.php';

require_once 'vendor.viready.php';

/**
 * PHP7, PHP >= 5.2.0, PECL json >= 1.2.0
 *
 * @desc	Sensor Query
 * @author 	QZ
 * @version 1.0.281216
 * @verified 2016.12.28
 */
final class AppSensor implements ISingleton
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
		$this->_sensors[] = ViReady::sensors();
	}
	
	public function update($keyedParams = null)
	{
		$_result = array();

		// get sensors array
		// call vendors own service to update sensor data,
		// with assigned format
		foreach($this->_sensors as $sensor) {
			switch($sensor['vendor']) {
				case 'viready':
					// fetch data via webservice
					// re-organize $data format
					$_result = ViReady::Update($sensor);
					break;
			}
		}
		
		//
		return $_result;
	}
}
?>
