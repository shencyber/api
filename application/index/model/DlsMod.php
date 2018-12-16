<?php

/**
 * 代理商数据库
 */

namespace app\index\model;
Use think\Model;
Use think\Db;
class DlsMod extends Model
{

    // 设置当前模型对应的完整数据表名称
    protected $table = 'dailishang';



    /**
     * 注册
     * @param [string] $[weixinopenid] [<微信openid>]
     * @param [string] $[phone] [<手机号>]
     * @param [string] $[password] [<密码>]
     * @param [string] $[nickname] [<昵称>]
     * @param [string] $[avatar] [<头像地址>]
     * @return [type] [description]
     */
    public function register( $weixinopenid , $phone , $password ,$nickname , $avatar  )
    {
        $modelObj = model('DlsMod');

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
       

        $modelObj->data( [ 'weixinopenid'=>$weixinopenid , 'phone'=>$phone , 'password'=>MD5
            (Md5($password))  , 'nickname'=>$nickname , 'avatar'=>$avatar
         ] );
        

        $res = $modelObj->save();
      
        if( $res !== false )
        {
            $obj = array( 
                "status" => 0 , 
                "inseredId" => $modelObj->id ,
                "desc" => "注册成功"
            );


            //生成code
            
        }
        else
        {
            $obj = array( 
                "status" => -1,   //插入数据错误
                "desc" => "注册失败"
            );


            //生成code
            
        }

        return json_encode( $obj , JSON_UNESCAPED_UNICODE) ;die;   

    }

    /**
     * 代理商管登录
     * @param  [type] $phone    [手机]
     * @param  [type] $password [密码]
     * @return [type]           [description]
     */
    public function login( $phone , $password )
    {

        $modelObj = model('DlsMod');
        // $res = $modelObj->where(['phone'=>$phone , 'password'=>$password])->value('id') ; 
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
             $obj = array(
                
               'result'=>['userid'=>$res],
                "status" => 0,
                "desc"=>"登录成功"   //插入数据错误
            );
                
        }
        
        return json_encode( $obj , JSON_UNESCAPED_UNICODE );
        die;
        
    }

    /**
     * 获取代理商的手机号、昵称、头像地址
     * @param  [type] $modelObjid [description]
     * @return [type]        [description]
     */
    public function getDlsInfo( $dlsid )
    {
        $modelObj = model('DlsMod');
       
        $res = $modelObj->where( [ 'id'=>$dlsid ])->column( 'phone,nickname','avatar'    ) ;

        return $res ;die;
         if( !$res ) 
        {
            $obj = array(
                'result'=>null,
                "status" => -1,
                "desc"=>"用户未找到"   //插入数据错误
            );

        }
        else
        {

            $obj = array(
                'result'=>[],
                "status" => 0,
                "desc"=>"查询成功"
            );

            foreach( $res as $value )
            {
                array_push( $obj['result'] , $value );
            }


        }
        
        return json_encode( $obj , JSON_UNESCAPED_UNICODE );
        die;
    }



}

?>