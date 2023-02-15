<?php

namespace App;

use DateTime;
/**
 * Date
 *
 * PHP version 7.3
 */
class Date
{
    /**
     * Return current date (Y-m-d)
	 */
	public static function getCurrentDate()
	{
		$today = new DateTime();
		return $today->format('Y-m-d');
	}
	
	public static function getLastDate()
	{
		$lastDate = new DateTime();
		return $lastDate->format('Y-m-t');
	}
}