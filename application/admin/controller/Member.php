<?php
namespace app\admin\controller;
 

 use  think\Controller;
 use  think\Request;


 /**
  * 会员管理
  */
 class Member extends Controller
 {
 	
	/**
	 * lilu
	 * 会员列表
	 * 
	 */
	
 	public function member_list()
 	{
		//  //获取会员列表
		//  $where['is_use']='1';       //会员运状态开启
		 $list=db('member')->select();
		 $list=paging_data($list,'admin/Member/member_list','10');   //分页函数处理
		 $list->appends($_GET);
		 $this->assign('listpage', $list->render());
 		 return view('member_list',['data'=>$list]);
 	}
	 /**
	  * lilu
	   * 会员检索
	  */
	  public function member_search()
	  {
		  //获取检索条件
		  $input=input();
		  $where=[];
		  if($input['account'])
		{
		   $where['account']=$input['account'];

		}
		if($input['help_account'])
		{
			$name=db('member')->where('account',$input['help_account'])->value('id');
			if($name)
			{
				$where['pid']=$name;
			}
		}
		if($input['start_time'] && $input['end_time'])
		{
            $where['join_time']=array('between',array(strtotime($input['start_time']),strtotime($input['end_time'])));
		}elseif($input['start_time'] && !$input['end_time']){
            $where['join_time']=array('between',array(strtotime($input['start_time']),time()));
            
		}else{

		}
		//根据检索条件获取会员列表
		$list=db('member')->where($where)->select();
		$list=paging_data($list,'admin/Member/member_list','10');    //分页函数处理
		$list->appends($_GET);
		$this->assign('listpage', $list->render());
		return view('member_list',['data'=>$list]);

	  }
	  /**
	   * lilu
	   * 会员编辑
	   * parsm  id   会员id
	   */
	  public function member_list_edit(Request $request)
	  {
		  $id=$request->only(['id'])['id'];   //获取memberid
		  //获取会员基本信息
		  $info=db('member')->where('id',$id)->find();
		  $upuser=db('member')->where('id',$info['pid'])->value('account');
		  $info['help_account']=$upuser;
		  return view('member_list_edit',['data'=>$info]);
	  }
	  /**
	   * lilu
	   * notes: 编辑处理
	   */
	  public function member_list_edit_do()
	  {
		  //获取编辑的信息
		  $input=input();
		  //根据账号信息获取pid
		  $pid=db('member')->where('account',$input['pid'])->value('id');
		  $input['pid']=$pid;
		  //更新信息
		  $res=db('member')->where('id',$input['id'])->update($input);
		  if($res !== false)
		  {
           $this->success('编辑成功',url('admin/Member/member_list'));
		  }else{
			  $this->error('编辑失败');
		  }
	  }

	  /**
	   * lilu
	   * 会员删除
	   */
	  public function member_del(Request $request)
	  {
		  //获取会员id
		  $id=$request->only(['id'])['id'];
		  $re=db('member')->delete($id);
		  if($re){
             $this->success('删除成功',url('admin/Member/member_list'));
		  }else{
			  $this->error('删除失败');
		  }
	  }
	  
	  /**
	   * lilu
	   * 会员批量删除
	   */
	  public function member_dels(Request $request)
	  {
		  //获取会员id
		  $id=$request->only(['id'])['id'];
            $num=count($id);
            $i=0;
            foreach ($id as $k=>$v){
                    //1.删除会员信息
                    $re=db('member')->delete($v);
                    if($re){
                        $i++;
                    }
            }
            if($i==$num){
                return ajax_success('批量删除成功');
            }else{
                return ajax_error('批量删除失败');
            }
		  
	  }
	



 }