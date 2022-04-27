<?php

namespace App;

/**
 * Helper class/wrapper for cURL
 *
 * Class CurlHelper
 * @package App
 */
class CurlHelper
{
    /**
     * Send GET request to given url
     *
     * @param string $url
     * @return mixed
     * @throws \Exception
     */
    public static function get(string $url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_FAILONERROR => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json'
            ]
        ]);

        $result = curl_exec($curl);

        if (!$result) {
            throw new \Exception('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }

        curl_close($curl);

        return json_decode($result);
    }
}