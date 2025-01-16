<?php
require_once __DIR__ . '../../config/loadENV.php';
    if ($_ENV['APP_ENV'] === 'dev') {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $mysqli = new mysqli($_ENV['DB_DEV_HOST'], $_ENV['DB_DEV_USER'], $_ENV['DB_DEV_PWD'], $_ENV['DB_DEV_NAME']);
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);
        $mysqli = new mysqli($_ENV['DB_LIVE_HOST'], $_ENV['DB_LIVE_USER'], $_ENV['DB_LIVE_PWD'], $_ENV['DB_LIVE_NAME']);
    }
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
?>