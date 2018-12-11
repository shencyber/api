<?php
//订单控制器
namespace app\index\controller;
Use think\Controller;
Use app\index\model\OrdersMod;
Use app\index\model\OrderDetailMod;
Use app\index\model\GoodsMod;


class Orders extends Controller
{
   
   
    /**
     * 添加订单
     * @param [int] $ghsid [<供货商id>]
     * @param [string] $ghsname [<供货商名称>]
     * @param [int] $dlsid [<代理商id>]
     * @param [string] $receivername [<收件人姓名>]
     * @param [string] $receiverphone [<收件人电话>]
     * @param [string] $address [<收件人地址>]
     * @param [float] $totalprice [<总价>]
     * @param [float] $totalfreight [总运费>]
     * @param [array] $totalfreight [商品数组，包括商品id、商品单价、商品数量>]
     * @return [type] [description]
     */
    // public function add( $ghsid,$ghsname,$dlsid,$receivername,$receiverphone,$address,$totalprice,$totalfreight   )
    public function add(   )
    {
        // echo "add order DLS id = 1"；
        $modelObj  = new OrdersMod();
        // 1、生成订单
        $res = $modelObj->add($this->creareOrderCode(), 1,"张三",1,"收件人","15899636699","收件地址",200,0 );
        
        if( !$res ){return $res ; die;}

        //2、生成订单详情
        $orderDetailCon = controller( 'Orderdetail' );
        $res = $orderDetailCon->addAll(  );
        if( !$res ){return $res ; die;}
        $goodsArr = array(
            ["goodid"=>1,"unitprice"=>120,"amount"=>10],
            ["goodid"=>2,"unitprice"=>120,"amount"=>20]
        );

        //2、更新GoodsMod的soldamount字段
        $goodObj = new GoodsMod();
        foreach ($goodsArr as $index => $value) 
        {
            $res = $goodObj->updateGoodSoldAmount( $value['goodid'] , $value['amount'] );
            $resArr = json_decode( $res  ,  true );
            if( 0 != $resArr['status'] ){return $res ; die;}
        }

        $obj = array(
                'status' => 0,
                'desc'   => '订单生成成功'
            );

        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; die;
        exit("生成订单详情");

    }

    /**
     * 收款-待发货
     * @param  [int] $orderid [订单id]
     * @return [type]          [description]
     */
    // public function shouKuan( $orderid )
    public function shouKuan(  )
    {
        $modelObj  = new OrdersMod();
        return $modelObj->updateOrderStatus( 3 , OrdersMod::DAI_FA_HUO );
    }

    /**
     * 发货
     * @param  [int] $orderid [订单id]
     * @return [type]          [description]
     */
    // public function faHuo( $orderid )
    public function faHuo(  )
    {
        $modelObj  = new OrdersMod();
        return $modelObj->updateOrderStatus( 3 , OrdersMod::YI_FA_HUO );
    }

    /**
     * 取消
     * @param  [int] $orderid [订单id]
     * @return [type]          [description]
     */
    public function quXiao(  )
    {
        $modelObj  = new OrdersMod();
        $res = $modelObj->updateOrderStatus( $_POST['orderid'] , OrdersMod::YI_QU_XIAO );
        if( !$res ){return $res ; die;} 

        //1、查询订单详情内的商品信息

        $modelObj  = new OrderDetailMod();
        $details = $modelObj->getDetail( $_POST['orderid']  );

        //die;

        //2、更新GoodsMod的soldamount字段
        // $goodsArr = array(
        //     ["goodid"=>1,"unitprice"=>120,"amount"=>-2],
        //     ["goodid"=>2,"unitprice"=>120,"amount"=>-2]
        // );
        $goodsArr = [] ;
        $details_arr = json_decode( $details , true );
        // print_r( $details_arr );
        if( 0 == $details_arr['status'] && $details_arr['result'] )
        {
            foreach( $details_arr['result'] as $key=>$value )
            {
                array_push( $goodsArr , ["goodid"=>$value['goodid'],"amount"=>$value['amount']] );
                
            }
        }

        $goodObj = new GoodsMod();
        foreach ($goodsArr as $index => $value) 
        {
            $res = $goodObj->updateGoodSoldAmount( $value['goodid'] , $value['amount'] );
            if( !$res ){return $res ; die;}
        }

        $obj = array(
            'status' => 0,
            'desc'   => '订单取消成功'
        );

        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; die;
    }


    /**
     * 生成唯一的订单号
     * @return [String] [订单号]
     */
    private function creareOrderCode()
    {
        //生成24位唯一订单号码，格式：YYYY-MMDD-HHII-SS-NNNN,NNNN-CC，其中：YYYY=年份，MM=月份，DD=日期，HH=24格式小时，II=分，SS=秒，NNNNNNNN=随机数，CC=检查码
 
         @date_default_timezone_set("PRC");
         //订购日期
         
          $order_date = date('Y-m-d');
         
          //订单号码主体（YYYYMMDDHHIISSNNNNNNNN）
         
          $order_id_main = date('YmdHis') . rand(10000000,99999999);
         
          //订单号码主体长度
         
          $order_id_len = strlen($order_id_main);
         
          $order_id_sum = 0;
         
          for($i=0; $i<$order_id_len; $i++){
         
          $order_id_sum += (int)(substr($order_id_main,$i,1));
         
          }
         
          //唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
         
          $order_id = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100,2,'0',STR_PAD_LEFT);
          return $order_id ;
    }

    /**
     * 获取订单列表[已完成]
     * @param  [type] $ghsid       [供货商id]
     * @param  [type] $currentpage [当前页数]
     * @param  [type] $pagesize    [每页显示数量]
     * @return [type]              [description]
     */
    // public function orderList( $ghsid ,  $currentpage , $pagesize )
    public function orderList(  )
    {
        $modelObj  = new OrdersMod();

        return $modelObj->orderList( $_POST['ghsid'] , $_POST['currentpage'] , $_POST['pagesize'] , $_POST['status']  );

    }

    

}

?>
