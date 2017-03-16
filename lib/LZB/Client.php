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

namespace LZB;

class Client {

    private $_baseUrl;
 
    private $_privateKey;

    private $_lzbPublicKey;

    private $_appKey;

    private $_version = "1.0.0";

    private $_format = "JSON";

    private $_charset = "UTF-8";

    private $_signType = "RSA";

    public function __construct($baseUrl, $privateKey, $lzbPublicKey, $appKey) {
        $this->_baseUrl = $baseUrl;
        $this->_privateKey = $privateKey;
        $this->_lzbPublicKey = $lzbPublicKey;
        $this->_appKey = $appKey;
    }

    public function __set($name,$value){
        $innerName = "_".$name;
        if (isset($this->$innerName)) {
            $this->$innerName = $value;
        }
    }

    public function __get($name){
        $innerName = "_".$name;
        if (isset($this->$innerName)) {
            return $this->$innerName;
        }

        return null;
    }

    public function invoke($serviceName, $bizParams) {
        $bizContent = Utils\RSAHelper::encrypt($bizParams, $this->_lzbPublicKey);

        $publicParams = array (
            "service_name" => $serviceName,
            "app_key" => $this->_appKey,
            "version" => $this->_version,
            "format" => $this->_format,
            "charset" => $this->_charset,
            "sign_type" => $this->_signType,
            "timestamp" => time(),
            "biz_content" => $bizContent 
        );

        $sign = Utils\RSAHelper::genSign($publicParams, $this->_privateKey);
        $publicParams["sign"] = $sign;

        $res = Utils\HttpHelper::request($this->_baseUrl, $publicParams);
        
        if ( !isset($res["ret_code"]) 
            || $res["ret_code"] != 200) {
            throw new Exception\ResponseError($res["ret_msg"], $res["ret_code"]);
        } 

        if (!Utils\RSAHelper::checkSign($res, $res["sign"], $this->_lzbPublicKey)) {
            throw new Exception\SignVerificationError("response signature fails to verify");
        }

        return Utils\RSAHelper::decrypt($res["biz_content"], $this->_privateKey);
    }

    public function buildRequestUrl($bizParams) {
        $bizContent = Utils\RSAHelper::encrypt($bizParams, $this->_lzbPublicKey);

        $publicParams = array (
            "app_key" => $this->_appKey,
            "version" => $this->_version,
            "format" => $this->_format,
            "charset" => $this->_charset,
            "sign_type" => $this->_signType,
            "timestamp" => time(),
            "biz_content" => $bizContent
        );

        $sign = Utils\RSAHelper::genSign($publicParams, $this->_privateKey);
        $publicParams["sign"] = $sign;

        return Utils\HttpHelper::buildRequestUrl($this->_baseUrl, $publicParams);
    }

}
