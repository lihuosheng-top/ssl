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

}