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
	public function update($keyedParams);
}

?>
