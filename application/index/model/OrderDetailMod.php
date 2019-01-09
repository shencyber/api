<?php

/**
 * 照片数据库
 */

namespace app\index\model;
Use think\Model;
Use think\Db;
Use think\Config;
Use app\index\model\GoodsMod;



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


        $goods = Db::table('orderdetail')->where(['orderid'=>$orderid])
                ->field('goodid,unitprice,amount')
                ->select();

        
        $modelObj = model('GoodsMod');
        foreach( $goods as $key=>$val )
        {
            $goodDetail = $modelObj->getGoodsById( $val['goodid'] );
            $goods[$key]['name'] = $goodDetail[0]['name'];
            $goods[$key]['source'] = $goodDetail[0]['source'];
            $goods[$key]['status'] = $goodDetail[0]['status'];
            $goods[$key]['unitprice'] = $goodDetail[0]['unitprice'];
            $goods[$key]['unit'] = $goodDetail[0]['unit'];
            $goods[$key]['desc'] = $goodDetail[0]['desc'];

            $urls = explode("," , $goodDetail[0]['urls'] );
            foreach( $urls as $subkey=>$subval )
            {
                if( 1 == $goodDetail[0]['source'] )
                    $urls[$subkey] = Config::get('ImageBaseURL').$subval;
                else
                    $urls[$subkey] = Config::get('YPImageBaseUrl').$subval;

            }
            // print_r( $urls);die;
            $goods[$key]['urls'] = $urls;
            // print_r($goods);
        }
        return $goods ;
   
       
    }
    



}

?>