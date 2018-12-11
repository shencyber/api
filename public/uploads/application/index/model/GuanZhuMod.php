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
     * @return [type] [description]
     */
    public function add( $dlsid , $ghsid    )
    {
        $modelObj = model('GuanZhuMod');


         // 1、查询该手机号是否已经存在
        $res = $modelObj->where([ 'dlsid'=>$dlsid , 'ghsid'=>$ghsid ] )->find() ; 

        //如果查到该记录，则说明该手机号已经被注册了
        if( $res )
        {

            $obj = array(
                "status" => 2 , 
                "desc" => "已收藏"
            );

            return json_encode( $obj , JSON_UNESCAPED_UNICODE) ;die;

        }
       
        $modelObj->data( [ 
            'dlsid'=>$dlsid , 
            'ghsid'=>$ghsid , 
            'gztime'=>date('Y-m-d H:i:s'),
            'status'=>1
            ] );

        $res = $modelObj->save();
        if( $res !== false )
        {
            $obj = array( 
                "status" => 0 , 
                "inseredId" => $modelObj->id ,
                "desc" => "添加成功"
            );


            return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ;die;
        }
        else
        {
            $obj = array( 
                "status" => -1 ,  //插入数据错误
                "desc" => "添加失败"
            );


            //生成code
            
            return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ;die;   
    	}

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
     * 获取素有关注信息
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
     * @return [type]        [description]
     */
    public function getListByDlsId( $dlsid )
    {
        $modelObj = model('GuanZhuMod');
        $res = $modelObj->where(["dlsid"=>$dlsid])->column('id,dlsid,ghsid,gztime,status');
        
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



}

?>