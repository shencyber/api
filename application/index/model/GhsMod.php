<?php

/**
 * 供货商数据库
 */

namespace app\index\model;
Use think\Model;
Use think\Db;
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
            
        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ;die;   
        }

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
            $res_arr = json_decode($res,true);
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
                $obj['result']['name'] =$res_arr['result']['name']; 
                $obj['result']['phone'] =$res_arr['result']['phone']; 
                $obj['result']['gno'] =$res_arr['result']['gno']; 
            }
        }
       return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; 
        die;
        
    }

    /**
     * 根据供货商id获取供货商的姓名、手机号、供货商编号
     * @param  [type] $modelObjid [description]
     * @return [type]        [description]
     */
    public function getGhsInfo( $ghsid )
    {
        // $modelObj = model('GhsMod');
       
        // $res = $modelObj->where( [ 'id'=>$ghsid ])->column( 'name,phone,gno'    ) ;
        $res = Db::table('gonghuoshang')->where( [ 'id'=>$ghsid ])->field( 'name,phone,gno')->select() ;
        // dump( $res );die;
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

            
            return json_encode( $obj , JSON_UNESCAPED_UNICODE );

        }
        
        die;
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


}

?>