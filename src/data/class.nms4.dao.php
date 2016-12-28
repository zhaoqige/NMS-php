<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';
require_once 'limit.data.php';
require_once 'abs.data.db.php';

/**
 * PHP7, PHP >= 5.2.0, PECL json >= 1.2.0
 *
 * @desc	App Database
 * @author 	QZ
 * @version 1.1.301116a
 * @verified 2016.11.30
 */
final class NMS4DAO extends _baseDAO implements IBaseDAO
{
	static private $_instance = null;
	private $_connection;
	
	protected $_pipe;
	protected $_queryTableName = '', $_queryKeyField = '';
	
	private $_uri = 'localhost',
	$_port = 3306,
	$_user = 'root',
	$_passwd = '',
	$_database = 'nms3';
	
	
	private $_profile = null;
	
	
	static public function getInstance()
	{
		if (! self::$_instance instanceof self) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	private function __construct()
	{
		$this->_connection = _Database::getInstance($this->_uri, $this->_user, $this->_passwd,
				$this->_database, $this->_port);
		if ($this->_connection) {
			$this->_pipe = $this->_connection->getTunnel();
		}
	}
	
	public function close()
	{
		$this->_connection->close();
		unset($this->_connection);
		unset($this->_pipe);
	}
	
	public function __destruct()
	{
		self::close();
	}
	
	public function setTable($table = '')
	{
		$this->_queryTableName = $table;
	}
}
?>
