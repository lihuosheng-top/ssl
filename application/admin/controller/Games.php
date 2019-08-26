<?php

namespace app\admin\controller;

use think\Controller;
use think\Console;
use think\Db;
use think\Request;

/**
 * 小游戏管理
 */
class Games extends Controller
{
	
	/**
	*  答题库
	*/
	public function answer_bank()
	{
        $problem=db('problem_house')->order('id asc')->select();
        //获取后台的问题库
        foreach($problem as $k =>$v){
            $answer=json_decode($v['answer']);
            $problem[$k]['answer']=$answer;
            $problem_type=json_decode($v['problem_type'],true);
            $arr='';
            $num=count($problem_type);
            $i=0;
            foreach($problem_type as $k2=>$v2){
                if($k2=='twdl'){
                    $problem_type[$k2]='天文地理';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='rwls'){
                    $problem_type[$k2]='人物历史';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='shbk'){
                    $problem_type[$k2]='生活百科';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='mxbg'){
                    $problem_type[$k2]='明星八卦';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='wlhx'){
                    $problem_type[$k2]='物理化学';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='keji'){
                    $problem_type[$k2]='科技';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='politics'){
                    $problem_type[$k2]='政治';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='history'){
                    $problem_type[$k2]='文学';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='music'){
                    $problem_type[$k2]='音乐';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='sport'){
                    $problem_type[$k2]='体育';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                $i++;
            }
            $problem[$k]['problem_type']=$arr;
            if($v['problem_status']=='1'){
                $problem[$k]['problem_status']="简单";
            }
            if($v['problem_status']=='2'){
                $problem[$k]['problem_status']="中难";
            }
            if($v['problem_status']=='3'){
                $problem[$k]['problem_status']="较难";
            }
            if($v['problem_status']=='4'){
                $problem[$k]['problem_status']="很难";
            }
        }
        $problem=paging_data($problem,'admin/Games/answer_bank','15');   //分页函数处理
        $problem->appends($_GET);
        $this->assign('listpage', $problem->render());
         return view('answer_bank',['data'=>$problem]);
	}

    /**
     * lilu
	*  添加答题
	*/
	public function answer_bank_add(Request $request)
	{
        if($request->isPost())
        {
            $input=input();
            if($input){
                $data['problem']=$input['problem'];  //问题 
                $answer=json_encode($input['ans']);
                $data['answer']=$answer;                //问题选项
                $data['true_ans']=$input['aa'];         //正确答案
                $data['problem_status']=$input['b'];    //问题难度
                $problem_type=json_encode($input['sss']);
                $data['problem_type']=$problem_type;
                $data['problem_status']=$input['b'];    //问题难度
                $re=db('problem_house')->insert($data);
                if($re)
                {
                     $this->success('添加成功',url('admin/Games/answer_bank'));
                }else{
                     $this->error('添加失败');
                }
            }else{
              $this->error('参数错误');
            }
        }else{
            return view('answer_bank_add');
        }
        
	}

	/**
     * lilu
	*  答题阶梯
	*/
	public function answer_bri()
	{
        //获取已配置的信息
        $key="answer_bri";
        $info=db('sys_setting')->where('key',$key)->find();
        if($info){
            $value=json_decode($info['value'],true);
            $id=$info['id'];
        }else{
            $value=[];
            $id='';
        }
      return view('answer_bri',['data'=>$value,'id'=>$info['id']]);
	}
	/**
     * lilu
	*  答题阶梯处理
	*/
	public function answer_bri_do()
	{
        $key='answer_bri';    //答题阶梯的key值
        //获取后台配置参数
        $input=input();
        foreach($input as $k=>$v){
            if (substr($k, 0, 3) == "sss") { 
                foreach($v as $k2=>$v2)
                {
                    if(!array_key_exists('status',$v[$k2])){
                          $input[$k][$k2]['status']='0';
                    }
                } 
            }
        }
        if($input){
            if($input['id']){
                $data['value']=json_encode($input);
                $data['key']='answer_bri';
                $data['status']='1';
                $re=db('sys_setting')->where('id',$input['id'])->update($data);
                if($re !== false){
                   $this->success('保存成功',url('admin/Games/answer_bri'));
                }else{
                   $this->error('保存失败');
                }
            }else{
                $data['value']=json_encode($input);
                $data['key']='answer_bri';
                $data['status']='1';
                $re=db('sys_setting')->insert($data);
                if($re){
                   $this->success('保存成功',url('admin/Games/answer_bri'));
                }else{
                   $this->error('保存失败');
                }

            }
        }else{
            $this->error('保存失败');

        }
	}

	/**
	*  找不同
	*/
	public function differ()
	{
       return view('differ');
	}

	/**
	*  出现概率
	*/
	public function appear_pro()
	{   
        //获取已有的配置
        $key='game_chance';
        $re=db('sys_setting')->where('key',$key)->find();
        if($re){
            $data['sss']=json_decode($re['value'],true);
            $data['id']=$re['id'];
        }else{
            $data['sss']=[];
            $data['id']='';
        }
        return view('appear_pro',['data'=>$data]);
	}
	/**
     * lilu
	*  出现概率处理
	*/
	public function appear_pro_do()
	{
        //获取数据
        $input=input();
        if($input){
            if($input['id'])
            {
                $data['key']='game_chance';
                $value=json_encode($input['sss']);
                $data['value']=$value;
                $data['status']='1';
                $re=db('sys_setting')->where('id',$input['id'])->update($data);
                if($re !==false){
                    $this->success('保存成功');
                }else{
                    $this->error('保存失败');
                }
            }else{
                $data['key']='game_chance';
                $value=json_encode($input['sss']);
                $data['value']=$value;
                $data['status']='1';
                $re=db('sys_setting')->insert($data);
                if($re){
                    $this->success('保存成功',url('admin/Game/appear_pro'));
                }else{
                    $this->error('保存失败');
                }
            }
        }else{
            $this->error('保存失败');
        }
}
    public function differ_add()
	{
        return view('differ_add');
	}

	/*
	  找不同页面
	*/
	public  function differ_index(){
	    return view('differ_index');
    }



    /**
     * 后台环境信息
     * @return mixed
     */
    /**
     * 轮播图片 -> 获取前端要修改的id和新图片
     */
    public function update_images(){

        // 接收前端传来点击修改的id的值和前端在本地选择想要更换上传的图片 -> 获取表单上传文件
        $file = request()->file('file');
        // 判断是否有上传的图片
        if($file == null) {
            $this->error("很抱歉,您未选择图片!!");
        }
        // 进行文件上传
//        $info = $file->rule('md5')->move(__UPLOAD__.'/index/images/slideshow/');
//        $saveName = $info->getsaveName();
        $show = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        $list = str_replace("\\", "/", $show->getSaveName());    //图片
        $image_url=ROOT_PATH . 'public' . DS . 'uploads'.DS.$list;
//        $root_pa=env('root_path');
//        $url="D:/phpStudy/PHPTutorial/WWW/ThinkAdmin/public/static/index/images/slideshow/".$saveName;
//        $url = strtr($url, '\\', '/');
        //$savePath="D:/phpStudy/PHPTutorial/WWW/ThinkAdmin/public/static/index/images/copper/";
        $savePath = ROOT_PATH . 'public' . DS . 'uploads';
        $data=$this->img_tom($image_url,$savePath);  //返回切割的图片
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $res[$key]['image_name']=$value['image_name'];
                $res[$key]['num_string']=$value['num_string'];
            }
        }else{
            exit();
        }
        $result[]=array_chunk($res, 11);
        return $result;


    }
    /*
     * lilu
     * @parsm  url  图片地址
     * $parsm  savePath   切割后的保存地址
       切割图片
   */
    public function img_tom($url,$savePath)
    {
        //定义切割属性
        $xNum = 11;
        $xLocation = ["A","B","C","D","E","F","G","H","I","J","K"]; // x坐标
        $yNum = 11;
        //$imagePath = "./20190427124851.jpg";  // 分割的图片
        $imagePath=$url;
        $image = imagecreatefromstring(file_get_contents($imagePath)); // 大图片
        $imgInfo = getimagesize($imagePath);
        // $savePath = "/imagePath";// 分割图片的保持地址
        if(!file_exists($savePath)){
            mkdir($savePath);
        }
        if($imgInfo){
            list($srcW, $srcH) = $imgInfo;

            $targetW =  intval($srcW/11); // 小图片的宽
            $targetH =  intval($srcH/11); // 小图片的高
            $outPut = []; // 输出结果
            $i =1;//
            for ($y = 1;$y <= $yNum;$y++){
                for ($x = 1;$x <= $xNum;$x++){
                    $tempResult['num_string'] =$xLocation[$x-1].$y; // 对应的点击区域
                    $tempResult['image_name'] =$i.'_'.$tempResult['num_string'].'_'.$this->getUuid().'.png'; // 生成图片的名称

                    $targetImage = imagecreatetruecolor($targetW, $targetH); // 输出的图片大小
                    imagesavealpha($targetImage, true);
                    // imagecopyresampled($targetImage, $image, 0,0,($x-1)*$targetW, ($y-1)*$targetH,  $targetW, $targetH, $targetW, $targetH);
                    imagecopy($targetImage, $image, 0,0,($x-1)*$targetW, ($y-1)*$targetH,  $targetW, $targetH);
                    imagepng($targetImage, $savePath.'/'. $tempResult['image_name']);
                    imagedestroy($targetImage);
                    $outPut[] =$tempResult;
                    $i++;
                }
            }
            return  $outPut;

        }

    }
    public function getUuid() {

        mt_srand ( ( double ) microtime () * 10000 ); //optional for php 4.2.0 and up.随便数播种，4.2.0以后不需要了。
        $charid = strtoupper ( md5 ( uniqid ( rand (), true ) ) ); //根据当前时间（微秒计）生成唯一id.
        $hyphen = chr ( 45 ); // "-"
        $uuid = '' . //chr(123)// "{"
            substr ( $charid, 0, 8 ) . $hyphen . substr ( $charid, 8, 4 ) . $hyphen . substr ( $charid, 12, 4 ) . $hyphen . substr ( $charid, 16, 4 ) . $hyphen . substr ( $charid, 20, 12 );
        //.chr(125);// "}"
        return $uuid;
    }
    /**
     * lilu
     * 答题编辑
     */
    public function  answer_bank_edit()
    {
        //获取答题id
        $input=input();
        if($input['id']){
            //获取后台的问题库
            $problem=db('problem_house')->where('id',$input['id'])->select();
            foreach($problem as $k =>$v){
                $answer=json_decode($v['answer']);
                $problem[$k]['answer']=$answer;
                $problem_type=json_decode($v['problem_type'],true);
                $arr='';
                $num=count($problem_type);
                $i=0;
                foreach($problem_type as $k2=>$v2){
                    if($k2=='twdl'){
                        $problem_type[$k2]='天文地理';
                    }
                    if($k2=='rwls'){
                        $problem_type[$k2]='人物历史';
                    }
                    if($k2=='shbk'){
                        $problem_type[$k2]='生活百科';
                    }
                    if($k2=='mxbg'){
                        $problem_type[$k2]='明星八卦';
                    }
                    if($k2=='wlhx'){
                        $problem_type[$k2]='物理化学';
                    }
                    if($k2=='keji'){
                        $problem_type[$k2]='科技';
                    }
                    if($k2=='politics'){
                        $problem_type[$k2]='政治';
                    }
                    if($k2=='history'){
                        $problem_type[$k2]='文学';
                    }
                    if($k2=='music'){
                        $problem_type[$k2]='音乐';
                    }
                    if($k2=='sport'){
                        $problem_type[$k2]='体育';
                    }
                }
                $problem[$k]['problem_type']=$problem_type;
                // if($v['problem_status']=='1'){
                //     $problem[$k]['problem_status']="简单";
                // }
                // if($v['problem_status']=='2'){
                //     $problem[$k]['problem_status']="中难";
                // }
                // if($v['problem_status']=='3'){
                //     $problem[$k]['problem_status']="较难";
                // }
                // if($v['problem_status']=='4'){
                //     $problem[$k]['problem_status']="很难";
                // }
            }
            return view('answer_bank_edit',['data'=>$problem]);
        }else{
            $this->error('参数错误');
        }
    }
    /**
     * lilu
     * 添加问题编辑处理
     */
    public function answer_bank_edit_do()
    {
        $input=input();
        if($input){
                $data['problem']=$input['problem'];  //问题 
                $answer=json_encode($input['ans'],true);
                $data['answer']=$answer;                //问题选项
                $data['true_ans']=$input['aa'];         //正确答案
                $data['problem_status']=$input['b'];    //问题难度
                $problem_type=json_encode($input['sss']);
                $data['problem_type']=$problem_type;
                $data['problem_status']=$input['b'];    //问题难度
                $re=db('problem_house')->where('id',$input['id'])->update($data);
                if($re !== false)
                {
                    $this->success('保存成功',url('admin/Games/answer_bank'));
                }else{
                    $this->error('保存失败');
                }
        }else{
            $this->error('保存失败');
        }
    }
    /**
     * lilu
     * 答题问题删除
     */
    public function answer_bank_del()
    {
        //获取所有的参数
        $input=input();
        if($input['id']){
            $re=db('problem_house')->delete($input['id']);
            if($re)
            {
                $this->success('删除成功',url('admin/Games/answer_bank'));
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('删除失败');
        }
    }
    /**
	*  答题库
	*/
	public function answer_bank_search()
	{
        $input=input();
        $problem=db('problem_house')->where('problem','like','%'.$input['content'].'%')->order('id asc')->select();
        //获取后台的问题库
        foreach($problem as $k =>$v){
            $answer=json_decode($v['answer']);
            $problem[$k]['answer']=$answer;
            $problem_type=json_decode($v['problem_type'],true);
            $arr='';
            $num=count($problem_type);
            $i=0;
            foreach($problem_type as $k2=>$v2){
                if($k2=='twdl'){
                    $problem_type[$k2]='天文地理';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='rwls'){
                    $problem_type[$k2]='人物历史';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='shbk'){
                    $problem_type[$k2]='生活百科';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='mxbg'){
                    $problem_type[$k2]='明星八卦';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='wlhx'){
                    $problem_type[$k2]='物理化学';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='keji'){
                    $problem_type[$k2]='科技';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='politics'){
                    $problem_type[$k2]='政治';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='history'){
                    $problem_type[$k2]='文学';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='music'){
                    $problem_type[$k2]='音乐';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='sport'){
                    $problem_type[$k2]='体育';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                $i++;
            }
            $problem[$k]['problem_type']=$arr;
            if($v['problem_status']=='1'){
                $problem[$k]['problem_status']="简单";
            }
            if($v['problem_status']=='2'){
                $problem[$k]['problem_status']="中难";
            }
            if($v['problem_status']=='3'){
                $problem[$k]['problem_status']="较难";
            }
            if($v['problem_status']=='4'){
                $problem[$k]['problem_status']="很难";
            }
        }
        $problem=paging_data($problem,'admin/Games/answer_bank','15');   //分页函数处理
        $problem->appends($_GET);
        $this->assign('listpage', $problem->render());
         return view('answer_bank',['data'=>$problem]);
	}










}
