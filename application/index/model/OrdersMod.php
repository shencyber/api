<?php

/**
 * 照片数据库
 */

namespace app\index\model;
Use think\Model;
Use think\Db;

Use app\index\model\GhsMod;
class OrdersMod extends Model
{

    // 设置当前模型对应的完整数据表名称
    protected $table = 'orders';

    const DAI_SHOU_KUAN  = 1 ;//代收款
    const DAI_FA_HUO     = 2 ;//待发货
    const YI_FA_HUO      = 3 ;//已发货
    const YI_QU_XIAO     = 4 ;//已取消

    /**
     * 添加订单
     * @param [int] $ordercode [<订单编号>]
     * @param [int] $ghsid [<供货商id>]
     * @param [int] $ghsname [<供货商名称>]
     * @param [int] $dlsid [<代理商id>]
     * @param [int] $receivername [<收件人姓名>]
     * @param [int] $receiverphone [<收件人电话>]
     * @param [int] $address [<收件人地址>]
     * @param [int] $totalprice [<总价>]
     * @param [int] $totalfreight [总运费>]
     * @return [type] [description]
     */
    public function add($ordercode,$ghsid,$ghsname,$dlsid,$receivername,$receiverphone,$address,$totalprice,$totalfreight )
    {
        $modelObj = model('OrdersMod');
        $modelObj->data( [ 
            'ordercode'      =>   $ordercode , 
            'ghsid'          =>   $ghsid , 
            'ghsname'        =>   $ghsname , 
            'dlsid'          =>   $dlsid,
            'receivername'   =>   $receivername,
            'receiverphone'  =>   $receiverphone,
            'address'        =>   $address,
            'totalprice'     =>   $totalprice,
            'totalfreight'   =>   $totalfreight,
            'createtime'     =>   date('Y-m-d H:i:m'),
            'status'         =>   OrdersMod::DAI_SHOU_KUAN, 
            ] );


        $res = $modelObj->save();

        return $modelObj->id  ;die ;
        dump($res);   die;  
        if( $res !== false )
        {
            $obj = array( 
                "status" => 0 , 
                "result" => $modelObj->id ,
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
     * 收款-待发货
     * @param  [int] $orderid [订单id]
     * @param  [int] $actualprice [收款金额]
     * @return [type]          [description]
     */
    public function shouKuan( $orderid , $actualprice )
    {

        return Db::table($this->table)->where(['id'=>$orderid])->update(['actualprice'=>$actualprice,'status'=>OrdersMod::DAI_FA_HUO]);
        
    }


   /**
     * 发货
     * @param  [int] $orderid [订单id]
     * @param  [int] $express [订单号]
     * @return [type]          [description]
     */
    public function fahuo( $orderid , $express )
    {

        return Db::table($this->table)->where(['id'=>$orderid])->update(['expressno'=>$express,'status'=>OrdersMod::YI_FA_HUO]);
    }

     /**
     * 取消
     * @param  [int] $orderid [订单id]
     * @return [type]          [description]
     */
    public function cancel( $orderid )
    {
        return Db::table($this->table)->where(['id'=>$orderid])->update(['status'=>OrdersMod::YI_QU_XIAO]);
    }

    /**
     * 更新订单状态
     * @param  [int] $orderid [订单di]
     * @param  [int] $status [订单状态]
     * @return [type]            [description]
     */
    // public function updateOrderStatus( $orderid , $status )
    // {

    //     $modelObj = model('OrdersMod');

    //      // 1、查询该关注记录是否已经存在
    //     $res = $modelObj->where([ 'id'=>$orderid  ] )->find() ; 

    //     if( !$res )
    //     {
    //         $obj = array( 
    //             "status" => -1 , 
    //             "desc" => "没有该订单"
    //         );
    //         return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ;die;   

    //     }


    //     $res = $modelObj->save(['status'  => $status ],['id' => $orderid]);

    //     if( !$res ) 
    //     {
    //         $obj = array(
    //             'result'=>null,
    //             "status" => -1,
    //             "desc"=>"更新失败"   
    //         );
    //     }
    //     else
    //     {
    //         $obj =  array(
    //             'result'=>null,
    //             "status" => 0,
    //             "desc"=>"更新成功"   
    //         );
    //     }

    //    return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; 
    //     die;
        
    // }


    /**
     * 根据供货商id获取订单列表【Done】
     * @param  [int] $ghsid       [供货商id]
     * @param  [int] $currentpage [当前页数]
     * @param  [int] $pagesize    [每页显示数量]
     * @param  [int] $status    [订单状态 1-待收款 2-待发货 3-已发货 4-已取消]
     * @return [type]              [description]
     */
    public function orderList( $ghsid ,  $currentpage , $pagesize , $status )
    {
        $modelObj  = new OrdersMod();
        $count = $modelObj->getTotalOrderByGhsid( $ghsid , $status );
        if( 0 == $count )
        {
            $obj = array(
                'result'=>null,
                "status" => 0,
                "desc"=>"无数据" 
            );
            return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; die;
        }


        $list = Db::table('orders')->alias('ord')
            ->join('dailishang dls','ord.dlsid = dls.id','left')
            ->where([ 'ord.ghsid'=> $ghsid , 'ord.status'=>$status ])
            ->order('createtime desc')
            ->page($currentpage , $pagesize)
            ->field('ord.id,ord.ordercode,ord.totalprice,ord.dlsid,ord.createtime,ord.status,ord.expressno,dls.phone,dls.nickname,dls.avatar')
            ->select();


      
        $obj = array(
            'result'=>array(),
            'total'=>$count,
            "status" => 0,
            "desc"=>"找到了" 
        );

        foreach($list as $url)
        {
            array_push( $obj['result'] , $url );
        }

        // print_r( $obj );die;
        //根据代理商id获取代理商的详细信息
        // foreach( $obj['result'] as $index=>$item  )
        {

            // $dlsMod = new DlsMod();
            // $dlsMod->getDlsInfo( $item['$dlsid'] );

        }
        // dump(  );
        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; die;

    }


    /**
     * 根据代理商id获取订单列表【done】
     * @param  [int] $dlsid       [供货商id]
     * @param  [int] $currentpage [当前页数]
     * @param  [int] $pagesize    [每页显示数量]
     * @param  [int] $status    [订单状态 1-待收款 2-待发货 3-已发货 4-已取消]
     * @return [type]              [description]
     */
    public function dlsOrderList( $dlsid ,  $currentpage , $pagesize , $status )
    {
        $modelObj  = new OrdersMod();
        $count = $modelObj->getTotalOrderByDlsid( $dlsid , $status );
        if( 0 == $count )
        {
            $obj = array(
                'result'=>null,
                "status" => 0,
                "desc"=>"无数据" 
            );
            return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; die;
        }


        $list = Db::table('orders')->alias('ord')
            ->join('dailishang dls','ord.dlsid = dls.id','left')
            ->where([ 'ord.dlsid'=> $dlsid , 'ord.status'=>$status ])
            ->order('createtime desc')
            ->page($currentpage , $pagesize)
            ->field('ord.id,ord.ordercode,ord.totalprice,ord.dlsid,ord.createtime,ord.status,ord.expressno,dls.phone,dls.nickname,dls.avatar')
            ->select();


      
        $obj = array(
            'result'=>array(),
            'total'=>$count,
            "status" => 0,
            "desc"=>"找到了" 
        );

        foreach($list as $url)
        {
            array_push( $obj['result'] , $url );
        }

        // print_r( $obj );die;
        //根据代理商id获取代理商的详细信息
        // foreach( $obj['result'] as $index=>$item  )
        {

            // $dlsMod = new DlsMod();
            // $dlsMod->getDlsInfo( $item['$dlsid'] );

        }
        // dump(  );
        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; die;

    }

    /**
     * 根据供货商id获取订单总数量
     * @param  [type] $ghsid [供货商id]
     * @param  [type] $status [状态]
     * @return [type]        [description]
     */
    public function getTotalOrderByGhsid( $ghsid , $status )
    {
        $modelObj  = new OrdersMod();
        $count = $modelObj->where( [ "ghsid"=> $ghsid  , 'status'=>$status] )->count();
        return $count;
    }

    /**
     * 根据代理商id获取订单总数量
     * @param  [type] $dlsid [代理商id]
     * @param  [type] $status [状态]
     * @return [type]        [description]
     */
    public function getTotalOrderByDlsid( $dlsid , $status )
    {
        $modelObj  = new OrdersMod();
        $count = $modelObj->where( [ "dlsid"=> $dlsid  , 'status'=>$status] )->count();
        return $count;
    }


    

    

   


}

?>