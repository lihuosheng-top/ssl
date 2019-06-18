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
        $notify_url="https://ssl.siring.com.cn/alipaynotify";
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
     * 支付宝异步通知
     */
    public function alipaynotify()
	{
       //原始订单号
       $out_trade_no = input('out_trade_no');
       //支付宝交易号
       $trade_no = input('trade_no');
       //交易状态
       $trade_status = input('trade_status');
       //插入数据库
       $pp['txt']=$out_trade_no.$trade_no.$trade_status;
       db('text')->insert($pp);
       if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') 
       {
        //修改订单的状态
        $map['status']='2';
        $map['pay_time']=time();
        $res2=db('order')->where('order_number',$out_trade_no)->update($map);
            //新增加答题记录
            $info=db('order')->where('order_number',$val['out_trade_no'])->find();
            $where['goods_id']=$info['goods_id'];
            $where['member_id']=$info['member_id'];
            $where['help_id']=$info['help_id'];
            $where['status']=2;        
            $where['order_number']='2019123132132';
            $where['create_time']=time();
            $re=db('answer_record')->insert($where);
            //判断用户是否为第一次甩该商品
            $order_info=db('order')->where('order_number',$out_trade_no)->find();
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
                //更改当前的人为旧人
                db('member')->where('id',$order_info['member_id'])->setField('is_new',0);
            }
            //消费记录
            $member=db('member')->where('id',$info['member_id'])->find();
            $where5['member_id']=$member['id'];
            $where5['goods_id']=$info['goods_id'];
            $where5['order_number']= $info['order_number'];
            $where5['income']=0;
            $where5['pay']=$info['order_amount'];
            $where5['pay_type']='2';   //weixin   
            if($info['help_id']==0)
            {
                $where5['order_type']='1';    //自己甩
                $where5['order_status']='0';
            }else{
                $where5['order_type']='5';     //帮甩
                $where5['order_status']='1';
            }
            $where5['help_id']=$info['help_id'];
            $re=db('captical_record')->insert($where5);
         //判断用户是否达拉新人条件
         //1.获取配置信息
         $key="star_value";
         $info2=db('sys_setting')->where('key',$key)->find();
         $info2['value']=json_decode($info2['value'],true);
         //获取用户信息
         if($res['help_id']!='0')
         {    //帮甩
            //  $is_new=db('order')->where('member_id',$res['help_id'])->find();
            $mm=db('member')->where('id',$res['help_id'])->find();
             if($mm['is_new']=='1')
             {      //新人
               $num=db('order')->where('member_id',$res['help_id'])->count();
               if($num>=$info2['value']['star_value']['num']){   //帮甩下单数大于配置数
                   //给他的上级增加星光值
                   db('member')->where('id',$mm['pid'])->setInc('star_value',$info2['value']['star_value']['value']);
               }
             }
         }
        // }
          echo 'success';
       }else{
           echo "fail";
       }

	}

}
