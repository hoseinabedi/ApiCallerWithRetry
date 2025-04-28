<?php
require 'vendor/autoload.php';

use Api\ApiCallerWithRetry;

/**
 * Example usage of the ApiCallerWithRetry class.
 */
function main() {
    // Example API endpoint (replace with a real API).
    $apiUrl = 'https://jsonplaceholder.typicode.com/todos/1'; //  Good for testing, always returns 200
    //$apiUrl = 'https://httpstat.us/500';  // Forcing a server error for testing
    //$apiUrl = 'https://httpstat.us/200'; // Forcing a success

    $apiOptions = [
        'method'  => 'GET', // Or 'POST', 'PUT', 'DELETE', etc.  Make sure you use the correct method.
        'headers' => [
            'Content-Type' => 'application/json',
            // Add any other headers your API requires.
        ],
        //'body'    => json_encode(['key' => 'value']), //  Include data for POST, PUT, etc.
    ];

    $maxRetries = 3;
    $retryDelay = 2000; // in milliseconds

    $apiCaller = new ApiCallerWithRetry($maxRetries, $retryDelay);

    try {
        $result = $apiCaller->callApiWithRetry($apiUrl, $apiOptions);
        echo 'API call successful: ';
        var_dump($result);
    } catch (Exception $e) {
        echo 'API call failed: ' . $e->getMessage() . PHP_EOL;
    }
}

// Run the main function.
main();
?>
