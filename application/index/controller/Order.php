<?php
namespace  app\index\controller;

use think\Controller;
use think\Console;
use think\Db;
use app\index\controller\Wxpay as pay;
include('../extend/WxpayAll/lib/WxPay.Api.php');
include('../extend/WxpayAll/example/WxPay.NativePay.php');
include('../extend/WxpayAll/example/log.php');

/*
 * @Author: lilu 
 * @Date: 2019-05-14 15:17:20 
 * @Last Modified by: lilu
 * @Last Modified time: 2019-05-14 16:09:50
 */
class Order extends Controller
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
     * 商品订单生成----支付
     */
    public function goods_order()
    {
        $input=input();   //获取传递的参数
        if($input){
            $data['order_number']=date('YmdHis',time());    //自定义生成订单号
            $data['member_id']=$input['member_id'];         //会员id
            $data['goods_id']=$input['goods_id'];            //甩品、商品id
            $data['order_amount']=$input['income'];               //甩费、收入
            // $data['pay_type']=$input['pay_type'];           //支付类型
            // $data['order_type']=$input['order_type'];       //订单类型
            $data['goods_name']=db('goods')->where('id',$data['goods_id'])->value('goods_name');
            $data['create_time']=time();                    //订单创建时间
           if($input['special_id']){
                $data['special_id']=$input['special_id'];
           }else{
                $data['special_id']='0';
           }
           $data['order_quantity']=$input['order_quantity'];   //商品数量
            $re=db('order')->insert($data);
            if($re)
            {
                $object['paymoney']=$data['order_amount'];
                $object['goods_name']=$data['goods_name'];
                $object['goods_id']=$data['goods_id'];
                
                $pay = new pay();//统一下单
                $pay->index($object);
                die;
               return ajax_success('订单生成成功');
            }else{
               return ajax_error('订单生成失败');
            }
            
        }else{
            ajax_error('参数错误');
        }
    }
}