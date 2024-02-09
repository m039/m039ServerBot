<?php

namespace m039;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ServerChecker {

    public function check() : bool {
        try {
            $client = new Client();
            $response = $client->request("GET", "https://m039.site/ping.php");
            if ($response->getStatusCode() !== 200)
                return false;

            $message = json_decode($response->getBody());

            return $message->code == "ok";
        } catch (ClientException $e) {
            return false;
        }
    }
    
}