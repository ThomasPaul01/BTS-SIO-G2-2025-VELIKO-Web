<?php

namespace App\Request;

class Request
{
    public function RequestApi($url)
    {
        // Methode request donné Api Via url

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT => $_ENV['API_VELIKO_PORT'],
            CURLOPT_URL => $_ENV["API_VELIKO_URL"].$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);


    }
}