<?php

namespace app\admin\controller;

use think\Controller;


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
	/**
	*  找不同添加
	*/
	public function differ_add()
	{
        return view('differ_add');
	}
}
