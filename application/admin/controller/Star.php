<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\index\controller\Base;
use think\paginator\driver\Bootstrap;


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
		//获取星光值奖品的商品
		$list=db('star_goods')->select();
		if($list)
		{
			return  view('star_exchange',['data'=>$list]);
		}else{

			return  view('star_exchange');
		}
	}
	/**
	 * lilu
	*  商品编辑
	**/
	public function prize_edit()
	{
		//获取商品id
		$input=input();
        //获取星光值奖品的商品
		$list=db('star_goods')->where('id',$input['id'])->find();
		return  view('prize_edit',['data'=>$list]);
	}


	/**
	*   奖品添加
	**/
	public function prize_add()
	{
       return  view('prize_add');
	}
	
	/**
	 * lilu
	*   奖品添加处理
	**/
	public function prize_add_do(Request $request)
	{
	   //获取参数
	   $input=input();
	   $goods_data=$input;
	   $show_images = $request->file("goods_show_images");
	   if($show_images){
		   $info = $show_images->move(ROOT_PATH . 'public' . DS . 'uploads');
		   $goods_data['goods_image'] = '/uploads/'.str_replace("\\", "/", $info->getSaveName()); 
	   }
	   $re=db('star_goods')->insert($goods_data);
	   if($re){
            $this->success('添加成功',url('admin/Star/star_exchange'));
	   }else{
             $this->error('添加失败');
	   }
	}
	/**
	 * lilu
	*   奖品添加处理
	**/
	public function prize_edit_do(Request $request)
	{
	   //获取参数
	   $input=input();
	   $goods_data=$input;
	   $show_images = $request->file("goods_show_images");
	   if($show_images){
		   $info = $show_images->move(ROOT_PATH . 'public' . DS . 'uploads');
		   $goods_data['goods_image'] = '/uploads/'.str_replace("\\", "/", $info->getSaveName()); 
	   }
	   $re=db('star_goods')->where('id',$goods_data['id'])->update($goods_data);
	   if($re){
            $this->success('编辑成功',url('admin/Star/star_exchange'));
	   }else{
             $this->error('编辑失败');
	   }
	}

  /**
   * lilu
   * 商品删除
   */
  public function prize_del()
  {
	  //获取id
	  $input=input();
	  if($input){
		  $re=db('star_goods')->where('id',$input['id'])->delete();
		  if($re)
		  {
             $this->success('删除成功',url('admin/Star/star_exchange'));
		  }else{
			  $this->error('删除失败');
			  
		  }
	  }else{
		  $this->error('获取参数失败');
	  }
  }
	
	/**
	 * lilu
	*  星光值兑换记录
	**/
	public function list_exchange()
	{
		//获取后台的星光值兑换记录
		$list=db('exchange_list')->order('create_time desc')->select();
		foreach($list as $k=>$v)
		{
			$goods_info=db('star_goods')->where('id',$v['star_goods_id'])->find();
			$list[$k]['goods_name']=$goods_info['goods_name'];
		}
		$all_idents = $list;               //获取分页的数据
        $curPage = input('get.page') ? input('get.page') : 1;//接收前端分页传值
        $listRow = 10;//每页10行记录
        $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
        $list = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path' => url('admin/Star/list_exchange'),//这里根据需要修改url
            'query' => [],
            'fragment' => '',
        ]);
        $list->appends($_GET);
        $this->assign('listpage', $list->render());
        return  view('list_exchange',['data'=>$list]);
	}
	
}