<?php

require_once "SiftClient.php";

class Sift {

    private static $instance;
    private static $apiKey;

    /**
     * Initialize Sift with an api key
     */
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

    public static function track($event, $properties, $timeout = 2, $path = null) {
        self::getInstance().track($event, $properties, $timeout, $path);
    }

    public static function score($userId, $timeout = 2) {
        self::getInstance().score($userId, $timeout);
    }

    public static function label($userId, $properties, $timeout = 2) {
        self::getInstance().label($userId, $properties, $timeout);
    }
}