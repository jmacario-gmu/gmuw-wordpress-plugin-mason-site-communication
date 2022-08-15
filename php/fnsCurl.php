<?php

// Curl functions


function gmuw_msc_get_url_content($url) {

	// Function to return get response from a URL
    // https://stackoverflow.com/questions/17363545/file-get-contents-is-not-working-for-some-url

    // Parse URL
    $parts = parse_url($url);
    
    // Get host
    $host = $parts['host'];
    
    // Create cURL object
    $ch = curl_init();

    // Define request header
    $header = array('GET /1575051 HTTP/1.1',
        "Host: {$host}",
        'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language:en-US,en;q=0.8',
        'Cache-Control:max-age=0',
        'Connection:keep-alive',
        'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36',
    );

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    
    // Attempt request
    $result = curl_exec($ch);
    
    // Close cURL
    curl_close($ch);
    
    // Return value
    return $result;

}
