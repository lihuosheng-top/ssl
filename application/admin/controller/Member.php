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
 	* 会员列表编辑
 	**/
 	public function member_list_edit()
 	{
 		return view('member_list_edit');
 	}


 }