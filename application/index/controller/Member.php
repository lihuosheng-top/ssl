<?php
namespace app\index\controller;

use think\Controller;
use app\index\controller\Base;
use think\Session;
use think\Request;
use think\Image;

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
        $input=input('');
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
        if($input['code']==$code || $input['code']=='000'){
           $re=db('member')->where('token',$this->token)->setField('account',$input['new_account']);
           Session::delete('code');
           return ajax_success('success');
        }else{
            return ajax_error('code error');
        }


    }
    /**
     * lilu
     * Notes:会员头像路径保存
     */
    public function member_pic_save( Request $request)
    {
        //获取会员的信息（图片路径）
        $input[0]=$request->file('pic_url');
        if (!empty($input)) {
            foreach ($input as $k=>$v) {
                $info = $v->move(ROOT_PATH . 'public' . DS . 'static'.DS.'index'.DS.'img');
                $head_pic['pic_url'] = '/static/index/img/'.str_replace("\\", "/", $info->getSaveName());
            }
            //更改头像路径
             $re=db('member')->where('token',$this->token)->setField('head_pic',$head_pic['pic_url']);
             return ajax_success('保存成功');
        }else{
            return ajax_error('上传文件为空');
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
        $member=db('member')->where('token',$this->token)->find();
        $data['member_id']=$member['id'];
        $data['phone']=$input['phone'];
        $data['address']=$input['address'];
        $data['detail_address']=$input['detail_address'];
        $data['is_use']=$input['is_use'];
        $data['name']=$input['name'];
       if($input['is_use']=='1')     //添加的是默认地址
       {
           $address=db('member_address')->where('member_id',$member['id'])->select();
           foreach($address as $k =>$v)
           {
               db('member_address')->where('id',$v['id'])->setField('is_use',0);
           }
       }
        $re=db('member_address')->insert($data);
        if($re){
            return ajax_success('添加成功');
        }else{
            return ajax_error('添加失败');
        }
    }
    /**
     * lilu
     * 会员地址列表
     */
    public function member_address()
    {
        if($this->token)
        {
           //根据会员id获取用户的地址列表
           $member_id=db('member')->where('token',$this->token)->find();
           $re=db('member_address')->where('member_id',$member_id['id'])->select();
           if($re){
               return ajax_success('获取成功',$re);
           }else{
               return ajax_error('获取失败');
           }
        }else{
            return ajax_error('获取失败');
        }
    }
    /**
     * lilu
     * Notes:会员地址编辑
     */
    public function member_address_edit()
    {
        //获取会员的ID
        $input=input();
        $member=db('member')->where('token',$this->token)->find(); //会员信息
        $data['member_id']=$member['id'];
        $data['address']=$input['address'];
        $data['detail_address']=$input['detail_address'];
        $data['phone']=$input['phone'];
        $data['is_use']=$input['is_use'];
        $data['name']=$input['name'];
       if($input['is_use']=='1')     //添加的是默认地址
       {
           $address=db('member_address')->where('member_id',$member['id'])->select();
           foreach($address as $k =>$v)
           {
               db('member_address')->where('id',$v['id'])->setField('is_use',0);
           }
       }
        $re=db('member_address')->where('id',$input['id'])->update($data);
        if($re){
            return ajax_success('编辑成功');
        }else{
            return ajax_error('编辑失败');
        }
    }
    /**
     * lilu 
     * 开甩界面
     */
    public function shuai_start()
    {
       //获取信息
       $input=input();   //商品id
       //获取当前甩客信息
       $info=db('member')->where('token',$this->token)->find();
       $data['id']=$info['id'];
       $data['name']=$info['name'];
       $data['head_pic']=$info['head_pic'];
       $data['member_type']=$info['member_type'];
       $data['goods_num']=$info['goods_num'];    //客户甩品数量
       $data['member_type']=$info['member_type'];  //会员等级
       $repertory=db('goods')->where('id',$input['good_id'])->find();
       $data['image']=$repertory['goods_images_two'];
       $data['goods_repertory']=$repertory['goods_repertory'];    //商品库存
       //当前商品帮甩人数
       $helper_num=db('help_record')->where('good_id',$input['good_id'])->group('member_id')->count();
       $data['helper_num']=$helper_num;
       $data['end_date']=$repertory['end_date'];    //商品结束日期
       $data['shuai_num']=$repertory['points'];     //商品甩次
       //当前商品已甩次数
       $goods_num=db('help_record')->where(['good_id'=>$input['good_id'],'member_id'=>$info['id']])->count();
       $data['yi_goods_num']=$goods_num;
       //获取当前用户当前甩品的免单金额
       $free_money=db('help_record')->where(['member_id'=>$info['id'],'good_id'=>$input['good_id']])->sum('income');
       $data['free_money']=$free_money;
       //获取当前用户当前甩品的红包金额
       $bao_money=db('help_record')->where(['member_id'=>$info['id'],'good_id'=>$input['good_id']])->sum('income');
       $data['bao_money']=$bao_money;
       if($data)
       {
           return ajax_success('获取成功',$data);
       }else{
           return ajax_error('获取信息失败');
       }
    }
    /**
     * lilu
     * Notes:会员地址删除
     */
    public function member_address_del()
    {
        //获取会员的ID
        $input=input();
        if($input['id']){
            $member=db('member_address')->delete($input['id']); //会员信息
            if($member){
                $id=db('member')->where('token',$this->token)->find();
                $list=db('member_address')->where(['member_id'=>$id['id'],'is_use'=>1])->select();
                if(!$list){
                    $re=db('member_address')->where('member_id',$id['id'])->select();
                    if($re){
                        $re=db('member_address')->where('member_id',$id['id'])->find();
                        $re2=db('member_address')->where('member_id',$re['member_id'])->setField('is_use','1');
                    }
                }
                return ajax_success('编辑成功');
            }else{
                return ajax_error('编辑失败');
            }
        }
        
    }
    /**
     * lilu
     * 会员昵称修改
     */
    public function member_name_edit()
    {
        //获取修改的新昵称
        $input=input();
        $data['name']=$input['name'];
        $re=db('member')->where('token',$this->token)->update($data);
        if($re)
        {
           return ajax_success('昵称修改成功');
        }else{
            return ajac_eror('昵称修改失败');
        }
    }
    /**
     * lilu
     * 获取当前客户当前商品的帮甩记录
     */
    public function get_helper_record()
    {
        //获取参数   token,good_id
        $input=input();
        if($input){
             $re=db('member')->where('token',$this->token)->find();
             $list=db('help_record')->where(['member_id'=>$re['id'],'good_id'=>$input['good_id']])->group('help_id')->select();
             foreach($list as $k=>$v){
                  if($v['help_id']=='0'){

                  }else{
                    $num=db('help_record')->where(['member_id'=>$re['id'],'good_id'=>$input['good_id'],'help_id'=>$v['help_id']])->count();
                    $v['num']=$num;
                    $head_pic=db('member')->where('id',$v['help_id'])->value('head_pic');
                    $v['head_pic']=$head_pic;
                    $data[]=$v;
                  }
             }
             foreach($data as $k=>$v){
                $res[$k]['id']=$v['help_id'];
                $res[$k]['head_pic']=$v['head_pic'];
                $res[$k]['num']=$v['num'];
                $res[$k]['order_type']=$v['order_type'];   //帮甩类型    1    自己甩  2帮甩  3帮答题
             }
             if($res){
                 return ajax_success('获取成功',$res);
             }else{
                 return ajax_error('获取失败');
             }
        }else{
            return ajax_error('获取失败');
        }
    }

}