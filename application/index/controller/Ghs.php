<?php

namespace app\index\controller;
Use think\Controller;
Use \think\Request;
Use think\Config;
Use think\Db;
Use app\index\model\GhsMod;

class Ghs extends Base
{
    public function index()
    {
        print_r( "index" );
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
       
       
       
        $req = Request::instance()->param();
        // $res = $ghs->login( $req['phone'] , $req['password'] );
        // dump( $res );die;
        // return json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        // return json_encode($res);
        // return $res ;


        $res = Db::table( "gonghuoshang" )->where(['phone'=>$req['phone']])->field('id,password')->select();
        print_r( "dnvd" );
        dump( $res );

        if( empty( $res ) ) 
        {
            return json_encode( Array( 'status'=>1 , 'desc'=>'账号或密码错误' ,'result'=>null) , JSON_UNESCAPED_UNICODE );
            die;
        }
        if( $res[0]['password'] != md5(md5( $req['password'] )) )
        {
            return json_encode( Array( 'status'=>1 , 'desc'=>'账号或密码错误' ,'result'=>null) , JSON_UNESCAPED_UNICODE );
            die;
        }

       
            //获取供货商的其他信息
            $ghs  = new GhsMod();
            $res = $ghs->getGhsInfo($res['id']);
            print_r("gonghuoshanginfo");
            dump($res);

            // $res_arr = json_decode($res,true);
            if( 0!=$res_arr['status'] )
            {
                $obj = array(
                    'result'=>null,
                    "status" => -1,
                    "desc"=>"获取供货商信息失败"   //插入数据错误
                );       
            }
            else
            {
                $obj['result']['name'] =$res[0]['name']; 
                $obj['result']['phone'] =$res[0]['phone']; 
                $obj['result']['gno'] =$res[0]['gno']; 
            }
        // }
        
        
       return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; 
       // 
       // 
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
        $res = $ghs->getGhsInfo( $req['ghsid'] );
        return $res ;
        if( $res )
        {
            $obj =Array(
                'status' => 0,
                'desc'   => '获取供货商信息',
                'result' => $res 

            );
        }
        else
        {
            $obj =Array(
                'status' => -1,
                'desc'   => '未找到供货商',
                'result' => ''

            );   
        }
        return json_encode($obj ,JSON_UNESCAPED_UNICODE);die;
    }

    /**
     * [getGhsByNo 根据供货商编号查找供货商]
     * @return [type] [description]
     */
    public function searchGhsByNo()
    {
        $req = Request::instance()->param();
        $res = Db::table('gonghuoshang')->where(['gno'=>$req['ghsno']])->field('id,name,gno')->select();
        if( empty($res) )
        {
            $obj = Array(
                'status' => 1,
                'desc'   => '没有该供货商' ,
                'result' => null
            );
        }
        else
        {
            $obj = Array(
                'status' => 0,
                'desc'   => '供货商信息' ,
                'result' => $res
            );
        }

        return json_encode($obj , JSON_UNESCAPED_UNICODE);die;
    }

    /**
     * [exists 查询供货商是否存在]
     * @param  [type] $ghsid [供货商id]
     * @return [type]        [description]
     */
    public function exists( $ghsid )
    {
        $ghs  = new GhsMod();
        $res = $ghs->getGhsInfo( $ghsid );
        return !empty($res);
        // if( $res )
        // {
        //     $obj =Array(
        //         'status' => 0,
        //         'desc'   => '供货商存在',
        //         'result' => '' 
        //     );
        // }
        // else
        // {
        //     $obj =Array(
        //         'status' => -1,
        //         'desc'   => '未找到供货商',
        //         'result' => ''
        //     );   
        // }
        // return json_encode($obj , JSON_UNESCAPED_UNICODE);die;
        
    }



     /**
     * [授权 又拍授权]
     * @param  [type] $token   [description]
     * @param  [type] $opendId [description]
     * @return [type]          [description]
     */
    public function auhorize( )
    {

        $req = Request::instance()->param();
        $ghs  = new GhsMod();
        $res = $ghs->auhorize( $req['token'] , $req['openid'] , Config::get('YPAppKey') ,$req['userid'] );
        if(  $res )
        {
            $obj = Array(
                'status' => 0 ,
                'desc'   => '生成成功' ,
                'result' => null
            );
        }
        else
        {
            $obj = Array(
                'status' => 1 ,
                'desc'   => '生成失败' ,
                'result' => null
            );   
        }

        return  json_encode( $obj , JSON_UNESCAPED_UNICODE );
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

    /**
     * 判断用户是否有又拍的userid并且token是否过期
     * @param  [int]  $ghsid [description]
     * @return boolean        [description]
     */
    public function isExpireTokenYP(  )
    {
        $req = Request::instance()->param();
        $ghs  = new GhsMod();
        $res = $ghs->isExpireTokenYP( $req['ghsid'] );
        if( $res )
        {
            $obj = Array(
                'status'=>0,
                'desc'=>"token正常",
                'result'=>$res
            );

        }
        else
        {
            $obj = Array(
                'status'=>0,
                'desc'=>"账号不存在、或token已过期",
                'result'=>$res
            );            
        }
        return json_encode($obj , JSON_UNESCAPED_UNICODE) ;
    }


    /**
    *根据供货商用户id获取对应的相册
    *@parma [int] $ghsid       [供货商id]
    *@parma [int] $currentpage [当前页数]
    *@return  
    */
    public function getAlbumsByGhsId( $ghsid , $currentpage )
    {
        $req = Request::instance()->param();
        $ghs  = new GhsMod();
        //1、根据用户id获取供货商的又拍userid
        $res = $ghs->getGhsInfo( $ghsid );
        // dump(  $res);
        $res_arr = json_decode( $res , true );
        if( 0 != $res_arr['status'] ) 
        {
            return $res;
            die;
        }
        $res =$ghs->getAlbumsByGhsId( $res_arr['result']['youpaiuserid'] , $req['currentpage'] );
        if( $res )
        {
            $obj =Array(
                'status' => 0 ,
                'desc'   => "查询相册成功",
                'result' => $res
            );
        }
        else
        {
            $obj =Array(
                'status' => 1 ,
                'desc'   => "查询相册成功",
                'result' => null
            );   
        }

        return json_encode( $obj  , JSON_UNESCAPED_UNICODE );
    }

    
}

?>
