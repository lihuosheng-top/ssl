<?php
 namespace   app\admin\controller;


use think\Controller;
use think\Request;

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
 	public function order_list(Request $request)
    {
        //获取检索的条件信息
        $order_number=input('get.order_number');      //订单号、商品名称、用户账号
        $order_create_time=input('get.order_create_time');
        $order_end_time=input('get.order_end_time');
        if($order_create_time && $order_end_time){
            $where['create_time']=array('between',array($order_create_time,$order_end_time));
        }else{
            $where['create_time']=array('between',array($order_create_time,time()));
        }
        if($order_number){
            $goods_list=db('order')
                         ->where('goods_name',$order_number)
                         ->whereOr('order_number',$order_number)
                         ->whereOr('user_account_name',$order_number)
                         ->whereOr($where)
                         ->order('create_time desc')
                         ->select();
        }else{
            $goods_list=db('order')
                ->order('create_time desc')
                ->select();
        }
         return view('order_list',['data'=>$goods_list]);

 	}
     /**
      * lilu
      * 订单检索
      * @return Request
      */
     public function order_search()
     {
         //获取检索的条件信息
         $order_number=input('get.order_number');      //订单号、商品名称、用户账号
         $order_create_time=input('get.order_create_time');
         $order_end_time=input('get.order_end_time');
         if($order_create_time && $order_end_time){
             $where['create_time']=array('between',array($order_create_time,$order_end_time));
         }elseif(!$order_create_time && $order_end_time){
             $where['create_time']=array('between',array($order_create_time,time()));
         }
         if($order_number){
             $goods_list=db('order')
                 ->where('goods_name',$order_number)
                 ->whereOr('order_number',$order_number)
                 ->whereOr('user_account_name',$order_number)
                 ->whereOr($where)
                 ->order('create_time desc')
                 ->select();
         }else{
             $goods_list=db('order')
                 ->order('create_time desc')
                 ->select();
         }
         return view('order_list');
     }
     /**
      * lilu
      * 不同订单切换
      * @return Request
      */
     public function order_status(Request $request)
     {
         $status=$request->only(['status'])['status'];   //获取订单的状态
         if($status !='0'){
             $order_list=db('order')->where('status',$status)->order('create_time desc')->select();
         }else{
             $order_list=db('order')->order('create_time desc')->select();
         }
         return view('order_list',['data'=>$order_list]);
     }


 }