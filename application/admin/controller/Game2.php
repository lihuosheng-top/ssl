<?php

// +----------------------------------------------------------------------
// | framework
// +----------------------------------------------------------------------
// | 版权所有 2014~2018 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://framework.thinkadmin.top
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/framework
// +----------------------------------------------------------------------

namespace app\admin\controller;

use library\Controller;
use library\tools\Data;
use think\Console;
use think\Db;

/**
 * 后台入口管理
 * Class Index
 * @package app\admin\controller
 */
class Index extends Controller
{

    

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
        $info = $file->rule('md5')->move('./static/index/images/slideshow/');
        // 获取上传文件的目录
        $saveName = $info->getsaveName();
         $root_pa=env('root_path');
        $url="D:/phpStudy/PHPTutorial/WWW/ThinkAdmin/public/static/index/images/slideshow/".$saveName;
          $url = strtr($url, '\\', '/');
           //$savePath="D:/phpStudy/PHPTutorial/WWW/ThinkAdmin/public/static/index/images/copper/";
        $savePath = $root_pa."\public\static\index\images\copper".'\w';
         $data=$this->img_tom($url,$savePath);
         if(!empty($data)){
             foreach ($data as $key => $value) {
             $res[$key]['image_name']='./../../../static/index/images/copper/w/'.$value['image_name'];
             $res[$key]['num_string']=$value['num_string'];
            }
         }else{
              dump($data);exit();
         }
         $result[]=array_chunk($res, 10);
          # code...
        die(json_encode($result));
        $saveName = $info->getsaveName();
        $str = "/Blogs/public/static/index/images/slideshow/". $saveName;
         //dump($saveName);
        // dump($str);
        // strtr 字符串替换函数 -> 将路径的\\替换成
        $str1 = strtr($str, '\\', '/');
        // 将拼接成的字符串路径插入到数据库中
        exit();
        $code = Db::execute("update all_heads set all_images='$str1' where id = '$id'");
        // 判断数据是否成功插入到数据库中
        if($code) {
            $this->redirect("allimages");
        } else {
            $this->error("很抱歉,轮播图更换失败!!");
        }
    }
     /*
        切割图片
    */
        public function img_tom($url,$savePath)
        {

            $xNum = 11;
            $xLocation = ["A","B","C","D","E","F","G","H","I","J","K"]; // x坐标
            $yNum = 11;
            //$imagePath = "./20190427124851.jpg";  // 分割的图片
            $imagePath=$url;
            $image = imagecreatefromstring(file_get_contents($imagePath)); // 大图片
            $imgInfo = getimagesize($imagePath);
           // $savePath = "/imagePath";// 分割图片的保持地址

            //$savePath = "D:/phpStudy/PHPTutorial/WWW/ThinkAdmin/public/static/index/images/copper/";

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
        function getUuid() {

        mt_srand ( ( double ) microtime () * 10000 ); //optional for php 4.2.0 and up.随便数播种，4.2.0以后不需要了。
        $charid = strtoupper ( md5 ( uniqid ( rand (), true ) ) ); //根据当前时间（微秒计）生成唯一id.
        $hyphen = chr ( 45 ); // "-"
        $uuid = '' . //chr(123)// "{"
            substr ( $charid, 0, 8 ) . $hyphen . substr ( $charid, 8, 4 ) . $hyphen . substr ( $charid, 12, 4 ) . $hyphen . substr ( $charid, 16, 4 ) . $hyphen . substr ( $charid, 20, 12 );
        //.chr(125);// "}"
        return $uuid;
      }

  
   
   


   

}