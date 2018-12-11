<?php

namespace app\index\controller;
Use think\Controller;
Use app\index\model\GhsMod;


class Ghs extends Controller
{
    public function index()
    {


        return 'ok'; 
    }

    /**
     * 供货商注册
     * @param  [type] $name     [姓名]
     * @param  [type] $phone    [手机]
     * @param  [type] $password [密码]
     * @return [type]           [description]
     */
    // public function  register( $name , $phone , $password )
    public function  register( )
    {
        $ghs  = new GhsMod();
        $res = $ghs->register( '李四' ,'2369885' ,'12345678' );
        return $res ;

    }

    /**
     * 供货商管后台登录
     * @param  [type] $phone    [手机]
     * @param  [type] $password [密码]
     * @return [type]           [description]
     */
    public function login(  )
    {
        $ghs  = new GhsMod();
        // $newpwd = md5(md5( $password ));
        return $ghs->login( '88888888888' , '12345678' );
    	// return $ghs->login( $phone , $newpwd );
    }

    /**
     * 获取供货商的姓名、手机号、供货商编号
     * @param  [type] $ghsid [description]
     * @return [type]        [description]
     */
    public function getghsinfo(  )
    {
        $ghs  = new GhsMod();
        return $ghs->getGhsInfo( 2 );
      
    }

}

?>
