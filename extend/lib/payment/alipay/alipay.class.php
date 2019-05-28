<?php

class alipay {

    private $configure;
    private $alipaySubmit;   

    function __construct() {
        
        require_once(EXTEND_PATH . "lib/ssi/config/payment/alipay.conf.php");
        require_once(EXTEND_PATH . "lib/payment/alipay/alipay_notify.class.php");
        require_once(EXTEND_PATH . "lib/payment/alipay/alipay_submit.class.php");        
        $this->configure = $alipay_config;
        $this->AlipayNotify = new AlipayNotify($this->configure);
        $this->alipaySubmit = new AlipaySubmit($this->configure);
    }

    function get_configure_value($str_key){
        return $this->configure["$str_key"];
    }
    
    /*
     * 建立支付宝即时付款请求
     *@param       $arr_pay_data = array(                    
     *               "notify_url" => 'http://xxxx.com/notify.php',//必填
     *               "return_url" => 'http://xxxx.com/notify.php',//必填                    
     *               "out_trade_no" => 123,//订单号//必填
     *               "subject" => "购买hdmi数据线",//订单名称//必填
     *               "total_fee" => 123.45,//订单总金额//必填
     *           );
     */
    function make_form($arr_parameter,$bool_return_html=false) {

        //构造支付宝请求数据
        $arr_para = array(
            "partner" => trim($this->configure['partner']),
            "_input_charset" => trim(strtolower($this->configure['input_charset'])),            
        );
        
        $arr_para = array_merge($arr_para, $arr_parameter);        
        $str_html_text = $this->alipaySubmit->buildRequestForm($arr_para, 'get',"确认");
		if($bool_return_html){
			return $str_html_text;
		}
        echo $str_html_text;
    }
    
    /*
     * 验证异步返回数据
     * @return type
     */
    function verify_notify() {
        return $verify_result = $this->AlipayNotify->verifyNotify();
    }

    
    function log($arr_data){        
        $str_sql = "INSERT INTO " . $this->index('third_pay_log') .make_sql($arr_data, 'insert');
        return $this->db->query($str_sql);
    }

}
