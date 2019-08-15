<?php
namespace app\index\model;

use think\Model;
use think\Config;

/**
 * lilu
 * alipay支付宝支付模型
 */
class  Alipay extends Model
{
     
    /**
     * lilu
     * 发起支付宝支付
     * $body            名称
     * $total_amount    价格
     * $product_code    订单号
     * $notify_url      异步回调地址
     */
    public function pay($body, $total_amount, $product_code, $notify_url)
    {

        /**
         * 调用支付宝接口。
         */
        include('../extend/Alipay/aop/AopClient.php');
        include('../extend/Alipay/aop/request/AlipayTradeAppPayRequest.php');

        $aop = new \AopClient();
        
        $aop->gatewayUrl            = Config::get('alipay')['gatewayUrl'];
        $aop->appId                 = Config::get('alipay')['appId'];
        $aop->rsaPrivateKey         = Config::get('alipay')['rsaPrivateKey'];
        $aop->format                = Config::get('alipay')['format'];
        $aop->charset               = Config::get('alipay')['charset'];
        $aop->signType              = Config::get('alipay')['signType'];
        $aop->alipayrsaPublicKey    = Config::get('alipay')['alipayrsaPublicKey'];
        $request = new \AlipayTradeAppPayRequest();
        $arr['body']                = $body;
        $arr['subject']             = $body;
        $arr['out_trade_no']        = $product_code;
        $arr['timeout_express']     = '30m';
        $arr['total_amount']        = floatval($total_amount);
        $arr['product_code']        = 'QUICK_MSECURITY_PAY';

        $json = json_encode($arr);
        $request->setNotifyUrl($notify_url);
        $request->setBizContent($json);
        $response = $aop->sdkExecute($request);
       
        return $response;

    }
    /**
     * lilu
     * 发起支付宝支付
     * $body            名称
     * $total_amount    价格
     * $product_code    订单号
     * $notify_url      异步回调地址
     */
    public function order_refound($total_amount, $product_code)
    {
        /**
         * 调用支付宝接口。
         */
        include('../extend/Alipay/aop/AopClient.php');
        include('../extend/Alipay/aop/request/AlipayTradeRefundRequest.php');
        $aop = new \AopClient();
        
        $aop->gatewayUrl            = Config::get('alipay')['gatewayUrl'];
        $aop->appId                 = Config::get('alipay')['appId'];
        $aop->rsaPrivateKey         = Config::get('alipay')['rsaPrivateKey'];
        $aop->format                = Config::get('alipay')['format'];
        $aop->charset               = Config::get('alipay')['charset'];
        $aop->signType              = Config::get('alipay')['signType'];
        $aop->alipayrsaPublicKey    = Config::get('alipay')['alipayrsaPublicKey'];
        $aop->aliVersion            = Config::get('alipay')['version'];
        $request = new \AlipayTradeRefundRequest();
        $arr['refund_reason']       = '正常退款';
        $arr['out_trade_no']        = $product_code;
        $arr['refund_currency']     = 'CNY';
        $arr['refund_amount']       = floatval($total_amount);
        $arr['out_request_no']        = date('YmdHis').'000';


        $json = json_encode($arr);
        $request->setBizContent($json);
        // $response = $aop->sdkExecute($request);
        $result = $aop->execute ( $request); 

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            return  '1';    //成功
        } else {
          return   '2';
        }

    }
    
 


}