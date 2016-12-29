<?php
// Handle ajax request from "bing.html?k={dev|user|find|sensor}&t={list|map|new|edit|del}[&kw={x}][&id={x}]"
// 6Harmonics Qige @ 2016.12.27
// verified by Qige @ 2016.12.27, v1.0.271216
define('TASKLET_ID', 'DATA');

// make sure no echo before header();
// ob_start();

'use strict';
date_default_timezone_set('PRC'); // set timezone
require_once 'base.filter.php';
require_once 'format.json.php';
require_once 'app.nms4.php';


// saved result
$_result = array();

// read user input
$GET = Filter::secureArray($_GET);
$_k = 'dev';
$_t = 'list';
$_id = 0;
$_kw = '';

// read $_REQUEST
if (is_array($GET)) {
	if (key_exists('k', $GET)) $_k = $GET['k'];
	if (key_exists('t', $GET)) $_t = $GET['t'];
	if (key_exists('id', $GET)) $_t = $GET['kw'];
	if (key_exists('kw', $GET)) $_t = $GET['id'];
}

$_env = array(
		'key' => $_k,
		'type' => $_t,
		'id' => $_id,
		'kw' => $_kw,
		'limit' => 50
);

$app = new AppNMS4($_env);
$_result = $app->exec();
//var_dump($_result);

// prepare OUTPUT
if (version_compare("5.3", PHP_VERSION, ">")) {
	$_resultString = JSON::encode($_result, JSON_UNESCAPED_UNICODE);
} else {
	$_resultString = JSON::encode($_result);
}


// connection desc
// ob_end_clean();
// header("Content-type: text/html; charset=utf-8");
// header("Cache-Control: no-cache");
// header("Pragma: no-cache");
// header("Connection: close");

echo $_resultString;

?>
