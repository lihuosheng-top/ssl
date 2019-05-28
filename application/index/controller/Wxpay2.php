<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Wxpay2 extends Controller{
    /*
    配置参数
    */
    private $config = array(
        'appid' => "wxd3bc655771a1e376",//"wxcf1dded808489e2c",    /*微信开放平台上的应用id*/
        'mch_id' => "1536319261",//"1440493402",   /*微信申请成功之后邮件中的商户id*/
        'api_key' => "dTkOKWah1BpiEpabmyZtig4vxu7jrmht"    /*在微信商户平台上自己设定的api密钥 32位*/
        // 'api_key' => "d3dc3b56623ba9d8cf933351825ce349"    /*在微信商户平台上自己设定的api密钥 32位*/
    );


    //获取预支付订单
    public function getPrePayOrder($body, $out_trade_no, $total_fee){
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";

        $onoce_str = $this->getRandChar(32);

        $data["appid"] = $this->config["appid"];
        $data["body"] = $body;
        $data["mch_id"] = $this->config['mch_id'];
        $data["nonce_str"] = $onoce_str;
        $data["notify_url"] = "https://ssl.siring.com.cn/wxpaynotifyurl";
        $data["out_trade_no"] = $out_trade_no;
        $data["spbill_create_ip"] = $this->get_client_ip();
        $data["total_fee"] = $total_fee;
        $data["trade_type"] = "APP";
        $s = $this->getSign($data, false);
        $data["sign"] = $s;
        $xml = $this->arrayToXml($data);
        $response = $this->postXmlCurl($xml, $url);
        //将微信返回的结果xml转成数组
    //    return $this->xmlstr_to_array($response);
        return $this->xmlToArray($response);
    }

    //执行第二次签名，才能返回给客户端使用
    public function getOrder($prepayId,$orderid){
        $data["appid"] = $this->config["appid"];
        $data["noncestr"] = $this->getRandChar(32);;
        $data["package"] = "Sign=WXPay";
        $data["partnerid"] = $this->config['mch_id'];
        $data["prepayid"] = $prepayId;
        $data["timestamp"] = time();
        $s = $this->getSign($data, false);
        $data["order_number"] = $orderid;
        $data["sign"] = $s;

        return $data;
    }

    /*
        生成签名
    */
    function getSign($Obj)
    {
        foreach ($Obj as $k => $v)
        {
            $Parameters[strtolower($k)] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //echo "【string】 =".$String."</br>";
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".$this->config['api_key'];
//        echo "<textarea style='width: 50%; height: 150px;'>$String</textarea> <br />";
        //签名步骤三：MD5加密
        $result_ = strtoupper(md5($String));
        return $result_;
    }

    //获取指定长度的随机字符串
    function getRandChar($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }

    //数组转xml
    function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val))
            {
                $xml.="<".$key.">".$val."</".$key.">";

            }
            else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }

    //post https请求，CURLOPT_POSTFIELDS xml格式
    function postXmlCurl($xml,$url,$second=30)
    {
        //初始化curl
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data)
        {
            curl_close($ch);
            return $data;
        }
        else
        {
            $error = curl_errno($ch);
            // echo "curl出错，错误码:$error"."<br>";
            // echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }

    /*
        获取当前服务器的IP
    */
    function get_client_ip()
    {
        if ($_SERVER['REMOTE_ADDR']) {
            $cip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv("REMOTE_ADDR")) {
            $cip = getenv("REMOTE_ADDR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $cip = getenv("HTTP_CLIENT_IP");
        } else {
            $cip = "unknown";
        }
        return $cip;
    }

    //将数组转成uri字符串
    function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
                $v = urlencode($v);
            }
            $buff .= strtolower($k) . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    /**
    *xml转成数组
     */
//    function xmlstr_to_array($xmlstr) {
//        $doc = new DOMDocument();
//        $doc->loadXML($xmlstr);
//        return $this->domnode_to_array($doc->documentElement);
//    }
    function domnode_to_array($node) {
        $output = array();
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->domnode_to_array($child);
                    if(isset($child->tagName)) {
                        $t = $child->tagName;
                        if(!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    }
                    elseif($v) {
                        $output = (string) $v;
                    }
                }
                if(is_array($output)) {
                    if($node->attributes->length) {
                        $a = array();
                        foreach($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if(is_array($v) && count($v)==1 && $t!='@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }
        return $output;
    }
    // function xmlToArray($xml)
    // {
    //     $arr = $this->xml_to_array($xml);
    //     $key = array_keys($arr);
    //     return $arr[$key[0]];
    // }

    function xml_to_array($xml)
{
    $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
    if(preg_match_all($reg, $xml, $matches))
    {
        $count = count($matches[0]);
        $arr = array();
        for($i = 0; $i < $count; $i++)
        {
            $key = $matches[1][$i];
            $val = $this->xml_to_array( $matches[2][$i] );  // 递归
            if(array_key_exists($key, $arr))
            {
                if(is_array($arr[$key]))
                {
                    if(!array_key_exists(0,$arr[$key]))
                    {
                        $arr[$key] = array($arr[$key]);
                    }
                }else{
                    $arr[$key] = array($arr[$key]);
                }
                $arr[$key][] = $val;
            }else{
                $arr[$key] = $val;
            }
        }
        return $arr;
    }else{
        return $xml;
    }
}

function xmlToArray($xml)
{
    //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $values;
}
/**
 * lilu
 * 回调地址
 */
    public function wxpaynotifyurl(){ 
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xml_data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xml_data), true);
        if($xml){
            $data2['txt']=$xml;
            db('text')->insert($data2);
        }
        if($val["result_code"] == "SUCCESS" ){
            //修改订单的状态
            $map['status']='2';
            $map['pay_time']=time();
            $res=db('order')->where('order_number',$val['out_trade_no'])->update($map);
            //新增加答题记录
            $info=db('order')->where('order_number',$val['out_trade_no'])->find();
            $where['goods_id']=$info['goods_id'];
            $where['member_id']=$info['member_id'];
            $where['status']=2;
            $where['order_number']=$val['out_trade_no'];
            $re=db('answer_record')->insert($where);
            if($res && $re){
                // //做消费记录
                // $information =Db::name("reward")->field("money,order_number,crowd_name,member_id")->where("order_number",$val["out_trade_no"])->find();
                // $member_wallet =Db::name("member")
                //     ->where("member_id",$information["member_id"])
                //     ->value('member_wallet');
                // $datas= [
                //     "user_id"=>$information["member_id"],//用户ID
                //     "wallet_operation"=> $information["money"],//消费金额
                //     "wallet_type"=>-1,//消费操作(1入，-1出)
                //     "operation_time"=> date("Y-m-d H:i:s"),//操作时间
                //     "operation_linux_time"=>time(), //操作时间
                //     "wallet_remarks"=>"订单号：".$val["out_trade_no"]."众筹打赏".$information["money"]."元",//消费备注
                //     "wallet_img"=>" ",//图标
                //     "title"=>$information["crowd_name"],//标题（消费内容）
                //     "order_nums"=>$val["out_trade_no"],//订单编号
                //     "pay_type"=>"小程序", //支付方式/
                //     "wallet_balance"=>$member_wallet,//此刻钱包余额
                // ];
                // Db::name("wallet")->insert($datas); //存入消费记录表
                echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
            }else{
                echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
                return ajax_error("失败");
            }
        }
        //  // 返回状态给微信服务器 
        //  if ($result) { 
        //   $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>'; 
        //  }else{ 
        //   $str='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>'; 
        //  } 
        //  echo $str; 
        //  return $result; 
        }
        /** 
         * LILU
        * 生成签名 
        * @return 签名，本函数不覆盖sign成员变量 
        */
         function makeSign($data){ 
            //获取微信支付秘钥 
            include('../extend/WxpayAll/lib/WxPay.Api.php');
            // require_once APP_ROOT."/Api/wxpay/lib/WxPay.Api.php"; 
            $key = \WxPayConfig::KEY; 
            // 去空 
            $data=array_filter($data); 
            //签名步骤一：按字典序排序参数 
            ksort($data); 
            $string_a=http_build_query($data); 
            $string_a=urldecode($string_a); 
            //签名步骤二：在string后加入KEY 
            //$config=$this->config; 
            $string_sign_temp=$string_a."&key=".$key; 
            //签名步骤三：MD5加密 
            $sign = md5($string_sign_temp); 
            // 签名步骤四：所有字符转为大写 
            $result=strtoupper($sign); 
            return $result; 
        }
        /**
         * lilu
         * 免单反钱给用户
         */
        public function back_free_money($openid,$money,$name)
        {
            $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";

            $onoce_str = $this->getRandChar(32);
            $out_trade_no=time().rand(10000, 99999);//商户订单号
            $data["mch_appid"] = $this->config["appid"];
            $data["mchid"] = $this->config['mch_id'];
            $data["nonce_str"] = $onoce_str;
            $data["partner_trade_no"] = $out_trade_no;
            $data["spbill_create_ip"] = $this->get_client_ip();
            $data['openid']=$openid;
            $data['check_name']='NO_CHECK';   //真实姓名验证
            $data["amount"] = $money*100;
            $data['re_user_name']=$name;      //用户姓名
            $data["desc"] = "免单红包";
            $s = $this->getSign($data, false);
            $data["sign"] = $s;
            $xml = $this->arrayToXml($data);
            $response = $this->postXmlCurl2($xml, $url);
            //将微信返回的结果xml转成数组
        //    return $this->xmlstr_to_array($response);
            return $this->xmlToArray($response);
        }
        /**
         * lilu
         * 免单发送post请求
         */
        public function postXmlCurl2($xml,$url)
        {
            $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST" );
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
           curl_setopt($ch, CURLOPT_SSLCERT, "../extend/WxpayAll/cert/apiclient_cert.pem");
           curl_setopt($ch, CURLOPT_SSLKEY, "../extend/WxpayAll/cert/apiclient_key.pem");
           curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
           curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             $data=curl_exec($ch);
        //返回结果
        if($data)
        {
            curl_close($ch);
            return $data;
        }
        else
        {
            $error = curl_errno($ch);
            // echo "curl出错，错误码:$error"."<br>";
            // echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
     }

}