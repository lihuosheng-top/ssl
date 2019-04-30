<?php

namespace app\admin\controller;

use think\Controller;


/**
 * 星光值管理
 */
class Star extends Controller
{
	
	/**
	*   星光值兑换
	**/
	public function star_exchange()
	{
       return  view('star_exchange');
	}


	/**
	*   星光值兑换记录
	**/
	public function list_exchange()
	{
       return  view('list_exchange');
	}
}