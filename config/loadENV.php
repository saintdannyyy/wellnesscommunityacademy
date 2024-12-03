<?php
// Path to your .env file
$envFile = __DIR__ . '/../.env';

// Check if the .env file exists
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Read file and ignore empty lines

    foreach ($lines as $line) {
        // Trim spaces around the line and skip if the line is empty or a comment
        $line = trim($line);

        // Skip lines that are comments (starting with #) or are empty
        if (empty($line) || $line[0] == '#') {
            continue;
        }

        // Ensure the line contains an equal sign
        if (strpos($line, '=') !== false) {
            // Split the line into key and value
            list($key, $value) = explode('=', $line, 2);

            // Remove surrounding quotes (if any)
            $key = trim($key);
            $value = trim($value, '"'); // Remove double quotes around value if present

            // Set the environment variable
            putenv("$key=$value");
            $_ENV[$key] = $value; // Optionally, set the value in the $_ENV superglobal for access in PHP
        }
    }
} else {
    // If the .env file doesn't exist, show an error
    die('Error: .env file not found.');
}
?>