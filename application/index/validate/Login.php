<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/5 0005
 * Time: 12:06
 */
namespace  app\index\validate;

use think\Validate;

class Login extends  Validate{
    protected  $rule =[
        'account'=>"require", //用户名
        'password'=>"require",//密码
        'yzm'=>"require", //验证码
    ];
    protected $message = [
        'account.require' => '请输入用户名',
        'password.require' => '请输入密码',
        'yzm.require' => '请输入验证码',
        'yzm.captcha' => '验证码不正确',
    ];
    //设置场景
    protected  $scene =[
        'login'=>['account','password','yzm'],
    ];
}