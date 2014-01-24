<?php

require_once "SiftClient.php";

class Sift {

    private static $instance;
    private static $apiKey;

    public static function init($apiKey) {
        self::$apiKey = $apiKey;
    }

    /**
     * Returns a singleton instance of the Sift client
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new SiftClient(self::$apiKey);
        }
        return self::$instance;
    }
}