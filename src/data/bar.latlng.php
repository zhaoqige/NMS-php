<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';

/**
 * Calc Maps Center by Push($point) after Init()
 *
 * @author QZ
 * @version 1.2.291216c
 * @verified 2016.12.29
 */
class BarLatLng
{
	// default location: Beijing Office
	static private $_mapCenter = null;
	static private $_mapBorder = null;
	
	static public function Init()
	{
		self::$_mapCenter = array('zoom' => 15, 'lat' => 40.0492, 'lng' => 116.2902, 'gap' => array('lat' => 0, 'lng' => 0));
		self::$_mapBorder = array('lat' => array('min' => 0, 'max' => 0), 'lng' => array('min' => 0, 'max' => 0));
	}
	
	static public function Result()
	{
		self::calcZoom();
		return self::$_mapCenter;
	}
	
	static private function calcZoom()
	{
		$latMin = self::$_mapBorder['lat']['min']; $latMax = self::$_mapBorder['lat']['max'];
		$lngMin = self::$_mapBorder['lng']['min']; $lngMax = self::$_mapBorder['lng']['max'];
	
		$latGap = $latMax - $latMin;
		$lngGap = $lngMax - $lngMin;
		$gap = max($latGap, $lngGap);
	
		// FIXME: zoom level calibrate
		$zoom = 16;
		if ($gap > 0) {
			if ($gap < 0.12)	$zoom = 17;
			if ($gap < 0.07)	$zoom = 18;
			if ($gap < 0.03) 	$zoom = 19;
			if ($gap < 0.004) 	$zoom = 20;
			if ($gap < 0.0003) 	$zoom = 21;
		
			self::$_mapCenter['lat'] = ($latMin + $latMax) / 2;
			self::$_mapCenter['lng'] = ($lngMin + $lngMax) / 2;
		
			self::$_mapCenter['gap']['lat'] = (float) number_format($latGap, 8);
			self::$_mapCenter['gap']['lng'] = (float) number_format($lngGap, 8);
		}
	}
	
	static public function Push($point = null)
	{
		if (is_array($point) && key_exists('lat', $point) && key_exists('lng', $point)) {
			if (self::$_mapBorder['lat']['min'] == 0 || self::$_mapBorder['lat']['min'] > $point['lat']) {
				self::$_mapBorder['lat']['min'] = $point['lat'];
			}
			if (self::$_mapBorder['lat']['max'] == 0 || self::$_mapBorder['lat']['max'] < $point['lat']) {
				self::$_mapBorder['lat']['max'] = $point['lat'];
			}
			if ((! self::$_mapBorder['lng']['min']) || self::$_mapBorder['lng']['min'] > $point['lng']) {
				self::$_mapBorder['lng']['min'] = $point['lng'];
			}
			if ((! self::$_mapBorder['lng']['max']) || self::$_mapBorder['lng']['max'] < $point['lng']) {
				self::$_mapBorder['lng']['max'] = $point['lng'];
			}
		}
	}
}

?>
