<?php
namespace app\index\controller;

use think\Controller;
use think\Session;

/**
 * lilu
 * 前端基础控制器
 */
class Base extends Controller
{
    /**
     * lilu
     * 自定义前置操作
     * 初始化方法,可以控制用户权限、获取菜单等等，只要是继承base类的其它业务类就不需要再重写
     */
    protected function _initialize()
    {
        parent::_initialize();
        //获取缓存信息
        $token=input();
        $re=db('member')->where('token',$token['token'])->find();
        if($re)
        {
           //判断token时效
        //    if($re['token_time']<= time()){    //失效
        //             //重新生成token
        //                $key=$re['passwd'];          //客户秘钥--注册时生成
        //                $data['time']=time();        //当前时间戳
        //                $token_new=md5($key.md5($data['time']));    //token加密
        //                $map['token']=$token_new;
        //                $map['token_time']=time()+30*24*60*60;
        //                $res=db('member')->where('token',$token['token'])->update($map);  //更新为新的token
        //                if(!$res){
        //                   return ajax_error('error');
        //                }else{
        //                    $this->token=$token_new;
        //                }
        //    }else{
        //        $token_new=$token['token'];
        //        $this->token=$token_new;
        //    }
        $this->token=$token['token'];
        }else{
             return ajax_error('token error');
        }
 
    }
    
}