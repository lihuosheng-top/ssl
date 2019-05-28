<?php
header("Content-type: text/html; charset=utf-8");
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once "WxPay.NativePay.php";
require_once 'log.php';

//模式一
/**
 * 流程：
 * 1、组装包含支付信息的url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、确定支付之后，微信服务器会回调预先配置的回调地址，在【微信开放平台-微信支付-支付配置】中进行配置
 * 4、在接到回调通知之后，用户进行统一下单支付，并返回支付信息以完成支付（见：native_notify.php）
 * 5、支付完成之后，微信服务器会通知支付成功
 * 6、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
$notify = new NativePay();
//$url1 = $notify->GetPrePayUrl("123456789");

//模式二
/**
 * 流程：
 * 1、调用统一下单，取得code_url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、支付完成之后，微信服务器会通知支付成功
 * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */



$input = new WxPayUnifiedOrder();
/**
 * 设置商品或支付单简要描述
 */
$input->SetBody("测试商品支付");
/**
 * 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
 */
$input->SetAttach("ceshidingdan1");
/**
 * 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
 */
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
/**
 * 设置订单总金额，只能为整数，详见支付金额
 * @param string $value
 **/
$input->SetTotal_fee("1");
/**
 * 设置订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
 * @param string $value
 **/
$input->SetTime_start(date("YmdHis"));
/**
 * 设置订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则
 * @param string $value
 **/
$input->SetTime_expire(date("YmdHis", time() + 600));
/**
 * 设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
 * @param string $value
 **/
$input->SetGoods_tag("无");
/**
 * 设置接收微信支付异步通知回调地址
 * @param string $value
 **/
$input->SetNotify_url("http://localhost/automobile/public/indexs");
/**
 * 设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
 * @param string $value
 **/
$input->SetTrade_type("NATIVE");
/**
 * 设置trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
 * @param string $value
 **/
$input->SetProduct_id("123456789");
/**
 * 生成直接支付url，支付url有效期为2小时,模式二
 * @param UnifiedOrderInput $input
 */

$result = $notify->GetPayUrl($input);
$url2 = $result["code_url"];
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-退款</title>
</head>
<body>
	<!--<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式一</div><br/>
	<img alt="模式一扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php /*echo urlencode($url1);*/?>" style="width:150px;height:150px;"/>-->
	<br/><br/><br/>
	<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式二</div><br/>
	<img alt="模式二扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php echo urlencode($url2);?>" style="width:150px;height:150px;"/>
	
</body>
</html>