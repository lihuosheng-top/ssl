<?php

namespace app\admin\controller;

use think\Controller;
use think\Console;
use think\Db;

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
         return view('answer_bank');
	}

	/**
	*  答题阶梯
	*/
	public function answer_bri()
	{
      return view('answer_bri');
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
        return view('appear_pro');
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









}
