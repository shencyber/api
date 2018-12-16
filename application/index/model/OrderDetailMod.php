<?php

/**
 * 照片数据库
 */

namespace app\index\model;
Use think\Model;
Use think\Db;



class OrderDetailMod extends Model
{

    // 设置当前模型对应的完整数据表名称
    protected $table = 'orderdetail';


    /**
     * 添加单条订单详情记录 
     * @param [int] $orderid [订单id]
     * @param [int] $goodid [商品id]
     * @param [int] $unitprice [购买单价]
     * @param [int] $amount [购买数量]
     * @return [type] [description]
     */
    public function add( $orderid , $goodid ,$unitprice,$amount )
    {
        $modelObj = model('OrderDetailMod');
       
        $modelObj->data( [ 
            'orderid'    =>  $orderid , 
            'goodid'     =>  $goodid , 
            'unitprice'  =>  $unitprice,
            'amount'     =>  $amount
            ] );

        $res = $modelObj->save();
        
        if( $res !== false )
        {
            $obj = array( 
                "status" => 0 , 
                "inseredId" => $modelObj->id ,
                "desc" => "添加成功"
            );


        }
        else
        {
            $obj = array( 
                "status" => -1 ,  //插入数据错误
                "desc" => "添加失败"
            );

        }

        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ;die;
    }

    /**
     * 添加多条商品信息
     * @param [type] $orderid  [description]
     * @param [type] $goodsArr [description]
     */
    public function addAll( $orderid , $goodsArr )
    {
        // dump( $goodsArr );die;
        $modelObj = model('OrderDetailMod');
        foreach( $goodsArr as $index=>$good )
        {
            $goodsArr[$index]['orderid'] = $orderid ;   
        }

        $res = $modelObj->saveAll( $goodsArr );
        if( !$res ) return $res;
        else
        {   
            $obj = [] ;
            foreach ($res as $key => $value) 
            {
                array_push($obj, $value['id']);
            }

            return $obj ;
        }
    }

    /**
     * 订单详情
     * @param  [type] $orderid [订单id]
     * @return [type]          [description]
     */
    public function getDetail( $orderid  )
    {
        // $modelObj = model('OrderDetailMod');
        $list = Db::table('orders')
            ->where(['id'=>$orderid])

            ->select();

        $list = Db::table('orders')->alias('ord')
            ->join('orderdetail det','ord.id = det.orderid','left')
            ->where([ 'ord.id'=> $orderid ])
            ->field('ord.id,ord.ordercode,ord.totalprice,ord.dlsid,ord.createtime,ord.status,ord.expressno,det.goodid,det.unitprice,det.amount')
            ->select();

        // $list  = Array( 
            //     Array(
            //         [id] => 1,[ordercode] => 1,[totalprice] => 200,[dlsid] => 1,
            //         [createtime] => 2018-12-06 13:08:12,[status] => 2,[expressno] => ,
            //         [goodid] => 1,[unitprice] => 158,[amount] => 200),
            //     Array(
            //         [id] => 1,[ordercode] => 1,[totalprice] => 200,[dlsid] => 1,
            //         [createtime] => 2018-12-06 13:08:12,[status] => 2,[expressno] => ,
            //         [goodid] => 1,[unitprice] => 158,[amount] => 200),
            // )


        // print_r($list);die;
        if( $list )
        {
            //根据商品id获取商品信息
            $goodsCon = controller( 'goods' );
            foreach ($list as $key => $value) {
                
            }

            $obj = array( 
                "status" => 0 , 
                "result" => [] ,
                "desc" => "查询成功"
            );

             foreach( $list as $value )
            {
                array_push( $obj['result'] , $value );
            }


        }
        else
        {
            $obj = array( 
                "status" => -1 ,  //插入数据错误
                "desc" => "查询失败"
            );

        }

        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ;die;
    }
    



}

?>