<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/4/30
 * Time: 15:59
 */

namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Loader;
use think\Session;
use think\Cache;
use think\Request;
use think\Config;

class Login extends Controller{


    /**
     **************lilu*******************
     * @param Request $request
     * Notes:登录短信验证
     **************************************
     */
    public function index_login()
    {
         $post = input('');              //获取前端提交数据
        if($post){
            $re=DB::name('member')
                ->where('account',$post['account'])
                ->find();
            //判断用户是否存在
            if($re){
                //发送短信
                $code=rand(100,999);
                Session::set('code',$code);
                $content="【甩甩乐】尊敬的用户，您本次验证码为".$code."，十分钟内有效";//带签名的短息内容
                $mobile = $post['account'];//手机号
                $re2= sms_message($content,$mobile);
                if($re2){
                    return  ajax_success('发送短信成功');
                }else{
                    return  ajax_error('发送短信失败');
                }
            }else{                           //用户信息不存在
                //注册用户信息
                $user['account']=$post['account'];       //用户手机号
                $user['passwd']=md5($post['account'].time());
                $user['join_time']=time();
                $user['head_pic']='/static/index/img/head_pic.jpg';
                $re2=DB::name('member')
                    ->insert($user);                //添加用户信息
                if($re2){
                    //发送短信
                    $code=rand(100,999);
                    Session::set('code',$code);
                    $content='您的验证码：'.$code;
                    $re3=sms_message($content,$post['account']);
//                        //获取token
//                        $key=$user['passwd'];          //客户秘钥--注册时生成
//                        $data['time']=time();        //当前时间戳
//                        $data['token']=md5($key.md5($data['time']));    //token加密
                    if($re3){
                        return  ajax_success('发送短信成功');
                    }else{
                        return  ajax_error('发送短信失败');
                    }
                }else{
                    return  ajax_error('发送短信失败');
                }

            }
        }else{
            return  ajax_error('发送短信失败');
        }
    }


    /**
     **************lilu*******************
     * @param Request $request
     * Notes:登陆操作
     **************************************
     * @param Request $request
     */
    public function index_dolog(Request $request){
            $user_mobile =$request->only(['account'])["account"];       //获取登录账号
            $code =$request->only(["code"])["code"];                    //获取验证码
            if(empty($user_mobile)){
                return  ajax_error('手机号不能为空',$user_mobile);
            }
            if(empty($code)){
                $code='000';            //若code为空，给默认值000
            }
            //获取缓存验证码，并判断验证码
            $code_se=Session::get('code');
            $user=DB::name('member')
                ->where('account',$user_mobile)
                ->find();
            if($user){           //用户存在
                //验证码判断
                // if($code==$code_se){   
                if(1){   
                    Session::delete('code');
                    //获取token
                    $key=$user['passwd'];          //客户秘钥--注册时生成
                    $data['token_time']=time()+600;        //当前时间戳
                    $data['token']=md5($key.md5($data['token_time']));    //token加密
                    //将token保存数据库
                    $re=db('member')->where('account',$user_mobile)->update($data);
                    if($re){
                        //清空code缓存
                      Session::delete('code');
                      //
                      $re2=DB::name('member')->where('account',$user_mobile)->find();
                      $re3=$re2['id'];
                            //新建会员表
                            $sql="CREATE TABLE "."tb_".$re3." (
                              `id`  int NOT NULL AUTO_INCREMENT ,
                              `name`  varchar(255) NULL COMMENT '昵称' ,
                              `account`  varchar(255) NULL COMMENT '账号' ,
                              `passwd`  varchar(255) NULL COMMENT '密码' ,
                              `sex`  tinyint NULL COMMENT '性别' ,
                              `star_value`  int NOT NULL DEFAULT 0 COMMENT '星光值' ,
                              `address`  varchar(255) NULL ,
                              `head_pic`  varchar(255) NULL ,
                              `is_use`  tinyint NULL ,
                              `join_time`  varchar(255) NULL ,
                              `pid`  tinyint NOT NULL DEFAULT 1 COMMENT '上级id   好友关系' ,
                              `member_type`  tinyint NOT NULL DEFAULT 0 ,
                              `help_num`  int NOT NULL DEFAULT 0 COMMENT '帮' ,
                              `token`  varchar(255) NULL ,
                              `token_time`  varchar(255) NULL ,
                              `exchange_star_value`  int NOT NULL DEFAULT 0 ,
                              `income`  float NULL COMMENT '收入' ,
                              `goods_num`  int NULL DEFAULT 0 COMMENT '甩到的商品数量' ,
                              `openid`  varchar(255) NULL COMMENT 'openid' ,
                              `is_new`  tinyint NULL DEFAULT 1 COMMENT '1  新人   0  不是' ,
                              PRIMARY KEY (`id`)
                              )
                              ENGINE=InnoDB
                              COMMENT='用户好友表'
                            ";
                            $con=mysqli_connect("rm-wz9l3z92630ora5wjwo.mysql.rds.aliyuncs.com","siring","ei%&#w9l3z3",'ssl');
                            if($con)
                            {
                                mysqli_query($con,$sql);   //新建表
                              //   $re2=DB::name($re)->insertGetId($member);
                            }
                      return   ajax_success('登录成功',$data);
                    }else{
                      return   ajax_error('登录失败');
                    }
                }elseif($code=='000'){
                    //获取token
                    $key=$user['passwd'];          //客户秘钥--注册时生成
                    $data['token_time']=time();        //当前时间戳
                    $data['token']=md5($key.md5($data['token_time']));    //token加密
                    //将token保存数据库
                    $re=db('member')->where('account',$user_mobile)->update($data);
                    if($re !== false){
                        //清空code缓存
                      Session::delete('code');
                      //
                      $re2=DB::name('member')->where('account',$user_mobile)->find();
                      $re3=$re2['id'];
                            //新建会员表
                            $sql="CREATE TABLE "."tb_".$re3." (
                              `id`  int NOT NULL AUTO_INCREMENT ,
                              `name`  varchar(255) NULL COMMENT '昵称' ,
                              `account`  varchar(255) NULL COMMENT '账号' ,
                              `passwd`  varchar(255) NULL COMMENT '密码' ,
                              `sex`  tinyint NULL COMMENT '性别' ,
                              `star_value`  int NOT NULL DEFAULT 0 COMMENT '星光值' ,
                              `address`  varchar(255) NULL ,
                              `head_pic`  varchar(255) NULL ,
                              `is_use`  tinyint NULL ,
                              `join_time`  varchar(255) NULL ,
                              `pid`  tinyint NOT NULL DEFAULT 1 COMMENT '上级id   好友关系' ,
                              `member_type`  tinyint NOT NULL DEFAULT 0 ,
                              `help_num`  int NOT NULL DEFAULT 0 COMMENT '帮' ,
                              `token`  varchar(255) NULL ,
                              `token_time`  varchar(255) NULL ,
                              `exchange_star_value`  int NOT NULL DEFAULT 0 ,
                              `income`  float NULL COMMENT '收入' ,
                              `goods_num`  int NULL DEFAULT 0 COMMENT '甩到的商品数量' ,
                              `openid`  varchar(255) NULL COMMENT 'openid' ,
                              `is_new`  tinyint NULL DEFAULT 1 COMMENT '1  新人   0  不是' ,
                              PRIMARY KEY (`id`)
                              )
                              ENGINE=InnoDB
                              COMMENT='用户好友表'
                            ";
                            $con=mysqli_connect("rm-wz9l3z92630ora5wjwo.mysql.rds.aliyuncs.com","siring","ei%&#w9l3z3",'ssl');
                            if($con)
                            {
                                mysqli_query($con,$sql);   //新建表
                              //   $re2=DB::name($re)->insertGetId($member);
                            }

                      return   ajax_success('登录成功',$data);
                    }else{
                      return   ajax_error('登录失败');
                    }
                }else{
                    return   ajax_error('登录失败');

                }
            }else{             //用户不存在，注册
                //注册新用户
                $member['account']=$user_mobile;
                $member['passwd']=md5($member['account'].time());
                $member['join_time']=time();
                $member['head_pic']='/static/index/img/head_pic.jpg';
                $re=DB::name('member')->insert($member);
                $re2=DB::name('member')->where('account',$user_mobile)->find();
                $re3=$re2['id'];
                      //新建会员表
                      $sql="CREATE TABLE "."tb_".$re3." (
                        `id`  int NOT NULL AUTO_INCREMENT ,
                        `name`  varchar(255) NULL COMMENT '昵称' ,
                        `account`  varchar(255) NULL COMMENT '账号' ,
                        `passwd`  varchar(255) NULL COMMENT '密码' ,
                        `sex`  tinyint NULL COMMENT '性别' ,
                        `star_value`  int NOT NULL DEFAULT 0 COMMENT '星光值' ,
                        `address`  varchar(255) NULL ,
                        `head_pic`  varchar(255) NULL ,
                        `is_use`  tinyint NULL ,
                        `join_time`  varchar(255) NULL ,
                        `pid`  tinyint NOT NULL DEFAULT 1 COMMENT '上级id   好友关系' ,
                        `member_type`  tinyint NOT NULL DEFAULT 0 ,
                        `help_num`  int NOT NULL DEFAULT 0 COMMENT '帮' ,
                        `token`  varchar(255) NULL ,
                        `token_time`  varchar(255) NULL ,
                        `exchange_star_value`  int NOT NULL DEFAULT 0 ,
                        `income`  float NULL COMMENT '收入' ,
                        `goods_num`  int NULL DEFAULT 0 COMMENT '甩到的商品数量' ,
                        `openid`  varchar(255) NULL COMMENT 'openid' ,
                        `is_new`  tinyint NULL DEFAULT 1 COMMENT '1  新人   0  不是' ,
                        PRIMARY KEY (`id`)
                        )
                        ENGINE=InnoDB
                        COMMENT='用户好友表'
                      ";
                      $con=mysqli_connect("rm-wz9l3z92630ora5wjwo.mysql.rds.aliyuncs.com","siring","ei%&#w9l3z3",'ssl');
                      if($con)
                      {
                          mysqli_query($con,$sql);   //新建表
                        //   $re2=DB::name($re)->insertGetId($member);
                      }
                      //验证码判断
                       $code_se=Session::get('code');
                    // if($code==$code_se || $code=='000'){                             //验证码通过
                        Session::delete('code');
                        //获取token
                        $key=$user['passwd'];          //客户秘钥--注册时生成
                        $data['token_time']=time();        //当前时间戳
                        $data['token']=md5($key.md5($data['token_time']));    //token加密
                        //将token保存数据库
                        $re3=db('member')->where('account',$user_mobile)->update($data);
                        // $re4=db($re)->where('account',$user_mobile)->update($data);
                            if($re){
                                return   ajax_success('登录成功',$data);
                            }else{
                                return   ajax_error('登录失败');
                            }
                    //   }else{
                    //             return   ajax_error('登录失败');
                    //   }
            }
    }

    /**
     **************lilu*******************
     * @param Request $request
     * Notes:退出操作
     **************************************
     */
    public function logout(Request $request){
        if($request->isPost()){
            //前台退出
            Session('member',null);
            Session::delete("user");//用户推出
            //后台退出
            Session("user_id",null);
            Session("user_info", null);
            return ajax_success('退出成功',['status'=>1]);
        }
    }

    /**
     **************lilu*******************
     * @param Request $request
     * Notes:判断是否登录
     **************************************
     * @param Request $request
     */
    public function isLogin(Request $request){
        if($request->isPost()){
            $member_data =session('member');
            if(!empty($member_data)){
                $phone_num = $member_data['phone_number'];
                if(!empty($phone_num)){
                    $return_data =Db::name('pc_user')
                        ->where("phone_number",$phone_num)
                        ->find();
                    if(!empty($return_data)){
                        return ajax_success('用户信息返回成功',$return_data);
                    }else{
                        return ajax_error('没有该用户信息',['status'=>0]);
                    }
                }
            }else{
                return ajax_error('请前往登录',['status'=>0]);
            }
        }
    }
    /*
     *   lilu
     *   notes:前端首页展示
     *   @param  Request  $request
     */
    public function index(){
        return view('index');
    }
    /**
     * lilu
     * 
     */
  public function test()
  {
    // $sql="CREATE TABLE ".'tb_2'." (
    //     `id`  int NOT NULL ,
    //     `name`  varchar(255) NULL COMMENT '昵称' ,
    //     `account`  varchar(255) NULL COMMENT '账号' ,
    //     `passwd`  varchar(255) NULL COMMENT '密码' ,
    //     `star_value`  int NOT NULL DEFAULT 0 COMMENT '星光值' ,
    //     `address`  varchar(255) NULL ,
    //     `head_pic`  varchar(255) NULL ,
    //     `is_use`  tinyint NULL ,
    //     `join_time`  varchar(255) NULL ,
    //     `pid`  tinyint NOT NULL DEFAULT 1 COMMENT '上级id   好友关系' ,
    //     `member_type`  tinyint NOT NULL DEFAULT 0 ,
    //     `help_num`  int NOT NULL DEFAULT 0 COMMENT '帮' ,
    //     `token`  varchar(255) NULL ,
    //     `token_time`  varchar(255) NULL ,
    //     `exchange_star_value`  int NOT NULL DEFAULT 0 ,
    //     `income`  float NULL COMMENT '收入' ,
    //     `goods_num`  int NULL DEFAULT 0 COMMENT '甩到的商品数量' ,
    //     `openid`  varchar(255) NULL COMMENT 'openid' ,
    //     `is_new`  tinyint NULL DEFAULT 1 COMMENT '1  新人   0  不是' ,
    //     PRIMARY KEY (`id`)
    //     )
    //     ENGINE=InnoDB
    //     COMMENT='用户好友表'
    //   ";
    //   $con=mysqli_connect("rm-wz9l3z92630ora5wjwo.mysql.rds.aliyuncs.com","siring","ei%&#w9l3z3",'ssl');
    //   if($con)
    //   {
    //       mysqli_query($con,$sql);
    //   }
     //注册新用户
     $member['account']='18369652212';
     $member['passwd']=md5('18369652212'.time());
     $member['join_time']=time();
     $re=DB::name('member')->insertGetId($member);
     if($re){
           //新建会员表
           $sql="CREATE TABLE "."tb_".$re." (
             `id`  int NOT NULL AUTO_INCREMENT ,
             `name`  varchar(255) NULL COMMENT '昵称' ,
             `account`  varchar(255) NULL COMMENT '账号' ,
             `passwd`  varchar(255) NULL COMMENT '密码' ,
             `star_value`  int NOT NULL DEFAULT 0 COMMENT '星光值' ,
             `address`  varchar(255) NULL ,
             `head_pic`  varchar(255) NULL ,
             `is_use`  tinyint NULL ,
             `join_time`  varchar(255) NULL ,
             `pid`  tinyint NOT NULL DEFAULT 1 COMMENT '上级id   好友关系' ,
             `member_type`  tinyint NOT NULL DEFAULT 0 ,
             `help_num`  int NOT NULL DEFAULT 0 COMMENT '帮' ,
             `token`  varchar(255) NULL ,
             `token_time`  varchar(255) NULL ,
             `exchange_star_value`  int NOT NULL DEFAULT 0 ,
             `income`  float NULL COMMENT '收入' ,
             `goods_num`  int NULL DEFAULT 0 COMMENT '甩到的商品数量' ,
             `openid`  varchar(255) NULL COMMENT 'openid' ,
             `is_new`  tinyint NULL DEFAULT 1 COMMENT '1  新人   0  不是' ,
             PRIMARY KEY (`id`)
             )
             ENGINE=InnoDB
             COMMENT='用户好友表'
           ";
           $con=mysqli_connect("rm-wz9l3z92630ora5wjwo.mysql.rds.aliyuncs.com","siring","ei%&#w9l3z3",'ssl');
           if($con)
           {
               mysqli_query($con,$sql);   //新建表
             //   $re2=DB::name($re)->insertGetId($member);
           }
  }
}


}