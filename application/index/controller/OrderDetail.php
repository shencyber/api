<?php
//订单详情控制器
namespace app\index\controller;
Use think\Controller;
Use think\Db;
Use think\Request;
Use app\index\model\OrderDetailMod;

class Orderdetail extends Base
{
   
   
    /**
     * 添加单条
     * @param [int] $orderid [订单id]
     * @param [int] $goodid [商品id]
     * @param [int] $unitprice [购买单价]
     * @param [int] $amount [购买数量]
     * @return [type] [description]
     */
    // public function add( $orderid , $goodid ,$unitprice,$amount )
    public function add(   )
    {
        // echo "add order DLS id = 1"；
        $modelObj  = new OrderDetailMod();
        $res = $modelObj->add( 1,1,"158",200 );
        return $res ;

    }

    /**
     * 添加多条商品信息
     * @param [int] $orderid  [订单id]
     * @param [二维数组] $goodsArr [商品信息  字段 goodid、unitprice、amount]
     */
    public function addAll(  $orderid , $goods )
    {
        dump( $goods );die;
        // echo "add order DLS id = 1"；
        $modelObj  = new OrderDetailMod();
        $res = $modelObj->addAll( 1 , [["goodid"=>1,"unitprice"=>120,"amount"=>10],["goodid"=>2,"unitprice"=>120,"amount"=>20]] );
        return $res ;

    }


    /**
     * Api
     * 订单详情
     * @param  [type] $orderid [订单id]
 
     * @return [type]          [description]
     */
    public function getDetailApi(  )
    {

        $req = Request::instance()->param();
        $modelObj  = new OrderDetailMod();
        return $modelObj->getDetail( $req['orderid']  );
    }


    /**
     * 方法
     * 订单详情
     * @param  [type] $orderid [订单id]
 
     * @return [type]          [description]
     */
    public function getDetail(  $orderid )
    {

        $modelObj  = new OrderDetailMod();
        return $modelObj->getDetail( $orderid  );
    }



    

}

?>
