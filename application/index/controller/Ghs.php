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
     * @param  [type] $phone    [手机]
     * @param  [type] $password [密码]
     * @return [type]           [description]
     */
    // public function  register( $phone , $password )
    public function  register( )
    {
       
        $req = Request::instance();
        $param = $req->param();
        $phone = $param['phone'];
        $password = $param['password'];

        // 1、判断手机号是否已经被注册
        $account = Db::table('gonghuoshang')->where('phone' , $phone )->field('id,password,token,token_end_time')->select();
        if( !empty($account) )
        {
            return json_encode(Array( 'status'=>1 , 'desc'=>'该手机号已被注册' , 'result'=>'' ) , JSON_UNESCAPED_UNICODE) ;
            
        }
        else
        {
            //token过期时间  30天
            $token_end_time = mktime( date("H") ,date("i"), date("s") , date("m") ,date("d")+30 ,date("Y") );
            $data = [
                'phone'    => $phone ,
                'password' => md5(md5($password)) ,
                'regtime'  => date("Y-m-d H:i:s") ,
                'status'   => 0 ,
                'token'    => $this->createToken($phone,$password),
                'token_end_time' =>  date("Y-m-d H:i:s" , $token_end_time)
            ] ;
            $newAccount = Db::table('gonghuoshang')->insert( $data );
            
            return json_encode(Array('id'=>Db::table('gonghuoshang')->getLastInsID(),'token'=>$data['token']) , JSON_UNESCAPED_UNICODE);
        }

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
        // print_r( "dnvd" );
        // dump( $res );

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
            $resInfo = $ghs->getGhsInfo($res[0]['id']);
            // print_r("gonghuoshanginfo");
            // dump($resInfo);

            // $res_arr = json_decode($res,true);
            // if( 0!=$resInfo[0]['status'] )
            // {
            //     $obj = array(
            //         'result'=>null,
            //         "status" => -1,
            //         "desc"=>"获取供货商信息失败"   //插入数据错误
            //     );       
            // }
            // else
            // {
                $obj = array( 'status'=>0 , 'desc'=>"登录成功" , 'result' => array() );
                $obj['result']['userid'] =$res[0]['id']; 
                $obj['result']['name'] =$resInfo[0]['name']; 
                $obj['result']['phone'] =$resInfo[0]['phone']; 
                $obj['result']['gno'] =$resInfo[0]['gno']; 
                $obj['result']['token'] =$this->createToken( $req['phone'] , $req['password'] );
                $this->updateToken( $res[0]['id'] , $obj['result']['token'] );
            // }
        // }
        
        // dump( $obj );
       return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; 
       // 
       // 
    }

    /**
     * [createToken 生成token]
     * @param  [type] $phone    [手机号]
     * @param  [type] $password [密码]
     * @return [type]           [token]
     */
    private function createToken( $phone , $password )
    {
        $header=Array("alg"=>"HS256","type"=>"JWT");
        $payload = Array("phone"=>$phone , "password"=>$password , "time"=>time());

        $base64_h = base64_encode( json_encode($header) ) ;
        $base64_p = base64_encode( json_encode($payload) ) ;
        return  md5(md5(md5( $base64_h.$base64_p ))) ;
    }

    /**
     * [hasExpiredToken 判断用户的token是否已经过期]
     * @param  [type]  $uid [用户id]
     * @return boolean      [true-过期 false-未过期]
     */
    // public function hasExpiredToken( $uid )
    public function hasExpiredToken(  )
    {
        $param = Request::instance()->param();
        $uid = $param['uid'];
        $token_end_time = Db::table('gonghuoshang')->where('id' , $uid)->field('token_end_time')->select();
        $token_end_time =  $token_end_time[0]['token_end_time'] ;
        $now = date('Y-m-d H:i:s');

        if( $now >= $token_end_time  ) return "true";
        else return "false" ;

    }

    /**
     * [updateToken 更新token]
     * @param  [type] $ghsid [供货商id]
     * @param  [type] $newToken [description]
     * @return [type]           [true-更新成功 false-更新失败]
     */
    public function updateToken( $ghsid , $newToken )
    {
        Db::table('gonghuoshang')->where('id' , $ghsid)->update(['token'=>$newToken]);
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
