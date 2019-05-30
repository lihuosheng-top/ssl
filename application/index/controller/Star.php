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
        if($list)
        {
           return ajax_success('获取成功',$list);
        }else{
            return ajax_error('获取失败');
        }
    }
    /**
     * lilu
     * 兑换奖品
     * id   星光值商品id
     * token
     */
    public function get_star_goods()
    {
        //获取参数
        $input=input();
        if($input['id'])
        {
            //获取商品信息
            $goods_info=db('star_goods')->where('id',$input['id'])->find();            
        }

    
    }


}