<?php
namespace app\admin\controller;


use  think\Controller;




/**
 * 策略管理
 */
class Tactics extends Controller
{
	
	/*
	*  免单策略
	*/
	public function free_tactics()
	{
       return  view('free_tactics');
	}

	/*
	*  红包策略
	*/
	public function bao_tactics()
	{
       return  view('bao_tactics');
	}

	/*
	*  增积分策略
	*/
	public function zpoint_tactics()
	{
       return  view('zpoint_tactics');
	}

	/*
	*  大满贯策略
	*/
	public function big_slam_tactics()
	{
       return  view('big_slam_tactics');
	}

	/*
	*  新人帮甩策略
	*/
	public function new_man_tactics()
	{
       return  view('new_man_tactics');
	}

	/*
	*  旧人帮甩策略
	*/
	public function old_man_tactics()
	{
       return  view('old_man_tactics');
	}
	
}
