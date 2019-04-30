<?php
namespace app\admin\controller;
 

 use  think\Controller;


 /**
  * 会员管理
  */
 class Member extends Controller
 {
 	
 	/*
 	* 会员列表
 	**/
 	public function member_list()
 	{
 		return view('member_list');
 	}

 	/*
 	* 会员等级
 	**/
 	public function member_type()
 	{
 		return view('member_type');
 	}


 	/*
 	* 会员意见反馈
 	**/
 	public function member_back()
 	{
 		return view('member_back');
 	}
 }