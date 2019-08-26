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
	   if($re !== false){
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
			$member=db('member')->where('id',$v['member_id'])->find();
			if($member)
			{
				$list[$k]['member_name']=$member['name'];
			}
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
	/**
	 * lilu
	 * 奖品检索
	 */
	public function prize_search()
	{
		//获取检索的参数
		$input=input();
		if($input)
		{
		   $goods=db('star_goods')->where('goods_name',$input['goods_name'])->select();
		   return  view('star_exchange',['data'=>$goods]);
		}
	}
	/**
	 * lilu
	 * 星光商品批量删除
	 */
	public function prize_dels(Request $request)
	{
		 //获取会员id
		 $id=$request->only(['id'])['id'];
		 $num=count($id);
		 $i=0;
		 foreach ($id as $k=>$v){
				 //1.删除会员信息
				 $re=db('star_goods')->delete($v);
				 if($re){
					 $i++;
				 }
		 }
		 if($i==$num){
			 return ajax_success('批量删除成功');
		 }else{
			 return ajax_error('批量删除失败');
		 }
	   
	}
	/**
	 * lilu
	 * 星光值兑换检索
	 */
	public function exchange_list_search()
	{
		//获取检索的条件
		$input=input();
		if($input)
		{
			if($input['order_number'])    //订单编号
			{
                 $where['order_number']=$input['order_number'];
			}
			if($input['name'])    //昵称
			{
				$member=db('member')->where('name',$input['name'])->find();
				if($member)
				{
					$where['member_id']=$member['id'];
				}
			}
			if($input['date_min'] && $input['date_max']){
					$where['create_time']=array('between',array($input['date_min'],$input['date_max']));
			}
			$list=db('exchange_list')->where($where)->select();
			foreach($list as $k=>$v)
		{
			$goods_info=db('star_goods')->where('id',$v['star_goods_id'])->find();
			$list[$k]['goods_name']=$goods_info['goods_name'];
			$member=db('member')->where('id',$v['member_id'])->find();
			if($member)
			{
				$list[$k]['member_name']=$member['name'];
			}
		}
			//分页处理
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
	/**
	 * lilu
	 * 星光值兑换列表删除
	 */
	public function exchange_list_dels(Request $request)
	{
		//获取会员id
		$id=$request->only(['id'])['id'];
		$num=count($id);
		$i=0;
		foreach ($id as $k=>$v){
				//1.删除会员信息
				$re=db('exchange_list')->delete($v);
				if($re){
					$i++;
				}
		}
		if($i==$num){
			return ajax_success('批量删除成功');
		}else{
			return ajax_error('批量删除失败');
		}
	}
	
}