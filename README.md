# 量子保开放平台SDK - PHP版
对量子保开放平台（ http://www.liangzibao.cn/apidoc/ ）的API接入协议做基础的封装，既做为参考实现，也可以做为项目的依赖直接引用。

## API接入例子
<pre><code>
$baseUrl = "对应环境的API网关URL";

$appKey = "量子保为开发者分配的app_key";

//量子保对应环境的公钥
$lzbPublicKey = "-----BEGIN PUBLIC KEY-----
...
-----END PUBLIC KEY-----";

//开发者密钥对的私钥，请私密保管
$privateKey = "-----BEGIN RSA PRIVATE KEY-----
...
-----END RSA PRIVATE KEY-----";

$client = new LZB\Client($baseUrl, $privateKey, $lzbPublicKey, $appKey);

try {
    $serviceName = "业务API名称";
    //请参考业务API文档，进行数组的组装
    $bizParams = array();

    $result = $client->invoke($serviceName, $bizParams);
    print_r($result);
} catch (Exception $e) {
    print_r($e);
}

</code></pre>

## HTML5 URL生成例子
<pre><code>
$baseUrl = "对应环境的HTML5网关URL";

$appKey = "量子保为开发者分配的app_key";

//量子保对应环境的公钥
$lzbPublicKey = "-----BEGIN PUBLIC KEY----- 
...
-----END PUBLIC KEY-----";

//开发者密钥对的私钥，请私密保管
$privateKey = "-----BEGIN RSA PRIVATE KEY----- 
...
-----END RSA PRIVATE KEY-----";

$client = new LZB\Client($baseUrl, $privateKey, $lzbPublicKey, $appKey);

try {
    //请参考业务API文档，进行数组的组装
    $bizParams = array();

    $result = $client->buildRequestUrl($bizParams);
    echo $result."\n";
} catch (Exception $e) {
    print_r($e);
}

</code></pre>

## 回调处理例子
<pre><code>
$baseUrl = "对应环境的HTML5网关URL";

$appKey = "量子保为开发者分配的app_key";

//量子保对应环境的公钥
$lzbPublicKey = "-----BEGIN PUBLIC KEY-----
...
-----END PUBLIC KEY-----";

//开发者密钥对的私钥，请私密保管
$privateKey = "-----BEGIN RSA PRIVATE KEY-----
...
-----END RSA PRIVATE KEY-----";

$client = new LZB\Client($baseUrl, $privateKey, $lzbPublicKey, $appKey);

//构建公共参数列表
$commonParams = ...;

//响应参数列表
$result = array();

try {
    //验证签名，并获取业务参数列表
    $params = $client->verifySignature($commonParams);

    //处理回调信息，返回ret_code为200
    ...
    result["ret_code"] = 200;
} catch (Exception $e) {
    //处理签名失败，返回错误
    ...
    result["ret_code"] = 410; //自定义，非200
    result["ret_msg"] = "Fail to verify the signature"; //自定义出错信息
}

//输出响应报文
...

</code></pre>
