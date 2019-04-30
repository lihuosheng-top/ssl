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
     * Notes:甩甩乐授权登录
     **************************************
     */
    public function index_login()
    {
        //获取app配置信息
        $app_info=Config::get('app_info'); 
        // $post = input('');               //获取前端提交数据
        $post = '15729016523';              //获取前端提交数据
        if($post){
            
        }else{                              // 没有获取到前端信息                            
            return ajax_error('没有数据',['status'=>0]);
        }
        
        if (!empty($session_key['session_key'])) {
            $appid = $params['appid'];
            $encryptedData = urldecode($get['encryptedData']);
            $iv = define_str_replace($get['iv']);
            $errCode = decryptData($appid,$session_key['session_key'],$encryptedData, $iv);
            $register_login = db("recommend_integral")
                ->where("id","1")
                ->value("register_integral");//授权通过即送积分
            if(!empty($errCode )){
                $is_register =Db::name('member')
                    ->where("store_id",$get["uniacid"])
                    ->where('member_openid',$errCode['openId'])
                    ->find();
                if(empty($is_register)){
                    $data['member_openid'] =$errCode['openId'];
                    $data['member_head_img'] =$errCode['avatarUrl'];
                    $data['member_name'] =$errCode['nickName'];
                    $data['member_create_time'] =time();
                    $data['member_grade_create_time'] =time();
                    $data['member_grade_id']=1;
                    $data['member_status']=1;
                    $data['member_integral_wallet'] = $register_login;
                    $grade_name =Db::name('member_grade')
                        ->field('member_grade_name')
                        ->where('member_grade_id',1)
                        ->find();
                    $data['member_grade_name'] =$grade_name['member_grade_name'];
                    $data["store_id"] =$get["uniacid"]; //店铺id
                    $bool = Db::name('member')->insertGetId($data);
                if($register_login > 0){
                    //插入积分记录
                    $integral_data = [
                        "member_id" => $bool,
                        "integral_operation" => $register_login,//获得积分
                        "integral_balance" => $register_login,//积分余额
                        "integral_type" => 1, //积分类型（1获得，-1消费）
                        "operation_time" => date("Y-m-d H:i:s"), //操作时间
                        "integral_remarks" => "成功注册送" . $register_login . "积分",
                    ];
                    Db::name("integral")->insert($integral_data);
                }
                    if($bool){
                        $member_grade_info =Db::name("member_grade")
                            ->field("member_grade_name,member_grade_img,member_grade_id")
                            ->where("member_grade_id",1)
                            ->find();
                        $info_data =[
                            "member_grade_info"=>$member_grade_info,
                            "openid"=>$errCode['openId'],
                            "member_id"=>$bool
                        ];
                        return ajax_success('返回数据成功',$info_data);
                    }else{
                        return ajax_success('返回数据失败',['status'=>0]);
                    }
                }else{
                    //已注册则进行登录
                    $member_grade_info =Db::name("member_grade")
                        ->field("member_grade_name,member_grade_img,member_grade_id")
                        ->where("member_grade_id",$is_register["member_grade_id"])
                        ->find();
                    $data =[
                        "member_grade_info"=>$member_grade_info,
                        "openid"=>$errCode['openId'],
                        "member_id"=>$is_register["member_id"]
                    ];
                    return ajax_error('该用户已经注册过，请不要重复注册',$data);
                }
            }else{
                return ajax_error('没有数据',['status'=>0]);
            }
        } else {
            return ajax_error('获取session_key失败',['status'=>0]);
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
            //判断用户是否存在
            $res = Db::name('member')->field('password')->where('phone_number',$user_mobile)->find();
            $datas =[
                'phone_number'=> $user_mobile,
            ];
            if(password_verify($password,$res["password"])){
                if($res){
                    $ress =Db::name('pc_user')
                        ->where('phone_number',$user_mobile)
                        ->where('status',1)
                        ->field("id")
                        ->find();
                    if($ress)
                    {
                        // 前台使用
                        Session::set("user",$ress["id"]);
                        Session::set('member',$datas);
                        return ajax_success('登录成功',$datas);
                    }else{
                        ajax_error('此用户已被管理员设置停用',$datas);
                    }
                }
            }else{
                //直接登录后台页面（如某个商家的客服）
                $res_admin =Db::name('admin')
                    ->where("account",$user_mobile)
                    ->where("status","<>",1)
                    ->select();
                if(!empty($res_admin)){
                    if(password_verify($password,$res_admin[0]["passwd"])){
                        Session("user_id", $res_admin[0]["id"]);
                        Session("user_info", $res_admin);
                        Session("store_id", $res_admin[0]["store_id"]);
                        exit(json_encode(array("status"=>2,"info"=>"登录成功")));
                    }else{
                        return ajax_error('用户或密码错误',['status'=>0]);
                    }
                }else{
                    return ajax_error('用户或密码错误',['status'=>0]);
                }

            }

        }
    }

    /**
     **************李火生*******************
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
     **************李火生*******************
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




}