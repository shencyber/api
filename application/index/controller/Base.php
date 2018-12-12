<?php

namespace app\index\controller;
Use think\Controller;
Use \think\Request;

class Base extends Controller
{
	/**
     * 初始化方法,可以控制用户权限、获取菜单等等，只要是继承base类的其它业务类就不需要再重写
     */
    protected function _initialize()
    {
        parent::_initialize();

        if (Request::instance()->isOptions()) 
        {
                header('Access-Control-Allow-Origin:*');
                header('Access-Control-Allow-Headers:Accept,Referer,Host,Keep-Alive,User-Agent,X-Requested-With,Cache-Control,Content-Type,Cookie,token');
                header('Access-Control-Allow-Credentials:true');
                header('Access-Control-Allow-Methods:GET,POST,OPTIONS');
                header('Access-Control-Max-Age:1728000');
                header('Content-Type:text/plain charset=UTF-8');
                header('Content-Length: 0', true);
                header('status: 204');
                header('HTTP/1.0 204 No Content');
        }
        else
        {
                header('Access-Control-Allow-Origin:*');
                header('Access-Control-Allow-Headers:Accept,Referer,Host,Keep-Alive,User-Agent,X-Requested-With,Cache-Control,Content-Type,Cookie,token');
                header('Access-Control-Allow-Credentials:true');
                header('Access-Control-Allow-Methods:GET,POST,OPTIONS');
        }


        // //判断登陆状态：
        // if (Session::has('user')) {
 
        //     if (!defined('US')) define('US', Session::get('user.user_name'));
 
        //     //判断帐户状态，如帐号被禁用/删除，即时生效：
        //     $user= Db::name('user')->where(['username' => US])->value('status');
        //     if ($user== 2 || $user== -1) $this->error('没有权限');
 
        //     //获取菜单：
        //     $this->menu(US);
 
 
        // } else {
 
        //     if (!defined('US')) define('US', 0);
        //     $this->redirect('user/login');
        // }
 
    }

}


?>