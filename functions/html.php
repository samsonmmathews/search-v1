<?php

// Fetch all link URLs, only URLs in the hfre attribute
function html_fetch_urls($html)
{
    $urls = array();

    // Use regex to find all URLs in the HTML content that are in a href attribute
    $pattern = '/href=["\'](https?:\/\/[^\s"\'>]+)["\']/i';
    preg_match_all($pattern, $html, $matches);

    // Remove all trailing slashes from URLs
    $matches = array_map(function($url) {
        return rtrim($url, '/');
    }, $matches[1]);

    // Get unique URLs
    if (!empty($matches[1])) {
        $urls = array_unique($matches);
    }

    // Remove link to ICO, JS and CSS files
    $urls = array_filter($urls, function($url) {
        return !preg_match('/\.(ico|js|css)(\?|$)/i', $url);
    });

    return $urls;
}

// Fecth title from title tage from html
function html_fetch_title($html)
{
    $title = '';

    // Use regex to find the title tag content
    $pattern = '/<title>(.*?)<\/title>/is';
    if (preg_match($pattern, $html, $matches)) {
        $title = trim($matches[1]);
    }

    return $title;
}

// Fetch array of every content word in this html
function html_fetch_words( $html)
{
    $words = array();

    // Strip HTML tags and decode HTML entities
    $text = strip_tags($html);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Convert to lowercase
    $text = mb_strtolower($text, 'UTF-8');

    // Split text into words using regex
    preg_match_all('/\p{L}+/u', $text, $matches);

    if (!empty($matches[0])) {
        foreach ($matches[0] as $word) {
            if (strlen($word) > 1) { // Ignore single character words
                if (isset($words[$word])) {
                    $words[$word]++;
                } else {
                    $words[$word] = 1;
                }
            }
        }
    }

    return $words;

}