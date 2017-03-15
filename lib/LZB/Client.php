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

    }

    public function buildRequestUrl($bizParams) {

    }

}
