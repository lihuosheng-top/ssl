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
        //获取检索的条件信息
        $order_number=input('get.order_number');      //订单号、商品名称、用户账号
        $order_create_time=input('get.order_create_time');
        $order_end_time=input('get.order_end_time');
        if($order_number){
            $where['order_number']='00';
        }
        if($order_create_time && $order_end_time){
            $where['create_time']=array('between',array($order_create_time,$order_end_time));
        }elseif($order_create_time && ! $order_end_time){
            $where['create_time']=array('between',array($order_create_time,time()));
        }else{
            $where['create_time']=array('between',array($order_create_time,$order_end_time));
        }
        if($order_number){
            $where['order_number']='';
        }

        //获取订单列表的所有记录
        $goods_list=db('order')->order('create_time decc')->select();
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