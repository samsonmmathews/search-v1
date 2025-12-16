<?php

/*
 * URL Functions
 * 
 */

/*
 * Check if a URL exists and is accessible
 */
function url_exists($url)
{

    // Validate URL format first
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    // Initialize cURL
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_NOBODY, true); // Don't download the body
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout after 10 seconds
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Don't verify SSL (for testing)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Execute the request
    curl_exec($ch);
    
    // Get the HTTP response code
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Close cURL
    curl_close($ch);
    
    // Check if the response code indicates success (2xx or 3xx)
    return ($http_code >= 200 && $http_code < 400);
    
}

function url_status($url)
{

    // Return false if not a valid URL
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $headers = get_headers($url);
    if ($headers && isset($headers[0])) {
        preg_match('/\d{3}/', $headers[0], $matches);
        if (isset($matches[0])) {
            return (int)$matches[0];
        }
    }
    return null;
}

function url_content($url)
{
    $options = array(
        'http' => array(
            'method'  => 'GET',
            'header'=>  "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3\r\n"
        )
    );
    $context  = stream_context_create($options);
    $content = @file_get_contents($url, false, $context);
    return $content;
}

// Check if domain contains brickmmo.com or codeadam.ca
function url_check_domain($url)
{

    $parsed_url = parse_url($url);
    if (isset($parsed_url['host'])) {
        $host = $parsed_url['host'];
        if (preg_match('/\.?brickmmo\.com$/', $host) || preg_match('/\.?codeadam\.ca$/', $host)) {
            return true;
        }
    }
    return false;

}

function url_clean($url)
{

    return $url;

}

function url_get_redirect($url)
{
    $headers = get_headers($url, 1);
    if ($headers !== false && isset($headers['Location'])) {
        return is_array($headers['Location']) ? end($headers['Location']) : $headers['Location'];
    }
    return null;
}