<?php
    // Connect to database
    $mysqli = new mysqli("localhost", "wellnes1_wellnes1", "@1ph@5t0rmerh05t", "wellnes1_ebooks");

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
?>