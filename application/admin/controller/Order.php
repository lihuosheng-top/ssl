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
        //处理order_number
        $pp1=db('goods_receive')->where('order_number',$order_number)->find();
        if($pp1)
        {
            $where['order_number']=$order_number;
        }
        $pp2=db('goods')->where('goods_name',$order_number)->find();
        if($pp2)
        {
            $where['goods_id']=$pp2['id'];
        }
        $pp3=db('member')->where('name',$order_number)->find();
        if($pp3)
        {
            $where['member_id']=$pp3['id'];
        }
        $order_create_time=input('get.order_create_time');
        $order_end_time=input('get.order_end_time');
        if($order_create_time && $order_end_time){
            $where['create_time']=array('between',array($order_create_time,$order_end_time));
        }else{
            $where['create_time']=array('between',array($order_create_time,time()));
        }
        $where['order_type']=array('gt',0);
        if($order_number){
            $goods_list=db('goods_receive')
                         ->where($where)
                         ->order('create_time desc')
                         ->select();
        }else{
            $goods_list=db('goods_receive')
            ->where('order_type','gt',0)
                ->order('create_time desc')
                ->select();
        }
        foreach($goods_list as $k=>$v)
        {
            $goods_info=db('goods')->where('id',$v['goods_id'])->find();
            $goods_list[$k]['goods_name']=$goods_info['goods_name'];
            $goods_list[$k]['goods_image']=$goods_info['goods_show_image'];
            $goods_list[$k]['goods_money']=$goods_info['goods_price'];
            $member=db('member')->where('id',$v['member_id'])->find();
            $goods_list[$k]['user_account_name']=$member['name'];
            $goods_list[$k]['order_quantity']='1';

        }
        $goods_list=paging_data($goods_list,'admin/Order/order_list','10');   //分页函数处理
        $goods_list->appends($_GET);
        $this->assign('listpage', $goods_list->render());
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
         $status=$request->only(['order_type'])['order_type'];   //获取订单的状态
         if($status<10 ){
             $order_list=db('goods_receive')->where('order_type',$status)->order('create_time desc')->select();
         }else{
             $order_list=db('goods_receive')->where('order_type','gt',0)->order('create_time desc')->select();
         }
         foreach($order_list as $k=>$v)
         {
             $goods_info=db('goods')->where('id',$v['goods_id'])->find();
             $order_list[$k]['goods_name']=$goods_info['goods_name'];
             $order_list[$k]['goods_image']=$goods_info['goods_show_image'];
             $order_list[$k]['goods_money']=$goods_info['goods_price'];
             $member=db('member')->where('id',$v['member_id'])->find();
             $order_list[$k]['user_account_name']=$member['name'];
             $order_list[$k]['order_quantity']='1';
 
         }
         return view('order_list',['data'=>$order_list]);
     }
     /**
      * lilu
      * 订单删除---批量删除
      */
     public function order_del(Request $request)
     {
         $id=$request->only(['id'])['id'];   //获取order的id
         $num=count($id);
         $i=0;
         foreach ($id as $k =>$v)
         {
             $re=db('goods_receive')->delete($v);
             if($re)
             {
                  $i++;
             }else{

             }
         }
         if($num==$i)
         {
             return ajax_success('批量删除成功');
         }else{
             return ajax_error('批量删除失败');
         }
     }
     /**
      * lilu
       * $parsm $id   订单id
        *return orderinfo   订单信息
      */
      public function get_orderinfo()
      {
        $id=input('post.id');
        if($id){
            $orderinfo=db('goods_receive')->where('id',$id)->find();   //获取信息
            //获取用户的默认地址
            $address=db('member_address')->where(['member_id'=>$orderinfo['member_id'],'is_use'=>'1'])->find();
            $orderinfo['express_name']=db('delivery')->where('id',$orderinfo['express_id'])->value('express_name');
            if($orderinfo['order_type']==1)
            {   //待确定
                $key="admin_message";
                $info=db('sys_setting')->where('key',$key)->find();
                $info['value']=json_decode($info['value'],true);
                $orderinfo['message']=$info['value']['message']['message'];
            }
            if($address)
            {
              $orderinfo['address']=$address['address'].$address['detail_address'];
              $orderinfo['phone']=$address['phone'];
              $orderinfo['address_id']=$address['id'];
            }else{
                $orderinfo['address']='用户没有默认地址';
                $orderinfo['phone']='0';
                $orderinfo['address_id']='0';
            }
            return ajax_success('获取成功',$orderinfo);
        }else{
            return  ajax_error('参数错误');
        }
    }
    /**
     * lilu
     * 发送短信
     */
    public function send_message()
    {
        $input=input();
        if($input)
        {
            //判断是否开启了短信开启功能  key="admin_message"
            $key="admin_message";   
            $info=db('sys_setting')->where('key',$key)->find();
            $info['value']=json_decode($info['value'],true);
            if($info['value']['message']['status']=='1')
            {
               
                $pp=sms_message($input['mes'],$input['phone']);
            }else{
                return ajax_error('发送失败,短信功能未开启');
            }
            if($pp)
            {
                return ajax_success('发送成功');
            }
        }else{
               return ajax_error('发送失败');
        }
    }
    /**
     * lilu
     * 快递单信息保存
     */
    public function order_save()
    {
        //获取信息
        $input=input();
        if($input)
        {
            //获取用户的地址
            $address=db('member_address')->where('id',$input['address_id'])->find();
            $data['receiver']=db('member')->where('id',$address['member_id'])->value('name');
            $data['receive_phone']=$address['phone'];
            $data['receive_address']=$address['address'].$address['detail_address'];
            $data['order_info']=$input['order_info'];
            $data['express_number']=$input['order'];
            $data['express_id']=$input['express'];
            $data['order_type']='3';
            $res=db('goods_receive')->where('order_number',$input['order_number'])->update($data);
            if($res !==false)
            {
                return ajax_success('保存成功');
            }
        }else{
                return ajax_error('参数获取失败');
        }
         
            
    }


 }