<?php



//wx232efcf35623ea71
//283dc0dfed7f3e54df4d9d3a4d3a7087

//支付
//商户号 1227928502
//api密钥
//wewewekakaluote20161104goodsawas

/**
 * 	配置账号信息
 */
class WxPayConf_pub
{

    //=======【基本信息设置】=====================================
    //微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
    const APPID = 'wx141337862de5e392';
    //受理商ID，身份标识
//    const MCHID = '1488407552';
    const MCHID = '';
    //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
    const KEY = 'Zlh188cnZwxcqgzyszunlianhuiappZy';
    //JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
    const APPSECRET = 'c9cf2b51e78c8ea9616db3cfe8a4ab1d';
    //=======【JSAPI路径设置】===================================
    //获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
    const JS_API_CALL_URL = 'http://www.ryegoo.com/wxpay'; //微信支付
    

    //=======【证书路径设置】=====================================
    //证书路径,注意应该填写绝对路径
    //=======【证书路径设置】=====================================
    /**
     * TODO：设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * @var path
     */
    const SSLCERT_PATH = '../cert/apiclient_cert.pem';
    const SSLKEY_PATH = '../cert/apiclient_key.pem';


    //const SSLCERT_PATH = S_ROOT.'ssi/config/payment/apiclient_cert.pem';
    //const SSLKEY_PATH = S_ROOT.'ssi/config/payment/apiclient_key.pem';
    //=======【异步通知url设置】===================================
    //异步通知url，商户根据实际开发过程设定
    const NOTIFY_URL = 'http://automobile.siring.com.cn/weixin_notify';
    //=======【curl超时设置】===================================
    //本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
    const CURL_TIMEOUT = 30;
}
?>