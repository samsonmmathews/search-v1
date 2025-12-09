<?php

// Fetch all link URLs, only URLs in the hfre attribute
function html_fetch_urls($html)
{
    $urls = array();

    // Use regex to find all URLs in the HTML content that are in a href attribute
    $pattern = '/href=["\'](https?:\/\/[^\s"\'>]+)["\']/i';
    preg_match_all($pattern, $html, $matches);

    if (!empty($matches[1])) {
        $urls = array_unique($matches[1]);
    }

    // Remove link to JS and CSS files
    $urls = array_filter($urls, function($url) {
        return !preg_match('/\.(js|css)(\?|$)/i', $url);
    });

    return $urls;
}