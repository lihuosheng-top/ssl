<?php
namespace app\admin\model;

use think\Model;

class Tactics extends Model
{
    protected $table = "tb_tactics";



    public function get_tactics_info($key)
    {
       $info=$this->where('tactics_key',$key)->find();
       return $info;
    }

}