<?php
namespace app\admin\controller;


use  think\Controller;
use  app\admin\model\Tactics as T ;




/**
 * 策略管理
 */
class Tactics extends Controller
{
	
	/*
	   lilu 
	*  免单策略
	*/
	public function free_tactics()
	{
		$key1="free_tactics_own";                  //自己甩免单策略的信息key
		$value1=db('tactics')->where('tactics_key',$key1)->select();
		$key2="free_tactics_other";                //帮别人甩免单策略的信息key
		$value2=db('tactics')->where('tactics_key',$key2)->select();
       return  view('free_tactics',['tactics1'=>$value1,'tactics2'=>$value2]);
	}

	/*
	   lilu
	*  红包策略
	*/
	public function bao_tactics()
	{
		$key1="bao_tactics_own";                  //自己甩红包策略的信息key
		$user =new T();
		$value1=$user->get_tactics_info($key1);
		$key2="bao_tactics_other";                //帮别人甩红包策略的信息key
		$$value2=$user->get_tactics_info($key2);
			
               return  view('bao_tactics',['tactics'=>$value1,'tactics2'=>$value2]);
	}

	/*
	   lilu
	*  增积分策略
	*/
	public function zpoint_tactics()
	{
		$key1="bao_tactics_own";                  //自己甩增积分策略的信息key
		$user =new T();
		$value1=$user->get_tactics_info($key1);
		$key2="bao_tactics_other";                //帮别人甩增积分策略的信息key
		$$value1=$user->get_tactics_info($key2);
				  
		return  view('zpoint_tactics');
	}

	/*
	   lilu 
	*  大满贯策略
	*/
	public function big_slam_tactics()
	{
		$key1="big_slam_tactics_own";                  //自己甩大满贯策略的信息key
		$user =new T();
		$value1=$user->get_tactics_info($key1);
		$key2="big_slam_tactics_other";                //帮别人甩大满贯策略的信息key
		$$value1=$user->get_tactics_info($key2);
			  
		return  view('big_slam_tactics');
	}

	/*
	   lilu   
	*  新人帮甩策略
	*/
	public function new_man_tactics()
	{
		$key1="new_man_tactics_own";                  //新人帮甩策略的信息key
		$user =new T();
		$value1=$user->get_tactics_info($key1);
				  
		return  view('new_man_tactics');
	}

	/*
	   lilu
	*  旧人帮甩策略
	*/
	public function old_man_tactics()
	{
		$key1="old_man_tactics_own";                  //老人帮甩策略的信息key
		$user =new T();
		$value1=$user->get_tactics_info($key1);

      return  view('old_man_tactics');
	}
	/**
	 * lilu
	 * 免费策略处理
	 */
	public function free_tactics_do()
	{
		$input=input('post.');  //获取表单数据
		if($input){
			$attr=[];
			$i=0;
			$p=0;
			foreach($input as $k =>$vo)
			{
				if (substr($k, 0, 3) == "one")  
				{
					if(!$vo["id"])   //判断是否为新增加的记录
					{ //添加
					$data['tactics_key']=$vo['tactics_key'];                     //开启状态
					$data['tactics_status']=$vo['status'];                     //开启状态
					$data['tactics_name']='免费策略key值';                   //key
					$data['tactics_pre']=$vo['percent'];                       //百分比
					$data['tactics_num']=$vo['probability'];                   //数字
					$res=db('tactics')->insert($data);
					if($res)
					  {  
                         $i++;
					  }
					}else{
						$data['tactics_key']=$vo['tactics_key'];                     //开启状态
						$data['tactics_status']=$vo['status'];                     //开启状态
						$data['tactics_name']='免费策略key值';                   //key
						$data['tactics_pre']=$vo['percent'];                       //百分比
						$data['tactics_num']=$vo['probability'];                   //数字
						$res=db('tactics')->where('id',$vo['id'])->update($data);
						if($res){
							$i++;
						}
					}
					
				}
              $p++;
			}
			if($i==$p)
			{
				$this->success('添加成功',url('admin/admin/free_tactics'));
			}else{
				$this->error();
			}
		}
    

		return view('free_tactics');
	}
	/**
	 * lilu
	 * 红包策略处理
	 */
	public function bao_tactics_do()
	{
		$input=input('post.');
		halt($input);

		return view('bao_tactics');
	}
	/**
	 * lilu
	 * 赠积分策略处理
	 */
	public function zpoints_tactics_do()
	{
		$input=input('post.');
		halt($input);

		return view('zpoints_tactics');
	}
	/**
	 * lilu
	 * 大满贯策略处理
	 */
	public function big_slam_tactics_do()
	{
		$input=input('post.');
		halt($input);

		return view('bid_slam_tactics');


	}
	/**
	 * lilu
	 * 新人帮甩策略处理
	 */
	public function new_man_tactics_do()
	{
		$input=input('post.');
		halt($input);

		return view('new_man_tactics');
	}
	/**
	 * lilu
	 * 老人策略处理
	 */
	public function old_man_tactics_do()
	{
		$input=input('post.');
		halt($input);

		return view('old_man_tactics');
	}
	
}
