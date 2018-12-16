<?php

namespace app\index\controller;
Use think\Controller;
Use think\Request;
Use think\Db;
Use app\index\controller\dls;
Use app\index\controller\ghs;
Use app\index\model\GuanZhuMod;


class Guanzhu extends Base
{
   
   
    /**
     * 添加关注关系
     * @param [int] $[dlsid] [<代理商id>]
     * @param [int] $[ghsid] [<供货商id>]
     * @return [type] [description]
     */
    // public function guanZhu(  $dlsid , $ghsid  )
    public function guanZhu(    )
    {
        $req = Request::instance()->param();

        // 1、查询代理商是否存在
        $res = controller('dls')->exists( $req['dlsid'] );

        if( !$res )
        {
            $obj =Array(
                'status' => 1,
                'desc'   => '该代理商不存在',
                'result' => '' 
            );
            return json_encode($obj , JSON_UNESCAPED_UNICODE);die;
        }
        
        // 2、查询供货商是否存在 
        $res = controller('ghs')->exists( $req['ghsid'] );

        if( !$res )
        {
            $obj =Array(
                'status' => 2,
                'desc'   => '该供货商不存在',
                'result' => '' 
            );
            return json_encode($obj , JSON_UNESCAPED_UNICODE);die;
        }
        
        //3、查询代理商和供货商的关注关系是否已经存在
        $modelObj  = new GuanZhuMod();
        $res  = $modelObj->exists( $req['dlsid'] ,  $req['ghsid'] );
        // dump( $res );
        if( $res )
        {
            $obj =Array(
                'status' => 3,
                'desc'   => '已关注',
                'result' => '' 
            );
            return json_encode($obj , JSON_UNESCAPED_UNICODE);die;
        }
        // die;

        //4、添加关注记录
        $res = $modelObj->add( $req['dlsid'] ,  $req['ghsid'] );
        // dump( $res );die;
        if( $res )
        {
            $obj = array( 
                "status" => 0 , 
                "result" => $res ,
                "desc" => "添加成功"
            );
        }
        else
        {
            $obj = array( 
                "status" => -1 ,  //插入数据错误
                "desc" => "添加失败",
                "result"=>""
            );
        }

        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ;die;   

    }

    /**
     * 取消代理商和供货商的关注关系
     * @param  [type] $guanzhuid [description]
     * @return [type]            [description]
     */
    // public function cancel( $guanzhuid )
    public function cancel(  )
    {
        $modelObj  = new GuanZhuMod();
        $res = $modelObj->cancel( 2  );
        return $res ;
    }

     /**
     * 获取所有关注信息
     * @return [type]        [description]
     */
    public function getAllGuanZhuInfo()
    {
        $modelObj  = new GuanZhuMod();
        $res = $modelObj->allList(  );
        return $res ;   
    }

    /**
     * 根据代理商id，获取关注列表
     * @param  [type] $dlsid [代理商id]
     * @return [type]        [description]
     */
    // public function getListByDlsId( $dlsid )
    public function getListByDlsId(  )
    {   
        $req = Request::instance()->param();
        $modelObj  = new GuanZhuMod();
        $res = $modelObj->getListByDlsId( $req['dlsid'] );
        
        if( is_array($res) )
        {
            $obj = Array(
                'status'  => 0,
                'desc'    => '查询成功',
                'result'  => $res
            );
        }
        else
        {
            $obj = Array(
                'status'  => -1,
                'desc'    => '查询失败',
                'result'  => $res
            );

        }
        return json_encode($obj , JSON_UNESCAPED_UNICODE);
    }

    /**
     * 根据供货商id，获取关注列表
     * @param  [type] $dlsid [供货商id]
     * @return [type]        [description]
     */
    // public function getListByGhsId( $dlsid )
    public function getListByGhsId(  )
    {
        $modelObj  = new GuanZhuMod();
        $res = $modelObj->getListByGhsId( 4 );
        return $res ;      
    }

  

}

?>
