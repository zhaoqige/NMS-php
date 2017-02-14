<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';


/**
 * @desc	Design Patten: Singleton
 * @author 	QZ
 * @version 1.0.281216
 */
interface ISingleton
{
	static public function getInstance();
}


/**
 * @desc	Database Connections
 * @author 	QZ
 * @version 1.1.301116
 */
interface IBaseDatabase extends ISingleton
{
	public function getTunnel();
	public function close();
}


/**
 * @desc	Basic Data Queries
 * 
 * @author 	QZ
 * @version 1.1.281216
 */
interface IBaseDAO
{
	public function update($keyedParams);
	public function fetch($keyedParams);
}


/**
 * @desc	json/xml/csv/ini
 * @author 	QZ
 * @version 1.1.281216
 */
interface IDataFormat
{
	static public function decode($string);
	static public function encode($keyedArray);
}


/**
 * @desc	ajax/http request
 * @author 	QZ
 * @version 1.1.281216
 */
interface IApp
{
	public function __construct();
	public function exec($keyedParams);
}


/**
 * @desc	Network Management System
 * @author 	QZ
 * @version 1.0.281216
 */
interface INMS4
{
	public function fetchDevices($keyedCondistions);
	public function setDevice($keyedRecord);
	
	public function fetchUser($keyedConditions);
	public function setUser($keyedRecord);
}

?>
