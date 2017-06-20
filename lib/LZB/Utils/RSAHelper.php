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

class RSAHelper {

    /**
     * 加密
     * 
     * @param $params 待加密参数
     * @param $publicKey 商户公钥
     */
    public static function encrypt($params, $publicKey){
        $rawData = json_encode(self::_filterParam($params));

        $list = [];
        $step = 117;

        $publicKey = self::_getKey($publicKey);
        for($i = 0, $len = strlen($rawData); $i < $len; $i += $step){
            $data = substr($rawData, $i, $step);
            $encryted = '';

            openssl_public_encrypt($data, $encryted, $publicKey);
            $list[] = $encryted;
        }
        return base64_encode(join('', $list));
    }

    /**
     * 解密
     *
     * @param $encrytedData 待解密参数
     * @param $privateKey LZB私钥
     */
    public static function decrypt($encrytedData, $privateKey){
        $encrytedData = base64_decode($encrytedData);

        $list = [];
        $step = 128;
        if(strlen($privateKey) > 1000){
            $step = 256;
        }

        $privateKey = self::_getKey($privateKey);
        for($i = 0, $len = strlen($encrytedData); $i < $len; $i += $step){
            $data = substr($encrytedData, $i, $step);
            $decrypted = '';

            $ret = openssl_private_decrypt($data, $decrypted, $privateKey);
            if (!$ret) {
                return false;
            }            

            $list[] = $decrypted;
        }

        return json_decode(join('', $list), true);
    }

    /**
     * 加签
     * 
     * @param $params 待加签参数
     * @param $privateKey LZB私钥
     */
    public static function genSign($params, $privateKey){
        $params = self::_filterParam($params);
        ksort($params);
        $signStr = json_encode($params);
        $signStr = stripslashes($signStr);


        $privateKey = self::_getKey($privateKey);
        $privateKeyId = openssl_get_privatekey($privateKey);
        openssl_sign($signStr, $data, $privateKeyId);
        openssl_free_key($privateKeyId);
        
        return base64_encode($data);
    }

    /**
     * 验签
     *
     * @param $params 待验签参数
     * @param $sign 检验字符串
     * @param $publicKey 商户公钥
     */
    public static function checkSign($params, $sign, $publicKey){
        $params = self::_filterParam($params);
        if(isset($params['sign'])){
            unset($params['sign']);
        }
        ksort($params);

        $publicKey = self::_getKey($publicKey);

        $publicKeyId = openssl_get_publickey($publicKey);
        if(!$publicKeyId){
            return false;
        }

        $str = json_encode($params);
        $str = stripslashes($str);
        $ret = openssl_verify($str, base64_decode($sign), $publicKeyId, 'sha1WithRSAEncryption');
        openssl_free_key($publicKeyId);

        return $ret;
    }

    private static function _getKey($key){
        if(is_file($key)){
            return file_get_contents($key);
        }
        return $key;
    }
    
    private static function _filterParam($params){
        foreach($params as $key => $val){
            if(is_array($val)){
                $params[$key] = self::_filterParam($val);
            }elseif(empty($val) && !is_numeric($val)){
                unset($params[$key]);
                continue;
            }else{
                $params[$key] = strval($val);
            }
        }

        return $params;
    }

}
