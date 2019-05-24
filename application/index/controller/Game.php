<?php
namespace app\index\controller;

use think\Controller;
use app\index\controller\Base;
use think\Session;
use think\Request;
use think\Db;

/**
 * lilu
 * 游戏模块
 */
class Game extends Controller
{

    /**
     * lilu
     * 根据概率获取游戏种类及游戏接口
     */
    public function game()
    {
          //随机取出9条数据
          $sql='SELECT * FROM tb_problem_house WHERE id >= ((SELECT MAX(id) FROM tb_problem_house)-(SELECT MIN(id) FROM tb_problem_house)) * RAND() + (SELECT MIN(id) FROM tb_problem_house) LIMIT 1';
          $list=DB::query($sql);
          foreach($list as $k =>$v){
            $answer=json_decode($v['answer']);
            $list[$k]['answer']=$answer;
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
            $list[$k]['problem_type']=$arr;
            if($v['problem_status']=='1'){
                $list[$k]['problem_status']="简单";
            }
            if($v['problem_status']=='2'){
                $list[$k]['problem_status']="中难";
            }
            if($v['problem_status']=='3'){
                $list[$k]['problem_status']="较难";
            }
            if($v['problem_status']=='4'){
                $list[$k]['problem_status']="很难";
            }
        }
        if($list){
              return  ajax_success('获取成功',$list);
        }else{
             return   ajax_error('获取失败');
        }
    }

}