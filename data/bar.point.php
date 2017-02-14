<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';

/**
 * Calc Point Levels
 *
 * @author QZ
 * @version 1.1.301116a
 * @verified 2016.11.30
 */
class BarPoint
{
	// default SNR calc fix
	static private $_snrGroundFix = 8;
	
	// point statistics
	static private $_pointStat = null;
	
	
	static public function Init()
	{
		self::$_snrGroundFix = 8;
		self::$_pointStat = array(
			'bad' => 0,
			'weak' => 0,
			'normal' => 0,
			'strong' => 0,
			'total' => 0
		);
	}
	
	static public function Result()
	{
		return null;
	}
	
	static public function Push(& $point, $type = 0)
	{
		$level = 0;

		switch($type) {
			case 5:
				$level = self::calcPointByViready($point);
				break;
			case 4:
				$level = self::calcPointByPER($point);
				break;
			case 3:
				$level = self::calcPointByNoise($point);
				break;
			case 2:
				$level = self::calcPointBySNR($point);
				break;
			case 1:
			default:
				$level = self::calcPointByThrpt($point);
				break;
		}
	
		if ($level >= 7) 	$level = 7;
		if ($level < 0)		$level = 0;
		switch($level) {
			case 7:
			case 6:
			case 5:
			case 4:
				self::$_pointStat['strong'] ++;
				break;
			case 3:
			case 2:
				self::$_pointStat['normal'] ++;
				break;
			case 1:
				self::$_pointStat['weak'] ++;
				break;
			case 0:
			default:
				self::$_pointStat['bad'] ++;
				break;
		}
		$point['level'] = $level;
	}
	
	
	// TODO: judge bar for ViReady Sensors
	// just noise/pm2.5/pm10, etc.
	// default return "Perfect"
	static private function calcPointByViready(& $point)
	{
		$point['status'] = 'Perfect';
		return 0;
	}
	
	static private function calcPointByThrpt($point)
	{
		$level = 0;
	
		if (is_array($point) && key_exists('tx', $point) && key_exists('rx', $point)) {
			// thrpt = txthrpt + rxthrpt
			$thrpt = $point['tx'] + $point['rx'];
			
			if ($thrpt < 0.3)						$level = 0;
			if ($thrpt >= 0.3 && $thrpt < 0.8) 		$level = 1;
			if ($thrpt >= 0.8 && $thrpt < 1.5) 		$level = 2;
			if ($thrpt >= 1.5 && $thrpt < 2.0) 		$level = 3;
			if ($thrpt >= 2.0 && $thrpt < 2.5) 		$level = 4;
			if ($thrpt >= 2.5 && $thrpt < 3.0) 		$level = 5;
			if ($thrpt >= 3.0 && $thrpt < 4.0) 		$level = 6;
			if ($thrpt >= 4.0) 						$level = 7;
		}
		return $level;
	}
	
	static private function calcPointBySNR($point)
	{
		$level = 0;
		
		if (is_array($point) && key_exists('signal', $point) && key_exists('noise', $point)) {
			// snr calibrate
			$snr = $point['signal'] - $point['noise'];
			$snr -= self::$_snrGroundFix;
		
			if ($snr < 6) 							$level = 0;
			if ($snr >= 6 && $snr < 12) 			$level = 1;
			if ($snr >= 12 && $snr < 18) 			$level = 2;
			if ($snr >= 18 && $snr < 24) 			$level = 3;
			if ($snr >= 24 && $snr < 30) 			$level = 4;
			if ($snr >= 30 && $snr < 36) 			$level = 5;
			if ($snr >= 36 && $snr < 42) 			$level = 6;
			if ($snr >= 42) 						$level = 7;
		
		}
		return $level;
	}
	static private function calcPointByNoise($point)
	{
		$level = 0;

		if (is_array($point) && key_exists('noise', $point)) {
			// unit: dBm
			$noise = $point['noise'];
		
			if ($noise >= -60) 						$level = 0;
			if ($noise < -60 && $noise >= -70) 		$level = 2;
			if ($noise < -70 && $noise >= -80) 		$level = 3;
			if ($noise < -80 && $noise >= -90) 		$level = 4;
			if ($noise < -90 && $noise >= -95) 		$level = 5;
			if ($noise < -95)				 		$level = 6;
		}	
		return $level;
	}
	static private function calcPointByPER($point)
	{
		// TODO: add PER support for ART2 test
		return 7;
	}
}

?>
