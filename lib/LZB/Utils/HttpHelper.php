<?php

class HttpHelper {

    const USER_AGENT = "LZB/Openapi SDK/v1.0.0(PHP)";
    const CONTENT_TYPE = "application/x-www-form-urlencoded;charset=utf-8";

    const HTTP_POST = 'POST';
    const HTTP_GET = 'GET';

    static public function request($url, $params, $method = self::POST){
        $ch = curl_init();

        $headers['User-Agent'] = self::USER_AGENT;
        $headers['Content-Type'] = self::CONTENT_TYPE;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method == self::POST) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else {
            $url .= '?' . http_build_query($params);
        }
        curl_setopt($ch, CURLOPT_URL, $url);

        $response = curl_exec($ch);
        if(curl_errno($ch)){
            throw new Exception("The SDK request error: ". curl_error($ch));
        }

        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200){
            throw new Exception("The SDK request error: ". $response);
        }
        curl_close($ch);

        return json_decode($response, true);
    }
    
}
