<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\paginator\driver\Bootstrap;

/**
 * lilu
 * 资金
 */
class Capital extends Controller
{
	
	/**
	 *  lilu
	*   资金流水
	**/
    public function  capital()
    {
		//获取资金流水的所有记录
		$list=db('captical_record')->order('create_time desc')->select();
		$all_idents = $list; 
		$curPage = input('get.page') ? input('get.page') : 1;//接收前端分页传值
        $listRow = 10;//每页10行记录
        $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
        $list = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path' => url('admin/Capital/capital'),//这里根据需要修改url
            'query' => [],
            'fragment' => '',
        ]);
        $list->appends($_GET);
        $this->assign('listpage', $list->render());
    	return   view('capital',['data'=>$list]);
	}
	
	/**
	 *  lilu
	*   资金流水详情
	*    parsm   order_type   订单类型
	**/
    public function  capital_details()
    {
		//获取订单类型
		$order_type=input('order_type');
		$id=input('id');
		$info=db('help_record')->where('id',$id)->find();
    	return   view('capital_details',['order_type'=>$order_type,'data'=>$info]);
	}
	/**
	 * lilu
	 * Notes:资金流水检索
	 * parsm: account
	 * parsm:order_type   订单类型
	 * parsm:pay_type     支付类型
	 * parsm:start_date   开始时间
	 * parsm:end_date     结束时间
	 * return   $list     检索结果
	 */
	public function capital_search(Request $request)
	{
		//定义检索条件
		$where=[];
		$input=input();     //获取表单数据
		if($input['account'])
		{
			$name=db('member')->where('account',$input['account'])->value('id');
			if($name)
			{
				$where['member_id']=$name;
			}
		}
		if($input['order_type'])
		{
             $where['order_type']=$input['order_type'];
		}
		if($input['pay_type']!='0')
		{
             $where['pay_type']=$input['pay_type'];
		}
		if($input['start_time'] && $input['end_time'])
		{
            $where['create_time']=array('between',array(strtotime($input['start_time']),strtotime($input['end_time'])));
		}elseif($input['start_time'] && !$input['end_time']){
            $where['create_time']=array('between',array(strtotime($input['start_time']),time()));
            
		}else{

		}
		//根据检索条件获取数据
		$list=db('help_record')->where($where)->order('create_time desc')->select();
		$all_idents = $list; 
		$curPage = input('get.page') ? input('get.page') : 1;//接收前端分页传值
        $listRow = 10;//每页10行记录
        $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
        $list = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path' => url('admin/Capital/capital'),//这里根据需要修改url
            'query' => [],
            'fragment' => '',
        ]);
        $list->appends($_GET);
		$this->assign('listpage', $list->render());

	 	return  view('capital',['data'=>$list]); 

	}
	
	/**
	 * lilu 
	 * 资金流水记录删除
	 */
	public function help_record_del(Request $request)
	{
		if($request->isPost())
		{
			$id = $request->only(["id"])["id"];    //获取资金流水记录id
			if($id){
				$i=0;
				$num=count($id);
				foreach($id as $k=>$v)
				{
                  if($v){
					  $re=db('help_record')->delete($v);
					  if($re){
						  $i++;
					  }
				  }
				}
				if($num==$i){
					return ajax_success('批量删除成功');
				}else{
					return ajax_error('删除失败');
				}

			}else{
				return ajax_error('删除失败');
			}
			
			

		}
	}

	
	/**
	 * lilu
	*  商品账单
	**/
    public function  goods_bill()
    {
		//获取商品流水的所有记录
		$list=db('help_record')
		      ->where('order_type',1)
		      ->whereOr('order_type',2)
		      ->whereOr('order_type',4)
		      ->whereOr('order_type',5)
		      ->order('create_time desc')->select();
		$all_idents = $list; 
		$curPage = input('get.page') ? input('get.page') : 1;//接收前端分页传值
        $listRow = 10;//每页10行记录
        $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
        $list = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path' => url('admin/Capital/capital'),//这里根据需要修改url
            'query' => [],
            'fragment' => '',
        ]);
        $list->appends($_GET);
        $this->assign('listpage', $list->render());
    	return   view('goods_bill',['data'=>$list]);
    }


	/**
	*   商品账单详情
	**/
    public function  goods_bill_details()
    {
		//获取订单类型
		$order_type=input('order_type');
		$id=input('id');
		$info=db('help_record')->where('id',$id)->find();
    	return   view('goods_bill_details',['order_type'=>$order_type,'data'=>$info]);
	}
	
	/**
	 * lilu
	 * Notes:商品账单检索
	 * parsm: account
	 * parsm: help_account
	 * parsm: goods_number  商品编号/商品名称
	 * parsm:order_type   订单类型
	 * parsm:start_date   开始时间
	 * parsm:end_date     结束时间
	 * return   $list     检索结果
	 */
	public function goods_bill_search(Request $request)
	{
		//定义检索条件
		$where=[];
		$input=input();     //获取表单数据
		if($input['goods_name'])
		{
		   $name=db('goods')->where('goods_name',$input['goods_name'])->value('id');
		   $where['good_id']=$name;

		}
		if($input['account'])
		{
			$name=db('member')->where('account',$input['account'])->value('id');
			if($name)
			{
				$where['member_id']=$name;
			}
		}
		if($input['help_account'])
		{
			$name=db('member')->where('account',$input['help_account'])->value('id');
			if($name)
			{
				$where['help_id']=$name;
			}
		}
		if($input['order_type'])
		{
             $where['order_type']=$input['order_type'];
		}
		if($input['start_time'] && $input['end_time'])
		{
            $where['create_time']=array('between',array(strtotime($input['start_time']),strtotime($input['end_time'])));
		}elseif($input['start_time'] && !$input['end_time']){
            $where['create_time']=array('between',array(strtotime($input['start_time']),time()));
            
		}else{

		}
		//根据检索条件获取数据
			$list=db('help_record')->where($where)
			     ->order('create_time desc')->select();
		$all_idents = $list; 
		$curPage = input('get.page') ? input('get.page') : 1;//接收前端分页传值
        $listRow = 10;//每页10行记录
        $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
        $list = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path' => url('admin/Capital/capital'),//这里根据需要修改url
            'query' => [],
            'fragment' => '',
        ]);
        $list->appends($_GET);
		$this->assign('listpage', $list->render());

	 	return  view('goods_bill',['data'=>$list]); 

	}
}