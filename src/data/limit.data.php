<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';


interface ISingleton
{
	static public function getInstance();
}

/**
 * @desc	Database Limit
 * @author 	QZ
 * @version 1.1.301116
 */
interface IBaseDatabase extends ISingleton
{
	public function getTunnel();
	public function close();
}

interface IBaseDAO
{
	public function update($keyedArray);
	public function fetch($keyedArray);
}



interface IDataFormat
{
	static public function decode($string);
	static public function encode($keyedArray);
}


interface IApp
{
	public function __construct();
	public function exec($env = null);
}

interface IAppNMS4
{
	public function fetchDevices($keyedCondistions);
	public function setDevice($keyedRecord);
	
	public function fetchUser($keyedConditions);
	public function setUser($keyedRecord);
}

?>
