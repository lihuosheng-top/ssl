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
    "index"      =>"index/Index/index",               //前端登录页面
    "index_login"=>"index/Login/index_login",         //前端登录--获取验证码
    "index_dolog"=>"index/Login/index_dolog",         //前端登录处理


   /*商品*/
   "goods_info"=>"index/Goods/goods_info",            //商品详细信息
   
   /*订单模块*/
   "help_record"=>"index/Order/help_record" ,          //生成帮甩记录
   "help_count"=>"index/Order/help_count"    ,         //帮甩统计
   "goods_order"=>"index/Order/goods_order",           //商品订单生成----自己甩----微信
   "goods_order2"=>"index/Order/goods_order2",           //商品订单生成----自己甩---支付宝
   "goods_order_help"=>"index/Order/goods_order_help",  //商品订单生成----帮甩---微信
   "goods_order_help2"=>"index/Order/goods_order_help2", //商品订单生成----帮甩---支付宝
   "order_refund"=>"index/Order/order_refund",         //商品账单展示
   "order_refund_help"=>"index/Order/order_refund_help",   //帮甩商品账单展示
   "order_refund_do"=>"index/Order/order_refund_do",   //商品退款处理



   /**支付---微信*/
   'wxpaynotifyurl'=>"index/Wxpay2/wxpaynotifyurl"  ,    //微信支付回调地址      
   'wxpayrefundurl'=>"index/Wxpay2/wxpayrefundurl"  ,    //退款回调地址      
   
   /**支付---支付宝 */
   'alipay'=>"index/Alipay/alipay"  ,             //支付宝支付          
   'ali_pay'=>"index/Alipay/ali_pay"            ,
   'alipaynotify'=>"index/Alipay/alipaynotify"            ,



   /**会员模块 */
   'member_info'=>"index/Member/member_info"  ,            //会员信息
   'member_exchange_phone'=>"index/Member/member_exchange_phone"  ,  //会员信息编辑
   'member_pic_save'=>"index/Member/member_pic_save"  ,    //会员头像路径保存
   'member_address'=>"index/Member/member_address"  ,      //会员地址
   'member_address_add'=>"index/Member/member_address_add"  ,   //会员地址添加
   'member_address_edit'=>"index/Member/member_address_edit"  ,   //会员地址添加
   'member_address_del'=>"index/Member/member_address_del"  ,   //会员地址添加
   'shuai_start'=>"index/Member/shuai_start"  ,                 //开甩界面
   'member_name_edit'=>"index/Member/member_name_edit"  ,       //昵称编辑
   'get_helper_record'=>"index/Member/get_helper_record"  ,     //获取帮甩记录
   'member_ranking'=>"index/Member/member_ranking"  ,           //排行榜
   'rank_member_info'=>"index/Member/rank_member_info"  ,       //排行榜界面--个人信息
   'get_openid'=>"index/Member/get_openid"  ,       //获取openid
   'help_setting'=>"index/Member/help_setting"  ,   //帮甩设置
   'friend'=>"index/Member/friend"  ,               //好友展示
   'friend_add'=>"index/Member/friend_add"  ,       //分享----添加好友关系
   'goods_show'=>"index/Member/goods_show"  ,       //排行榜界面--个人信息
   'person_record'=>"index/Member/person_record"  ,       //排行榜界面--个人信息账单
   'goods_get'=>"index/Member/goods_get"  ,          //领取甩好的商品
   'goods_confirm'=>"index/Member/goods_confirm"  ,  //确认收货
   'rank_three'=>"index/Member/rank_three"  ,        //当前商品的甩次排行前三
   'get_new_old_record'=>"index/Member/get_new_old_record"  ,        //甩甩乐档案--新旧人帮甩

   /**游戏 */
   'game'=>"index/Game/game"  ,                           //游戏
   'is_answer'=>"index/Game/is_answer"  ,                 //判断是否答题
   'is_answer_help'=>"index/Game/is_answer_help"  ,       //判断是否答题
   'is_answer_help2'=>"index/Game/is_answer_help2"  ,     //判断是否答题
   'is_right'=>"index/Game/is_right"  ,                   //判断是否答题
   'is_right_help'=>"index/Game/is_right_help"  ,          //判断是否答题
   'is_right_help2'=>"index/Game/is_right_help2"  ,         //判断是否答题
   'get_money'=>"index/Game/get_money"  ,                   //获取免单金额和红包金额
   'back_bao_money'=>"index/Game/back_bao_money"  ,         //红包反还
   'unlock'=>"index/Game/unlock"  ,                         //自动解锁记录
   'member_answer_lock'=>"index/Game/member_answer_lock"  , //扫码分享--判断用户是否解锁

   /**甩甩乐分享 */
   'ssl_share'=>"index/Index/ssl_share"  ,                  //甩甩乐分享
   'ssl'=>"index/Index/ssl"  ,                  //甩甩乐分享
   'abc'=>"index/Index/abc"  ,                  //甩甩乐分享


   /**星光值管理 */
   'prize_list'=>"index/Star/prize_list"  ,                 //星光商品列表
   'get_star_goods'=>"index/Star/get_star_goods"  ,         //星光商品领取

  /**测试 */
  'test'=>"index/Login/test"  ,         //星光商品领取
 


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
    "logout"=>"admin/Login/logout",  //退出登录


    /*验证码*/
    "login_captcha"=>"admin/Login/captchas",


    /*后台设置*/
    "admin_index"=>"admin/Admin/index",
    "admin_add"=>"admin/Admin/add",
    "admin_save"=>"admin/Admin/save",
    "admin_del"=>"admin/adAdminmin/del",
    "admin_edit"=>"admin/admin/edit",
    "admin_updata"=>"admin/admin/updata",
    "admin_status"=>"admin/admin/status",
    "admin_passwd"=>"admin/admin/passwd",
    "admin_opera"=>"admin/admin/admin_opera",                       //玩法广告
    "admin_agreement"=>"admin/Admin/admin_agreement",               //平台协议
    "admin_agreement_do"=>"admin/Admin/admin_agreement_do",         //平台协议处理
    "admin_agreement_add"=>"admin/Admin/admin_agreement_add",       //添加平台协议
    "admin_goods_tactics"=>"admin/admin/admin_goods_tactics",       //甩品策略
    "admin_message_note"=>"admin/admin/admin_message_note",         //消息提醒
    "admin_message_note_do"=>"admin/admin/admin_message_note_do",   //消息提醒处理
    "admin_lock_time"=>"admin/admin/admin_lock_time",               //锁定时间
    "admin_lock_time_do"=>"admin/admin/admin_lock_time_do",         //锁定时间
    "admin_get_star"=>"admin/admin/admin_get_star",                 //星光值获取配置
    "admin_get_star_do"=>"admin/admin/admin_get_star_do",           //星光值获取配置
    "admin_fei"=>"admin/admin/admin_fei",                           //获取平台手续费
    "admin_fei_do"=>"admin/admin/admin_fei_do",                           //获取平台手续费DS




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
    "goods_search"   =>"admin/Goods/goods_search",           //商品检索
    "goods_label_edit"   =>"admin/Goods/goods_label_edit",   //商品上下架设置
    "goods_add_do"   =>"admin/Goods/goods_add_do",           //商品添加处理
    "goods_edit_do"   =>"admin/Goods/goods_edit_do",           //商品编辑处理
    "goods_del"   =>"admin/Goods/goods_del",                  //商品删除
    "goods_strategy"   =>"admin/Goods/goods_strategy",        //商品策略配置
    "goods_strategy_do"   =>"admin/Goods/goods_strategy_do",        //商品策略配置处理


    /*订单列表*/
    "order_list"=>"admin/Order/order_list",                   //订单列表
    "order_search"=>"admin/Order/order_search",               //订单检索
    "order_status"=>"admin/Order/order_status",               //订单检索
    "order_del"=>"admin/Order/order_del",                     //订单删除
    "get_orderinfo"=>"admin/Order/get_orderinfo",             //获取订单信息
    "send_message"=>"admin/Order/send_message",               //发送短信---提醒
    "order_save"=>"admin/Order/order_save",                   //快递单信息保存




    /*策略管理*/
    "free_tactics"=>"admin/Tactics/free_tactics",            //免单策略
    "bao_tactics"=>"admin/Tactics/bao_tactics",              //红包策略
    "zpoint_tactics"=>"admin/Tactics/zpoint_tactics",        //赠积分策略
    "big_slam_tactics"=>"admin/Tactics/big_slam_tactics",    //大满贯策略
    "new_man_tactics"=>"admin/Tactics/new_man_tactics",      //新人帮甩策略
    "old_man_tactics"=>"admin/Tactics/old_man_tactics",      //老人帮甩策略
    "bao_tactics_do"=>"admin/Tactics/bao_tactics_do",              //红包策略处理
    "zpoint_tactics_do"=>"admin/Tactics/zpoint_tactics_do",        //赠积分策略处理
    "big_slam_tactics_do"=>"admin/Tactics/big_slam_tactics_do",    //大满贯策略处理
    "new_man_tactics_do"=>"admin/Tactics/new_man_tactics_do",      //新人帮甩策略处理
    "old_man_tactics_do"=>"admin/Tactics/old_man_tactics_do",      //老人帮甩策略处理
    "free_tactics_do"=>"admin/Tactics/free_tactics_do",            //免单策略处理


    /*小游戏管理*/
    "answer_bank"=>"admin/Games/answer_bank",                //答题库
    "answer_bank_add"=>"admin/Games/answer_bank_add",        //添加答题
    "answer_bri"=>"admin/Games/answer_bri",                  //答题阶梯
    "differ"=>"admin/Games/differ",                          //找不同
    "differ_add"=>"admin/Games/differ_add",                  //添加找不同
    "appear_pro"=>"admin/Games/appear_pro",                  //出现概率
    "differ_index"=>"admin/Games/differ_index",              //出现概率
    "update_images"=>"admin/Games/update_images",              //更新图片
    "answer_bank_edit"=>"admin/Games/answer_bank_edit",        //答题编辑
    "answer_bank_edit_do"=>"admin/Games/answer_bank_edit_do",   //答题编辑处理
    "answer_bank_del"=>"admin/Games/answer_bank_del",        //答题删除
    "appear_pro_do"=>"admin/Games/appear_pro_do",            //出现概率处理
    "answer_bri_do"=>"admin/Games/answer_bri_do",            //答题阶梯处理
    "answer_bank_search"=>"admin/Games/answer_bank_search",            //答题阶梯处理

    



    /*星光值管理*/   
    "star_exchange"=>"admin/Star/star_exchange",                       //星光值兑换
    "star_exchange_search"=>"admin/Star/star_exchange_search",         //星光值兑换
    "prize_del"=>"admin/Star/prize_del",                     //星光值兑换
    "prize_edit"=>"admin/Star/prize_edit",                   //星光值兑换
    "prize_edit_do"=>"admin/Star/prize_edit_do",             //星光值兑换
    "prize_add"=>"admin/Star/prize_add",                     //奖品添加
    "prize_add_do"=>"admin/Star/prize_add_do",               //奖品添加处理
    "list_exchange"=>"admin/Star/list_exchange",             //星光值兑换列表
    "exchange_list_search"=>"admin/Star/exchange_list_search", //星光值兑换列表查询
    "exchange_list_dels"=>"admin/Star/exchange_list_dels", //星光值兑换列表批量删除
    "prize_search"=>"admin/Star/prize_search",               //星光商品检索
    "prize_dels"=>"admin/Star/prize_dels",                   //星光商品批量删除


    /*会员管理*/
    "member_list"=>"admin/Member/member_list",               //会员列表
    "member_list_edit"=>"admin/Member/member_list_edit",     //会员列表编辑
    "member_list_edit_do"=>"admin/Member/member_list_edit_do",     //会员列表编辑处理
    "member_search"=>"admin/Member/member_search",           //会员检索
    "member_del"=>"admin/Member/member_del",                 //会员删除
    "member_dels"=>"admin/Member/member_dels",               //会员批量删除
    


    /*资金流水*/
    "capital"=>"admin/Capital/capital",                       //资金流水
    "capital_details"=>"admin/Capital/capital_details",       //资金流水详情
    "goods_bill"=>"admin/Capital/goods_bill",                 //商品账单
    "goods_bill_details"=>"admin/Capital/goods_bill_details",                 //商品账单
    "capital_search"=>"admin/Capital/capital_search",         //帮甩检索
    "help_record_del"=>"admin/Capital/help_record_del",       //帮甩删除
    "goods_bill_search"=>"admin/Capital/goods_bill_search",   //商品账单检索

    /*设置*/











]);