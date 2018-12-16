<?php

/**
 * 供货商数据库
 */

namespace app\index\model;
Use think\Model;
Use think\Db;
Use think\Config;
class GhsMod extends Model
{

    // 设置当前模型对应的完整数据表名称
    protected $table = 'gonghuoshang';



    /**
     * 注册
     * @param [string] $[name] [<姓名>]
     * @param [string] $[phone] [<手机号>]
     * @param [string] $[password] [<密码>]
     * @return [type] [description]
     */
    public function register( $name , $phone , $password   )
    {
        $modelObj = model('GhsMod');


         // 1、查询该手机号是否已经存在
        $res = $modelObj->where(['phone'=>$phone] )->find() ; 

        //如果查到该记录，则说明该手机号已经被注册了
        if( $res )
        {

            $obj = array(
                "status" => 2 , 
                "desc" => "已被注册"
            );

            return json_encode( $obj , JSON_UNESCAPED_UNICODE) ;die;

        }
       

        $modelObj->data( [ 'name'=>$name , 'phone'=>$phone , 'password'=>md5( md5($password) ) ] );

        $res = $modelObj->save();
        if( $res !== false )
        {
            $obj = array( 
                "status" => 0 , 
                "inseredId" => $modelObj->id ,
                "desc"=>"添加成功"
            );

        }
        else
        {
            $obj = array( 
                "status" => -1,   //插入数据错误
                "desc"=>"添加失败"
            );


            //生成code
        }
            
        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ;die;   

    }

    public function login( $phone , $password )
    {

        $modelObj = model('GhsMod');
        $res = $modelObj->where(['phone'=>$phone , 'password'=>md5(md5($password))])->value('id') ; 

        if( !$res ) 
        {
            $obj = array(
                'result'=>null,
                "status" => -1,
                "desc"=>"手机号或密码错误"   //插入数据错误
            );
        }
        else
        {
            $obj =  array(
                'result'=>['userid'=>$res],
                "status" => 0,
                "desc"=>"登录成功"   //插入数据错误
            );

            

            //获取供货商的其他信息
            $res = $this->getGhsInfo($res);
            // print_r("gonghuoshanginfo");
            // dump($res);

            // $res_arr = json_decode($res,true);
            // if( 0!=$res_arr['status'] )
            // {
            //     $obj = array(
            //         'result'=>null,
            //         "status" => -1,
            //         "desc"=>"获取供货商信息失败"   //插入数据错误
            //     );       
            // }
            // else
            // {
                $obj['result']['name'] =$res[0]['name']; 
                $obj['result']['phone'] =$res[0]['phone']; 
                $obj['result']['gno'] =$res[0]['gno']; 
            // }
        }
        
        
       return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; 
      
        
    }

    /**
     * 根据供货商id获取供货商的姓名、手机号、供货商编号
     * @param  [type] $modelObjid [description]
     * @return [type]        [Array]
     */
    public function getGhsInfo( $ghsid )
    {
        // $modelObj = model('GhsMod');
       
        // $res = $modelObj->where( [ 'id'=>$ghsid ])->column( 'name,phone,gno'    ) ;
        $res = Db::table('gonghuoshang')->where( [ 'id'=>$ghsid ])->field( 'name,phone,gno,youpaiuserid,youpaiopenid,youpaitoken')->select() ;

        return $res ;die;
         if( !$res ) 
        {
            $obj = array(
                     'result'=>null,
                    "status" => -1,
                    "desc"=>"用户未找到"
                );

        }
        else
        {

            $obj = array(
                'result'=>$res[0],
                "status" => 0,
                "desc"=>"查询成功"
            );

            // print_r('供货商信息');
            // dump( $obj );die;
            return json_encode( $obj , JSON_UNESCAPED_UNICODE );

        }
        
        die;
    }

    /**
     * 生成又拍的userid
     */
    
    /**
     * [auhorize 又拍相册鉴权]
     * @param  [type] $token   [description]
     * @param  [type] $opendId [description]
     * @param  [type] $appKey  [description]
     * @param  [type] $userid  [用户id]
     * @return [type]          [description]
     */
    public function auhorize( $token , $openId , $appKey , $userid )
    {   
        // print_r('token');
        // dump( $token );
        // print_r('openId');
        // dump( $openId );
        // print_r('appKey');
        // dump( $appKey );
        // $str = $token.'/account/general/openId='.$openId.$appKey ;
        $sign = md5( join('' , [ $token , '/account/general/openId=' , $openId , $appKey ]) );
        // print_r('sign');
        // dump( $sign );

 // (3)获取userId
 //    get请求https://x.yupoo.com/api/account/general?token={{token}}&sign={{sign}}&openId={{openId}}


        // 发送curlGET请求获取userid
        $api = Array(
            Config::get('YPApi'),
            'account/general?token=',
            $token,
            '&sign=',
            $sign,
            '&openId=',
            $openId
            );
        // dump( join('' , $api ) );
        $ch = curl_init();
        curl_setopt( $ch , CURLOPT_URL , join('' , $api) );
        curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1);
        curl_setopt( $ch , CURLOPT_HEADER , 0);
        $output = json_decode( curl_exec( $ch ) , true   );
        curl_close($ch);
        // print_r('jie');
        dump( $output );
        // die;
        // 解析返回结果
        $useridYP = $output['data']['account']['userId'] ;
        // $useridYP = $output['data']['account']['userId'] ;
        // $useridYP = $output['data']['account']['userId'] ;

        // 更新数据库信息,包括youpaiuserid，youpaiopenid ,youpaitoken
        $res = Db::table($this->table)->where(['id'=>$userid])->update(['youpaiuserid'=>$useridYP,'youpaiopenid'=>$openId,'youpaitoken'=>$token]);
        return !!$res ;
    }   

    /**
     * 添加供货商的又拍信息，userid openid  token
     * @param  [int] $ghsid    [供货商id]
     * @param  [string] $userId   [userid]
     * @param  [string] $openid   [openid]
     * @param  [string] $token    [token 会过期]
     * @return [type]           [description]
     */
    public function updateYouPai( $ghsid , $userid , $openid , $token=''  )
    {
        $modelObj = model("GhsMod");
        $res = $modelObj->save(['youpaiuserid'=>$userid,'youpaiopenid'=>$openid,'youpaitoken'=>$token],['id' => $ghsid]);

        if( $res )
        {
            $obj = array(
                'status' => 0,
                'desc'   => '添加成功'
            );
        }
        else
        {
            $obj = array(
                'status' => -1,
                'desc'   => '添加失败'
            );   
        }

        return json_encode($obj , JSON_UNESCAPED_UNICODE );
    }

    /**
     * 更新供货商token
     * @param  [type] $ghsid    [供货商id]
     * @param  [type] $newtoken [新的token]
     * @return [type]           [description]
     */
    public function updateYPToken( $ghsid , $newtoken )
    {
        $modelObj = model("GhsMod");
        $res = $modelObj->save(['youpaitoken'=>$newtoken],['id' => $ghsid]);

        if( $res )
        {
            $obj = array(
                'status' => 0,
                'desc'   => 'token更新成功'
            );
        }
        else
        {
            $obj = array(
                'status' => -1,
                'desc'   => 'token更新失败'
            );   
        }

        return json_encode($obj , JSON_UNESCAPED_UNICODE );
    }


    /**
     * 判断供货商是否有又拍的userid
     * @param  [int]  $ghsid [description]
     * @return boolean        [description]
     */
    public function hasUseridYP( $ghsid )
    {
        $res = DB::table('gonghuoshang')->where(['id'=>$ghsid])->field('youpaiuserid')->select();
        // dump( $res );die;
        return !!$res[0]['youpaiuserid'] ;
        
    }

    /**
    *根据供货商用户id获取对应的相册
    *@parma [int] $youpaiuserid       [供货商id]
    *@parma [int] $currentpage [当前页数]
    *@return  
    */
    public function getAlbumsByGhsId( $youpaiuserid , $currentpage )
    {
        // $api = Config::get('YPApi').'web/users/{userId}/albums?page=1';
        $api = Array(
            Config::get('YPApi') ,
            'web/users/' ,
            $youpaiuserid ,
            '/albums?page=' ,
            $currentpage
        );
        // print_r( join('',$api) );die;
        $ch = curl_init();
        curl_setopt( $ch , CURLOPT_URL , join('' , $api) );
        curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1);
        curl_setopt( $ch , CURLOPT_HEADER , 0);
        $output = json_decode( curl_exec( $ch ) , true   );
        curl_close($ch);
        // print_r('-------------');
        // dump( $output );

        //1、解析结果
        $res = $output['data']['list'];
        // 其中需要的字段是name、description、cover、photoId字段
        // 添加上长地址 
        foreach( $res as $i=>$item)
        {
            $res[$i]['longUrl'] = Config::get('YPImageBaseUrl').$item['cover'];
        }
        // dump($res);die;
        return  $res ;
    }



}

?>