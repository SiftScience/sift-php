<?php

abstract class Sift
{
    /**
     *	@var string the API key to be used for requests if none is provided to the constructor.
     */
    public static $api_key;

        /**
     *	@var string the account ID to be used for requests if none is provided to the constructor.
     */
    public static $account_id;

    const VERSION = '4.8.0';

    public static function setApiKey($api_key)
    {
        self::$api_key = $api_key;
    }

    public static function setAccountId($account_id)
    {
        self::$account_id = $account_id;
    }
}
