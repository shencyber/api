<?php

namespace app\index\controller;
Use think\Controller;
Use \think\Request;
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
     * 供货商管后台登录【已完成】
     * @param  [type] $phone    [手机]
     * @param  [type] $password [密码]
     * @return [type]           [description]
     */
    public function login(  )
    {
       
        $ghs  = new GhsMod();
        // header('Access-Control-Allow-Origin:*');  
        // // 响应类型  
        // header('Access-Control-Allow-Methods:*');  
        // // 响应头设置  
        // header('Access-Control-Allow-Headers:x-requested-with,content-type');

        // header("Content-type: text/html; charset=utf-8"); 
        $req = Request::instance()->param();
        return $ghs->login( $req['phone'] , $req['password'] );

    }

    /**
     * 获取供货商的姓名、手机号、供货商编号
     * @param  [type] $ghsid [description]
     * @return [type]        [description]
     */
    public function getghsinfo(  )
    {
        $ghs  = new GhsMod();
        $req = Request::instance()->param();
        return $ghs->getGhsInfo( $req['ghsid'] );
      
    }


    /**
     * 添加供货商的又拍信息，userid openid  token
     * @param  [int] $ghsid    [供货商id]
     * @param  [string] $userId   [userid]
     * @param  [string] $openid   [openid]
     * @param  [string] $token    [token 会过期]
     * @return [type]           [description]
     */
    // public function updateYouPai( $ghsid , $userId , $openid , $token  )
    public function updateYouPai( )
    {
        $ghs  = new GhsMod();
        return $ghs->updateYouPai( 1 , "10006" , "dsyugfishdfss34655434" , "gnkdgr" );
    }


    /**
     * 更新供货商token
     * @param  [type] $ghsid    [供货商id]
     * @param  [type] $newtoken [新的token]
     * @return [type]           [description]
     */
    // public function updateYPToken( $ghsid , $newtoken )
    public function updateYPToken(  )
    {
        $ghs  = new GhsMod();
        $res = $ghs->updateYPToken( 1 , "56767878" );;
        return $res ;

    }

}

?>
