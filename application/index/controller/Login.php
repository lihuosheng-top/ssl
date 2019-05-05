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
        //获取app配置信息
        $app_info=Config::get('app_info');
        $post['account'] = '15729016523';               //获取前端提交数据
        // $post = input('post.');              //获取前端提交数据
        if($post){
            $re=DB::name('member')
                ->where('account',$post['account'])
                ->find();
            //判断用户是否存在
            if($re){
                //发送短信
                $code=rand(100,999);
                Session::set('code',$code);
                $content='尊敬的'.$post['account'].'请输入有效码'.$code.'有效期10分钟';
                $re= sms_message($post['account'],$content);
                //获取token
//                $key=$re['passwd'];          //客户秘钥--注册时生成
//                $data['time']=time();        //当前时间戳
//                $data['token']=md5($key.md5($data['time']));    //token加密
                if($re){
                    return  ajax_success('发送短信成功');
                }else{
                    return  ajax_error('发送短信失败');
                }
            }else{                           //用户信息不存在
                //注册用户信息
                $user['account']=$post['account'];       //用户手机号
                $user['passwd']=md5($post['account'].time());
                $re=DB::name('member')
                    ->insert($user);                //添加用户信息
                if($re){
                    //发送短信
                    $content='甩甩乐做测试';
                    $re=sms_message($post['account'],$content);
//                        //获取token
//                        $key=$user['passwd'];          //客户秘钥--注册时生成
//                        $data['time']=time();        //当前时间戳
//                        $data['token']=md5($key.md5($data['time']));    //token加密
                    if($re){
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
        if($request->isPost()){
            $user_mobile =$request->only(['account'])["account"];       //获取登录账号
            $code =$request->only(["code"])["code"];                    //获取验证码
            if(empty($user_mobile)){
                return  ajax_error('手机号不能为空',$user_mobile);
            }
            if(empty($code)){
                return  ajax_error('验证码不能为空',$code);
            }
            //获取缓存验证码，并判断验证码
            $code_se=Session::get('code');
            $user=DB::name('member')
                ->where('account',$user_mobile)
                ->find();
            if($user){
                //验证码判断
                if(password_verify($code,$code_se)){                         //验证码通过
                    Session::delete('code');
                    //获取token
                    $key=$user['passwd'];          //客户秘钥--注册时生成
                    $data['time']=time();        //当前时间戳
                    $data['token']=md5($key.md5($data['time']));    //token加密
                    return   ajax_success('登录成功'，$data);
                }else{
                    return   ajax_error('登录失败');
                }
            }else{             //用户不存在
                //注册新用户
                $member['account']=$user_mobile;
                $member['passwd']=md5($member['account'].time());
                $re=DB::name('member')->insert($member);
                if($re){
                    //验证码判断
                    if(password_verify($code,$code_se)){                         //验证码通过
                        Session::delete('code');
                        //获取token
                        $key=$user['passwd'];          //客户秘钥--注册时生成
                        $data['time']=time();        //当前时间戳
                        $data['token']=md5($key.md5($data['time']));    //token加密
                        return   ajax_success('登录成功'，$data);
                }else{
                        return   ajax_error('登录失败');
                    }
                }else{
                    return   ajax_error('请输入摇一摇获取的验证码');
                }

            }
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



}