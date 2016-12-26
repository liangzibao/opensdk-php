<?php
#########################################################################
# File Name: src/LZBRsa.php
# Desc: 
# Author: Shaolin Wang
# mail: shaon86@163.com
# Created Time: Fri 23 Dec 2016 05:33:26 PM CST
#########################################################################


class LZBRsa{

    private function _filterParam($params){
        foreach($params as $key => $val){
            if(is_array($val)){
                $params[$key] = $this->_filterParam($val);
            }elseif(empty($val) && $val !== 0){
                unset($params[$key]);
                continue;
            }else{
                $params[$key] = strval($val);
            }
        }

        return $params;
    }
    /**
     * 加密
     * 
     * @param $params 待加密参数
     * @param $publicKey 商户公钥
    */
    public function encrypt($params, $publicKey){
        $rawData = json_encode($this->_filterParam($params));

        $list = [];
        $step = 117;

        $publicKey = $this->_getKey($publicKey);
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
    **/
    public function decrypt($encrytedData, $privateKey){
        $encrytedData = base64_decode($encrytedData);

        $list = [];
        $step = 128;
        if(strlen($privateKey) > 1000){
            $step = 256;
        }

        $privateKey = $this->_getKey($privateKey);
        for($i = 0, $len = strlen($encrytedData); $i < $len; $i += $step){
            $data = substr($encrytedData, $i, $step);
            $decrypted = '';

            openssl_private_decrypt($data, $decrypted, $privateKey);
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
    public function genSign($params, $privateKey){
        $params = $this->_filterParam($params);
        ksort($params);
        $signStr = json_encode($params);
        $signStr = stripslashes($signStr);


        $privateKey = $this->_getKey($privateKey);
        $privateKeyId = openssl_get_privatekey($privateKey);
        openssl_sign($signStr, $data, $privateKeyId);
        openssl_free_key($privateKeyId);
        
        return base64_encode($data);
    }

    private function _getKey($key){
        if(is_file($key)){
            return file_get_contents($key);
        }
        return $key;
    }

    /**
     * 验签
     *
     * @param $params 待验签参数
     * @param $sign 检验字符串
     * @param $publicKey 商户公钥
     */
    public function checkSign($params, $sign, $publicKey){
        $params = $this->_filterParam($params);
        if(isset($params['sign'])){
            unset($params['sign']);
        }
        ksort($params);

        $publicKey = $this->_getKey($publicKey);

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
}
