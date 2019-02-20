<?php
//订单控制器
namespace app\index\controller;
Use think\Controller;
Use think\Request;
Use think\Db;
Use app\index\model\OrdersMod;
Use app\index\model\OrderDetailMod;
Use app\index\model\GoodsMod;


class Orders extends Base
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
     * @param [array] $goods [商品数组，包括商品id-gid、商品单价-unitprice、商品数量-amount>]
     * @return [type] [description]
     */
    // public function add( $ghsid,$ghsname,$dlsid,$receivername,$receiverphone,$address,$totalprice,$totalfreight   )
    public function add(   )
    {
        $req = Request::instance()->param();
        $modelObj  = new OrdersMod();
        // 1、生成订单
        $insertedId = $modelObj->add( 
            $this->creareOrderCode(), 
            $req['ghsid'],
            $req['ghsnickname'],
            $req['dlsid'],
            // $req['receivername'],
            // $req['receiverphone'],
            $req['address'],
            $req['totalprice'],
            $req['totalfreight']
            // $req['goods'] 
        );
        
        if( !$insertedId )
        {
            $obj = Array(
                'status' => 0 ,
                'desc'   => '添加订单失败',
                'result' => null 
            );
            return json_encode($obj , JSON_UNESCAPED_UNICODE) ; die;
        }


        //2、生成订单详情
        //
        //$detModelObj  = new OrdersMod();
        //$detModelObj->addAll();
        //$req['goods']示例 [{goodid:1、unitprice:100、amount:3}]
        // print_r( $req['goods'] );die;
        // print_r( json_decode( $req['goods'] ,true ) );die;
        $orderDetailMod = new OrderDetailMod();
        $res_detailids = $orderDetailMod->addAll( $insertedId ,  $req['goods'] );  
        // dump( $res );die;
        // $arr = json_decode($res);
        // dump( $arr[0] );
        // die;
        

        if( !$res_detailids )
        {
            $obj = Array(
                'status' => 0 ,
                'desc'   => '添加订单失败',
                'result' => null 
            );
            return json_encode($obj , JSON_UNESCAPED_UNICODE) ; die;
        }


        // $goodsArr = array(
        //     ["goodid"=>1,"unitprice"=>120,"amount"=>10],
        //     ["goodid"=>2,"unitprice"=>120,"amount"=>20]
        // );

        //2、更新GoodsMod的soldamount字段
        $goodObj = new GoodsMod();
        $goodsArr = $req['goods'];
        foreach ($goodsArr as $index => $value) 
        {
            $res = $goodObj->updateGoodSoldAmount( $value['goodid'] , $value['amount'] );
            $resArr = json_decode( $res  ,  true );
            if( 0 != $resArr['status'] ){return $res ; die;}
        }
        $obj = array(
                'status' => 0,
                'desc'   => '订单生成成功',
                'result' => [ 'ordersId' => $insertedId , 'orderdetailIds'=>$res_detailids  ]
            );

        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; die;
        exit("生成订单详情");

    }

    /**
     * 收款-待发货
     * @param  [int] $orderid [订单id]
     * @param  [int] $actualprice [收款金额]
     * @return [type]          [description]
     */
    // public function shouKuan( $orderid )
    public function shouKuan(  )
    {
        $req = Request::instance()->param();

        $modelObj  = new OrdersMod();
        $res = $modelObj->shouKuan( $req['orderid'] , $req['actualprice'] );
        if( $res >=0  )
        {
            $obj = Array(
                'status' => 0 ,
                'desc'   => '收款成功',
                'result' => null
            );
        }
        else
        {
            $obj = Array(
                'status' => -1 ,
                'desc'   => '收款失败',
                'result' => null
            );  
        }
        return json_encode($obj , JSON_UNESCAPED_UNICODE);
    }


    /**
     * 发货
     * @param  [int] $orderid [订单id]
     * @param  [int] $express [订单号]
     * @return [type]          [description]
     */
    public function fahuo( $orderid , $express )
    {
        $req = Request::instance()->param();

        $modelObj  = new OrdersMod();
        $res = $modelObj->fahuo( $req['orderid'] , $req['express'] );
        if( $res >=0  )
        {
            $obj = Array(
                'status' => 0 ,
                'desc'   => '保存成功',
                'result' => null
            );
        }
        else
        {
            $obj = Array(
                'status' => -1 ,
                'desc'   => '保存失败',
                'result' => null
            );  
        }
        return json_encode($obj , JSON_UNESCAPED_UNICODE);
    }

    /**
     * 取消
     * @param  [int] $orderid [订单id]
     * @return [type]          [description]
     */
    public function quxiao(  )
    {   
        $req = Request::instance()->param();
        $modelObj  = new OrdersMod();
        $res = $modelObj->cancel( $req['orderid'] );
        if( $res >=0  )
        {
            $obj = Array(
                'status' => 0 ,
                'desc'   => '取消成功',
                'result' => null
            );
        }
        else
        {
            $obj = Array(
                'status' => -1 ,
                'desc'   => '取消失败',
                'result' => null
            );  
        }
        return json_encode($obj , JSON_UNESCAPED_UNICODE);
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
     * Api
     * 获取订单列表[已完成]
     * @param  [int] $ghsid       [供货商id]
     * @param  [int] $currentpage [当前页数]
     * @param  [int] $pagesize    [每页显示数量]
     * @param  [int] $status      [1-代收款 2-待发货  3-已发货  4-已取消]
     * @return [type]              [description]
     */
    // public function orderList( $ghsid ,  $currentpage , $pagesize )
    public function orderListApi(  )
    {
      $req = Request::instance()->param();

      $modelObj  = new OrdersMod();

      return $modelObj->orderList( $req['ghsid'] , $req['currentpage'] , $req['pagesize'] , $req['status']  );

    }

    /**
     * Api[done]
     * 根据代理商id获取订单列表[已完成]
     * @param  [int] $dlsid       [代理商id]
     * @param  [int] $currentpage [当前页数]
     * @param  [int] $pagesize    [每页显示数量]
     * @param  [int] $status      [1-代收款 2-待发货  3-已发货  4-已取消]
     * @return [type]              [description]
     */
    public function dlsOrderListApi(    )
    {
       $req = Request::instance()->param();

       $modelObj  = new OrdersMod();

       return $modelObj->dlsOrderList( $req['dlsid'] , $req['currentpage'] , $req['pagesize'] , $req['status']  );
    }

    /**
     * [getDetailApi 根据订单id获取订单详情 需要查询order、orderdetail、goods、photo表]
     * @param  [type] $orderid [description]
     * @return [type]          [description]
     */
    public function getDetailApi()
    {

      $req = Request::instance()->param();
      $oid = $req['orderid'] ;
      //1、获取订单信息
      $order = Db::table('orders')
            ->join('gonghuoshang' , 'orders.ghsid=gonghuoshang.id','left')
            ->field('orders.id,orders.ordercode,orders.ghsid,orders.dlsid,orders.ghsnickname,orders.createtime,orders.receivername,orders.receiverphone,orders.address,orders.totalprice,orders.expressno,orders.actualprice,orders.status,gonghuoshang.phone')
            ->where( 'orders.id' , $oid )->select();


      //2、获取订单详情-订单对应商品信息
      $detailCon = controller('Orderdetail');
      $detail = $detailCon->getDetail( $oid );
      // print_r( $detail );die;
      $order[0]['detail'] = $detail;
      // print_r( $order[0]['detail'] );die;

      //3、根据订单信息内的代理商id获取代理商信息
      $dlsid = $order[0]['dlsid'] ;
      $dlsres = Db::table('dailishang')->where('id',$dlsid )->field('nickname,avatar')->select();
      // print_r($order[0]);die;
      $order[0]['dlsnickname'] = $dlsres[0]['nickname'];
      $order[0]['dlsavatar'] = $dlsres[0]['avatar'];
      if( $order[0] )
      {
        $obj = array(
          'status' => 0,
          'desc'   => '查询成功',
          'result' => $order[0]
        );
      }
      else
      {
        $obj = array(
          'status' => 1,
          'desc'   => '查询失败',
          'result' => null
        ); 
      }

      return json_encode($obj , JSON_UNESCAPED_UNICODE);die;


    }

    

}

?>
