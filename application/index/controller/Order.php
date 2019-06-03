<?php
namespace  app\index\controller;

use think\Controller;
use think\Console;
use think\Db;
use app\index\controller\Wxpay2 as pay;
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
     * 订单生成----支付----自己甩
     * goods_id
     * token
     * special_id
     */
    public function goods_order()
    {   
        $input=input();   //获取传递的参数
        //根据token获取会员id
        $member_id=db('member')->where('token',$this->token)->field('id')->find();
        //判断甩商品订单帮甩数量
        $re=db('goods_receive')->where(['goods_id'=>$input['goods_id'],'member_id'=>$member_id['id']])->find();
        if($re){     //商品已开甩
            if($re['order_type']=='1'){
                return    ajax_error('商品已甩，不能开甩');
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
            $re=db('order')->insert($data);
            if($re)
            {
                // $body, $out_trade_no, $total_fee
                $body='微信测试';
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
     * 帮甩订单生成----支付----帮甩
     * goods_id
     * token
     * token_help
     * special_id
     */
    public function goods_order_help()
    {   
        $input=input();   //获取传递的参数
        //根据token获取会员id
        $member_id=db('member')->where('token',$this->token)->field('id')->find();
        //判断甩商品订单帮甩数量
        $re=db('goods_receive')->where(['goods_id'=>$input['goods_id'],'member_id'=>$member_id['id']])->find();
        if($re){     //商品已开甩
            if($re['order_type']=='1'){
                return    ajax_error('商品已甩，不能开甩');
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
            $data['help_id']=$member_id['id'];
            $re3=db('member')->where('token',$input['token_help'])->find();
            $data['member_id']=$re3['id'];         //会员id
            $data['order_type']='1';         // 1   自己甩订单     2 帮甩订单
            $re2=db('order')->insert($data);
            if($re2)
            {
                // $body, $out_trade_no, $total_fee
                $body='微信测试';
                $out_trade_no=$data['order_number']; //商户订单号(自定义)
                $total_fee=$data['order_amount']*100;
                $pay = new pay();//统一下单
                $order= $pay->getPrePayOrder($body, $out_trade_no, $total_fee);
                if ($order['prepay_id']){    //判断返回参数中是否有prepay_id
                    //添加好友关系----帮甩
                    $member_info=db('member')->where('id',$re3['id'])->find();     //
                    //判断是否已是好友
                    $is=db($re3['id'])->where('account',$member_id['account'])->find();
                    if($is)
                    {   //已是好友关系

                    }else{       //需添加好友关系
                        db($re3['id'])->insert($member_id);
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
        //获取当前用户的所有已付款甩记录
        $list=db('order')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'status'=>'2'])->select();
        $shuai_momey=db('order')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'status'=>'2'])->sum('order_amount');    //总甩费
        //保留两位小数
        $shuai_momey=sprintf("%.2f",$shuai_momey);      //总甩费
        $num=db('order')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'status'=>'2'])->count();    //总甩数
        
       //获取支付平台的扣费比率
       $key="admin_fei";
       $info=db('sys_setting')->where('key',$key)->find();
       $info['value']=json_decode($info['value'],true);
       $fei=$info['value']['fei']['fei']/100;       //平台收费每笔
       $fei2=$fei*$goods_info['goods_price'];
       $fei2=sprintf("%.2f",$fei2);
       $data['shuai_fei']=$shuai_momey;    //总甩费
       $data['fei']=$fei2;
       $data['num']=$num;                  //总帅次
       $data['goods_fei']=$goods_info['goods_price'];                  //总帅次
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
         $list=db('order')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'status'=>'2'])->group('help_id')->select();
         //获取支付平台的扣费比率
        $key="admin_fei";
        $info=db('sys_setting')->where('key',$key)->find();
        $info['value']=json_decode($info['value'],true);
        $fei=$info['value']['fei']['fei']/100;       //平台收费每笔
         //用户的退款信息
         $orderid=[];
         foreach($list as $k=>$v)
         {
            //  $list2=db('order')->where(['member_id'=>$member['id'],'goods_id'=>$input['goods_id'],'status'=>2,'help_id'=>$v['help_id']])->sum('order_amount');
            //循环遍历退款操作
           $money=$v['order_amount']*(1-$fei);
           $pay=new pay();
           $data2=$pay->order_refunds($v['order_number'],$money,$v['order_amount']);
           if($data2["return_code"] == "SUCCESS"  ){
            //退款记录
                $info=db('order')->where('order_number',$v['order_number'])->find();
                $where['member_id']=$member['id'];
                $where['help_id']=$v['help'];   //帮甩用户id
                $where['goods_id']=$info['goods_id'];
                $where['order_number']= $v['order_number'];
                $where['income']=$money;
                $where['pay']='0';
                $where['pay_type']='2';   //weixin   
                $where['order_type']='2';   //奖励红包
                if($v['help_id']=='0')
                {
                    $where['order_status']='0';   //自己甩
                }else{
                    $where['order_status']='1';   //帮甩
                }
               
                $re=db('captical_record')->insert($where);
           }
        }
        return ajax_success('已退款成功');

    }
        
}