<?php

namespace app\index\controller;
Use think\Controller;
Use think\Request;
Use think\Db;
Use app\index\model\DlsMod;
Use app\index\model\GuanZhuMod;


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
        $res = $modelObj->getDlsInfo( 1 );
        if( $res )
        {
            $obj =Array(
                'status' => 0,
                'desc'   => '代理商存在',
                'result' => $res 
            );
        }
        else
        {
            $obj =Array(
                'status' => -1,
                'desc'   => '未找到代理商',
                'result' => ''
            );   
        }
        return json_encode($obj , JSON_UNESCAPED_UNICODE);die;
      
    }

     /**
     * [exists 查询代理商是否存在]
     * @param  [type] $dlsid [代理商id]
     * @return [type]        [description]
     */
    public function exists( $dlsid )
    {
        $dls  = new DlsMod();
        $res = $dls->getDlsInfo( $dlsid );
        return !empty($res);

        // if( empty($res) ) return "false";
        // return "true" ;
        // dump(!!$res);
        // if( $res )
        // {111
        //     $obj =Array(
        //         'status' => 0,
        //         'desc'   => '代理商存在',
        //         'result' => '' 
        //     );
        // }
        // else
        // {
        //     $obj =Array(
        //         'status' => -1,
        //         'desc'   => '未找到代理商',
        //         'result' => ''
        //     );   
        // }
        // return json_encode($obj , JSON_UNESCAPED_UNICODE);die;
        
    }

    /**
     * [getGhsList 根据代理商id获取供货商列表]
     * @param  [type] $dlsid [description]
     * @return [type]        [description]
     */
    public function getGhsList( $dlsid )
    {
        $req = Request::instance()->param();

        //查询关注表
        $modelObj  = new GuanZhuMod();
        $res = $modelObj->getListByDlsId( $req['dlsid'] );
        // dump( $res );
        if( empty( $res ) )
        {
            $obj = Array(
                'status' => 0 ,
                'desc'   => '没有添加过供货商',
                'result' => []
            );
            return json_encode( $obj , JSON_UNESCAPED_UNICODE );
        }

        $ghsids = [] ;
        foreach( $res as $key=>$val )
        {
            array_push( $ghsids , $val['ghsid'] );
        }
        // dump( $ghsids );die;
        // 查询相关供货商信息
        $ghs = Db::table('gonghuoshang')->where('id', "in" , $ghsids )->field('id,name')->select();

        $obj = Array(
            'status' => 0,
            'desc'   => '供货商列表',
            'result' => $ghs
        );

        return json_encode( $obj , JSON_UNESCAPED_UNICODE );die;

    }

}

?>
