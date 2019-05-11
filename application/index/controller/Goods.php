<?php
namespace  app\index\controller;

use think\Controller;
use think\Console;
use think\Db;

/**
 * lilu
 * Class Goods
 * @package app\index\controller
 */
class Goods extends Controller
{
    /**
     **************lilu*******************
     * @param Request $request
     * Notes:前端获取商品信息
     **************************************
     */
     public  function goods_info()
     {
         //检索条件
         $where['label']=1;          //上架
         $where['goods_setting']=0;  //帮甩不限制
         $id=input('get.id');
         if($id){
             $where['id']=$id;
         }
        //获取商品
        $goods = db("goods")->where($where)->order("id asc")->select();
        foreach ($goods as $k=>$v)
        {
           if($v['goods_standard']=='1')
           {         //特殊规格
              $goods[$k]['attr']=db('special')->where('goods_id',$v['id'])->select();
           }else{
               $goods[$k]['attr']='统一规格';
           }
        }
//        $num = db("goods")->count();        //获取商品总数
//         $all_idents = $goods;               //获取分页的数据
//         $curPage = input('get.page') ? input('get.page') : 1;//接收前端分页传值
//         $listRow = 10;//每页10行记录
//         $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
//         $goods = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
//             'var_page' => 'page',
//             'path' => url('admin/Goods/goods_index'),//这里根据需要修改url
//             'query' => [],
//             'fragment' => '',
//         ]);
//         $goods->appends($_GET);
//         $this->assign('listpage', $goods->render());
//        return view("goods_index", ["goods" => $goods]);
          return ajax_success('获取成功',$goods);
     }
}