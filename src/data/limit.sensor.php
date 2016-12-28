<?php
(!defined('TASKLET_ID')) && exit('404: Page Not Found');

'use strict';


interface ISensor
{
	public function update($keyedParams);
}

?>
