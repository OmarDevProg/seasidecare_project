<?php
use GuzzleHttp\Client;

class BackgroundRemover
{
    private $apiKey;
    private $client;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->client = new Client();
    }

    public function removeBackground(string $imageUrl): string
    {
        $response = $this->client->post('https://api.remove.bg/v1.0/removebg', [
            'headers' => [
                'X-Api-Key' => $this->apiKey,
            ],
            'form_params' => [
                'image_url' => $imageUrl,
                'size' => 'auto',
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            $imageData = $response->getBody()->getContents();
            $fileName = '/uploads/removed_bg_' . uniqid() . '.png';
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . $fileName, $imageData);

            return $fileName; // Return new image URL
        }

        return $imageUrl; // Fallback if API fails
    }
}