<?php

namespace m039\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class ServerChecker {

    public function check() : bool {
        try {
            $client = new Client(["timeout" => 30]);
            $response = $client->request("GET", "https://m039.site/ping.php");
            if ($response->getStatusCode() !== 200)
                return false;

            $message = json_decode($response->getBody());

            return $message->code == "ok";
        } catch (ConnectException $e) {
            return false;
        } catch (ClientException $e) {
            return false;
        }
    }
    
}