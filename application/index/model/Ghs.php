<?php

/**
 * 供货商数据库
 */

namespace app\index\model;
Use think\Model;
class Ghs extends Model
{

    // 设置当前模型对应的完整数据表名称
    protected $table = 'gonghuoshang';



    /**
     * 添加单条数据
     * @param [string] $[name] [<姓名>]
     * @param [string] $[phone] [<手机号>]
     * @param [string] $[password] [<密码>]
     * @return [type] [description]
     */
    public function insertSingle( $name , $phone , $password   ){
        $ghs = model('Ghs');　　// 使用model 即可快速实例化模型，不必使用 $user = new User();
        $list = [
            [ 'name'=>$name , 'phone'=>$phone , 'password'=>$password ]
        ];
        $res = $ghs->save($list);
        if( $res )
        {
        	return $res ;die;
        }
    	else
    	{

    	}

    }



}