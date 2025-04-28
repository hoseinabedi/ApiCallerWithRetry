<?php

namespace Api;

use Exception;

/**
 * Class for making API calls with retry logic.
 */
class ApiCallerWithRetry {
    /**
     * @var int The maximum number of times to retry the API call.
     */
    private $retryLimit;

    /**
     * @var int The delay in milliseconds between retries.
     */
    private $delay;

    /**
     * Constructor for the ApiCallerWithRetry class.
     *
     * @param int $retryLimit The maximum number of times to retry the API call. Default is 3.
     * @param int $delay The delay in milliseconds between retries. Default is 1000.
     */
    public function __construct(int $retryLimit = 3, int $delay = 1000) {
        $this->retryLimit = $retryLimit;
        $this->delay = $delay;
    }

    /**
     * Function to make an API call with retry logic.
     *
     * @param string $url The URL of the API endpoint.
     * @param array $options Configuration options for the API call.  This can include method, headers, data, etc.
     * The 'method' option is required (e.g., 'GET', 'POST', 'PUT', 'DELETE').
     * Example:
     * [
     * 'method' => 'GET',
     * 'headers' => ['Content-Type' => 'application/json'],
     * 'body'    => json_encode(['key' => 'value']) // for POST, PUT
     * ]
     * @return mixed The API response data or false on failure after all retries.
     * @throws Exception If the 'method' is not provided in the options.
     */
    public function callApiWithRetry(string $url, array $options = []) {
        $attempt = 0;

        if (!isset($options['method'])) {
            throw new Exception("The 'method' option is required in the options array.");
        }

        while ($attempt < $this->retryLimit) {
            $attempt++;
            try {
                // Initialize cURL session
                $ch = curl_init($url);

                // Set cURL options based on the provided options array
                if (isset($options['headers'])) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->formatHeaders($options['headers']));
                }
                if (isset($options['method'])) {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $options['method']);
                }
                if (isset($options['body'])) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $options['body']);
                    curl_setopt($ch, CURLOPT_POST, true); // Ensure it's a POST if there's a body.  Will be overridden by CUSTOMREQUEST
                }
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10 second connection timeout
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);       // 30 second total timeout

                // Execute the cURL request
                $response = curl_exec($ch);

                // Check for cURL errors
                if (curl_errno($ch)) {
                    $error_message = curl_error($ch);
                    curl_close($ch);
                    throw new Exception("cURL error: " . $error_message); // Throw exception with cURL error
                }
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                // Check the HTTP status code.  200 is OK
                if ($httpCode >= 200 && $httpCode < 300) {
                    // Successful response.  Decode JSON if that's the content type.
                    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE); //moved inside the if
                    if (strpos($content_type, 'application/json') !== false) {
                        $responseData = json_decode($response, true); // true for associative array
                        return $responseData;
                    }
                    return $response; // Return raw response if not JSON.
                } else {
                    throw new Exception("HTTP error: $httpCode, Response: $response"); // Include response in error
                }


            } catch (Exception $e) {
                error_log("API call failed (Attempt $attempt/$this->retryLimit): " . $e->getMessage());
                if ($attempt < $this->retryLimit) {
                    // Wait for the specified delay before retrying.
                    usleep($this->delay * 1000); // Convert milliseconds to microseconds
                } else {
                    // After all retries, re-throw the exception, so the caller can handle it.
                    throw $e;
                }
            }
        }
        return false; //This line will never be reached, but added for clarity.
    }

    /**
     * Helper function to format headers for cURL.
     *
     * @param array $headers An associative array of headers (e.g., ['Content-Type' => 'application/json']).
     * @return array An array of formatted header strings (e.g., ['Content-Type: application/json']).
     */
    private function formatHeaders(array $headers): array {
        $formattedHeaders = [];
        foreach ($headers as $key => $value) {
            $formattedHeaders[] = "$key: $value";
        }
        return $formattedHeaders;
    }
}