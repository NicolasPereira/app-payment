<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class NotificationService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://o4d9z.mocklab.io/'
        ]);
    }

    public function sendNotification()
    {
        $url = 'notify';
        try {
            $result = $this->client->request('GET', $url);
            return json_decode($result->getBody(), true);
        }catch (GuzzleException $exception) {
            return ['message' => 'failure'];
        }
    }
}
