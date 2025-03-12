<?php
// Initialize cURL
$ch = curl_init();

// Set the URL
curl_setopt($ch, CURLOPT_URL, "http://localhost/user/signup.php");

// Enable POST request
curl_setopt($ch, CURLOPT_POST, 1);

// Set POST data
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    "key1" => "1",
    "key2" => "1",
    "key3" => "1",
    "key4" => "1"
]));

// Return response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // Handle response
    echo "Response: " . $response;
}

// Close cURL session
curl_close($ch);
?>
