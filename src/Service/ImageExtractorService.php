<?php

namespace App\Service;

use DOMDocument;
use DOMXPath;

class ImageExtractorService
{
    public function extractImageUrl(string $htmlContent): ?string
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Suppress errors due to malformed HTML
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $imgNodes = $xpath->query('//img');

        if ($imgNodes->length > 0) {
            return $imgNodes->item(0)->getAttribute('src');
        }

        return null; // Return null if no image is found
    }
}