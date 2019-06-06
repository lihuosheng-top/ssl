<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return '123';
    }
    /**
     * lilu
     * 甩甩乐分享
     */
    public function ssl_share(){
        
        return view('ssl_share');
    }
    /**
     * lilu
     * 甩甩乐分享2
     */
    public function ssl(){
        
        return view('ssl');
    }
    /**
     * lilu
     * 甩甩乐分享3
     */
    public function abc(){
        
        return view('abc');
    }
}
