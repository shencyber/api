<?php
//订单详情控制器
namespace app\index\controller;
Use think\Controller;
Use app\index\model\OrderDetailMod;

class OrderDetail extends Controller
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
     * @param [type] $orderid  [订单id]
     * @param [type] $goodsArr [商品信息  字段 goodid、unitprice、amount]
     */
    // public function addAll( $orderid , $goodsArr  )
    public function addAll(   )
    {
        // echo "add order DLS id = 1"；
        $modelObj  = new OrderDetailMod();
        $res = $modelObj->addAll( 1 , [["goodid"=>1,"unitprice"=>120,"amount"=>10],["goodid"=>2,"unitprice"=>120,"amount"=>20]] );
        return $res ;

    }


    /**
     * 订单详情
     * @param  [type] $orderid [订单id]
 
     * @return [type]          [description]
     */
    public function getDetil( )
    {
        $modelObj  = new OrderDetailMod();
        return $modelObj->getDetail( $_POST['orderid']  );
    }

    

}

?>
