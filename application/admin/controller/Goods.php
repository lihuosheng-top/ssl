<?php

/**
 * Created by PhpStorm.
 * User: lilu
 * Date: 2018/7/11
 * Time: 16:12
 */

namespace app\admin\controller;

use think\console\Input;
use think\Controller;
use think\Db;
use think\Request;
use think\Image;
use app\admin\model\Good;
use app\admin\model\GoodsImages;
use think\Session;
use think\Loader;
use think\paginator\driver\Bootstrap;
use app\admin\controller\Base;

class Goods extends Base
{


    /**
     * [商品列表显示]
     * lilu
     */
    public function goods_index(Request $request)
    {
        //获取商品
        $goods = db("goods")->order("id desc")->select();
        $num = db("goods")->count();        //获取商品总数
        $all_idents = $goods;               //获取分页的数据
        $curPage = input('get.page') ? input('get.page') : 1;//接收前端分页传值
        $listRow = 10;//每页10行记录
        $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
        $goods = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path' => url('admin/Goods/goods_index'),//这里根据需要修改url
            'query' => [],
            'fragment' => '',
        ]);
        $goods->appends($_GET);
        $this->assign('listpage', $goods->render());
        return view("goods_index", ["goods" => $goods,"num" => $num]);

    }



    /**
     * [商品列表添加组]
     * lilu
     */
    public function goods_add()
    {
        return view("goods_add");
    }



    /**
     * [商品列表组保存]
     * lilu
     * 
     */
    public function goods_add_do(Request $request)
    {
        if ($request->isPost()) {                       //判断请求类型
            $goods_data = $request->param();
            $show_images = $request->file("goods_show_images");        //商品大图
            //统一规格图片
            $goods_images_one = $request->file("goods_images_one");        //商品1
            $goods_images_two = $request->file("goods_images_two");        //商品2
            $goods_images_three = $request->file("goods_images_three");        //商品3
            //特殊规格图片
            $imgs_one = $request->file("imgs_one");
            $imgs_two = $request->file("imgs_two");
            $imgs_three = $request->file("imgs_three");
            //处理商品规格图片
            if (!empty($imgs_one)) {
                foreach ($imgs_one as $k=>$v) {
                    $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                    $goods_data['image_one'][] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                }
            }
            //处理商品规格图片
            if (!empty($imgs_two)) {
                foreach ($imgs_two as $k=>$v) {
                    $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                    $goods_data['image_two'][] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                }
            }
            //处理商品规格图片
            if (!empty($imgs_three)) {
                foreach ($imgs_three as $k=>$v) {
                    $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                    $goods_data['image_three'][] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                }
            }
            $list = [];
            unset($goods_data["aaa"]);
            if (!empty($show_images)) {
                foreach ($show_images as $k=>$v) {
                    $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                    $list[] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                }
                $goods_data["goods_show_image"] =  $list[0];
                $goods_data["goods_show_images"] = implode(',', $list);
            }
            if ($goods_data["goods_standard"] == "0") {         //统一规格
                if ((!empty($show_images))) {
                    if (!empty($goods_images_one)) {
                        foreach ($goods_images_one as $k=>$v) {
                            $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                            $goods_data['goods_images_one'] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                        }
                    }
                    if (!empty($goods_images_two)) {
                        foreach ($goods_images_two as $k=>$v) {
                            $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                            $goods_data['goods_images_two'] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                        }
                    }
                    if (!empty($goods_images_three)) {
                        foreach ($goods_images_three as $k=>$v) {
                            $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                            $goods_data['goods_images_three'] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                        }
                    }
                    $goods_data['brand']=$goods_data['produce'];
                    $goods_data['start_date']=strtotime($goods_data['start_date']);
                    $goods_data['end_date']=strtotime($goods_data['end_date']);
                    //随机生成商品编码（6位）
                    $goods_data['goods_number']=randomkeys();
                    $goods_data['goods_price']=$goods_data['goods_price'];
                    // $goods_data['goods_sign']=json_encode($goods_data['sss']);
                    unset($goods_data['sss']);
                    //判断商品名称是否存在
                    $name=db('goods')->where('goods_name',$goods_data['goods_name'])->find();
                    if($name)
                    {
                        $this->error("商品名称已存在");
                    }else{

                        $bool = db("goods")->insert($goods_data);
                    }
                    if($bool){
                        $this->success("添加成功", url("admin/Goods/goods_index"));
                    }else{
                        $this->error("添加失败");
                    }
                } else {
//                    $this->success("添加失败", url('admin/Goods/goods_add'));
                      $this->error("添加失败");
                }
            }
            if ($goods_data["goods_standard"] == "1") {          //特殊规格
                $goods_special = [];
                $goods_special["goods_name"] = $goods_data["goods_name"];
                $goods_special["produce"] = $goods_data["produce"];
                $goods_special["brand"] = $goods_data["produce"];
                $goods_special["start_date"] = strtotime($goods_data["start_date"]);
                $goods_special["end_date"] = strtotime($goods_data["end_date"]);
                $goods_special['goods_number']=randomkeys();                            //商品编号
                $goods_special["goods_standard"] = $goods_data["goods_standard"];    //商品规格
                $goods_special["goods_sign"] = $goods_data["goods_sign"];
                $goods_special["goods_share_describe"] = $goods_data["goods_share_describe"];
                $goods_special["goods_share_title"] = $goods_data["goods_share_title"];
                $goods_special["video_link"] = $goods_data["video_link"];             //视频链接
                $goods_special["goods_freight"] = $goods_data["goods_freight"];
                $goods_special["label"] = $goods_data['label'];                      //上下架   默认上架
                $goods_special["goods_setting"] = $goods_data['goods_setting'];     //上下架   默认上架

                $goods_special["goods_detail"] = $goods_data["goods_detail"];  //商品详情
                $goods_special["goods_show_images"] = $goods_data["goods_show_images"];
                $goods_special["goods_show_image"] = $goods_data["goods_show_image"];
                //判断商品名称是否存在
                $name2=db('goods')->where('goods_name',$goods_special['goods_name'])->find();
                if($name2)
                {
                    $this->error("商品名称已存在");
                }else{

                    $bool = db("goods")->insert($goods_data);
                }
                $goods_id = db('goods')->insertGetId($goods_special);       //添加商品数据,返回商品id
                $result = implode(",", $goods_data["lv1"]);         //商品规格title
                if (!empty($goods_data)) {
                    $attr=[];
                    $i=0;
                    foreach ($goods_data as $kn => $nl) {
                        if (substr($kn, 0, 3) == "sss") {      //判断是否是规格记录
                               $attr[$i]['stock']=$nl['stock'];            //库存
                               $attr[$i]['coding']=$nl['coding'];          //规格
                               $attr[$i]['cost']=$nl['cost'];              //成本价
                               $attr[$i]['line']=$nl['line'];              //划线价
                               $attr[$i]['total']=$nl['total'];            //积分
                               $attr[$i]['jilt']=$nl['jilt']; 
                               $arr['goods_price'] =$nl['jilt'];            //帮甩费用
                               $arr['goods_repertory'] =$nl['stock'];            //帮甩费用
                               $arr['goods_cost'] =$nl['cost'];            //帮甩费用
                               $arr['goods_bottom_money'] =$nl['line'];            //帮甩费用
                               $arr['points'] =$nl['total'];            //帮甩费用
                               db('goods')->where('id',$goods_id)->update($arr);
                               $attr[$i]['status']=$nl['status'];          //上下架
                               $attr[$i]['goods_id']=$goods_id;
                               $attr[$i]['lv1']=$result;                    //规格title
                               $attr[$i]['name']=$nl['name'];                //规格名称
                               $attr[$i]['image_one']=$goods_data['image_one'][$i];                //规格图片
                               $attr[$i]['image_two']=$goods_data['image_two'][$i];                //规格图片
                               $attr[$i]['image_three']=$goods_data['image_three'][$i];                //规格图片
                               $i++;
                        }
                    }
                }
                foreach ($attr as $kz => $vw) {
                    $rest = db('special')->insertGetId($vw);
                }    
                if ($rest && (!empty($show_images))) {
                    $this->success("添加成功", url("admin/Goods/goods_index"));
                } else {
                    $this->success("添加失败", url('admin/Goods/add'));
                }
            }
        }
    }


    /**
     * [商品修改]
     * lilu
     * @parsm  goods_id
     */
    public function goods_edit(Request $request, $id)
    {
        $goods = db("goods")->where("id", $id)->select();
//        $scope = db("member_grade")->field("member_grade_name")->select();    //获取会员列表
        $goods_standard = db("special")->where("goods_id", $id)->select();      //获取该商品规格
//        $expenses = db("express")->field("id,name")->select();                //获取快递列表
        foreach ($goods as $key => $value) {
            if(!empty($goods)){
            $goods[$key]["goods_show_images"] = explode(',', $goods[$key]["goods_show_images"]);
            $goods[$key]["goods_attr"] =  $goods_standard; //商品规格记录
//            $goods[$key]["scope"] = explode(',', $goods[$key]["scope"]);                   //面向会员范围
//            $goods[$key]["unit"] = explode(',', $goods[$key]["element"]);                    //单位名称
        }
     }
        $restel = $goods[0]["goods_standard"]; //判断是否为通用或特殊
        if ($restel == 0) {                 //统一规格
            return view("goods_edit", ["goods" => $goods]);
        } else {
            return view("goods_edit", ["goods" => $goods ]);
        }
    }
    /**
     * [商品修改处理]
     * lilu
     * @parsm  id   商品id
     */
    public function goods_edit_do(Request $request, $id)
    {
        if ($request->isPost()) {                                  //判断请求类型
            $goods_data = $request->param();                         //获取表单数据
            if($goods_data['goods_standard']=='0'){                //判断商品规格
                $show_images = $request->file("goods_show_images");        //商品大图
                if (!empty($show_images)) {
                    foreach ($show_images as $k=>$v) {
                        $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $list[] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                    }
                    $goods_data["goods_show_image"] =  $list[0];
                    $goods_data["goods_show_images"] = implode(',', $list);
                }
                //统一规格图片
                $goods_images_one = $request->file("goods_images_one");        //商品1
                $goods_images_two = $request->file("goods_images_two");        //商品2
                $goods_images_three = $request->file("goods_images_three");        //商品3
                if (!empty($goods_images_one)) {
                    foreach ($goods_images_one as $k=>$v) {
                        $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $goods_data['goods_images_one'] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                    }
                }
                if (!empty($goods_images_two)) {
                    foreach ($goods_images_two as $k=>$v) {
                        $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $goods_data['goods_images_two'] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                    }
                }
                if (!empty($goods_images_three)) {
                    foreach ($goods_images_three as $k=>$v) {
                        $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $goods_data['goods_images_three'] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                    }
                }
                $goods_data['start_date']=strtotime($goods_data['start_date']);
                $goods_data['end_date']=strtotime($goods_data['end_date']);
                // $goods_data['goods_sign']=json_encode($goods_data['goods_sign']);
                $re=db('goods')->where('id',$goods_data['id'])->update($goods_data);
                if($re !== false){
                    $this->success("编辑成功", url("admin/Goods/goods_index"));
                }else{
                    $this->error("编辑失败");
                }
            }else{
                //特殊规格图片
                $imgs_one = $request->file("imgs_one");
                $imgs_two = $request->file("imgs_two");
                $imgs_three = $request->file("imgs_three");
                //处理商品规格图片
                if (!empty($imgs_one)) {
                    foreach ($imgs_one as $k=>$v) {
                        $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $list1['images_one'][] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                    }
                }
                else{
                    $list1['images_one']=[];
                }
                //处理商品规格图片
                if (!empty($imgs_two)) {
                    foreach ($imgs_two as $k=>$v) {
                        $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $list2['images_two'][] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                    }
                }else{
                    $list2['images_two']=[];
                }
                //处理商品规格图片
                if (!empty($imgs_three)) {
                    foreach ($imgs_three as $k=>$v) {
                        $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                        $list2['imgs_three'][] = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
                    }
                }else{
                    $list2['imgs_three']=[];
                }
                $goods_special = [];                           //特殊商品基本信息
                $goods_special["goods_name"] = $goods_data["goods_name"];
                $goods_special["brand"] = $goods_data["brand"];
                $goods_special["start_date"] = strtotime($goods_data["start_date"]);
                $goods_special["end_date"] = strtotime($goods_data["end_date"]);
                $goods_special["goods_number"] = $goods_data["goods_number"];        //商品编号
                $goods_special["goods_standard"] = $goods_data["goods_standard"];    //商品规格
                $goods_special["goods_sign"] = $goods_data["goods_sign"];
                $goods_special["goods_share_describe"] = $goods_data["goods_share_describe"];
                $goods_special["goods_share_title"] = $goods_data["goods_share_title"];
                $goods_special["video_link"] = $goods_data["video_link"];             //视频链接
                $goods_special["goods_freight"] = $goods_data["goods_freight"];
                $goods_special["label"] = $goods_data['label'];                                //上下架
                $goods_special["goods_setting"] = $goods_data['goods_setting'];               //帮甩限制
                $goods_special["goods_detail"] = $goods_data["goods_detail"];
                $good_image=explode(',',$goods_data['goods_show_images']);
                $goods_special["goods_show_image"] = $good_image[0];
                //更新商品信息
                db('goods')->where('id',$goods_data['id'])->update($goods_special);
                if (!empty($goods_data)) {
                        $attr=[];
                        $i=0;
                        foreach ($goods_data as $kn => $nl) {
                            if (substr($kn, 0, 3) == "sss") {      //判断是否是规格记录
                                $attr[$i]['stock']=$nl['stock'];            //库存
                                $attr[$i]['coding']=$nl['coding'];          //规格
                                $attr[$i]['cost']=$nl['cost'];              //成本价
                                $attr[$i]['line']=$nl['line'];              //划线价
                                $attr[$i]['total']=$nl['total'];            //积分
                                $attr[$i]['jilt']=$nl['jilt'];              //帮甩费用
                                $attr[$i]['status']=$nl['status'];          //上下架
                                if(array_key_exists($i,$list1['images_one'])){
                                    $attr[$i]['image_one']=$list1['images_one'][$i];
                                }else{
                                    $attr[$i]['image_one']=$nl['image_one'];                //规格图片
                                }
                                if(array_key_exists($i,$list2['images_two'])){
                                    $attr[$i]['image_two']=$list2['images_two'][$i];
                                }else{
                                    $attr[$i]['image_two']=$nl['image_two'];                //规格图片
                                }
                                if(array_key_exists($i,$list2['imgs_three'])){
                                    $attr[$i]['image_three']=$list2['imgs_three'][$i];
                                }else{
                                    $attr[$i]['image_three']=$nl['image_three'];                //规格图片
                                }
                                $res=db('special')->where('id',$nl['id'])->update($attr[$i]);
                                $i++;
                            }
                        }
                    $this->success('编辑成功',url('admin/Goods/goods_index'));
                }else{
                    $this->error();
                }

            }
        }
    }
    /**
     * [商品列表组删除]
     * GY
     */
    public function del(Request $request)
    {
        $id = $request->only(["id"])["id"];
        $bool = db("goods")-> where("id", $id)->delete();
        $boole = db("special")->where("goods_id",$id)->delete();
        $res = db("commodity")->where("goods_id",$id)->find();

        if($res) {
            db("commodity")->where("goods_id", $id)->delete();
        }

        if ($bool || $boole) {
            $this->success("删除成功", url("admin/Goods/index"));
        } else {
            $this->success("删除失败", url('admin/Goods/add'));
        }
    }
    
    /**
     * [商品上下架状态]
     * lilu
     * label  0->下架   1->上架
     */
    public function goods_label_edit(Request $request)
    {
        if ($request->isPost()) {
            $status = $request->only(["status"])["status"];
            if ($status == 0) {
                $id = $request->only(["id"])["id"];
                $bool = db("goods")->where("id", $id)->update(["label" => 0]);
                if ($bool) {
                    $this->redirect(url("admin/Goods/goods_index"));
                } else {
                    $this->error("修改失败", url("admin/Goods/goods_index"));
                }
            }
            if ($status == 1) {
                $id = $request->only(["id"])["id"];
                $bool = db("goods")->where("id", $id)->update(["label" => 1]);
                if ($bool !== false) {
                    $this->redirect(url("admin/Goods/goods_index"));
                } else {
                    $this->error("修改失败", url("admin/Goods/goods_index"));
                }
            }
        }
    }




    /**
     * [商品列表组批量删除]
     * lilu
     * $parsm id   商品id
     */
    public function goods_del(Request $request,$id)
    {
        if ($request->isGet()) {
            $id = $request->only(["id"])["id"];    //商品id
            //获取商品信息
            $goods_info=db('goods')->where('id',$id)->find();
            if($goods_info['goods_standard']=='1'){    //特殊规格
                  //1.删除商品信息
                $re=db('goods')->delete($id);
                //2.删除规格信息
                $res=db('special')->where('goods_id',$id)->delete();
                if($re && $res){
                   $this->success('删除成功',url('admin/Goods/goods_index'));
                }else{
                   $this->error('删除失败');
                }
            }else{
                //1.删除商品信息
                $re=db('goods')->delete($id);
                if($re){
                    $this->success('删除成功',url('admin/Goods/goods_index'));
                }else{
                    $this->error('删除失败');
                }
            }
        }else{
            $id = $request->only(["id"])["id"];
            $num=count($id);
            $i=0;
            foreach ($id as $k=>$v){
                $goods_info=db('goods')->where('id',$v)->find();
                if($goods_info['goods_standard']=='1'){    //特殊规格
                    //1.删除商品信息
                    $re=db('goods')->delete($v);
                    //2.删除规格信息
                    $res=db('special')->where('goods_id',$v)->delete();
                    if($re && $res){
                        $i++;
                    }
                }else{
                    //1.删除商品信息
                    $re=db('goods')->delete($v);
                    if($re){
                        $i++;
                    }
                }
            }
            if($i==$num){
                return ajax_success('批量删除成功');
            }else{
                return ajax_error('批量删除失败');
            }
        }
    }


    /**
     * [商品列表规格图片删除]
     * 郭杨
     */
    public function photos(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->only(["id"])["id"];
            if (!empty($id)) {
                $photo = db("special")->where("id", $id)->update(["images" => null]);
            }
            if ($photo !==false) {
                return ajax_success('更新成功!');
            } else {
                return ajax_error('更新失败');
            }
        }
    }


    /**
     * [商品列表搜索]
     * lilu
     * goods_number  商品编号
     * goods_name    商品名称
     */
    public function goods_search()
    {
        $goods_number = input('goods_number');
        if ((!empty($goods_number))) {     //获取检索的商品
            $goods = db("goods")
                    ->where("goods_number",$goods_number)
                    ->whereOr('goods_name',$goods_number)
                    ->order("id asc")
                    ->select();
        }else {                             //获取不到则获取所有商品
            $goods = db("goods")->order("id desc")->select();
        }
        $all_idents = $goods;//这里是需要分页的数据
        $curPage = input('get.page') ? input('get.page') : 1;//接收前段分页传值
        $listRow = 10;//每页20行记录
        $showdata = array_slice($all_idents, ($curPage - 1) * $listRow, $listRow, true);// 数组中根据条件取出一段值，并返回
        $goods = Bootstrap::make($showdata, $listRow, $curPage, count($all_idents), false, [
            'var_page' => 'page',
            'path' => url('admin/Goods/index'),//这里根据需要修改url
            'query' => [],
            'fragment' => '',
        ]);
        $goods->appends($_GET);
        $this->assign('listpage', $goods->render());
        return view("goods_index", ["goods" => $goods]);
    }



    /**
     * [普通商品多规格列表单位编辑]
     * 郭杨
     */
    public function offer(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->only(["id"])["id"];
            $standard = db("goods")->where("id",$id)->value("goods_standard");
            if($standard == 1){
                $goods_standard = db("special")->where("goods_id", $id)->select();
                $offer = db("special")->where("goods_id", $id)->field("coding,id")->select();

                foreach($offer as $pp => $qq){
                    $offers[$pp] = $qq["coding"];
                    $specail_id[$pp] = $qq["id"];
                }

                foreach ($goods_standard as $k => $v) {
                    $goods_standard[$k]["title"] = explode('_', $v["name"]);
                    $res = explode(',', $v["lv1"]);      
                    $unit["unit"][] = explode(',', $v["unit"]);        
                    $num["num"][] = explode(',', $v["num"]);        
                }

                foreach($offers as $kk => $zz){
                    $rest1["unit"][$kk] = $unit["unit"][$kk];
                    $rest2["num"][$kk] = $num["num"][$kk];
                    $unit1[$kk]["unit"] =  $rest1["unit"][$kk];
                    $unit1[$kk]["num"] =  $rest2["num"][$kk];
                    $unit1[$kk]["number"] =  $offers[$kk];
                    $unit1[$kk]["id"] =  $specail_id[$kk];
                    
                             
                }
                
                if(!empty($unit1)){
                    return ajax_success('传输成功', $unit1);
                } else {
                    return ajax_error("数据为空");
                }

            } else {
                return ajax_error("该商品为统一规格商品");
            }
        }
    }
     
    /**
     * lilu
     * 商品策略配置
     */
    public function  goods_strategy(Request $request)
    {
              //获取参数信息
                $id=input();
                $goods_info=db('goods')->where('id',$id['id'])->field('free_tactics,bao_tactics,point_tactics,big_tactics,new_tactics,old_tactics')->find();
                if($goods_info['free_tactics']){
                    $data['free_tactics']=json_decode($goods_info['free_tactics'],true);
                }else{
                    $data['free_tactics']['own']=[];
                    $data['free_tactics']['other']=[];
                    $data['free_tactics']['free']='1';
                }
                if($goods_info['bao_tactics']){
                    $data['bao_tactics']=json_decode($goods_info['bao_tactics'],true);
                }else{
                    $data['bao_tactics']=[];
                }
                if($goods_info['point_tactics']){
                    $data['point_tactics']=json_decode($goods_info['point_tactics'],true);
                }else{
                    $data['point_tactics']=[];
                }
                if($goods_info['big_tactics']){
                    $data['big_tactics']=json_decode($goods_info['big_tactics'],true);
                }else{
                    $data['big_tactics']=[];
                }
                if($goods_info['new_tactics']){
                    $data['new_tactics']=json_decode($goods_info['new_tactics'],true);
                }else{
                    $data['new_tactics']=[];
                }
                if($goods_info['old_tactics']){
                    $data['old_tactics']=json_decode($goods_info['old_tactics'],true);
                }else{
                    $data['old_tactics']=[];
                }
                return view('goods_strategy',['id'=>$id['id'],'data'=>$data ]);
        
    }
    
    /**
     * lilu
     * 商品策略配置处理
     */
    public function  goods_strategy_do()
    {
        //获取后台参数
        $input=input();
        if($input['free']){
            $data['free_tactics']=json_encode($input['free']);
        }
        if($input['bao']){
            $data['bao_tactics']=json_encode($input['bao']);
        }
        if($input['zpoint']){
            $data['point_tactics']=json_encode($input['zpoint']);
        }
        if($input['big']){
            $data['big_tactics']=json_encode($input['big']);
        }
        if($input['new']){
            $data['new_tactics']=json_encode($input['new']);
        }
        if($input['old']){
            $data['old_tactics']=json_encode($input['old']);
        }
        if($data){
            $re=db('goods')->where('id',$input['id'])->update($data);
            if($re !== false){
                $this->success('保存成功',url('admin/Goods/goods_index'));
            }else{
                $this->error('保存失败');
            }

        }
        
           
    }

    
   
}