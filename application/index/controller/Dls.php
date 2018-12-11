<?php

namespace app\index\controller;
Use think\Controller;
Use app\index\model\DlsMod;


class Dls extends Controller
{
   
   
    /**
     * 代理商注册
     * @param [string] $[weixinopenid] [<微信openid>]
     * @param [string] $[phone] [<手机号>]
     * @param [string] $[password] [<密码>]
     * @param [string] $[nickname] [<昵称>]
     * @param [string] $[avatar] [<头像地址>]
     * @return [type] [description]
     */
    // public function register( $weixinopenid , $phone , $password ,$nickname , $avatar  )
    public function register(   )
    {
        $modelObj  = new DlsMod();
        $res = $modelObj->register( 'djfkjkkjdgdXXX'  ,'888888888888' ,'12345678' ,'psdt' ,'hfgkhdgk' );
        return $res ;

    }

    /**
     * 代理商登录
     * @param  [type] $phone    [手机]
     * @param  [type] $password [密码]
     * @return [type]           [description]
     */
    // public function login(  $phone ,  $password )
    public function login(  )
    {
        $modelObj  = new DlsMod();
        // $newpwd = md5(md5( $password ));
        return $modelObj->login( '88888888888' ,  '12345678'   );
    }

    /**
     * 获取代理商的手机号、昵称、头型
     * @param  [type] $dlsid [description]
     * @return [type]        [description]
     */
    public function getdlsinfo(  )
    {
        $modelObj  = new DlsMod();
        return $modelObj->getDlsInfo( 1 );
      
    }

}

?>
