<?php
namespace app\index\controller;

use think\Controller;

/**
 * lilu
 * 会员模块
 */
class Member extends Controller
{

    public function member_info()
    {
        //获取会员的id
        $id=input('get.id');
        //根据id来获取信息
        $res=db('member')->find($id);
        if($res){
           return ajax_success('success',$res);
        }else{
           return ajax_error('error');
        }
    }
}