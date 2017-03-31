<?php
// Copyright 2017 Liangzibao, Inc.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// See the License for the specific language governing permissions and
// limitations under the License.

namespace LZB\Utils;

class HttpHelper {

    const USER_AGENT = "LZB/Openapi SDK/v1.1.0(PHP)";
    const CONTENT_TYPE = "application/x-www-form-urlencoded;charset=utf-8";

    const HTTP_POST = 'POST';
    const HTTP_GET = 'GET';

    public static function request($url, $params, $method = self::HTTP_POST) {
        $ch = curl_init();

        $headers['User-Agent'] = self::USER_AGENT;
        $headers['Content-Type'] = self::CONTENT_TYPE;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method == self::HTTP_POST) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else {
            $url .= '?' . http_build_query($params);
        }
        curl_setopt($ch, CURLOPT_URL, $url);

        $response = curl_exec($ch);
        if (curl_errno($ch)){
            throw new Exception("The SDK request error: ". curl_error($ch));
        }

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200){
            throw new Exception("The SDK request error: ". $response);
        }
        curl_close($ch);

        return json_decode($response, true);
    }
    
    public static function buildRequestUrl($url, $params) {
        return $url . '?' . http_build_query($params);
    }

}
