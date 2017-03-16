# 量子保开放平台SDK - PHP版
对量子保开放平台（ http://www.liangzibao.cn/apidoc/ ）的API接入协议做基础的封装，即做为参考实现，也可以做为项目的依赖直接引用。

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
