<?php
namespace  app\index\controller;

use think\Controller;
use think\Console;
use think\Request;
use think\Db;
use app\index\controller\Wxpay2 as pay;
use app\index\controller\Alipay as alipay;
use app\index\controller\Base ;

/*
 * @Author: lilu 
 * @Date: 2019-05-14 15:17:20 
 * @Last Modified by: lilu
 * @Last Modified time: 2019-05-14 16:09:50
 */
class Order extends Base
{
    /**
     * lilu
     * Notes:前端生成帮甩记录
     */
    public function  help_record()
    {
        //获取前台参数
        $input=input();
        if($input)
        {
          $data['order_number']=date('YmdHis',time());    //自定义生成订单号
          $data['member_id']=$input['member_id'];         //会员id
          $data['help_id']=$input['help_id'];             //帮甩人id
          $data['good_id']=$input['good_id'];            //甩品、商品id
          $data['income']=$input['income'];               //甩费、收入
          $data['pay']=$input['pay'];                     //支出
          $data['pay_type']=$input['pay_type'];           //支付类型
          $data['order_type']=$input['order_type'];       //订单类型
          $data['create_time']=time();                    //订单创建时间
          $data['order_status']=$input['order_status'];   //订单状态    0 自己甩     1  别人帮甩
          $re=db('help_record')->insert($data);
          if($re)
          {
             return ajax_success('帮甩成功');
          }else{
             return ajax_error('帮甩失败');
          }
        }else{
            return ajax_error('参数错误');
        }
    }

    /**
     * lilu
     * Notes:帮甩统计
     */
    public function help_count()
    {
        //统计某一商品帮甩记录的前10
    }
    /**
     * lilu
     * 订单生成----微信支付----自己甩
     * goods_id
     * token
     * special_id
     */
    public function goods_order()
    {
        //判断当前用户当前商品是否锁定
        $input=input();
        $mem2=db('member')->where('token',$this->token)->find();
        //判断用户解锁时间是否到
        $is_lock=db('answer_record')->where(['member_id'=>$mem2['id'],'goods_id'=>$input['goods_id'],'status'=>0])->find();
        if($is_lock)
        {
            $time=time()-$is_lock['lock_time'];
            if($time<0){
                return ajax_error('商品已锁定，请先解锁');
            }else{
                $mm['status']=3;
                $mm['unlock_time']=time();
                $answer=db('answer_record')->where(['member_id'=>$mem2['id'],'goods_id'=>$input['goods_id'],'status'=>0])->update($mm);
            }
        }

        $is_shuai=db('answer_record')->where(['member_id'=>$mem2['id'],'goods_id'=>$input['goods_id'],'status'=>0])->find();
        if($is_shuai)
        {
            return ajax_error('商品已锁定，请先解锁');
        }
        //获取甩品配置信息
        $key1='goods_limit';        //甩品限制
        $info1=db('sys_setting')->where('key',$key1)->find();
        $info1['value']=json_decode($info1['value'],true);   
        $goods_num_limit=$info1['value']['limit_num'];    //同时甩限制个数
        //获取当天甩次限制
        $key2='goods_limit_own';       //自己甩品次数限制
        $info2=db('sys_setting')->where('key',$key2)->find();
        $info2['value']=json_decode($info2['value'],true);
        $goods_limit_own=$info2['value']['person_up_num'];     //单个商品每天上限
        $goods_limit_day=$info2['value']['own_num'];     //每天自己甩的上限
        $input=input();   //获取传递的参数
        //根据token获取会员id
        $member_id=db('member')->where('token',$this->token)->field('id')->find();
        //判断甩商品订单帮甩数量
        $re=db('goods_receive')->where(['goods_id'=>$input['goods_id'],'member_id'=>$member_id['id']])->find();
        if($re){     //商品已开甩
            if($re['order_type']=='1'){
                return    ajax_error('商品已甩，不能开甩');
            }
            if($re['order_type']=='-1'){
                return    ajax_error('商品退款，不能开甩');
            }
        }
        //判断用户是否是新的开甩商品
        $kai_shuai=db('goods_receive')->where(['member_id'=>$member_id['id'],'goods_id'=>$input['goods_id'],'order_type'=>'0'])->find();    //正在甩
        if($kai_shuai)     //商品已开甩
        {
            //用户当天这个商品的甩次
            $pp['member_id']=$member_id['id'];
            $pp['status']='2';
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d H:i:s');
            $pp['create_time']=array('between',array($start,$end));
            $pp['goods_id']=$input['goods_id'];
            $day_num=db('order')->where($pp)->count();    //当前会员这个商品当天已甩次数
            //判断是否有拉新人的赠甩次数
            $la_xin=db('la_new_record')->where(['member_id'=>$member_id['id'],'goods_id'=>$input['goods_id']])->find();
            if($la_xin)    //该用户有拉新人记录
            {
                //获取商品新人帮甩配置
                $goods_info=db('goods')->where('id',$input['goods_id'])->find();
                if($goods_info['new_tactics'])
                {
                    $value=json_decode($goods_info['new_tactics'],true);
                    if($value['new']==1)
                    {    //开启
                     $new=$value['help_num'];
                     $goods_limit_own +=$new*$la_xin['new_num'];
                    }
                }
            }
            //判断用户开甩当天的帮甩人数
            $time2=time();
            $time=date('Y-m-d',$kai_shuai['create_time']);
            $start=strtotime($time);
            $end=$start+24*60*60;
            if($time2>$start && $time2<$end)   //判断是否为当天
            {
                $map2['create_time']=array('between',array($start,$end));
                $map2['member_id']=$member_id['id'];
                $map2['help_id']=array('gt',0);
                $old_num=db('order')->where($map2)->count();   //老人当天的帮甩总次数
                //获取老人帮甩的配置
                $goods_info=db('goods')->where('id',$input['goods_id'])->find();
                if($goods_info['old_tactics'])
                {
                        $value=json_decode($goods_info['old_tactics'],true);
                        if($value['old']==1)
                        {    //开启
                         $old=$value['help_num'];
                         $goods_limit_own +=$old_num*$old;
                        }
                }

            }
            if($goods_limit_own<=$day_num)     //用户当天甩次达到上限
            {
                return    ajax_error('商品当天甩次达到上限，不能再甩');
            }
            // goods_limit_day
            $pp2['member_id']=$member_id['id'];
            $pp2['status']='2';
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d H:i:s');
            $pp2['create_time']=array('between',array($start,$end));
            $day_zong_num=db('order')->where($pp)->count();    //当天当前会员的总甩次
            if($goods_limit_day<=$day_zong_num)
            {     //用户当天总甩次达到上限
                return    ajax_error('当天总甩次达到上限，不能再甩');
            }
        }else{
            //商品未开甩
            $goods_num=db('goods_receive')->where(['member_id'=>$member_id['id'],'order_type'=>'0'])->group('goods_id')->count();  // 用户同时甩的商品数量
            if($goods_num>=$goods_num_limit)
            {
                return ajax_error('商品同时开甩达到上限，请完成开甩商品');
            }
        }
        if($input){
            $data['order_number']=date('YmdHis',time());    //自定义生成订单号
            $data['goods_id']=$input['goods_id'];            //甩品、商品id
            //根据goods_id获取
            if($input['special_id']=='0'){
                $data['special_id']='0';
                $price=db('goods')->where('id',$input['goods_id'])->find();
                $data['goods_money']=$price['goods_price'];
            }else{
                $data['special_id']=$input['special_id'];
                $price=db('special')->where('id',$input['special_id'])->find();
                $data['goods_money']=$price['jilt'];
           }
            $data['order_amount']=$data['goods_money'];               //甩费、收入
            // $data['pay_type']=$input['pay_type'];           //支付类型
            // $data['order_type']=$input['order_type'];       //订单类型
            $data['goods_name']=db('goods')->where('id',$data['goods_id'])->value('goods_name');
            $data['create_time']=time();                    //订单创建时间
            // $data['order_quantity']='1';   //商品数量
            $data['help_id']=0;
            $data['member_id']=$member_id['id'];         //会员id
            $data['pay_type']='1';         // 微信
            $re=db('order')->insert($data);
            if($re)
            {
                    //微信支付
                    $body='微信支付';
                    $out_trade_no=$data['order_number']; //商户订单号(自定义)
                    $total_fee=$data['order_amount']*100;
                    $pay = new pay();//统一下单
                    $order= $pay->getPrePayOrder($body, $out_trade_no, $total_fee);
                    if ($order['prepay_id']){//判断返回参数中是否有prepay_id
                        $order1 = $pay->getOrder($order['prepay_id'],$data['order_number']);//执行二次签名返回参数
                        return ajax_success('新建订单成功',$order1);
                        // echo json_encode(array('status' => 1, 'prepay_order' => no_null($order1)));
                    } else {
                        return ajax_error('新建订单失败',$order['err_code_des']);
                        // echo json_encode(array('status' => 0, 'msg' => $order['err_code_des']));
                    }
                    break;
            }else{
               return ajax_error('订单生成失败');
            }
            
        }else{
            ajax_error('参数错误');
        }
    }
    /**
     * lilu
     * 订单生成----支付宝支付----自己甩
     * goods_id
     * token
     * special_id
     */
    public function goods_order2()
    {  
           
        //判断当前用户当前商品是否锁定
        $input=input();
        $mem2=db('member')->where('token',$this->token)->find();
        //判断用户解锁时间是否到
        $is_lock=db('answer_record')->where(['member_id'=>$mem2['id'],'goods_id'=>$input['goods_id'],'status'=>0])->find();
        if($is_lock)
        {
            $time=time()-$is_lock['lock_time'];
            if($time<0){
                return ajax_error('商品已锁定，请先解锁');
            }else{
                $mm['status']=3;
                $mm['unlock_time']=time();
                $answer=db('answer_record')->where(['member_id'=>$mem2['id'],'goods_id'=>$input['goods_id'],'status'=>0])->update($mm);
            }
        }
        $is_shuai=db('answer_record')->where(['member_id'=>$mem2['id'],'goods_id'=>$input['goods_id'],'status'=>0])->find();
        if($is_shuai)
        {
            return ajax_error('商品已锁定，请先解锁');
        }
         //获取甩品配置信息
        $key1='goods_limit';        //甩品限制
        $info1=db('sys_setting')->where('key',$key1)->find();
        $info1['value']=json_decode($info1['value'],true);   
        $goods_num_limit=$info1['value']['limit_num'];    //同时甩限制个数
        //获取当天甩次限制
        $key2='goods_limit_own';       //自己甩品次数限制
        $info2=db('sys_setting')->where('key',$key2)->find();
        $info2['value']=json_decode($info2['value'],true);
        $goods_limit_own=$info2['value']['person_up_num'];     //单个商品每天上限
        $goods_limit_day=$info2['value']['own_num'];     //每天自己甩的上限
        $input=input();   //获取传递的参数
        //根据token获取会员id
        $member_id=db('member')->where('token',$this->token)->field('id')->find();
        //判断甩商品订单帮甩数量
        $re=db('goods_receive')->where(['goods_id'=>$input['goods_id'],'member_id'=>$member_id['id']])->find();
        if($re){     //商品已开甩
            if($re['order_type']=='1'){
                return    ajax_error('商品已甩，不能开甩');
            }
            if($re['order_type']=='-1'){
                return    ajax_error('商品退款，不能开甩');
            }
        }
        //判断用户是否是新的开甩商品
        $kai_shuai=db('goods_receive')->where(['member_id'=>$member_id['id'],'goods_id'=>$input['goods_id'],'order_type'=>'0'])->find();    //正在甩
        if($kai_shuai)     //商品已开甩
        {
            //用户当天这个商品的甩次
            $pp['member_id']=$member_id['id'];
            $pp['status']='2';
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d H:i:s');
            $pp['create_time']=array('between',array($start,$end));
            $pp['goods_id']=$input['goods_id'];
            $day_num=db('order')->where($pp)->count();    //当前会员这个商品当天已甩次数
            //判断是否有拉新人的赠甩次数
            $la_xin=db('la_new_record')->where(['member_id'=>$member_id['id'],'goods_id'=>$input['goods_id']])->find();
            if($la_xin)    //该用户有拉新人记录
            {
                //获取商品新人帮甩配置
                $goods_info=db('goods')->where('id',$input['goods_id'])->find();
                if($goods_info['new_tactics'])
                {
                    $value=json_decode($goods_info['new_tactics'],true);
                    if($value['new']==1)
                    {    //开启
                     $new=$value['help_num'];
                     $goods_limit_own +=$new*$la_xin['new_num'];
                    }
                }
            }
            //判断用户开甩当天的帮甩人数
            $time2=time();
            $time=date('Y-m-d',$kai_shuai['create_time']);
            $start=strtotime($time);
            $end=$start+24*60*60;
            if($time2>$start && $time2<$end)   //判断是否为当天
            {
                $map2['create_time']=array('between',array($start,$end));
                $map2['member_id']=$member_id['id'];
                $map2['help_id']=array('gt',0);
                $old_num=db('order')->where($map2)->count();   //老人当天的帮甩总次数
                //获取老人帮甩的配置
                $goods_info=db('goods')->where('id',$input['goods_id'])->find();
                if($goods_info['old_tactics'])
                {
                        $value=json_decode($goods_info['old_tactics'],true);
                        if($value['old']==1)
                        {    //开启
                         $old=$value['help_num'];
                         $goods_limit_own +=$old_num*$old;
                        }
                }

            }
            if($goods_limit_own<=$day_num)     //用户当天甩次达到上限
            {
                return    ajax_error('商品当天甩次达到上限，不能再甩');
            }
            // goods_limit_day
            $pp2['member_id']=$member_id['id'];
            $pp2['status']='2';
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d H:i:s');
            $pp2['create_time']=array('between',array($start,$end));
            $day_zong_num=db('order')->where($pp)->count();    //当天当前会员的总甩次
            if($goods_limit_day<=$day_zong_num)
            {     //用户当天总甩次达到上限
                return    ajax_error('当天总甩次达到上限，不能再甩');
            }
        }else{
            //商品未开甩
            $goods_num=db('goods_receive')->where(['member_id'=>$member_id['id'],'order_type'=>'0'])->group('goods_id')->count();  // 用户同时甩的商品数量
            if($goods_num>=$goods_num_limit)
            {
                return ajax_error('商品同时开甩达到上限，请完成开甩商品');
            }
        }
        if($input){
            $data['order_number']=date('YmdHis',time());    //自定义生成订单号
            $data['goods_id']=$input['goods_id'];            //甩品、商品id
            //根据goods_id获取
            if($input['special_id']=='0'){
                $data['special_id']='0';
                $price=db('goods')->where('id',$input['goods_id'])->find();
                $data['goods_money']=$price['goods_price'];
            }else{
                $data['special_id']=$input['special_id'];
                $price=db('special')->where('id',$input['special_id'])->find();
                $data['goods_money']=$price['jilt'];
           }
            $data['order_amount']=$data['goods_money'];               //甩费、收入
            // $data['pay_type']=$input['pay_type'];           //支付类型
            // $data['order_type']=$input['order_type'];       //订单类型
            $data['goods_name']=db('goods')->where('id',$data['goods_id'])->value('goods_name');
            $data['create_time']=time();                    //订单创建时间
            // $data['order_quantity']='1';   //商品数量
            $data['help_id']=0;
            $data['member_id']=$member_id['id'];         //会员id
            $data['pay_type']='2';         // 支付宝
            $re=db('order')->insert($data);
            if($re)
            {
                    //支付宝支付
                    $total_amount=$data['order_amount'];              //订单价格
                    $product_code=$data['order_number'];              //订单号
                    $body='甩甩乐';                      //详细信息
                    $ali = new alipay();//统一下单
                    $res=$ali->alipay( $total_amount, $product_code);
                    if($res)
                    {
                        $map['ali']=$res;
                        $map['order_number']=$data['order_number'];
                            // return $this->response($res, 1, '成功');
                            return  ajax_success('新建订单成功',$map);
                    }else {
                            return  ajax_error('新建订单失败');
                            // return $this->response('', 0, '对不起请检查相关参数');
                    }

            }else{
               return ajax_error('订单生成失败');
            }
        }else{
            ajax_error('参数错误');
        }
    }
    /**
     * lilu
     * 帮甩订单生成----支付----帮甩----微信
     * goods_id
     * token
     * token_help
     * special_id
     */
    public function goods_order_help()
    {   
        $input=input();   //获取传递的参数
        //判断新人
        $member1=db('member')->where('token',$this->token)->find();
        $member2=db('member')->where('token',$input['token_help'])->find();
        //判断用户解锁时间是否到
        $is_lock=db('answer_record')->where(['member_id'=>$member2['id'],'goods_id'=>$input['goods_id'],'status'=>0])->find();
        if($is_lock)
        {
            $time=time()-$is_lock['lock_time'];
            if($time<0){
                return ajax_error('商品已锁定，请先解锁');
            }else{
                $mm['status']=3;
                $mm['unlock_time']=time();
                $answer=db('answer_record')->where(['member_id'=>$member2['id'],'goods_id'=>$input['goods_id'],'status'=>0])->update($mm);
            }
        }
        //判断用户是否有帮甩记录
        $is_new=db('order')->where('member_id',$member1['id'])->find();
        if(!$is_new)    //就是个新人
        {
            //新人帮甩
            $data['person_type']='1';
            $rr=db('member')->where('id',$member1['id'])->setField('pid',$member2['id']);
            //判断该用户是否有拉新记录
             $re=db('la_new_record')->where('member_id',$member2['id'])->find();
             if($re)
             {
                db('la_new_record')->where('member_id',$member2['id'])->setInc('new_num',1);
            }else{
                $data3['member_id']=$member2['id'];
                $data3['goods_id']=$input['goods_id'];
                $data3['new_num']='1';
                db('la_new_record')->insert($data3);
             }
        }else{
            $create=db('goods_receive')->where(['member_id'=>$member2['id'],'goods_id'=>$input['goods_id']])->value('create_time');
            $start2 = strtotime(date('Y-m-d 00:00:00'));
            $end2 = strtotime(date('Y-m-d H:i:s'));
            if($create)
            {
                if($create>$start2 && $create<$end2)
                {   //当天甩
                    $data['person_type']='0';      //当天甩
                }else{
                    $data['person_type']='2';      //
                }
            }else{
              $data['person_type']=0;
            }
        }
        $key2='help_goods_limit';       //自己甩品次数限制
        $info2=db('sys_setting')->where('key',$key2)->find();
        $info2['value']=json_decode($info2['value'],true);
        $goods_limit_help=$info2['value']['goods_limit_num'];     //帮甩单个商品每天上限
        $goods_limit_zong=$info2['value']['own_help_num'];         //帮甩每天自己甩的上限
       
        //根据token获取会员id
        $member_id=db('member')->where('token',$this->token)->find();
        //判断甩商品订单帮甩数量
        $re=db('goods_receive')->where(['goods_id'=>$input['goods_id'],'member_id'=>$member_id['id']])->find();
        if($re){     //商品已开甩
            if($re['order_type']=='1'){
                return    ajax_error('商品已甩，不能开甩');
            }
            if($re['order_type']=='-1'){
                return    ajax_error('商品退款，不能开甩');
            }
        }
        //帮甩判断
        $pp['goods_id']=$input['goods_id'];
        $pp['help_id']=$member_id['id'];   //帮甩id
        $pp['status']='2';
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d H:i:s');
        $pp['create_time']=array('between',array($start,$end));
        $day_num=db('order')->where($pp)->count();    //当前会员这个商品当天帮甩次数
        if($goods_limit_help<=$day_num)     //用户当天帮甩次达到上限
        {
            return    ajax_error('商品当天帮甩次达到上限，不能再甩');
        }
        // goods_limit_day
        $pp2['help_id']=$member_id['id'];
        $pp2['status']='2';
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d H:i:s');
        $pp2['create_time']=array('between',array($start,$end));
        $day_zong_num=db('order')->where($pp)->count();    //当天当前会员的总甩次
        if($goods_limit_zong<=$day_zong_num)
        {     //用户当天总帮甩次达到上限
            return    ajax_error('当天总甩次达到上限，不能再甩');
        }
        if($input){
            $data['order_number']=date('YmdHis',time());    //自定义生成订单号
            $data['goods_id']=$input['goods_id'];            //甩品、商品id
            //根据goods_id获取
            if($input['special_id']=='0'){
                $data['special_id']='0';
                $price=db('goods')->where('id',$input['goods_id'])->find();
                $data['goods_money']=$price['goods_price'];
            }else{
                $data['special_id']=$input['special_id'];
                $price=db('special')->where('id',$input['special_id'])->find();
                $data['goods_money']=$price['jilt'];
           }
            $data['order_amount']=$data['goods_money'];               //甩费、收入
            // $data['pay_type']=$input['pay_type'];           //支付类型
            // $data['order_type']=$input['order_type'];       //订单类型
            $data['goods_name']=db('goods')->where('id',$data['goods_id'])->value('goods_name');
            $data['create_time']=time();                    //订单创建时间
            // $data['order_quantity']='1';   //商品数量
            $data['help_id']=$member_id['id'];
            $re3=db('member')->where('token',$input['token_help'])->find();
            $data['member_id']=$re3['id'];         //会员id
            $data['order_type']='1';         // 1   自己甩订单     2 帮甩订单
            $data['pay_type']='1';         // 微信
            $re2=db('order')->insert($data);
            if($re2)
            {
                $body='微信测试';
                $out_trade_no=$data['order_number']; //商户订单号(自定义)
                $total_fee=$data['order_amount']*100;
                $pay = new pay();//统一下单
                $order= $pay->getPrePayOrder($body, $out_trade_no, $total_fee);
                if ($order['prepay_id']){    //判断返回参数中是否有prepay_id
                    //添加好友关系----帮甩
                    $member_info=db('member')->where('id',$re3['id'])->find();     //
                    //判断是否已是好友
                    $is1=db($re3['id'])->where('account',$member_id['account'])->find();
                    $is2=db($member_id['id'])->where('account',$re3['account'])->find();
                    if($is1)
                    {   //已是好友关系

                    }else{       //需添加好友关系
                        db($re3['id'])->insert($member_id);
                    }
                    if(!$is2)
                    {  
                       //需添加好友关系
                        db($member_id['id'])->insert($re3);
                    }
                    $order1 = $pay->getOrder($order['prepay_id'],$data['order_number']);//执行二次签名返回参数
                    return ajax_success('新建订单成功',$order1);
                    // echo json_encode(array('status' => 1, 'prepay_order' => no_null($order1)));
                } else {
                    return ajax_error('新建订单失败',$order['err_code_des']);
                    // echo json_encode(array('status' => 0, 'msg' => $order['err_code_des']));
                }
                break;
            }else{
               return ajax_error('订单生成失败');
            }
            
        }else{
            ajax_error('参数错误');
        }
    }
    /**
     * lilu
     * 帮甩订单生成----支付----帮甩----支付宝
     * goods_id
     * token
     * token_help
     * special_id
     */
    public function goods_order_help2()
    {   
        $input=input();   //获取传递的参数
        //判断新人
        $member1=db('member')->where('token',$this->token)->find();
        $member2=db('member')->where('token',$input['token_help'])->find();
        //判断用户解锁时间是否到
        $is_lock=db('answer_record')->where(['member_id'=>$member2['id'],'goods_id'=>$input['goods_id'],'status'=>0])->find();
        if($is_lock)
        {
            $time=time()-$is_lock['lock_time'];
            if($time<0){
                return ajax_error('商品已锁定，请先解锁');
            }else{
                $mm['status']=3;
                $mm['unlock_time']=time();
                $answer=db('answer_record')->where(['member_id'=>$member2['id'],'goods_id'=>$input['goods_id'],'status'=>0])->update($mm);
            }
        }
        //判断用户是否有帮甩记录
        $is_new=db('order')->where('member_id',$member1['id'])->find();
        if(!$is_new)    //就是个新人
        {
            $data['person_type']='1';
            $rr=db('member')->where('id',$member1['id'])->setField('pid',$member2['id']);
            //判断该用户是否有拉新记录
             $re=db('la_new_record')->where('member_id',$member2['id'])->find();
             if($re)
             {
                db('la_new_record')->where('member_id',$member2['id'])->setInc('new_num',1);
            }else{
                $data3['member_id']=$member2['id'];
                $data3['goods_id']=$input['goods_id'];
                $data3['new_num']='1';
                db('la_new_record')->insert($data3);
             }
        }else{
            $create=db('goods_receive')->where(['member_id'=>$member2['id'],'goods_id'=>$input['goods_id']])->value('create_time');
            $start2 = strtotime(date('Y-m-d 00:00:00'));
            $end2 = strtotime(date('Y-m-d H:i:s'));
            if($create)
            {
                if($create>$start2 && $create<$end2)
                {   //当天甩
                    $data['person_type']='0';
                }else{
                    $data['person_type']='2';
                }
            }else{
              $data['person_type']=0;
            }
        }
        $key2='help_goods_limit';       //自己甩品次数限制
        $info2=db('sys_setting')->where('key',$key2)->find();
        $info2['value']=json_decode($info2['value'],true);
        $goods_limit_help=$info2['value']['goods_limit_num'];     //帮甩单个商品每天上限
        $goods_limit_zong=$info2['value']['own_help_num'];         //帮甩每天自己甩的上限
       
        //根据token获取会员id
        $member_id=db('member')->where('token',$this->token)->find();
        //判断甩商品订单帮甩数量
        $re=db('goods_receive')->where(['goods_id'=>$input['goods_id'],'member_id'=>$member_id['id']])->find();
        if($re){     //商品已开甩
            if($re['order_type']=='1'){
                return    ajax_error('商品已甩，不能开甩');
            }
            if($re['order_type']=='-1'){
                return    ajax_error('商品退款，不能开甩');
            }
        }
        //帮甩判断
        $pp['goods_id']=$input['goods_id'];
        $pp['help_id']=$member_id['id'];   //帮甩id
        $pp['status']='2';
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d H:i:s');
        $pp['create_time']=array('between',array($start,$end));
        $day_num=db('order')->where($pp)->count();    //当前会员这个商品当天帮甩次数
        if($goods_limit_help<=$day_num)     //用户当天帮甩次达到上限
        {
            return    ajax_error('商品当天帮甩次达到上限，不能再甩');
        }
        // goods_limit_day
        $pp2['help_id']=$member_id['id'];
        $pp2['status']='2';
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d H:i:s');
        $pp2['create_time']=array('between',array($start,$end));
        $day_zong_num=db('order')->where($pp)->count();    //当天当前会员的总甩次
        if($goods_limit_zong<=$day_zong_num)
        {     //用户当天总帮甩次达到上限
            return    ajax_error('当天总甩次达到上限，不能再甩');
        }
        if($input){
            $data['order_number']=date('YmdHis',time());    //自定义生成订单号
            $data['goods_id']=$input['goods_id'];            //甩品、商品id
            //根据goods_id获取
            if($input['special_id']=='0'){
                $data['special_id']='0';
                $price=db('goods')->where('id',$input['goods_id'])->find();
                $data['goods_money']=$price['goods_price'];
            }else{
                $data['special_id']=$input['special_id'];
                $price=db('special')->where('id',$input['special_id'])->find();
                $data['goods_money']=$price['jilt'];
           }
            $data['order_amount']=$data['goods_money'];               //甩费、收入
            // $data['pay_type']=$input['pay_type'];           //支付类型
            // $data['order_type']=$input['order_type'];       //订单类型
            $data['goods_name']=db('goods')->where('id',$data['goods_id'])->value('goods_name');
            $data['create_time']=time();                    //订单创建时间
            // $data['order_quantity']='1';   //商品数量
            $data['help_id']=$member_id['id'];
            $re3=db('member')->where('token',$input['token_help'])->find();
            $data['member_id']=$re3['id'];         //会员id
            $data['order_type']='1';         // 1   自己甩订单     2 帮甩订单
            $data['pay_type']='2';         // 支付宝
            $re2=db('order')->insert($data);
            if($re2)
            {
                //支付宝支付
                $total_amount=$data['order_amount'];              //订单价格
                $product_code=$data['order_number'];              //订单号
                $body='甩甩乐';                      //详细信息
                $ali = new alipay();//统一下单
                $res=$ali->alipay($body, $total_amount, $product_code);
                if($res)
                {
                    //添加好友关系----帮甩
                    $member_info=db('member')->where('id',$re3['id'])->find();     //
                    //判断是否已是好友
                    $is1=db($re3['id'])->where('account',$member_id['account'])->find();
                    $is2=db($member_id['id'])->where('account',$re3['account'])->find();
                    if($is1)
                    {   //已是好友关系

                    }else{       //需添加好友关系
                        db($re3['id'])->insert($member_id);
                    }
                    if(!$is2)
                    {  
                       //需添加好友关系
                        db($member_id['id'])->insert($re3);
                    }
                        // return $this->response($res, 1, '成功');
                        $map['ali']=$res;
                        $map['order_number']=$data['order_number'];
                    return  ajax_success('新建订单成功',$map);
                }else {
                        return  ajax_error('新建订单失败');
                        // return $this->response('', 0, '对不起请检查相关参数');
                }
                // $body, $out_trade_no, $total_fee
                // $body='微信测试';
                // $out_trade_no=$data['order_number']; //商户订单号(自定义)
                // $total_fee=$data['order_amount']*100;
                // $pay = new pay();//统一下单
                // $order= $pay->getPrePayOrder($body, $out_trade_no, $total_fee);
                // if ($order['prepay_id']){    //判断返回参数中是否有prepay_id
                //     //添加好友关系----帮甩
                //     $member_info=db('member')->where('id',$re3['id'])->find();     //
                //     //判断是否已是好友
                //     $is1=db($re3['id'])->where('account',$member_id['account'])->find();
                //     $is2=db($member_id['id'])->where('account',$re3['account'])->find();
                //     if($is1)
                //     {   //已是好友关系

                //     }else{       //需添加好友关系
                //         db($re3['id'])->insert($member_id);
                //     }
                //     if(!$is2)
                //     {  
                //        //需添加好友关系
                //         db($member_id['id'])->insert($re3);
                //     }
                //     $order1 = $pay->getOrder($order['prepay_id'],$data['order_number']);//执行二次签名返回参数
                //     return ajax_success('新建订单成功',$order1);
                //     // echo json_encode(array('status' => 1, 'prepay_order' => no_null($order1)));
                // } else {
                //     return ajax_error('新建订单失败',$order['err_code_des']);
                //     // echo json_encode(array('status' => 0, 'msg' => $order['err_code_des']));
                // }
                // break;
            }else{
               return ajax_error('订单生成失败');
            }
            
        }else{
            ajax_error('参数错误');
        }
    }
    /**
     * lilu 
     * 上划---退款账单
     * token
     * goods_id
     */
    public function order_refund()
    {
        //获取参数信息
        $input=input();
        //获取商品信息
        $goods_info=db('goods')->where('id',$input['goods_id'])->find();
        //获取用户的信息
        $member=db('member')->where('token',$this->token)->find();
        //判断当前商品当前用户是否退款
        $is_refund=db('goods_receive')->where(['goods_id'=>$input['goods_id'],'member_id'=>$member['id']])->value('order_type');
        if($is_refund<0){
           $data['order_status']=1;   //已退款
        }else{
            $data['order_status']=0;   //未退款
        }
        //获取当前用户的所有已付款甩记录
        $list=db('order')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'status'=>'2'])->select();
        $shuai_momey=db('order')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'status'=>'2'])->sum('order_amount');    //总甩费
        $shuai_momey_own=db('order')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'status'=>'2','help_id'=>0])->sum('order_amount');    //自己甩费
        $shuai_momey_help=$shuai_momey-$shuai_momey_own;    //帮甩费
        //保留两位小数
        $shuai_momey=sprintf("%.2f",$shuai_momey);      //总甩费
        $shuai_momey_own=sprintf("%.2f",$shuai_momey_own);      //自己甩费
        $shuai_momey_help=sprintf("%.2f",$shuai_momey_help);      //帮甩费
        $num=db('order')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'status'=>'2'])->count();    //总甩数
        $num_own=db('order')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'status'=>'2','help_id'=>0])->count();    //自己甩数
        $num_help=$num-$num_own;    //帮甩数
        //获取免单的笔数和金额
        $free_money=db('captical_record')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'help_id'=>0,'order_type'=>2])->sum('income'); //自己免单
        $free_money=sprintf("%.2f",$free_money);      //总甩费
        $free_num=db('captical_record')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'order_type'=>2])->count();  //总免单次数
       //获取支付平台的扣费比率
       $key="admin_fei";
       $info=db('sys_setting')->where('key',$key)->find();
       $info['value']=json_decode($info['value'],true);
       $fei=$info['value']['fei']['fei']/100;       //红包平台收费每笔
       $fei2=$fei*$goods_info['goods_price'];
       if($fei2==0)
       {
           $fei2=0.00;
        }elseif($fei2<=0.01 && $fei2!=0){
            $fei2=0.01;
       }
       $fei2=sprintf("%.2f",$fei2);
       $data['shuai_fei']=$shuai_momey;    //总甩费
       $data['shuai_momey_own']=$shuai_momey_own;    //自己甩费
       $data['shuai_momey_help']=$shuai_momey_help;    //帮甩费
       $data['fei']=$fei2;                  //平台收费
       $data['num']=$num;                  //总甩次
       $data['num_own']=$num_own;                  //自己甩次
       $data['num_help']=$num_help;                  //帮甩次
       $data['free_money']=$free_money;    //自己免单金额
       $data['free_num']=$free_num;        //总免单次数
       $data['goods_fei']=$goods_info['goods_price'];         //商品单次甩费
    //    $data['big_flag']=1;         //超级
        //获取当前商品的信息
        $good=db('goods')->where('id',$input['goods_id'])->field('id,goods_name,goods_bottom_money,goods_standard,goods_show_image')->find();
        $pp2['status']=array('gt',1);
        $pp2['goods_id']=$input['goods_id'];
        $good['shuai_num']=db('order')->where($pp2)->group('member_id')->count();
        if($good){
            $data['goods_info']=$good;
        }
       if($data)
       {
        return ajax_success('获取成功',$data);
       }else{
        return ajax_error('获取失败');
       }
    }
    /**
     * lilu
     * 帮甩退款账单
     * goods_id
     * token    当前token
     * token_help
     */
    public function order_refund_help()
    {
         //获取参数信息
         $input=input();
         //获取商品信息
         $goods_info=db('goods')->where('id',$input['goods_id'])->find();
         //获取当前帮甩用户的信息
         $member=db('member')->where('token',$this->token)->find();   
         $member_info=db('member')->where('token',$input['token_help'])->find();    //  
         //判断用户是否已退款   token_help
         $res=db('goods_receive')->where(['member_id'=>$member_info['id'],'goods_id'=>$input['goods_id']])->find();
         if($res['order_type']=='0')
         {
            $data['shuai_status']=0;
         }else{
             $data['shuai_status']=-1;
         }
         //获取当前用户的所有已帮甩记录
         $list=db('order')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'status'=>'2'])->select();
         $shuai_momey=db('order')->where(['member_id'=>$member_info['id'],'goods_id'=>$input['goods_id'],'status'=>'2','help_id'=>$member['id']])->sum('order_amount');    //帮总甩费
         $shuai_momey=sprintf("%.2f",$shuai_momey);      //总甩费
         $shuai_num=db('order')->where(['member_id'=>$member_info['id'],'goods_id'=>$input['goods_id'],'status'=>'2','help_id'=>$member['id']])->count();    //帮总甩数
         //获取免单的笔数和金额
        $free_money=db('captical_record')->where(['member_id'=>$member_info['id'],'goods_id'=>$input['goods_id'],'help_id'=>$member['id']])->sum('income'); //自己免单
        $free_money=sprintf("%.2f",$free_money);      //总甩费
        $free_num=db('captical_record')->where(['member_id'=>$member_info['id'],'goods_id'=>$input['goods_id'],'help_id'=>$member['id']])->count();
          //获取支付平台的扣费比率
         $key="admin_fei";
         $info=db('sys_setting')->where('key',$key)->find();
         $info['value']=json_decode($info['value'],true);
         $fei=$info['value']['fei']['fei']/100;       //红包平台收费每笔
         $fei2=$fei*$goods_info['goods_price'];
         if($fei2<0.01)
         {
           $fei2=0.01;
        }
         $data['goods_fei']=$goods_info['goods_price'];         //商品单次甩费
         $data['shuai_fei']=$shuai_momey;    //总甩费
         $data['fei']=$fei2;                  //平台收费
         $data['num']=$shuai_num;             //总甩次
         $data['free_money']=$free_money;    //自己免单金额
         $data['free_num']=$free_num;        //总免单次数
         if($data)
         {
          return ajax_success('获取成功',$data);
         }else{
          return ajax_error('获取失败');
         }
    }
    /**
     * lilu
     * 退款处理
     * token 
     * goods_id
     */
    public function order_refund_do()
    {
        //获取参数
        $input=input();
         //获取商品信息
        $goods_info=db('goods')->where('id',$input['goods_id'])->find();
         //获取用户的信息
         $member=db('member')->where('token',$this->token)->find();
         //获取当前用户的所有已付款甩记录
         $list=db('order')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'status'=>'2'])->select();
         //获取支付平台的扣费比率
        $key="admin_fei";
        $info=db('sys_setting')->where('key',$key)->find();
        $info['value']=json_decode($info['value'],true);
        $fei=$info['value']['fei']['fei']/100;       //平台收费每笔
         //用户的退款信息
         $orderid=[];
         foreach($list as $k=>$v)
         {
            //$list2=db('order')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'status'=>2,'help_id'=>$v['help_id']])->sum('order_amount');
            //循环遍历退款操作
            if($v['pay_type']==1)     //微信
            {
                //判断是否免单，获取免单金额
                $mian=db('captical_record')->where('order_number',$v['order_number'])->find(); 
                if($mian){
                    $money=$v['order_amount']*(1-$fei)-$mian['income'];
                } else{
                    $money=$v['order_amount']*(1-$fei);    //退款金额
                }
                // $money=floor($money*100)/100;
                $money=round($money,2);
                //资金明细中插入退款记录
                $info=db('order')->where('order_number',$v['order_number'])->find();
                $where['member_id']=$member['id'];
                $where['help_id']=$v['help_id'];   //帮甩用户id
                $where['goods_id']=$info['goods_id'];
                $where['order_number']= $v['order_number'];
                $where['income']=$money;
                $where['pay']='0';
                $where['pay_type']='2';   //weixin   
                $where['order_type']='4';   //退款记录----未到账
                $where['create_time']=time();   //退款记录----未到账
                if($v['help_id']=='0')
                {
                    $where['order_status']='0';   //自己甩
                }else{
                    $where['order_status']='1';   //帮甩
                }
                $re=db('captical_record')->insert($where);
                $pay=new pay();
                $data2=$pay->order_refunds($v['order_number'],$money,$v['order_amount']);
                if($data2["return_code"] == "SUCCESS"){
                    //退款记录
                    $mm['order_type']='6';    //退款到账
                    $mm['create_time']=time();    //退款到账
                    $ree=db('captical_record')->where(['order_number'=>$v['order_number'],'order_type'=>'4'])->update($mm);
                    //退款完成后，修改商品开甩记录的状态
                    $res=db('goods_receive')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id']])->setField('order_type',-1);
                    // $res2=db('order')->where('order_number',$v['order_number'])->setField('status',-1);     //已退款
                    return ajax_success('已退款成功');
                }else{
                    return ajax_success('已退款失败');
                }
            }else{      //支付宝
              //判断是否免单，获取免单金额
              $mian=db('captical_record')->where(['order_number'=>$v['order_number'],'order_type'=>2])->find(); 
              if($mian){
                  $money=$v['order_amount']*(1-$fei)-$mian['income'];
              } else{
                  $money=$v['order_amount']*(1-$fei);    //退款金额
              }
                // $money=floor($money*100)/100;    //上线使用
                $money=round($money,2);     //测试使用
                //退款记录
                $info=db('order')->where('order_number',$v['order_number'])->find();
                $where['member_id']=$member['id'];
                $where['help_id']=$v['help_id'];   //帮甩用户id
                $where['goods_id']=$info['goods_id'];
                $where['order_number']= $v['order_number'];
                $where['income']=$money;
                $where['pay']='0';
                $where['pay_type']='1';     //alipay   
                $where['order_type']='4';   //退款记录
                $where['create_time']=time();   //退款记录
                if($v['help_id']=='0')
                {
                    $where['order_status']='0';   //自己甩
                }else{
                    $where['order_status']='1';   //帮甩
                }
                $re=db('captical_record')->insert($where);
               $ali=new alipay();
               $data3=$ali->ali_order_refound($money,$v['order_number']);
               if($data3=='1'){    //成功
                     //退款记录
                     $mm['order_type']='6';    //退款到账
                     $mm['create_time']=time();    //退款到账
                     $ree=db('captical_record')->where(['order_number'=>$v['order_number'],'order_type'=>'4'])->update($mm);
                    //退款完成后，修改商品开甩记录的状态
                    $res=db('goods_receive')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id']])->setField('order_type',-1);
                    //  $res2=db('order')->where('order_number',$v['order_number'])->setField('status',-1);     //已退款
                    return ajax_success('已退款成功');
                }else{
                    return ajax_success('退款失败');
                }
            }
           
        }
        
    }
        
}