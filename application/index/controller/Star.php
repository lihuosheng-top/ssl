<?php

namespace app\index\controller;

use app\index\controller\Base;
use think\Controller;
use think\Request;

/**
 * lilu
 * 奖励管理
 */
class Star extends Base
{
    /**
     * lilu
     * 奖励商品列表
     * token
     */
    public function prize_list()
    {
        //获取所有的奖励商品
        $list=db('star_goods')->select();
        //获取用户的星光值
        $star_value=db('member')->where('token',$this->token)->find();
        foreach($list as $k =>$v){
            if($v['goods_value']<=$star_value['star_value'])
            {
                $list[$k]['is_exchange']=1;
            }else{
                $list[$k]['is_exchange']=0;
            }
        }
        if($list)
        {
           return ajax_success('获取成功',$list);
        }else{
            return ajax_error('获取失败');
        }
    }
    /**
     * lilu
     * 兑换奖品-领取商品
     * goods_id   星光值商品id
     * token
     */
    public function get_star_goods()
    {
        //获取参数
        $input=input();
        if($input['goods_id'])
        {
            //查看会员信息
            //获取商品信息
            $goods_info=db('star_goods')->where('id',$input['goods_id'])->find();
            $value=$goods_info['goods_value'];    //星光值商品价值（星光值） 
            $re=db('member')->where('token',$this->token)->find();
            $star_num['star_value']=$re['star_value']-$value;
            $star_num['exchange_star_value']=$re['exchange_star_value']+$value;
            $res=db('member')->where('token',$this->token)->update($star_num);
            if($res !==false)
            {
                // //修改商品领取状态
                // $re2=db('goods_receive')->where(['member_id'=>$re['id'],'goods_id'=>])->update();
                //获取用户信息
                $info=db('member')->where('token',$this->token)->find();
                //生成兑换记录
                $data['member_id']=$info['id'];
                $data['star_goods_id']=$goods_info['id'];
                $data['create_time']=time();
                $data['status']='1';
                $data['goods_value']=$goods_info['goods_value'];
                $data['goods_image']=$goods_info['goods_image'];
                $data['order_number']=date('YmdHis',time());
                db('exchange_list')->insert($data);
              return ajax_success('兑换成功');
            }else{
              return ajax_error('兑换失败');
            }
        }

    
    }


}