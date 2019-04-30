<?php
 namespace   app\admin\controller;


use think\Controller;

 /**
  * 订单控制器
  */
 class Order extends Controller
 {
 	
 	/*
 	*  订单列表
 	*/
 	public function order_list(){
         
         return view('order_list');

 	}


 }