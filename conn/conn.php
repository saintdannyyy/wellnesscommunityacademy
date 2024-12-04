<?php
require_once __DIR__ . '../../config/loadENV.php';
    if ($_ENV['APP_ENV'] === 'dev') {
        $mysqli = new mysqli("localhost", "root", "", "wellness");
    } else {
        $mysqli = new mysqli("localhost", "wellnes1_wellnes1", "@1ph@5t0rmerh05t", "wellnes1_ebooks");
    }
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
?>