<?php

namespace LZB;

class Client {

    private $_baseUrl;
 
    private $_privateKey;

    private $_lzbPublicKey;

    public function __construct($baseUrl, $privateKey, $lzbPublicKey) {
        $this->_baseUrl = $baseUrl;
        $this->_privateKey = $privateKey;
        $this->_lzbPublicKey = $lzbPublicKey;
    }

    public function __set($name,$value){
        $innerName = "_".$name;
        $this->$innerName = $value;
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
