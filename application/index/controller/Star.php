<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

/**
 * lilu
 * 奖励管理
 */
class Star extends Controller
{
    /**
     * lilu
     * 奖励商品列表
     */
    public function prize_list()
    {
        //获取所有的奖励商品
        $list=db('star_goods')->select();
        
    }


}