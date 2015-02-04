<?php

abstract class Sift
{
	/**
	 *	@var string the API key to be used for requests if none is provided to the constructor.
	 */
	public static $api_key;

	const VERSION = '1.2.0';

	public static function setApiKey($api_key)
	{
		self::$api_key = $api_key;
	}
}
