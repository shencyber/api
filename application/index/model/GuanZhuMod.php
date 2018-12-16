<?php

/**
 * 代理商供货商关注关系表
 */

namespace app\index\model;
Use think\Model;
Use think\Db;
class GuanZhuMod extends Model
{

    // 设置当前模型对应的完整数据表名称
    protected $table = 'guanzhu';



    /**
     * 添加关注关系
     * @param [int] $[dlsid] [<代理商id>]
     * @param [int] $[ghsid] [<供货商id>]
     * @return [int] [自增id]
     */
    public function add( $dlsid , $ghsid    )
    {
        $modelObj = model('GuanZhuMod');

        $modelObj->data( [ 
            'dlsid'=>$dlsid , 
            'ghsid'=>$ghsid , 
            'gztime'=>date('Y-m-d H:i:s'),
            'status'=>1
            ] );

        $modelObj->save();
        return $modelObj->id;
        

    }

    /**
     * 取消代理商和供货商的关注关系
     * @param  [type] $guanzhuid [description]
     * @return [type]            [description]
     */
    public function cancel( $guanzhuid )
    {

        $modelObj = model('GuanZhuMod');

         // 1、查询该关注记录是否已经存在
        $res = $modelObj->where([ 'id'=>$guanzhuid  ] )->find() ; 

        if( !$res )
        {
            $obj = array( 
                "status" => -1 , 
                "desc" => "没有该数据"
            );
            return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ;die;   

        }


        $res = $modelObj->save(['status'  => 0 ],['id' => $guanzhuid]);

        if( !$res ) 
        {
            $obj = array(
                'result'=>null,
                "status" => -1,
                "desc"=>"更新失败"   
            );
        }
        else
        {
            $obj =  array(
                'result'=>null,
                "status" => 0,
                "desc"=>"更新成功"   
            );
        }

       return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; 
        die;
        
    }

    /**
     * 获取所有关注信息
     * @return [type]        [description]
     */
    public function allList()
    {
        $modelObj = model('GuanZhuMod');
       
        $res = $modelObj->column('id,dlsid,ghsid,gztime,status');

        // echo "<pre>";
        // var_dump( $res );
        // echo "</pre>";
        // die;


         if( !$res ) 
        {
            $obj = array(
                    'result'=>null,
                    "status" => 0,
                    "desc"=>"无数据"
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
        
        return json_encode( $obj , JSON_UNESCAPED_UNICODE );die;
        
    }

    /**
     * 根据代理商id，获取关注列表
     * @param  [type] $dlsid [代理商id]
     * @return [Array]        [array(2) {
                                  [0] => array(5) {
                                    ["id"] => string(1) "4"
                                    ["dlsid"] => string(1) "1"
                                    ["ghsid"] => string(1) "3"
                                    ["gztime"] => string(19) "2018-12-04 20:19:12"
                                    ["status"] => string(1) "1"
                                  }
                                  [1] => array(5) {
                                    ["id"] => string(1) "5"
                                    ["dlsid"] => string(1) "1"
                                    ["ghsid"] => string(1) "4"
                                    ["gztime"] => string(19) "2018-12-04 20:20:11"
                                    ["status"] => string(1) "1"
                                  }
                                }]
     */
    public function getListByDlsId( $dlsid )
    {
        return Db::table($this->table)->where(['dlsid'=>$dlsid])->select();
        // return 
        // dump( $res );die;        
    }

     /**
     * 根据供货商id，获取关注列表
     * @param  [type] $dlsid [供货商id]
     * @return [type]        [description]
     */
    public function getListByGhsId( $ghsid )
    {
        $modelObj = model('GuanZhuMod');
        $res = $modelObj->where(["ghsid"=>$ghsid])->column('id,dlsid,ghsid,gztime,status');
        
        // echo "<pre>";
        // var_dump( $res );
        // echo "</pre>";
        // die;

        if( !$res )
        {
             $obj = array(
                    'result'=>null,
                    "status" => 0,
                    "desc"=>"无数据"
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

        return json_encode( $obj , JSON_UNESCAPED_UNICODE );die;


    }


    /**
     * [exists 判断代理商和供货商的关注关系是否已经存在]
     * @param  [type] $dlsid [description]
     * @param  [type] $ghsid [description]
     * @return [type]        [description]
     */
    public function exists( $dlsid , $ghsid )
    {
        $res = Db::table($this->table)->where([ 'dlsid'=>$dlsid , 'ghsid'=>$ghsid ])->select();
        // dump( empty($res) );die;
        // if( !empty($res) ) return true ; return false;die;
        return !empty($res);

    }



}

?>