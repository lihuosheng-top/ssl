<?php
namespace app\admin\controller;

use think\Controller;


/**
 * 资金
 */
class Capital extends Controller
{
	
	/**
	*   资金流水
	**/
    public function  capital()
    {
    	return   view('capital');
	}
	
	/**
	*   资金流水详情
	**/
    public function  capital_details()
    {
    	return   view('capital_details');
    }

}