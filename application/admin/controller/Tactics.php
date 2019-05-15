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
		$value1=db('tactics')->where('tactics_key',$key1)->select();
		$key2="bao_tactics_other";                //帮别人甩红包策略的信息key
		$value2=db('tactics')->where('tactics_key',$key2)->select();
			
        return  view('bao_tactics',['tactics'=>$value1,'tactics2'=>$value2]);
	}

	/*
	   lilu
	*  增积分策略
	*/
	public function zpoint_tactics()
	{
		$key1="zpoints_own";                  //自己甩增积分策略的信息key
		$value1=db('tactics')->where('tactics_key',$key1)->find();
		$key2="zpoints_other";                //帮别人甩增积分策略的信息key
		$value2=db('tactics')->where('tactics_key',$key2)->find();
		return  view('zpoint_tactics',['tactics1'=>$value1,'tactics2'=>$value2]);
	}

	/*
	   lilu 
	*  大满贯策略
	*/
	public function big_slam_tactics()
	{
		$key1="big_falm_condition";                  //大满贯赠积分条件策略的信息key
		$value1=db('tactics')->where('tactics_key',$key1)->find();
		$key2="giving";                             //满足条件赠送积分的key
		$value2=db('tactics')->where('tactics_key',$key2)->find();
			  
		return  view('big_slam_tactics',['tactics1'=>$value1,'tactics2'=>$value2]);
	}

	/*
	   lilu   
	*  新人帮甩策略
	*/
	public function new_man_tactics()
	{
		$key1="new_man_tactics_own";                  //新人帮甩策略的信息key
		$value1=db('tactics')->where('tactics_key',$key1)->find();
				  
		return  view('new_man_tactics',['tactics'=>$value1]);
	}

	/*
	   lilu
	*  旧人帮甩策略
	*/
	public function old_man_tactics()
	{
		$key1="old_man_tactics_own";                  //老人帮甩策略的信息key
		$value1=db('tactics')->where('tactics_key',$key1)->find();

      return  view('old_man_tactics',['tactics'=>$value1]);
	}
	/**
	 * lilu
	 * 免费策略处理
	 */
	public function free_tactics_do()
	{
		$input=input('post.');  //获取表单数据
		if($input){
			$data=[];
			$i=0;
			$p=0;
			foreach($input as $k =>$vo)
			{
				if (substr($k, 0, 3) == "one")  
				{
					if(!$vo['id'])  //判断是否为新增加的记录
					{ //添加
					$data['tactics_key']=$vo['tactics_key'];                     //开启状态
					$data['tactics_status']=$vo['status'];                     //开启状态
					$data['tactics_name']='免费策略key值';                   //key
					$data['tactics_pre']=$vo['percent'];                       //百分比
					$data['tactics_num']=$vo['probability'];                   //数字
					$res=db('tactics')->insert($data);
					}else{
						if(!array_key_exists('status',$input[$k])){
							db('tactics')->delete($vo['id']);
						}else{
							$data['tactics_key']=$vo['tactics_key'];                     //开启状态
							$data['tactics_status']=$vo['status'];                     //开启状态
							$data['tactics_name']='免费策略key值';                   //key
							$data['tactics_pre']=$vo['percent'];                       //百分比
							$data['tactics_num']=$vo['probability'];                   //数字
							$res=db('tactics')->where('id',$vo['id'])->update($data);
						}
						
					}
					
				}elseif(substr($k, 0, 4) == "help"){
					if(!$vo['id'])  //判断是否为新增加的记录
					{ //添加
					$data['tactics_key']=$vo['tactics_key'];                     //开启状态
					$data['tactics_status']=$vo['status'];                     //开启状态
					$data['tactics_name']='免费策略key值';                   //key
					$data['tactics_pre']=$vo['percent'];                       //百分比
					$data['tactics_num']=$vo['probability'];                   //数字
					$res=db('tactics')->insert($data);
					}else{
						if(!array_key_exists('status',$input[$k])){
							db('tactics')->delete($vo['id']);
						}else{
							$data['tactics_key']=$vo['tactics_key'];                     //开启状态
							$data['tactics_status']=$vo['status'];                     //开启状态
							$data['tactics_name']='免费策略key值';                   //key
							$data['tactics_pre']=$vo['percent'];                       //百分比
							$data['tactics_num']=$vo['probability'];                   //数字
							$res=db('tactics')->where('id',$vo['id'])->update($data);
						}
						
					}
				}else{

				}
			}
				$this->success('操作成功',url('admin/Tactics/free_tactics'));
			
		}
    

		// return view('free_tactics');
	}
	/**
	 * lilu
	 * 红包策略处理
	 */
	public function bao_tactics_do()
	{
		$input=input('post.');  //获取表单数据
		if($input){
			$data=[];
			$i=0;
			$p=0;
			foreach($input as $k =>$vo)
			{
				if (substr($k, 0, 3) == "one")  
				{
					if(!$vo['id'])  //判断是否为新增加的记录
					{ //添加
					$data['tactics_key']=$vo['tactics_key'];                     //开启状态
					$data['tactics_status']=$vo['status'];                     //开启状态
					$data['tactics_name']='红包策略key值';                   //key
					$data['tactics_pre']=$vo['percent'];                       //百分比
					$data['tactics_num']=$vo['probability'];                   //数字
					$res=db('tactics')->insert($data);
					}else{
						if(!array_key_exists('status',$input[$k])){
							db('tactics')->delete($vo['id']);
						}else{
							$data['tactics_key']=$vo['tactics_key'];                     //开启状态
							$data['tactics_status']=$vo['status'];                     //开启状态
							$data['tactics_name']='红包策略key值';                   //key
							$data['tactics_pre']=$vo['percent'];                       //百分比
							$data['tactics_num']=$vo['probability'];                   //数字
							$res=db('tactics')->where('id',$vo['id'])->update($data);
						}
						
					}
					
				}elseif(substr($k, 0, 4) == "help"){
					if(!$vo['id'])  //判断是否为新增加的记录
					{ //添加
					$data['tactics_key']=$vo['tactics_key'];                     //开启状态
					$data['tactics_status']=$vo['status'];                     //开启状态
					$data['tactics_name']='红包策略key值';                   //key
					$data['tactics_pre']=$vo['percent'];                       //百分比
					$data['tactics_num']=$vo['probability'];                   //数字
					$res=db('tactics')->insert($data);
					}else{
						if(!array_key_exists('status',$input[$k])){
							db('tactics')->delete($vo['id']);
						}else{
							$data['tactics_key']=$vo['tactics_key'];                     //开启状态
							$data['tactics_status']=$vo['status'];                     //开启状态
							$data['tactics_name']='红包策略key值';                   //key
							$data['tactics_pre']=$vo['percent'];                       //百分比
							$data['tactics_num']=$vo['probability'];                   //数字
							$res=db('tactics')->where('id',$vo['id'])->update($data);
						}
						
					}
				}else{

				}
			}
				$this->success('操作成功',url('admin/Tactics/bao_tactics'));
			
		}
	}
	/**
	 * lilu
	 * 赠积分策略处理
	 */
	public function zpoint_tactics_do()
	{
		$input=input('post.');
		foreach($input as $k =>$vo)
			{
				if (substr($k, 0, 3) == "sss")  
				{
					if(!$vo['id'] )   //判断是否为新增加的记录
					{ //添加
					$data['tactics_key']=$vo['tactics_key'];                     //开启状态
					$data['tactics_name']='红包策略key值';                   //key
					$data['tactics_num']=$vo['tactics_num'];                   //数字
					$res=db('tactics')->insert($data);
					}else{
						$data['tactics_key']=$vo['tactics_key'];                     //开启状态
						$data['tactics_name']='红包策略key值';                   //key
						$data['tactics_num']=$vo['tactics_num'];                   //数字
						$res=db('tactics')->where('id',$vo['id'])->update($data);
						
					}
					
				}
			}
		$this->success('保存成功',url('admin/Tactics/zpoint_tactics'));
	}
	/**
	 * lilu
	 * 大满贯策略处理
	 */
	public function big_slam_tactics_do()
	{
		$input=input('post.');
		foreach($input as $k =>$vo)
			{
				if (substr($k, 0, 3) == "sss")  
				{
					if(!$vo['id'] )   //判断是否为新增加的记录
					{ //添加
					$data['tactics_key']=$vo['tactics_key'];                     //开启状态
					$data['tactics_name']='大满贯满足条件的key值';                   //key
					$data['tactics_num']=$vo['tactics_num'];                   //数字
					$res=db('tactics')->insert($data);
					}else{
						$data['tactics_key']=$vo['tactics_key'];                     //开启状态
						$data['tactics_name']='大满贯满足条件赠送积分得key值';                   //key
						$data['tactics_num']=$vo['tactics_num'];                   //数字
						$res=db('tactics')->where('id',$vo['id'])->update($data);
						
					}
					
				}
			}
		$this->success('保存成功',url('admin/Tactics/big_slam_tactics'));

		// return view('bid_slam_tactics');


	}
	/**
	 * lilu
	 * 新人帮甩策略处理
	 */
	public function new_man_tactics_do()
	{
		$input=input('post.');
		foreach($input as $k =>$vo)
			{
				if (substr($k, 0, 3) == "sss")  
				{
					if(!$vo['id'] )   //判断是否为新增加的记录
					{ //添加
					$data['tactics_key']=$vo['tactics_key'];                   //key键
					$data['tactics_name']='新人帮甩策略key值';                      //key值
					$data['tactics_num']=$vo['tactics_num'];                   //新人帮甩限制次数
					$res=db('tactics')->insert($data);
					}else{
						$data['tactics_key']=$vo['tactics_key'];                 //key键
						$data['tactics_name']='新人帮甩策略key值';                    //key
						$data['tactics_num']=$vo['tactics_num'];                 //新人帮甩限制次数
						$res=db('tactics')->where('id',$vo['id'])->update($data);
						
					}
					
				}
			}
		$this->success('保存成功',url('admin/Tactics/new_man_tactics'));

		// return view('new_man_tactics');
	}
	/**
	 * lilu
	 * 老人策略处理
	 */
	public function old_man_tactics_do()
	{
		$input=input('post.');
		foreach($input as $k =>$vo)
			{
				if (substr($k, 0, 3) == "sss")  
				{
					if(!$vo['id'] )   //判断是否为新增加的记录
					{ //添加
					$data['tactics_key']=$vo['tactics_key'];                   //key键
					$data['tactics_name']='旧人帮甩策略key值';                      //key值
					$data['tactics_num']=$vo['tactics_num'];                   //新人帮甩限制次数
					$res=db('tactics')->insert($data);
					}else{
						$data['tactics_key']=$vo['tactics_key'];                 //key键
						$data['tactics_name']='旧人帮甩策略key值';                    //key
						$data['tactics_num']=$vo['tactics_num'];                 //新人帮甩限制次数
						$res=db('tactics')->where('id',$vo['id'])->update($data);
						
					}
					
				}
			}
		$this->success('保存成功',url('admin/Tactics/old_man_tactics'));
		// return view('old_man_tactics');
	}
	
}
