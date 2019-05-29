<?php
namespace app\index\controller;

use think\Controller;
use app\index\controller\Base;
use app\index\Model\Game as Game2;
use think\Session;
use think\Request;
use think\Db;
use app\index\controller\Wxpay2 as pay;

/**
 * lilu
 * 游戏模块
 */
class Game extends Base
{

    /**
     * lilu
     * 根据概率获取游戏种类及游戏接口
     * token
     */
    public function game()
    {
          //随机取出1条数据
          $sql='SELECT * FROM tb_problem_house WHERE id >= ((SELECT MAX(id) FROM tb_problem_house)-(SELECT MIN(id) FROM tb_problem_house)) * RAND() + (SELECT MIN(id) FROM tb_problem_house) LIMIT 1';
          $list=DB::query($sql);
          foreach($list as $k =>$v){
            $answer=json_decode($v['answer']);
            $list[$k]['answer']=$answer;
            $problem_type=json_decode($v['problem_type'],true);
            $arr='';
            $num=count($problem_type);
            $i=0;
            
            foreach($problem_type as $k2=>$v2){
                if($k2=='twdl'){
                    $problem_type[$k2]='天文地理';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='rwls'){
                    $problem_type[$k2]='人物历史';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='shbk'){
                    $problem_type[$k2]='生活百科';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='mxbg'){
                    $problem_type[$k2]='明星八卦';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='wlhx'){
                    $problem_type[$k2]='物理化学';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='keji'){
                    $problem_type[$k2]='科技';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='politics'){
                    $problem_type[$k2]='政治';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='history'){
                    $problem_type[$k2]='文学';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='music'){
                    $problem_type[$k2]='音乐';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                if($k2=='sport'){
                    $problem_type[$k2]='体育';
                    if($i==$num-1){
                        $arr .=$problem_type[$k2];
                    }else{
                        $arr .=$problem_type[$k2].',';
                    }
                }
                $i++;
            }
            $list[$k]['problem_type']=$arr;
            if($v['problem_status']=='1'){
                $list[$k]['problem_status']="简单";
            }
            if($v['problem_status']=='2'){
                $list[$k]['problem_status']="中难";
            }
            if($v['problem_status']=='3'){
                $list[$k]['problem_status']="较难";
            }
            if($v['problem_status']=='4'){
                $list[$k]['problem_status']="很难";
            }
        }
        //获取参数 member_id  goods_id
        $input=input();
        $member=db('member')->where('token',$this->token)->find();
        $data['member_id']=$member['id'];
        $data['goods_id']=$input['goods_id'];
        $re=db('answer_record')->where($data)->setField('answer_id',$list[0]['id']);
        $list2=$list[0];
        unset($list2['true_ans']);
        if($list2){
              return  ajax_success('获取成功',$list2);
        }else{
             return   ajax_error('获取失败');
        }
    }
    /**
     * lilu
     * 判断用户是否答题
     * token
     * goods_id
     * order_number
     */
    public function is_answer()
    {
        //获取参数
        $input=input();
        $member=db('member')->where('token',$this->token)->find();
        //没有答题判断
        $data['member_id']=$member['id'];
        $data['goods_id']=$input['goods_id'];
        $data['status']='2';
        $re=db('answer_record')->where($data)->find();
        //答错题
        $data2['member_id']=$member['id'];
        $data2['goods_id']=$input['goods_id'];
        $data2['status']='0';
        $re2=db('answer_record')->where($data2)->find();
        if($re){
            $map['answer_status']='2';
            $map['lock_time']='';
            return ajax_success('用户没有答题',$map);
        }
        if($re2){
             //根据配置获取锁定时间
            $key="lock_time";
            $info=db('sys_setting')->where('key',$key)->find();
            $info['value']=json_decode($info['value'],true);
            if($re2['help_id']=='0'){
                $lock_time=$re2['lock_time'];
            }else{
                $lock_time=$re2['lock_time'];
            }
            $map['lock_time']=$lock_time;
            $map['answer_status']='0';
            return ajax_success('用户答题错误',$map);
        }
        $map['answer_status']='1';
        $map['lock_time']='';
        return ajax_success('答题成功',1);
        
    }
    /**
     * lilu
     * 判断答案是否正确
     * 问题id
     * 答案
     * token
     * goods_id
     * order_number
     * help_id
     */
    public function is_right()
    {
        //获取参数
        $input=input();
        $info=db('problem_house')->where('id',$input['answer_id'])->find();
        $member=db('member')->where('token',$this->token)->find();
        $order_number = $input['order_number'];
        //插入答题列表
        if($info['true_ans']==$input['true_ans'])
        {
            //答题正确,修改客户答题记录
            $map['status']='1';
            $re=db('answer_record')->where('order_number',$order_number)->update($map);
            if($re){
                return ajax_success('答题正确');
            }
            // //根据概率，判断小游戏的种类
            // $youxi =new Game2();
            // $game=$youxi->get_games_chance();
            // $data=$game;

        }else{
            $map2['status']='0';
            $re=db('answer_record')->where('order_number',$order_number)->update($map2);
            $res=db('answer_record')->where('order_number',$order_number)->find();
            //根据配置获取锁定时间
            $key="lock_time";
            $info=db('sys_setting')->where('key',$key)->find();
            $info['value']=json_decode($info['value'],true);
            if($res['help_id']==0)
            {
                 $lock_time=time()+$info['value']['lock_time']['own']*60;
                 $lock['lock_time']=$lock_time;
                 db('answer_record')->where('order_number',$order_number)->update($lock);
            }else{
                 $lock_time=time()+$info['value']['lock_time']['other']*60;
                 $lock['lock_time']=$lock_time;
                 db('answer_record')->where('order_number',$order_number)->update($lock);
            }
            return ajax_error('答题失败',$lock_time);
        }

    }
    /**
     * lilu
     * 获取红包的金额和免甩单的金额
     * token
     * goods_id
     * order_number
     * openid
     */
    public function get_money()
    {
        $input=input();
        //获取用户信息
        $member=db('member')->where('token',$this->token)->find();
        $data['order_number']=$input['order_number'];
      //根据商品设置，获取甩免单和红包的金额以及概率
      $goods_info=db('goods')->where('id',$input['goods_id'])->find();
      if($goods_info['free_tactics'])      //商品免单策略是否配置
      {
          $value=json_decode($goods_info['free_tactics'],true);
          $free_percent_own=$value['own'][0]['percent']/100;
          $free_percent_other=$value['other'][0]['percent']/100;
      }else{
          $map['status']='0';
      }
       //获取订单金额
       $res=db('order')->where('order_number',$input['order_number'])->find();
       $re=db('help_record')->where($data)->find();
        if($re['help_id']!='0')     
        {    //帮甩
            $map['free_money']=$res['order_amount']*$free_percent_other;
        }else{    //自己甩
            $map['free_money']=$res['order_amount']*$free_percent_own;
        }
        //免单金额返还客户（$map['free_money]）
        $openid=$input['openid'];
        $money=$map['free_money'];
        db('member')->where('id',$member['id'])->setField('openid',$openid);
        $pay=new pay();
        $data=$pay->order_refunds($data['order_number'],$money,$res['order_amount']);
        if($data["return_code"] == "SUCCESS"  ){
            //红包记录
            $info=db('order')->where('order_number',$input['order_number'])->find();
            $where['member_id']=$member['id'];
            $where['help_id']='0';   //帮甩用户id
            $where['goods_id']=$info['goods_id'];
            $where['order_number']= $input['order_number'];
            $where['income']=$map['free_money'];
            $where['pay']='0';
            $where['pay_type']='2';   //weixin   
            $where['order_type']='3';   //奖励红包
            $where['order_status']='0';   //自己甩
            $re=db('captical_record')->insert($where);
        }
        if($map)
        {
          return ajax_success('获取成功',$map);
        }else{
          return ajax_error('获取失败');
        }

    }
  /**
   * lilu
   * 点击红包，返还红包
   * token
   * 
   * order_number
   */
  public function back_bao_money()
  {
      //获取参数
      $input=input();
      if($input){
          $data['order_number']=$input['order_number'];
          $re=db('help_record')->where($data)->find();
          $res=db('order')->where('order_number',$input['order_number'])->find();
          //根据商品设置，获取甩免单和红包的金额以及概率
        $goods_info=db('goods')->where('id',$res['goods_id'])->find();
        //获取红包的概率以及金额
        if($goods_info['bao_tactics'])
        {
            $value2=json_decode($goods_info['bao_tactics'],true);
            ////////自己甩
            $zong=0;
            foreach($value2['own'] as $k=>$v)
            {
               $zong +=$v['probability'];
            }
            //按照probability排序
            $last_names = array_column($value2['own'],'probability');
            array_multisort($last_names,SORT_ASC,$value2['own']);
            $num=mt_rand(1,$zong);   //随机数
            $pro=0;
            foreach($value2['own'] as $k2=>$v2)
            {
                $pro +=$v2['probability'];
                if($num<=$pro){
                    $map1['free_bao_own']=$res['order_amount']*$v2['percent']/100;
                    break;
                }
            }
            //帮甩红包---other
            $zong2=0;
            $pro2=0;
            foreach($value2['other'] as $k3=>$v3)
            {
               $zong2 +=$v3['probability'];
            }
            //按照probability排序
            $last_names2 = array_column($value2['other'],'probability');
            array_multisort($last_names2,SORT_ASC,$value2['other']);
            $num2=mt_rand(1,$zong2);   //随机数
            foreach($value2['other'] as $k4=>$v4)
            {
                $pro2 +=$v4['probability'];
                if($num2<=$pro2){
                    $map1['free_bao_other']=$res['order_amount']*$v4['percent']/100;
                    break;
                }
            }
            if($re['help_id']!='0')     
            {    //帮甩
                $map['free_bao']=$map1['free_bao_other'];
            }else{    //自己甩
                $map['free_bao']=$map1['free_bao_own'];
            }
         //获取用户信息
        $member=db('member')->where('token',$this->token)->find();
        $pay=new pay();
        $money=$map['free_bao'];
        $data=$pay->order_refunds($input['order_number'],$money,$res['order_amount']);
            //记录
            // $xml_data = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
            // $val = json_decode(json_encode($xml_data), true);
         if($data["return_code"] == "SUCCESS" ){  //成功
                //红包记录
                $info=db('order')->where('order_number',$input['order_number'])->find();
                $where['member_id']=$member['id'];
                $where['help_id']='0';   //帮甩用户id
                $where['goods_id']=$info['goods_id'];
                $where['order_number']= $input['order_number'];
                $where['income']=$map['free_bao'];
                $where['pay']='0';
                $where['pay_type']='2';   //weixin   
                $where['order_type']='3';   //奖励红包
                $where['order_status']='0';   //自己甩
                $re=db('captical_record')->insert($where);
            }
          return ajax_success('获取成功',$map);
      }else{
         return ajax_ERROR('参数错误');
      }
  }

 }
}