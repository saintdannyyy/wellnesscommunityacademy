<?php
    // Connect to prod database
    // $mysqli = new mysqli("localhost", "wellnes1_wellnes1", "@1ph@5t0rmerh05t", "wellnes1_ebooks");

    // Connect to the dev database
    $mysqli = new mysqli("localhost", "root", "", "wellness");

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
?>