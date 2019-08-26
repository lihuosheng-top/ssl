<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
class Admin extends Controller
{
    /**
     **************李火生*******************
     * @param Request $request
     * Notes: [管理员列表]
     **************************************
     * @param Request $request
     * @return \think\response\View
     */
    public function index(Request $request){
        $store_id =Session::get("store_id");
        //admin进来
        if(empty($store_id)){
            $account_list = db("admin")->order("id")->select();
            foreach ($account_list as $key=>$value){
                $account_list[$key]["role_name"] = db("role")->where("id",$value["role_id"])->value("name");
            }
            $roleList = getSelectList("role");
        }else{
            $account_list = db("admin")->where("store_id",$store_id)->where("role_id","NEQ",7)->order("id")->select();
            foreach ($account_list as $key=>$value){
                $account_list[$key]["role_name"] = db("role")->where("id",$value["role_id"])->value("name");
            }
            $roleList = getSelectList("role");
        }
        return view("index",["account_list"=>$account_list,"roleList"=>$roleList]);
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:管理员查询
     **************************************
     * @return \think\response\View
     */
    public function add(){
        $store_id =Session::get("store_id");
        if(!empty($store_id)){
            $roles = db("role")
                ->where("store_id",$store_id)
                ->where("status","1")
                ->field("id,name")
                ->select();
            $roleList = db("role")->where("store_id",$store_id)->field("id,name")->select();
        }else{
            $roles = db("role")->where("status","1")->field("id,name")->select();
            $roleList = getSelectList("role");
        }
        return view("save",["role"=>$roles,"roleList"=>$roleList]);
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:管理员添加入库
     **************************************
     * @param Request $request
     */
    public function save(Request $request){
        $data = $request->param();
        $store_id =Session::get("store_id");
        if(!empty($store_id)){
            $data["store_id"] =$store_id;
        }
        $data["passwd"] = password_hash($data["passwd"],PASSWORD_DEFAULT);
        $data["stime"] = date("Y-m-d H:i:s");
        $boolData = model("Admin")->sSave($data);
        if($boolData){
            $this->redirect("admin/admin/index");
        }else{
            $this->redirect("admin/admin/add");
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:管理员删除
     **************************************
     * @param $id
     */
    public function del($id){
        $bool = model("Admin")->where("id",$id)->delete();
        if($bool){
            $this->redirect("admin/admin/index");
        }else{
            $this->error("admin/admin/index");
        }
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:管理员编辑
     **************************************
     * @param $id
     * @return \think\response\View
     */
    public function edit($id){
        $store_id =Session::get("store_id");
        if(!empty($store_id)){
            $admin = db("Admin")->where("id","$id")->where("store_id",$store_id)->select();
            $roleList = db("role")->where("store_id",$store_id)->field("id,name")->select();
        }else{
            $admin = db("Admin")->where("id","$id")->select();
            $roleList = getSelectList("role");
        }
        return view("edit",["admin"=>$admin,"roleList"=>$roleList]);
    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:管理员修改
     **************************************
     * @param Request $request
     */
    public function updata(Request $request){
        $data = $request->param();
        $data["passwd"] = password_hash($data["passwd"],PASSWORD_DEFAULT);
        $data["stime"] = date("Y-m-d H:i:s");
        $id = $request->only(['id'])['id'];
        $bool = db("Admin")->where('id', $id)->update($data);
        if ($bool !== false){
            $this->success("编辑成功","admin/admin/index");
        }else{
            $this->error("编辑失败","admin/admin/edit");
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:管理员状态修改
     **************************************
     * @param Request $request
     */
    public function status(Request $request){
        if($request->isPost()) {
            $status = $request->only(["status"])["status"];
            if($status == 0) {
                $id = $request->only(["id"])["id"];
                $bool = db("Admin")->where("id", $id)->update(["status" => 0]);
                if ($bool) {
                    $this->redirect("admin/admin/index");
                } else {
                    $this->error("修改失败", "admin/admin/index");
                }
            }
            if($status == 1){
                $id = $request->only(["id"])["id"];
                $bool = db("Admin")->where("id", $id)->update(["status" => 1]);
                if ($bool) {
                    $this->redirect("admin/admin/index");
                } else {
                    $this->error("修改失败","admin/admin/index");
                }
            }
        }
    }


    /**
     **************李火生*******************
     * @param Request $request
     * Notes:密码修改
     **************************************
     * @param Request $request
     */
    public function passwd(Request $request){
        $id = $request->only(['id'])['id'];
        $passwd = md5($request->only(["passwd"])["passwd"]);
        $bool = db("Admin")->where("id",$id)->update(["passwd"=>$passwd]);
        if($bool !== false){
            $this->success("修改成功，请重新登录", "admin/Login/index");
        }
    }
    /**
     * lilu
     *  Notes:设置模块--运维
     */
    public function admin_opera(Request $request)
    {   
        $input=input();
        if($input){
            foreach($input as $k=>$v)
            {
              if(substr($k,0,4)=='one0'){
                  if($v['id']){
                    $value['video_link']=$v['video_link'];
                    $value['number']=$v['number'];
                    $arr['value']=json_encode($value,true);
                    $arr['key']=$v['key'];
                    $arr['status']=$v['status'];
                    $re=db('sys_setting')->where('id',$v['id'])->update($arr);
                  }else{
                    $value['video_link']=$v['video_link'];
                    $value['number']=$v['number'];
                    $arr['value']=json_encode($value,true);
                    $arr['key']=$v['key'];
                    $arr['status']=$v['status'];
                    $re=db('sys_setting')->insert($arr);
                  }
              }else{
                if($v['id']){
                    $value2['title']=$v['title'];
                    $value2['text']=$v['text'];
                    $arr2['value']=json_encode($value2,true);
                    $arr2['key']=$v['key'];
                    $arr2['status']=$v['status'];
                    $re=db('sys_setting')->where('id',$v['id'])->update($arr2);
                  }else{
                    $value2['title']=$v['title'];
                    $value2['text']=$v['text'];
                    $arr2['value']=json_encode($value2,true);
                    $arr2['key']=$v['key'];
                    $arr2['status']=$v['status'];
                    $re=db('sys_setting')->insert($arr2);
                  }
              }
            }
            $this->success('保存成功',url('admin/Admin/admin_opera'));
        }else{
            $key1='play_intro';        //玩法说明
            $info1=db('sys_setting')->where('key',$key1)->find();
            if($info1){
                $info1['value']=json_decode($info1['value'],true);
                $this->assign('data1',$info1);
            }
            $key2='video_link';       //视频链接
            $info2=db('sys_setting')->where('key',$key2)->find();
            if($info2){
                $info2['value']=json_decode($info2['value'],true);
                $this->assign('data2',$info2);
            }
            return view('admin_opera');
        }

    }
    
    /**
     * lilu
     *  Notes:设置模块--商品策略
     */
    public function admin_goods_tactics()
    {
        $input=input();
        if($input){
            foreach($input as $k=>$v)
            {
              if(substr($k,0,4)=='one0'){
                  if($v['id']){
                    $value['limit_num']=$v['limit_num'];
                    $arr['value']=json_encode($value,true);
                    $arr['key']=$v['key'];
                    $arr['status']=1;
                    $re=db('sys_setting')->where('id',$v['id'])->update($arr);
                  }else{
                    $value['limit_num']=$v['limit_num'];
                    $arr['value']=json_encode($value,true);
                    $arr['key']=$v['key'];
                    $arr['status']=1;
                    $re=db('sys_setting')->insert($arr);
                  }
              }elseif(substr($k,0,4)=='one1'){
                if($v['id']){
                    $value2['person_up_num']=$v['person_up_num'];
                    $value2['own_num']=$v['own_num'];
                    $arr2['value']=json_encode($value2,true);
                    $arr2['key']=$v['key'];
                    $arr2['status']=1;
                    $re=db('sys_setting')->where('id',$v['id'])->update($arr2);
                  }else{
                    $value2['person_up_num']=$v['person_up_num'];
                    $value2['own_num']=$v['own_num'];
                    $arr2['value']=json_encode($value2,true);
                    $arr2['key']=$v['key'];
                    $arr2['status']=1;
                    $re=db('sys_setting')->insert($arr2);
                  }
              }else{
                if($v['id']){
                    $value3['goods_limit_num']=$v['goods_limit_num'];
                    $value3['own_help_num']=$v['own_help_num'];
                    $arr3['value']=json_encode($value3,true);
                    $arr3['key']=$v['key'];
                    $arr3['status']=1;
                    $re=db('sys_setting')->where('id',$v['id'])->update($arr3);
                  }else{
                    $value3['goods_limit_num']=$v['goods_limit_num'];
                    $value3['own_help_num']=$v['own_help_num'];
                    $arr3['value']=json_encode($value3,true);
                    $arr3['key']=$v['key'];
                    $arr3['status']=1;
                    $re=db('sys_setting')->insert($arr3);
                  }
              }
            }
            $this->success('保存成功',url('admin/Admin/admin_goods_tactics'));
        }else{
            $key1='goods_limit';        //甩品限制
            $info1=db('sys_setting')->where('key',$key1)->find();
            if($info1){
                $info1['value']=json_decode($info1['value'],true);
                $this->assign('data1',$info1);
            }
            $key2='goods_limit_own';       //自己甩品次数限制
            $info2=db('sys_setting')->where('key',$key2)->find();
            if($info2){
                $info2['value']=json_decode($info2['value'],true);
                $this->assign('data2',$info2);
            }
            $key3='help_goods_limit';       //帮甩次数限制
            $info3=db('sys_setting')->where('key',$key3)->find();
            if($info3){
                $info3['value']=json_decode($info3['value'],true);
                $this->assign('data3',$info3);
            }
            return view('admin_goods_tactics'); 
        }
    }
    /**
     * lilu
     *  Notes:设置模块--平台协议
     */
    public function admin_agreement()
    {
        $key="agree_ment";
        $info=db('sys_setting')->where('key',$key)->find();
        if($info){
            $info['value']=json_decode($info['value'],true);
            return view('admin_agreement',['data'=>$info,'msg'=>1]);
        }else{
            $data='0';
            return view('admin_agreement',['data'=>$data]);
        }
    }
    /**
     * lilu
     *  Notes:设置模块--平台协议处理
     */
    public function admin_agreement_do()
    {
        //获取参数
        $input=input();
        if($input)
        {
            $key="agree_ment";
            $res=db('sys_setting')->where('key',$key)->find();
            if($res)
            {
                $value['value']=json_encode($input);
                $value['status']=1;
                $value['key']='agree_ment';
                db('sys_setting')->where('id',$res['id'])->update($value);
                $this->success('更新成功',url('admin/Admin/admin_agreement'));
            }else{
                $value['value']=json_encode($input);
                $value['status']=1;
                $value['key']='agree_ment';
                db('sys_setting')->insert($value);
                $this->success('添加成功',url('admin/Admin/admin_agreement'));
            }
        }

    }
    /**
     * lilu
     *  Notes:设置模块--添加平台协议
     */
    public function admin_agreement_add()
    {
        $input=input();
        if($input)          //判断是否获取添加的表单数据
        {
          $re=db('agreement')->insert($input);
          if($re)
          {
              $this->success('保存成功',url('admin/Admin/admin_agreement'));
          }else{
              $this->error('保存失败');
          }
        }else{
            return view('admin_agreement_add');
        }
    }
    /**
     * lilu
     *  Notes:设置模块--消息提醒
     */
    public function admin_message_note()
    {
        $key="admin_message";
        $info=db('sys_setting')->where('key',$key)->find();
        if($info){
            $info['value']=json_decode($info['value'],true);
            return view('admin_message_note',['data'=>$info,'msg'=>1]);
        }else{
            $data='0';
            return view('admin_message_note',['data'=>$data]);
        }
       return view('admin_message_note');
    }
    /**
     * lilu
     *  Notes:设置模块--消息提醒处理
     */
    public function admin_message_note_do()
    {
        //获取参数
        $input=input();
        if($input)
        {
            $key="admin_message";
            $res=db('sys_setting')->where('key',$key)->find();
            if($res)
            {
                $value['value']=json_encode($input);
                $value['status']=1;
                $value['key']='admin_message';
                db('sys_setting')->where('id',$res['id'])->update($value);
                $this->success('更新成功',url('admin/Admin/admin_message_note'));
            }else{
                $value['value']=json_encode($input);
                $value['status']=1;
                $value['key']='admin_message';
                db('sys_setting')->insert($value);
                $this->success('添加成功',url('admin/Admin/admin_message_note'));
            }
        }
    }
    
    /**
     * lilu
     *  Notes:设置模块--锁定时间
     */
    public function admin_lock_time()
    {
        //
        $key="lock_time";
        $info=db('sys_setting')->where('key',$key)->find();
        $info['value']=json_decode($info['value'],true);
        if($info){
            return view('admin_lock_time',['data'=>$info,'msg'=>1]);
        }else{
            return view('admin_lock_time');
        }
    }
    /**
     * lilu
     *  Notes:设置模块--锁定时间处理
     */
    public function admin_lock_time_do()
    {
        $key="lock_time";    //锁定时间字段
        $input=input();      //获取参数
        if($input){
            $res=db('sys_setting')->where('key',$key)->find();
            if($res)
            {
                $data['id']=$res['id'];
                $data['value']=json_encode($input);
                $data['key']=$key;
                $data['status']='1';
                $re=db('sys_setting')->update($data);
            }else{
                $data['value']=json_encode($input);
                $data['key']=$key;
                $data['status']='1';
                $re=db('sys_setting')->insert($data);
            }
        }
       
        if($re)
        {
          $this->success('保存成功',url('admin/Admin/admin_lock_time'));
        }else{
          $this->error('保存失败');
        }
    }
    /**
     * lilu
     * 星光值配置
     */
    public function admin_get_star()
    {
         //
         $key="star_value";
         $info=db('sys_setting')->where('key',$key)->find();
         $info['value']=json_decode($info['value'],true);
         if($info){
             return view('admin_get_star',['data'=>$info,'msg'=>1]);
         }else{
             return view('admin_get_star');
         }
    }
    /**
     * lilu
     * 星光值配置
     */
    public function admin_get_star_do()
    {
         $key="star_value";    //锁定时间字段
         $input=input();      //获取参数
         if($input){
             $res=db('sys_setting')->where('key',$key)->find();
             if($res)
             {
                 $data['id']=$res['id'];
                 $data['value']=json_encode($input);
                 $data['key']=$key;
                 $data['status']='1';
                 $re=db('sys_setting')->update($data);
             }else{
                 $data['value']=json_encode($input);
                 $data['key']=$key;
                 $data['status']='1';
                 $re=db('sys_setting')->insert($data);
             }
         }
         if($re)
         {
           $this->success('保存成功',url('admin/Admin/admin_get_star'));
         }else{
           $this->error('保存失败');
         }
    }
    /**
     * lilu
     * 平台手续费
     */
    public function admin_fei()
    {
        $key="admin_fei";
        $info=db('sys_setting')->where('key',$key)->find();
        if($info){
            $info['value']=json_decode($info['value'],true);
            return view('admin_fei',['data'=>$info,'msg'=>1]);
        }else{
            $data='0';
            return view('admin_fei',['data'=>$data]);
        }
    }
    /**
     * lilu
     * 星光值配置
     */
    public function admin_fei_do()
    {
         $key="admin_fei";    //锁定时间字段
         $input=input();      //获取参数
         if($input){
             $res=db('sys_setting')->where('key',$key)->find();
             if($res)
             {
                 $data['id']=$res['id'];
                 $data['value']=json_encode($input);
                 $data['key']=$key;
                 $data['status']='1';
                 $re=db('sys_setting')->update($data);
             }else{
                 $data['value']=json_encode($input);
                 $data['key']=$key;
                 $data['status']='1';
                 $re=db('sys_setting')->insert($data);
             }
         }
         if($re)
         {
           $this->success('保存成功',url('admin/Admin/admin_fei'));
         }else{
           $this->error('保存失败');
         }
    }


}