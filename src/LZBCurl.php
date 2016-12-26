<?php
#########################################################################
# File Name: src/LZBCurl.php
# Desc: 
# Author: Shaolin Wang
# mail: shaon86@163.com
# Created Time: Fri 23 Dec 2016 05:23:08 PM CST
#########################################################################

class LZBCurl{
    const POST = 'POST';
    const GET = 'GET';


    static public function request($url, $params, $method = LZBCurl::POST){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_USERAGENT, 'LZB/Openapi SDK/v1.0.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if($method == LZBCurl::POST){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }else{
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
