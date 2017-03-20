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

    /**
     * 量子保开放平台接口封装
     *
     * @param string $baseUrl 网关调用URL，不同环境该URL不同
     * @param string $privateKey 开发者密钥对的私钥，用于对公共请求参数做签名，和对业务API参数做解密
     * @param string $lzbPublicKey 量子保开放平台对外公钥，用于对公共响应参数做验签，和对业务API请求参数做加密
     * @param string $appKey 量子保开放平台为开发者分配的唯一标识
     */
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

    /**
     * 应用调用
     *
     * @param string $serviceName 业务API名称
     * @param array $bizParams 业务API请求参数表
     * @return array 返回业务API响应参数表，JSON对象
     * @throws Exception 网络传输层错误异常
     * @throws LZB\Exception\SignVerificationError 响应报文签名验证失败
     * @throws LZB\Exception\ResponseError 调用失败异常
     */
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

    /**
     * 生成GET方式完整的调用的URL，仅用于标准HTML5页面接入
     *
     * @param array $bizParams 业务API请求参数表
     * @return string 完整调用URL，可以直接用于浏览器或者开发平台的Webview
     */
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
