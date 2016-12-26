<?php
#########################################################################
# File Name: exemple.php
# Desc: 
# Author: Shaolin Wang
# mail: shaon86@163.com
# Created Time: Fri 23 Dec 2016 05:38:55 PM CST
#########################################################################

include __DIR__ . '/src/LZBConfig.php';
include __DIR__ . '/src/LZBRsa.php';
include __DIR__ . '/src/LZBCurl.php';

// 创建保单
$serviceName = 'lzb.policy.create';  //创建保单

// bizContent;
$bizContent = [
    'product_mask'      => '184371',
    'mer_order_id'      => '123',    //订单号，可包含字母、数字、下划线
    'mer_order_uuid'    => '',  //如果不传或为空，直接使用mer_order_id进行赋值，可包含字母、数字、下划线
    //订单信息
    'mer_order_info' => [
        'vehicle_number'    => '112111',
        'create_time'       => '1482215703', //unix时间戳
        'expect_start_time' => '',  //unix时间戳
        'expect_end_time'   => '', //unix时间戳
        'book_user_id'      => '111',
        'passenger_cellphone' => '18600000001',
    ],
    //司机信息
    'insure_user_info' => [
        'user_id' => '111',
        'cellphone' => '18600000001',
        'name' => '张三',
        'idno' => '110111111111111111'
    ],
    'start_time' => '1482215703', //保险开始时间, unix时间戳
    'end_time' => '' //保险结束时间  
];

// bizContent 加密
$rsa = new LZBRsa();
$bizContent = $rsa->encrypt($bizContent, LZBConfig::$lzbPublicKey);

// 添加公共参数
$requestData = [
    'app_key'       => LZBConfig::$appKey,
    'service_name'  => $serviceName,
    'timestamp'     => time(),
    'format'        => 'json',
    'sign_type'     => 'RSA',
    'charset'       => 'UTF8',
    'version'       => '1.0.0',
    'biz_content'    => $bizContent,
];


// 加签
$sign = $rsa->genSign($requestData, LZBConfig::$merPrivateKey);
$requestData['sign'] = $sign;

// 请求LZB服务器
$result = LZBCurl::request(LZBConfig::$url, $requestData, LZBCurl::POST);

if(!isset($result['status']) || $result['status'] !== 'success'){
    //TODO: 失败
    throw new Exception($result['ret_msg'], $result['ret_code']);
}

// 校验时间
$now = time();
if(abs($now - $result['timestamp']) > 300){
    throw new Exception('接口过期');
}

// 验签
$ret = $rsa->checkSign($result, $result['sign'], LZBConfig::$lzbPublicKey);
if(!$ret){
    throw new Exception('验签失败');
}

// 解密
$bizContent = $rsa->decrypt($result['biz_content'], LZBConfig::$merPrivateKey);
if(empty($bizContent)){
    throw new Exception('解密失败');
}

var_export($bizContent);
