<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

include('../extend/WxpayAPI/lib/WxPay.Api.php');
include('../extend/WxpayAPI/example/WxPay.NativePay.php');
include('../extend/WxpayAPI/lib/WxPay.Notify.php');
include('../extend/WxpayAPI/example/log.php');
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
            $res=db('order')->where('order_number',$val['out_trade_no'])->find();
            $res2=db('order')->where('order_number',$val['out_trade_no'])->update($map);
            if($res['status']=='1'){
                //新增加答题记录
                $info=db('order')->where('order_number',$val['out_trade_no'])->find();
                $where['goods_id']=$info['goods_id'];
                $where['member_id']=$info['member_id'];
                $where['help_id']=$info['help_id'];
                $where['status']=2;
                $where['order_number']=$val['out_trade_no'];
                $re=db('answer_record')->insert($where);
                //判断用户是否为第一次甩该商品
                $order_info=db('order')->where('order_number',$val['out_trade_no'])->find();
                $data['goods_id']=$order_info['goods_id'];
                $data['member_id']=$order_info['member_id'];
                $is_save=db('goods_receive')->where($data)->find();
                if($is_save)
                {    $yi_shuai=$is_save['yi_shuai'];
                    $num= db('goods_receive')->where($data)->setField('yi_shuai',$yi_shuai+1);
                    $info= db('goods_receive')->where($data)->find();
                    if($info['yi_shuai']==$info['shuai_num'])
                    {
                        $where2['order_type']='1';
                        db('goods_receive')->where($data)->update($where2);
                    }
                }else{
                    //新添加一条商品领取记录
                    $where3['member_id']=$info['member_id'];
                    $where3['help_id']=$info['help_id'];
                    $where3['goods_id']=$info['goods_id'];
                    $where3['order_number']=date('YmdHis',time());
                    $where3['yi_shuai']=1;
                    $points=db('goods')->where('id',$info['goods_id'])->find();
                    $where3['shuai_num']=$points['points'];
                    $where3['special_id']=$info['special_id'];
                    $where3['order_type']='0';
                    $where3['create_time']=time();
                    $res2=db('goods_receive')->insert($where3);
                }
                //消费记录
                $where5['member_id']=$member['id'];
                $where5['help_id']='0';   //帮甩用户id
                $where5['goods_id']=$info['goods_id'];
                $where5['order_number']= $info['order_number'];
                $where5['income']=0;
                $where5['pay']=$info['order_amount'];
                $where5['pay_type']='2';   //weixin   
                if($info['help_id']==0)
                {
                    $where5['order_type']='1';
                    $where5['order_status']='0';
                }else{
                    $where5['order_type']='5';
                    $where5['order_status']='1';
                }
                $re=db('captical_record')->insert($where5);
             //判断用户是否达拉新人条件
             //1.获取配置信息
             $key="star_value";
             $info2=db('sys_setting')->where('key',$key)->find();
             $info2['value']=json_decode($info2['value'],true);
             //获取用户信息
             $member2=db('member')->where('id',$res['member_id'])->find();
             if($member2['is_new']==1)
             {
               $num=db('order')->where('member_id',$res['member_id'])->count();
               if($num>=$info2['value']['star_value']['num']){
                   $mem_list=db('member')->order('create_time desc')->select();
                   foreach($mem_list as $k =>$v)
                   {
                       if($v['fid'])
                       {
                           $fid=json_decode($v['fid'],true);
                           if($fid){
                               foreach($fid as $k2 =>$v2)
                               {
                                   if($v2==$res['member_id'])
                                   {
                                       db('member')->where('id',$v['id'])->setField('star_value',$info2['value']['star_value']['value']);
                                   }
                               }
                           }
                       }
                   }
               }

             }
               
            }
            echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
         }else{
            echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
            return ajax_error("失败");
         }
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
         * 免单反钱给用户----商户->客户
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
            $data["desc"] = "123123";
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
           curl_setopt($ch, CURLOPT_SSLCERT, "/data/wwwroot/ssl.siring.com.cn/ssl/extend/WxpayAll/cert/apiclient_cert.pem");
           curl_setopt($ch, CURLOPT_SSLKEY, "/data/wwwroot/ssl.siring.com.cn/ssl/extend/WxpayAll/cert/apiclient_key.pem");
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
      /**
         * lilu
         * 免单反钱给用户---退款流程
         */
        public function back_money($orderid,$money,$total_fee)
        {
            $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";

            $onoce_str = $this->getRandChar(32);
            $out_trade_no=$orderid;//商户订单号
            $data["appid"] = $this->config["appid"];
            $data["mch_id"] = $this->config['mch_id'];
            $data["nonce_str"] = $onoce_str;
            $data["out_trade_no"] = $out_trade_no;
            $data["out_refund_no"] = time().rand(10000, 99999);    //退款单号
            $data["total_free"] = $total_fee*100;
            $data["free_free"] = $money*100;
            $data["op_user_id"] = '01';
            $data["refund_desc"] = "免单红包";
            $s = $this->getSign($data, false);
            $data["sign"] = $s;
            $xml = $this->arrayToXml($data);
            $response = $this->postXmlCurl($xml, $url);
            //将微信返回的结果xml转成数组
        //    return $this->xmlstr_to_array($response);
            return $this->xmlToArray($response);
        }
        public function order_refunds($orderid,$money,$total_fee){
            
            $map = array(
                'order_number'=>$orderid
            );
            $refund_amount =Db::name("order")
                ->where($map)
                ->find();
            if(!$refund_amount){
                return ajax_error("未找到该订单信息");
            }
            $out_trade_no=$orderid;
            $total_fee=$total_fee *100;
            $refund_fee= $money *100;
            $input = new \WxPayRefund();
            $input->SetOut_trade_no($out_trade_no);
            $input->SetTotal_fee($total_fee);
            $input->SetRefund_fee($refund_fee);
            $input->SetOut_refund_no(\WxPayConfig::MCHID.date("YmdHis"));
            $input->SetOp_user_id(\WxPayConfig::MCHID);
            $result =\WxPayApi::refund($input);
            //  file_put_contents("/data/wwwroot/ssl.siring.com.cn/ssl/refund.txt",$result);
              return $result;
        }

}