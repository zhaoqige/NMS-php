<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';


/**
 * @desc	Limit for Sensor WebService
 * @author 	QZ
 * @version 1.0.281216
 */
interface ISensor
{
	public function Sensors();
	public function Update($keyedSensors = null);
}

/**
 * @desc	Limit for Sensor Vendor
 * @author 	QZ
 * @version 1.0.291216
 */
interface ISensorVendor
{
	static public function Sensors();
	static public function Update($keyedSensors = null);
}

?>
