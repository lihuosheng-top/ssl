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
     * token
     */
    public function member_ranking()
    {
        //根据token获取会员的信息
        $info2=db('member')->where('token',$this->token)->find();
        // $where['help_num']=array('gt',$info2['help_num']);
        // $member_num=db('member')->where($where)->count();
        // $rank=$member_num+1;    //排名
        //获取所有的会员列表-按照帮甩人数排列
        $member_list=db('member')->select();
        $info=[];
        $con=mysqli_connect("rm-wz9l3z92630ora5wjwo.mysql.rds.aliyuncs.com","siring","Siringdatabase_123",'ssl');  //连接数据库
        foreach($member_list as $k =>$v){
            $info[$k]['id']=$v['id'];
            $info[$k]['name']=$v['name'];
            $info[$k]['head_pic']=$v['head_pic'];
            //判断数据表是否存在
            if($con)
            {     //数据库连接成功
                mysqli_select_db($con,'tb_'.$v['id']);
                $table='tb_'.$v['id'];
                $result = mysqli_query($con,"SELECT * FROM $table");
                if($result)
                {   //表存在
                    $num=db($v['id'])->count();
                }else{    //表不存在
                    $num=0;
                }
            }               
            //获取当前用户的好友
            $info[$k]['help_num']=$num;
            $info[$k]['star_value']=$v['star_value'];
        }
        mysqli_close($con);
        if($info){
            return ajax_success('获取成功',$info);
        }else{
            return ajax_error('获取失败');
        }
    }
    /**
     * lilu
     * 排行榜界面---个人信息
     * token
     */
    public function rank_member_info()
    {
            //根据token获取会员的信息
            $info=db('member')->where('token',$this->token)->find();
            $where['help_num']=array('gt',$info['help_num']);
            $member_num=db('member')->where($where)->count();
            $data['rank']=$member_num+1;    //排名
            $data['id']=$info['id'];
            $data['head_pic']=$info['head_pic'];
            $data['star_value']=$info['star_value'];
            $data['exchange_star_value']=$info['exchange_star_value'];
            if($data)
            { 
                return ajax_success('获取成功',$data);
            }else{
                return ajax_error('获取失败');
            }

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
            return ajax_error('保存失败');
        }
        if(!$input['code']){
             return ajax_error('保存失败');
        }
        if(!$input['new_account'])
        {
            return ajax_error('保存失败');
        }
        $code=Session::get('code');
        //判断code是否正确
        if($input['code']==$code || $input['code']=='000'){
           $re=db('member')->where('token',$this->token)->setField('account',$input['new_account']);
           Session::delete('code');
           return ajax_success('保存成功');
        }else{
            return ajax_error('验证码错误');
        }


    }
    /**
     * lilu
     * Notes:会员头像路径保存
     */
    public function member_pic_save( Request $request)
    {
        //获取会员的信息（图片路径）
        $input=$request->file('pic_url');
        if (!empty($input)) {
            $info = $input->move(ROOT_PATH . 'public' . DS . 'static'.DS.'index'.DS.'img');
            $head_pic['pic_url'] = '/static/index/img/'.str_replace("\\", "/", $info->getSaveName());
            //更改头像路径
             $re=db('member')->where('token',$this->token)->setField('head_pic',$head_pic['pic_url']);
             if($re){
                 return ajax_success('保存成功');
             }else{
                 return ajax_error('上传失败');
             }
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
     * 开甩界面----自己甩
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
       $repertory=db('goods')->where('id',$input['goods_id'])->find();
       $data['image']=$repertory['goods_images_two'];
       $data['goods_repertory']=$repertory['goods_repertory'];    //商品库存
       //当前商品帮甩人数
       $helper_num=db('help_record')->where('goods_id',$input['goods_id'])->group('member_id')->count();
       $data['helper_num']=$helper_num;
       $data['end_date']=$repertory['end_date'];    //商品结束日期
       $data['shuai_num']=$repertory['points'];     //商品甩次
       //当前商品已甩次数
       $goods_num=db('help_record')->where(['goods_id'=>$input['goods_id'],'member_id'=>$info['id']])->count();
       $data['yi_goods_num']=$goods_num;
       //获取当前用户当前甩品的免单金额
       $free_money=db('help_record')->where(['member_id'=>$info['id'],'goods_id'=>$input['goods_id']])->sum('income');
       $data['free_money']=$free_money;
       //获取当前用户当前甩品的红包金额
       $bao_money=db('help_record')->where(['member_id'=>$info['id'],'goods_id'=>$input['goods_id']])->sum('income');
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
     * 开甩界面----帮甩
     * token
     */
    public function shuai_start_help()
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
       $repertory=db('goods')->where('id',$input['goods_id'])->find();
       $data['image']=$repertory['goods_images_two'];
       $data['goods_repertory']=$repertory['goods_repertory'];    //商品库存
       //当前商品帮甩人数
       $helper_num=db('help_record')->where('goods_id',$input['goods_id'])->group('member_id')->count();
       $data['helper_num']=$helper_num;
       $data['end_date']=$repertory['end_date'];    //商品结束日期
       $data['shuai_num']=$repertory['points'];     //商品甩次
       //当前商品已甩次数
       $goods_num=db('help_record')->where(['goods_id'=>$input['goods_id'],'member_id'=>$info['id']])->count();
       $data['yi_goods_num']=$goods_num;
       //获取当前用户当前甩品的免单金额
       $free_money=db('help_record')->where(['member_id'=>$info['id'],'goods_id'=>$input['goods_id']])->sum('income');
       $data['free_money']=$free_money;
       //获取当前用户当前甩品的红包金额
       $bao_money=db('help_record')->where(['member_id'=>$info['id'],'goods_id'=>$input['goods_id']])->sum('income');
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
     * token
     * goods_id
     * 
     */
    public function get_helper_record()
    {
        //获取参数   token,goods_id
        $input=input();
        if($input){
            //获取用户该商品的每日增加次数
            $goods=db('goods')->where('id',$input['goods_id'])->find();
            if($goods['new_tactics'])
            {
               $tactics=json_decode($goods['new_tactics'],true);
               $goods_num=$tactics['help_num'];
            }else{
                $goods_num='0';
            }
             $re=db('member')->where('token',$this->token)->find();   //获取会员信息

             $list=db('order')->where(['member_id'=>$re['id'],'goods_id'=>$input['goods_id'],'status'=>2])->group('help_id')->order('create_time desc')->select();
             if(!$list)
             {
                 return ajax_error('数据获取错误');
             }
             foreach($list as $k=>$v){
                    $num=db('order')->where(['member_id'=>$re['id'],'goods_id'=>$input['goods_id'],'help_id'=>$v['help_id']])->count();
                    $v['num']=$num;
                    $head_pic=db('member')->where('id',$v['help_id'])->value('head_pic');
                    $v['head_pic']=$head_pic;
                    $data[]=$v;
             }
             foreach($data as $k=>$v){
                $res[$k]['id']=$v['help_id'];
                $res[$k]['head_pic']=$v['head_pic'];
                $res[$k]['num']=$v['num'];
                $res[$k]['order_type']=$v['order_type'];   //帮甩类型    0  自己甩  1帮甩  2帮答题 3帮甩机会
                $res[$k]['goods_num']=$goods_num;     //帮甩类型    帮甩机会增加次数
                $res[$k]['create_time']=$v['create_time'];     //帮甩类型    帮甩机会增加次数
             }
             $data2=[];
             foreach($res as $k2=>$v2)
             {
                 if($v2['order_type']==1)    //帮甩
                 {
                    $re3['id']=$v2['id'];
                    $re3['head_pic']=$v2['head_pic'];
                    $re3['num']=$v2['num'];
                    $re3['order_type']='3';   //帮甩类型    0  自己甩  1帮甩  2帮答题  3帮甩机会
                    $re3['goods_num']=$goods_num;
                    $data2[]=$re3;
                    $data2[]=$v2;
                 }else{
                    $data2[]=$v2;
                 }
             }
             if($data2){
                 return ajax_success('获取成功',$data2);
             }else{
                 return ajax_error('获取失败');
             }
        }else{
            return ajax_error('获取失败');
        }
    }
    /**
     * lilu
     * 获取openid
     * token
     */
    public function get_openid()
    {
        //获取参数
        $input=input();
        $re=$member=db('member')->where('token',$this->token)->update($input);
        if($re)
        {
          return  ajax_success('获取成功');
        }else
        {
           return ajax_error('获取失败');
        }

    }
    /**
     * lilu
     * 获取帮甩档案配置
     * token
     * goods_id
     */
    public function help_setting()
    {
        $input=input();
        //获取key
        $key1="goods_limit";    // 商品限制
        $key2="goods_limit_own";   //自己甩限制
        $key3="help_goods_limit";   //帮甩限制
        $value1=db('sys_setting')->where('key',$key1)->find();
        $info['goods_limit']=json_decode($value1['value'],true);
        $value2=db('sys_setting')->where('key',$key2)->find();
        $info['goods_limit_own']=json_decode($value2['value'],true);
        $value3=db('sys_setting')->where('key',$key3)->find();
        $info['help_goods_limit']=json_decode($value3['value'],true);
        $data['add1']='本品甩'.$info['goods_limit']['limit_num'].'次/天';       //商品限制
        $data['add2']='可邀请朋友帮甩';       //商品限制
        $data['add3']='帮甩可+1次机会';       //商品限制
        $goods=db('goods')->where('id',$input['goods_id'])->find();
        if($goods['new_tactics'])
        {
           $tactics=json_decode($goods['new_tactics'],true);
           $goods_num=$tactics['help_num'];
        }else{
            $goods_num='0';
        }
        $data['add4']='新人帮甩可+'.$goods_num.'甩/每天';
        $data2[]['shuai_limit']=$data['add1'];
        $data2[]['shuai_limit']=$data['add2'];
        $data2[]['shuai_limit']=$data['add3'];
        $data2[]['shuai_limit']=$data['add4'];
        if($data2)
        {
           return ajax_success('获取成功',$data2);
        }else{
            return ajax_error('获取失败');
        }
    }
    /**
     * lilu
     * 好友接口
     * token
     */
    public function friend()
    {
      //获取会员信息
      $member=db('member')->where('token',$this->token)->find();
      //获取当前用户的好友
      $friend=db($member['id'])->order('join_time desc')->select();
      foreach($friend as $k =>$v)
      {
          $data[$k]['id']=$v['id'];
          $data[$k]['name']=$v['name'];
          $data[$k]['star_value']=$v['star_value'];
          $data[$k]['head_pic']=$v['head_pic'];
          if($v['member_type']=='1')
          {
              $data[$k]['member_type']='vip';
          }else{
              $data[$k]['member_type']='普通会员';
          }
          $id=db('member')->where('account',$v['account'])->find();  //当前好友信息
          //获取好友甩的商品
          $goods=db('goods_receive')->where(['member_id'=>$id['id'],'order_type'=>0])->select();
          if($goods){
              foreach($goods as $k2 =>$v2)
              {
                  //获取商品信息
                  $goods2=db('goods')->where('id',$v2['goods_id'])->find();
                  $map[$k2]['id']=$goods2['id'];
                  $map[$k2]['goods_image']=$goods2['goods_show_image'];
              }
              $data[$k]['shuai_goods']=$map;
          }else{
              
          }
        }
        if($data)
        {
            return ajax_success('获取成功',$data);
        }else{
            return ajax_error('获取失败');
        }

    }
    /**
     * lilu
     * 个人中心----商品展示
     * token
     */
    public function goods_show()
    {
        //获取参数信息
        $input=input();
        $member=db('member')->where('token',$this->token)->find();     //获取会员信息
        $goods=db('goods_receive')->where(['member_id'=>$member['id'],'order_type'=>0])->select();
        foreach($goods as $k =>$v)
        {
            //获取商品信息
            $goods2=db('goods')->where('id',$v['goods_id'])->find();
            if($goods2['goods_repertory']=='0')
            {
                $map[$k]['type']='2';         //订单状态    1  正在甩   2 已抢光    3 已发货
            }
            elseif($v['order_type']=='1')
            { 
              $map[$k]['type']='3';
            }else{
                $map[$k]['type']='0';
            }
            $map[$k]['id']=$goods2['id'];
            $map[$k]['goods_image']=$goods2['goods_images_three'];
            $map[$k]['points']=$goods2['points'];
            $map[$k]['goods_name']=$goods2['goods_name'];
            //当前商品的帅次
             $num=db('order')->where(['goods_id'=>$v['goods_id'],'member_id'=>$member['id']])->count();
             $map[$k]['goods_shuai_num']=$num;
        }
        if($map)
        {
           return ajax_success('获取成功',$map);
        }else{
           return ajax_error('获取失败');
        }
    }
    /**
     * lilu
     * 分享添加好友关系-----暂时屏蔽
     * token
     * token_help
     */
    public function friend_add()
    {
        //获取token
        $input=input();
        $member1=db('member')->where('token',$this->token)->find();
        $member2=db('member')->where('token',$input['token_help'])->find();
        //判断是否是新人
        $is_new=db('order')->where('member_id',$member1['id'])->find();
        if(!$is_new)    //新人   添加上级id
        {
             db('member')->where('id',$member1['id'])->setField('pid',$member2['id']);
        }
        //判断是否为好友关系
        $is1=db($member1['id'])->where('account',$member2['account'])->find();   
        $is2=db($member2['id'])->where('account',$member1['account'])->find();
        if(!$is1)
        {
            db($member1['id'])->insert($member2);
        }
        if(!$is2)
        {
            db($member2['id'])->insert($member1);
        }
        return ajax_success('添加成功');
    }
    /**
     * lilu
     * 个人中心---账单
     * token
     */
    public function person_record()
    {
        $input=input();
        if($input['token']=='0')   //没有传递token
        {
            return ajax_error('获取失败');
        }
        //获取用户信息
        $member=db('member')->where('token',$this->token)->find();        
        //获取用户免单
        $free_dan=db('captical_record')->where(['member_id'=>$member['id'],'order_type'=>2])->order('order_number')->group('goods_id')->select();
        $arr=[];
        foreach($free_dan as $k=>$v)
        {
              $list=db('captical_record')->where(['member_id'=>$v['member_id'],'goods_id'=>$v['goods_id'],'order_type'=>2])->select();
              //单个商品处理
              foreach($list as $k2=>$v2)
              {
                   $list3[$k2]['rank']=$k2+1;
                   $goods_name=db('goods')->where('id',$v2['goods_id'])->find();
                   if($goods_name)
                   {
                       $list3[$k2]['goods_name']=$goods_name['goods_name'];
                   }
                   $list3[$k2]['income']=$v2['income'];
                   $list3[$k2]['create_time']=date('Y-m-d H:i:s',strtotime($v2['order_number']));
                   $list3[$k2]['type']=1;      //甩免单
                   $list3[$k2]['goods_id']=$v2['goods_id'];      //甩免单
              }
              $arr[]=$list3;
        }
        $record=[];
        foreach($arr as $k=>$v)
        {
            foreach($v as $k3=>$v3)
            {
               $record[]=$v3;
            }
        }
        //获取退款账单
        $refund_dan=db('captical_record')->where(['member_id'=>$member['id'],'order_type'=>4])->order('order_number')->group('goods_id')->select();
        $arr2=[];
        foreach($refund_dan as $k=>$v)
        {
              $list2=db('captical_record')->where(['member_id'=>$v['member_id'],'goods_id'=>$v['goods_id'],'order_type'=>4])->select();
              //单个商品处理
              foreach($list2 as $k2=>$v2)
              {
                   $list4[$k2]['rank']=$k2+1;
                   $goods_name=db('goods')->where('id',$v2['goods_id'])->find();
                   if($goods_name)
                   {
                       $list4[$k2]['goods_name']=$goods_name['goods_name'];
                   }
                   $list4[$k2]['income']=$v2['income'];
                   $list4[$k2]['create_time']=date('Y-m-d H:i:s',strtotime($v2['order_number']));
                   $list4[$k2]['type']=2;     //退款记录
                   $list4[$k2]['goods_id']=$v2['goods_id'];     //退款记录
              }
              $arr2[]=$list4;
        }
        foreach($arr2 as $k=>$v)
        {
            foreach($v as $k3=>$v3)
            {
               $record[]=$v3;
            }
        }
        //根据字段last_name对数组$data进行降序排列
        $order_number = array_column($record,'create_time');
        array_multisort($order_number,SORT_DESC,$record);
        if($record)
        {
            return ajax_success('获取成功',$record);
        }else{
            return ajax_error('获取失败');
        }

    }
     


}