<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';
require_once 'limit.data.php';


/**
 * Design Pattern: Singleton
 * @desc	only setup single database connection
 *
 * @param 	[$uri = 'localhost', [$user = 'root', [$passwd = '', \
 * 			[$database = 'nms3', [$port = 3306]]]]]
 * @return 	private $_instance
 * @final
 *
 * @author 	QZ
 * @tested 	2016.11.30 v1.1.301116a
 * @tested  2016.12.27 v1.1.271216b
 */
final class _Database implements IBaseDatabase
{
	static private $_instance;
	private $_connection;

	private function __construct($uri, $user, $passwd, $database, $port)
	{
		if (! function_exists('mysqli_connect')) return null;
		$this->_connection = @ mysqli_connect($uri, $user, $passwd, $database, $port);
	}

	final static public function getInstance($uri = 'localhost', $user = 'root', $passwd = '',
			$database = 'nms3', $port = 3306)
	{
		if (! self::$_instance instanceof self) {
			self::$_instance = new self($uri, $user, $passwd, $database, $port);
		}

		return self::$_instance;
	}

	public function getTunnel()
	{
		return $this->_connection;
	}

	public function close()
	{
		if ($this->_connection) {
			mysqli_close($this->_connection);
		}
		$this->_connection = null;
	}

	private function __clone() {}

	public function __destruct()
	{
		self::close();
	}
}


/**
 * @desc	base Data Accessing Object, learn from "php 23 design patterns"
 * @author 	QZ
 * @version 1.1.011216
 * @tested	2016.12.27 v1.1.271216b
 */
abstract class _baseDAO
{
	protected $_sqlCharset = 'set names "utf8"';

	public function configCharset()
	{
		if ($this->_pipe) {
			mysqli_query($this->_pipe, $this->_sqlCharset);
		}
	}

	private function query($sql = '')
	{
		$_result = null;

		if ($this->_pipe && $sql) {
			$_result = mysqli_query($this->_pipe, $sql);
		}

		return $_result;
	}

	public function update($keyId = 0, $keyedValueArray = null)
	{
		$_result = null;

		if ($keyId && is_array($keyedValueArray)) {
			$sql = "update {$this->_queryTableName} set ";

			$updates = array();
			foreach($keyedValueArray as $key => $value) {
				$updates = "{$key}='{$value}'";
			}

			$sql .= implode(',', $updates);
			$sql .= " where {$this->_queryKeyField}='{$keyId}'";

			$_result = $this->query($sql);
		}

		return $_result;
	}


	public function fetch($keyedConditionArray = null, $fieldArray = null)
	{
		$_result = null;

		$sql = "select ";
		if (is_array($fieldArray)) {
			$sql .= implode(',', $fieldArray);
		} else {
			$sql .= "*";
		}
		$sql .= " from {$this->_queryTableName} ";

		if (is_array($keyedConditionArray)) {
			$conditions = array();
			foreach($keyedConditionArray as $key => $value) {
				$conditions[] = "{$key}='{$value}'";
			}

			$sql .= "WHERE ";
			$sql .= implode(' and ', $conditions);

		}

		$result = $this->query($sql);
		while($result && $record = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$_result[] = $record;
		}

		return $_result;
	}
}

?>
