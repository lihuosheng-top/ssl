<?php
namespace app\index\controller;

use think\Controller;
use app\index\controller\Base;
use think\Session;

/**
 * lilu
 * 会员模块
 */
class Member extends Base
{

    /**
     * lilu
     * 会员基本信息
     * @parsm $this->token
     */
    public function member_info()
    {
        //获取参数信息
        $input=input('');
        $res=db('member')->where('token',$this->token)->find();
        unset($res['token']);
        unset($res['token_time']);
        if($res){
           return ajax_success('success',$res);
        }else{
           return ajax_error('error');
        }
    }
    /**
     * lilu
     * 会员排行榜（按照帮甩人数）
     */
    public function member_ranking()
    {
        //获取所有的会员列表-按照帮甩人数排列
        $member_list=db('member')->order('help_num desc')->select();
        
    }
    /**
     * lilu
     * Notes:前端会员修改手机号
     */
    public function member_exchange_phone()
    {
        //获取前端传递的参数
        $input=input('get.');
        if(!$input['account'])
        {
            return ajax_error('error');
        }
        if(!$input['code']){
             return ajax_error('error');
        }
        if(!$input['new_account'])
        {
            return ajax_error('error');
        }
        $code=Session::get('code');
        //判断code是否正确
        if($input['code']==$code){
           $re=db('member')->where('token',$this->token)->setField('account',$input['new_account']);
           return ajax_success('success');
        }else{
            return ajax_error('code error');
        }


    }
    /**
     * lilu
     * Notes:会员头像路径保存
     */
    public function member_pic_save()
    {
        //获取会员的信息（图片路径）
        $input=input('');
        if($input){
             if(!$input['pic_url']){
                 return ajax_error('error');
             }
             //更改头像路径
             $re=db('member')->where('token',$this->token)->setField('head_pic',$input['pic_url']);
             return ajax_success('success');
        }else{
            return ajax_error('error');
        }
    }
    /**
     * lilu
     * Notes:会员地址添加
     */
    public function member_address_add()
    {
        //获取会员的ID
        $input=input();
        if(!$input['member_id']){
            return ajax_error('error');
        }
        $data['member_id']=$input['member_id'];
        $data['address']=$input['address'];
        $data['phone']=$input['phone'];
        $data['is_use']=$input['is_use'];
       if($input['is_use']=='1')     //添加的是默认地址
       {
           $address=db('member_address')->where('member_id',$input['member_id'])->select();
           foreach($address as $k =>$v)
           {
               db('member_address')->where('id',$v['id'])->setField('is_use',0);
           }
       }
        $re=db('member_address')->insert($data);
        if($re){
            return ajax_success('success');
        }else{
            return ajax_error('error');
        }
    }
    /**
     * lilu
     * 会员地址列表
     */
    public function member_address()
    {
        //获取会员的id
        $input=input('');
        if($input)
        {
           //根据会员id获取用户的地址列表
           $re=db('member_address')->where('member_id',$input['member_id'])->select();
           if($re){
               return ajax_success('success',$re);
           }else{
               return ajax_error('error');
           }
        }else{
            return ajax_error('error');
        }
    }
}