<?php
namespace app\index\controller;


define("APPID", "2019042464293493"); // 商户账号appid
define("AES", "ykxRWh+P0UtlsqjOejNcPw==");  //AES
define("HTTPS", "https://openapi.alipay.com/gateway.do"); //支付宝链接
define("RSA", "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAniRtd+MDzmDOrGJ8HWf/sNFp+4i1U+54KTo9SDGGSTj9eocbNX/VfcT/eCWDE09UrKMUZtLxNQudH7BI5p/0Z4v8/TL8awaDzi8l7jRpC+VWifNLEMEbOYVKFdQL/TyDfCnd0m/hC4vdWU6oHOtqaAjVE4PafUs6r4ivfll0RMxnjDjfCCrQU6r6krJfHUQ3PNSED3l4qHVY6aI1ZP3RxBsg246+T3UvFFS9qdggGJfhG/gWoLaAIxqGFXeWkOah1tPmjhHhwj1RAJs9HqDh/U4Tcr/tZ6KtwsAK++SbkdIFxdsePzvGl2wobsCFno2eto8WlMFp6luMb4jH6KtQ1wIDAQAB"); // 商户账号appid

use think\Controller;
/**
 * lilu
 * 支付宝接口对接
 */
class Alipay extends Controller
{

    /**
     * lilu
     * 支付宝支付
     */
    public function ali_pay()
    {
        header("Content-type:text/html;charset=utf-8");
        include EXTEND_PATH . "/lib/payment/alipay/alipay.class.php";
        $int_order_id = intval(12);
        $obj_alipay = new \alipay();
        $arr_data = array(
            "return_url" => trim("http://sss.siring.com.cn/index"),
            "notify_url" => trim("http://sss.siring.com.cn/alipaynotify"),
            "service" => "create_direct_pay_by_user",
            "payment_type" => 1, //
            "seller_email" => 'lilusiring@163.com',
            "out_trade_no" => time(),
            "subject" => "siring支付测试", //商品订单的名称
            "total_fee" => number_format('0.01', 2, '.', ''),
        );
        if (isset($arr_order['paymethod']) && isset($arr_order['defaultbank']) && $arr_order['paymethod'] === "bankPay" && $arr_order['defaultbank'] != "") {
            $arr_data['paymethod'] = "bankPay";
            $arr_data['defaultbank'] = $arr_order['defaultbank'];
        }
        $str_pay_html = $obj_alipay->make_form($arr_data, true);
        halt($str_pay_html);
        return ajax_success();
    }
}
