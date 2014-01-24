<?php

class Sift {
    const DEFAULT_TIMEOUT = 2;

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

    public static function track($event, $properties, $timeout = self::DEFAULT_TIMEOUT, $path = null) {
        self::getInstance().track($event, $properties, $timeout, $path);
    }

    public static function score($userId, $timeout = self::DEFAULT_TIMEOUT) {
        self::getInstance().score($userId, $timeout);
    }

    public static function label($userId, $properties, $timeout = self::DEFAULT_TIMEOUT) {
        self::getInstance().label($userId, $properties, $timeout);
    }
}