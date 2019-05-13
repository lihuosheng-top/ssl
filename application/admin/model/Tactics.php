<?php
namespace app\admin\model;

use think\Model;

class Tactics extends Model
{
    protected $table = "tb_tactics";


    /**
     * lilu
     * 获取key对应的信息
     * parsm  key  
     * return info
     */
    public function get_tactics_info($key)
    {
       $info=$this->where('tactics_key',$key)->find();
       return $info;
    }
    /**
     * lilu
     * 获得概率
     */
    public function get_chance($key)
    {
        //获取当前key的所有记录
        $number=$this->where('tactics',$key)->count('tactics_num');
        return $number;
    }


}