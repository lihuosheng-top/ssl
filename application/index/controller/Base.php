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
     */
    protected $beforeActionList =[
      'first',                        //值为空（当前所有方法的前置方法）
    ];
    /**
     * lilu
     * Notes:判断用户token是否正确，时效
     */
    protected  function first()
    {
        //获取缓存信息
        $token=input();
        $re=db('member')->where('token',$token['token'])->find();
        if($re)
        {
           //判断token时效
           if($re['token_time']<= time()){    //失效
                    //重新生成token
                       $key=$re['passwd'];          //客户秘钥--注册时生成
                       $data['time']=time();        //当前时间戳
                       $token_new=md5($key.md5($data['time']));    //token加密
                       $map['token']=$token_new;
                       $map['token_time']=time()+600;
                       $re=db('member')->where('token',$token['token'])->update($map);
                       if($re){
                          echo $token_new;
                       }else{
                           $this->error('error');
                       }
           }

        }else{

        }
    }
}