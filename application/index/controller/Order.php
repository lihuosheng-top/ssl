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
     * 帮甩订单生成----支付
     * goods_id
     * token
     * help_id
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
            $data['member_id']=$member_id['id'];         //会员id
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
     * 用户开甩档案
     * token
     */
    public function get_user_shuai()
    {
        
    }
}