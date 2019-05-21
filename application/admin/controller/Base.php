<?php
namespace app\admin\controller;

use think\Controller;
use think\Session;

/**
 * lilu
 * 基础控制器
 */
class Base extends Controller
{
    /**
     * lilu
     * 自定义前置操作
     */
    protected $beforeActionList =[
      'first',                        //值为空（当前所有方法的前置方法）
    ];
    /**
     * lilu
     * Notes:判断用户是否登录
     */
    protected  function first()
    {
        //获取缓存信息
        $userid=Session::get('user_id');
        // if(!$userid){
        //     //清除所有缓存
        //     Session::delete("user_id");
        //     Session::delete("user_info");
        //     Session::delete("store_id");
        //     $this->redirect("admin/Login/index");
        // }
    }
}