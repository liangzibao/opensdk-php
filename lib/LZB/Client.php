<?php

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
            "serviceName" => $serviceName,
            "appKey" => $this->_appKey,
            "version" => $this->_version,
            "format" => $this->_format,
            "charset" => $this->_charset,
            "signType" => $this->_signType,
            "timestamp" => time(),
            "bizContent" => $bizContent 
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

        return Utils\RSAHelper::decrypt($res["bizContent"], $this->_privateKey);
    }

    public function buildRequestUrl($bizParams) {
        return Utils\HttpHelper::buildRequestUrl($this->_baseUrl, $bizParams);
    }

}
