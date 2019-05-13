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
		$user =new T();
		$value1=$user->get_tactics_info($key1);
		$key2="free_tactics_other";                //帮别人甩免单策略的信息key
		$value2=$user->get_tactics_info($key2);

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
	
}
