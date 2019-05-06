<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

/**
 *   [前端路由]
 **/
Route::group("",[

    /*登录*/
    "index"      =>"index/Index/index",             //前端登录页面
    "index_login"=>"index/Login/index_login",       //前端登录--获取验证码
    "index_dolog"=>"index/Login/index_dolog",       //前端登录处理


]);


/**
 * [后台路由]
 * lilu
 */
Route::group("admin",[
    /*首页*/
    "/$"=>"admin/index/index",
    "get_id_return_info"=>"admin/index/get_id_return_info",//获取点击二级菜单下三级菜单的权限菜单



    /*登录*/
    "index"=>"admin/Login/index",
    "login"=>"admin/Login/login",    //登录


    /*验证码*/
    "login_captcha"=>"admin/Login/captchas",


    /*管理员列表*/
    "admin_index"=>"admin/admin/index",
    "admin_add"=>"admin/admin/add",
    "admin_save"=>"admin/admin/save",
    "admin_del"=>"admin/admin/del",
    "admin_edit"=>"admin/admin/edit",
    "admin_updata"=>"admin/admin/updata",
    "admin_status"=>"admin/admin/status",
    "admin_passwd"=>"admin/admin/passwd",



    /*角色列表*/
    "role_index"=>"admin/role/index",//列表
    "role_search"=>"admin/role/role_search",//列表查询
    "role_add"=>"admin/role/add",//角色添加
    "role_save"=>"admin/role/save",//角色保存
    "role_del"=>"admin/role/del",//角色删除
    "role_edit"=>"admin/role/edit",//角色编辑
    "role_updata"=>"admin/role/updata",//角色数据更新
    "role_status"=>"admin/role/status",//角色状态修改



    /*菜单列表*/
    "menu_index"=>"admin/menu/index",
    "menu_add"=>"admin/menu/add",
    "menu_save"=>"admin/menu/save",
    "menu_del"=>"admin/menu/del",
    "menu_edit"=>"admin/menu/edit",
    "menu_updata"=>"admin/menu/updata",
    "menu_status"=>"admin/menu/status",

    /*商品管理*/
    "goods_index"=>"admin/Goods/goods_index",                //商品列表
    "goods_add"   =>"admin/Goods/goods_add",                 //商品添加
    "goods_edit"   =>"admin/Goods/goods_edit",               //商品编辑



    /*订单列表*/
    "order_list"=>"admin/Order/order_list",



    /*策略管理*/
    "free_tactics"=>"admin/tactics/free_tactics",          //面单策略
    "bao_tactics"=>"admin/tactics/bao_tactics",            //红包策略
    "zpoint_tactics"=>"admin/tactics/zpoint_tactics",      //增积分策略
    "big_slam_tactics"=>"admin/tactics/big_slam_tactics",      //大满贯策略
    "new_man_tactics"=>"admin/tactics/new_man_tactics",      //新人帮甩策略
    "old_man_tactics"=>"admin/tactics/old_man_tactics",      //老人帮甩策略


    /*小游戏管理*/
    "answer_bank"=>"admin/Games/answer_bank",                //答题库
    "answer_bri"=>"admin/Games/answer_bri",                  //答题阶梯
    "differ"=>"admin/Games/differ",                          //找不同
    "appear_pro"=>"admin/Games/appear_pro",                  //出现概率



    /*星光值管理*/
    "star_exchange"=>"admin/Star/star_exchange",             //星光值兑换
    "list_exchange"=>"admin/Star/list_exchange",             //星光值兑换列表


    /*会员管理*/
    "member_list"=>"admin/Member/member_list",               //会员列表
    "member_type"=>"admin/Member/member_type",               //会员等级
    "member_back"=>"admin/Member/member_back",               //会员意见反馈



    /*资金流水*/
    "capital"=>"admin/Capital/capital",                      //资金流水










]);