<?php
namespace app\index\model;

use think\Model;
use think\Db;

/**
 * lilu
 * 游戏模型
 */
class Game extends Model
{

    /**
     * 获取两种游戏的概率
     */
    public function get_games_chance()
    {
        $key='game_chance';
        $re=db('sys_setting')->where('key',$key)->find();
        if($re){  //默认答题
            $value=json_decode($re['value'],true);
            $zong=$value['answer'][1]+$value['differ'][1];
            $rand=mt_rand('1',$zong);
            if($rand>=$value['answer'][1])
            {   //答题
                   return  1;
            }else{   //找不同
                   return  2;
            }
        }else{
           return 1;
        }
    }
}