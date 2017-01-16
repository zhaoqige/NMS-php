<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';
require_once 'limit.data.php';
require_once 'bar.point.php';
require_once 'bar.latlng.php';
require_once 'class.nms4.php';
require_once 'class.sensor.php';

/**
 * App NMS4
 *
 * @desc		read top 50 devices for map/list
 * @author 		QZ
 * @version 	1.1.271216a
 * @verified 	sensor @ 2016.12.28, nms4: -
 */
final class AppNMS4 implements IApp
{
	private $_env = array();
	private $_envKey = 'dev', $_envType = 'list', $_envRespLimit = 50;
	
	// object that contains query resources
	// fit interface IDataQuery in 'limit.data.php'
	private $_nms4Res = null, $_sensorRes = null;
	
	
	public function __construct($env = null)
	{
		$this->initEnv($env);
	}
	
	private function initEnv($env = null)
	{
		if (is_array($env)) {
			$this->parseEnv($env);
		} else {
			$this->_env['key'] = $this->_envKey;
			$this->_env['type'] = $this->_envType;
			$this->_env['limit'] = $this->_envRespLimit;
		}
	}
	
	private function parseEnv($env = null)
	{
		if (is_array($env)) {
			if (key_exists('key', $env)) {
				$this->_env['key'] = $env['key'];
			}
			if (key_exists('type', $env)) {
				$this->_env['type'] = $env['type'];
			}
			if (key_exists('limit', $env)) {
				$this->_env['limit']['resp']['max'] = $env['limit'];
			}
		}
	}
	
	private function initNms4Res()
	{
		// check resource
		if (! $this->_nms4Res) {
			$this->_nms4Res = new NMS4Res();
		}
	}
	private function initSensorRes()
	{
		if (! $this->_sensorRes) {
			$this->_sensorRes = AppSensor::getInstance();
		}
	}
	
	public function exec($env = null)
	{
		$_result = null;
		
		
		$error = '';
		do { // start GOTO
			// set/init everything
			$this->parseEnv($env);
			
			// "bing.html?k={dev|user|find|sensor}&t={list|map|new|edit|del}[&kw={x}][&id={x}]"
			// prepare $data
			$condition = array();
			switch($this->_env['key']) {
				case 'sensor':
					$data = $this->readSensor($condition);
					$error = 'script: bad sensor';
					break;
				case 'user':
					$data = $this->fetchUser($condition);
					$error = 'script: bad nms4 user';
					break;
				case 'find':
					if (key_exists('kw', $env)) {
						$condition['kw'] = $env['kw'];
					}
				case 'map':
					$condition['bMap'] = true;
				case 'list':
				default:
					$data = $this->fetchDevices($condition);
					$error = 'script: bad nms4 dev';
					break;
			}

			// check result
			if (! is_array($data)) {
				break;
			}
			
			// save result
			$_result = $data;
			$error = '';
				
			// free up
			unset($this->_sensorRes);
			unset($this->_nms4Res);
			
		} while(0); // end GOTO
		
		if ($error) {
			$_result = array('error' => $error);
		}
		
		// debug use only
		//var_dump($_result);
		
		return $_result;
	}
	
	private function fetchDevices($keyedConditions)
	{
		$data = null;
		
		// check resource
		$this->initNms4Res();
		if ($this->_nms4Res) {
			$data = $this->_nms4Res->fetchUser($keyedConditions);
		}
		
		// re-assemble data here
		//
		
		return $data;
	}
	
	private function fetchUser($keyedConditions)
	{
		$data = null;
		
		// check resource
		$this->initNms4Res();
		if ($this->_nms4Res) {
			$data = $this->_nms4Res->fetchDevices($keyedConditions);
		}
		return $data;
	}
	
	private function readSensor($keyedCondition)
	{
		$data = null;
		
		// fetch sensor data
		$this->initSensorRes();
		if ($this->_sensorRes) {
			$sensors = $this->_sensorRes->update();
		}
		
		// check & calc map data
		if (is_array($sensors)) { //var_dump($sensors);
			
			// calc map data
			$map = array();
			BarPoint::Init();
			BarLatLng::Init();
			foreach($sensors as & $sensor) {
				BarPoint::Push($sensor, 5); // 5: ViReady Sensors
				BarLatLng::Push($sensor['pos']);
			}
			$map = BarLatLng::Result();
			
			$data['points'] = $sensors;
			$data['map'] = array(
				'zoom' => $map['zoom'],
				'center' => array(
					'lat' => $map['lat'],
					'lng' => $map['lng']
				)
			);
		}
		
		return $data;
	}
}

?>
