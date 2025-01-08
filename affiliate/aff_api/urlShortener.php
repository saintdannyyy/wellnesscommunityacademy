<?php
require_once __DIR__ . '/../../config/loadENV.php';



function shortenUrl($longUrl, $apiKey) {
    $apiUrl = "https://api.tinyurl.com/create";
    
    $data = [
        "url" => $longUrl,
        "domain" => "tiny.one" // You can use other domains if available on your account
    ];
    
    $options = [
        "http" => [
            "header" => "Content-Type: application/json\r\n" .
                        "Authorization: Bearer $apiKey\r\n",
            "method" => "POST",
            "content" => json_encode($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($apiUrl, false, $context);
    
    if ($result === FALSE) {
        return "Error: Unable to shorten URL.";
    }
    
    $response = json_decode($result, true);
    return $response['data']['tiny_url'] ?? "Error: Unable to get shortened URL.";
}

// // Example usage
// $longUrl = "https://www.example.com/some/long/url";
// $apiKey = $_ENV['TINY_KEY']; 

// $shortUrl = shortenUrl($longUrl, $apiKey);
// echo "Shortened URL: $shortUrl";
?>
