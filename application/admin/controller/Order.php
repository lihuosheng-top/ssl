<?php
 namespace   app\admin\controller;


use think\Controller;

 /**
  * 订单控制器
  */
 class Order extends Controller
 {
 	
 	/*
 	*  lilu
 	*  订单列表
 	 * Request  request
 	*/
 	public function order_list()
    {
         
         return view('order_list');

 	}
     /**
      * lilu
      * 订单检索
      * @return Request
      */
     public function order_search()
     {
         return view('order_list');
     }


 }