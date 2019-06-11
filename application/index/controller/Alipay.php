<?php
namespace app\index\controller;



use think\Controller;
use think\Config;
use app\index\model\Alipay as ali;


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
    public function alipay($body, $total_amount, $product_code)
    {
        //测试假数据
        $notify_url="https://ssl.siring.com.cn/alipay_notify";
        $pay = new ali();            
        $alipay= $pay->pay($body, $total_amount, $product_code, $notify_url);
        if($alipay)
        {
            return $alipay;
        }else{
            return false;
        }
    }
    /**
     * lilu
     * 支付宝支付
     */
    public function alipay2()
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
    /**
     * lilu
     * 支付宝异步通知
     */
    public function notify_alipay()
	{
       //原始订单号
       $out_trade_no = input('out_trade_no');
       //支付宝交易号
       $trade_no = input('trade_no');
       //交易状态
       $trade_status = input('trade_status');


       if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {

           $condition['ordersn'] = $out_trade_no;
           $data['status'] = 2;
           $data['third_ordersn'] = $trade_no;

           $result=db('order')->where($condition)->update($data);//修改订单状态,支付宝单号到数据库

           if($result){
               echo 'success';
           }else{
               echo 'fail';
           }

       }else{
           echo "fail";
       }

	}

}
