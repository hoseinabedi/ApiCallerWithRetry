# ApiCallerWithRetry

ApiCallerWithRetry is a PHP library that provides a robust solution for making API calls with built-in retry and delay capabilities. It's designed to enhance the reliability of your API interactions by automatically handling temporary failures and network issues.

## Features

- Make API calls with automatic retry on failure
- Configurable maximum number of retries
- Adjustable delay between retry attempts
- Support for various HTTP methods (GET, POST, PUT, DELETE, etc.)
- Customizable request headers and body
- Easy integration with existing PHP projects

## Requirements

- PHP 7.0 or higher
- Composer for dependency management

## Installation

You can install the ApiCallerWithRetry library via Composer. Run the following command in your project directory:

```bash
composer dump-autoload
```
## Usage

Here's a basic example of how to use ApiCallerWithRetry:

```bash
use Api\ApiCallerWithRetry;

// Configure the API call
$apiUrl = 'https://api.example.com/data';
$apiOptions = [
    'method'  => 'GET',
    'headers' => [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer your-token-here'
    ],
];

// Set retry parameters
$maxRetries = 3;
$retryDelay = 2000; // milliseconds

// Create an instance of ApiCallerWithRetry
$apiCaller = new ApiCallerWithRetry($maxRetries, $retryDelay);

try {
    $result = $apiCaller->callApiWithRetry($apiUrl, $apiOptions);
    echo 'API call successful: ';
    print_r($result);
} catch (Exception $e) {
    echo 'API call failed: ' . $e->getMessage() . PHP_EOL;
}
```

## Configuration

You can customize the behavior of ApiCallerWithRetry by adjusting the following parameters:

- `$maxRetries`: The maximum number of retry attempts before giving up.
- `$retryDelay`: The delay (in milliseconds) between retry attempts.

## API Reference
### ApiCallerWithRetry Class
#### `__construct(int $maxRetries, int $retryDelay)`

Creates a new instance of ApiCallerWithRetry.

- `$maxRetries`: Maximum number of retry attempts.
- `$retryDelay`: Delay between retries in milliseconds.

#### `callApiWithRetry(string $url, array $options)`

Makes an API call with retry capability.

- `$url`: The URL of the API endpoint.
- `$options`: An array of options for the API call (method, headers, body, etc.).

Returns the API response on success. Throws an exception if all retry attempts fail.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project has no license and feel free to use it any where

## Support

If you encounter any problems or have any questions, please open an issue on the GitHub repository.

## Acknowledgements

- Thanks to all contributors who have helped to improve this library.
- Inspired by the need for robust API communication in PHP applications.

