<?php

/**
 * 商品数据库
 */

namespace app\index\model;
Use think\Model;
Use think\Db;
Use app\index\model\PhotoMod;
class GoodsMod extends Model
{

    // 设置当前模型对应的完整数据表名称
    protected $table = 'goods';


    const SOURCE_LOCAL  = 1 ;//商品来源 1-本地  2-又拍
    const SOURCE_YOUPAI = 2 ;//商品来源 1-本地  2-又拍
    const SHANG_JIA     = 1 ;//商品上架
    const XIA_JIA       = 2 ;//商品下架


    /**
     * 添加本地商品
     * @param [int] $[name] [<商品名称>]
     * @param [int] $[desc] [<商品描述>]
     * @param [int] $[unitprice] [<单价>]
     * @param [int] $[unit] [<单位>]
     * @param [int] $[ghsid] [供货商id]
     * @param [int] $[freighttemplateid] [运费模板id>]<第一阶段先不加>
     * @param [int] $[imgurls] [商品图片地址数组>]<第一阶段先不加>
     * @return [type] [description]
     */
    public function addLocal( $name , $desc="" ,$unitprice, $unit ,$ghsid,$freighttemplateid=""   )
    {
        $modelObj = model('GoodsMod');

        $modelObj->data( [ 
            'name'=>$name , 
            'desc'=>$desc , 
            'unitprice'=>$unitprice,
            'ghsid'=>$ghsid,
            'freighttemplateid'=>$freighttemplateid,
            'source'=>GoodsMod::SOURCE_LOCAL,
            'status'=>GoodsMod::SHANG_JIA,
            'uptime'  => date('Y-m-d H:i:m'),
            ] );

        $res = $modelObj->save();
        
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
     * 商品上架
     * @param  [int] $goodsid [商品id]
     * @return [type]            [description]
     */
    
    public function shangjia( $goodsid )
    {

        $modelObj = model('GoodsMod');

        // 1、查询该关注记录是否已经存在
        $res = $modelObj->where([ 'id'=>$goodsid  ] )->find() ; 

        if( !$res )
        {
            $obj = array( 
                "status" => -1 , 
                "desc" => "没有该商品"
            );
            return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ;die;   

        }


        $res = $modelObj->save([
            'status'  => GoodsMod::SHANG_JIA,
            'uptime'  => date('Y-m-d H:i:m'),
            ],['id' => $goodsid]);

        if( !$res ) 
        {
            $obj = array(
                'result'=>null,
                "status" => -1,
                "desc"=>"上架失败"   
            );
        }
        else
        {
            $obj =  array(
                'result'=>null,
                "status" => 0,
                "desc"=>"上架成功"   
            );
        }

       return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; 
        die;
        
    }

    /**
     * 商品下架
     * @param  [int] $goodsid [商品id]
     * @return [type]            [description]
     */
    
    public function xiajia( $goodsid )
    {

        $modelObj = model('GoodsMod');

        // 1、查询该关注记录是否已经存在
        $res = $modelObj->where([ 'id'=>$goodsid  ] )->find() ; 

        if( !$res )
        {
            $obj = array( 
                "status" => -1 , 
                "desc" => "没有该商品"
            );
            return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ;die;   

        }


        $res = $modelObj->save([
            'status'  => GoodsMod::XIA_JIA ,
            'downtime'  => date('Y-m-d H:i:m'),
            ],['id' => $goodsid]);

        if( !$res ) 
        {
            $obj = array(
                'result'=>null,
                "status" => -1,
                "desc"=>"下架失败"   
            );
        }
        else
        {
            $obj =  array(
                'result'=>null,
                "status" => 0,
                "desc"=>"下架成功"   
            );
        }

       return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; die;
        
    }

    /**
     * 更新商品信息
     * @param [int] $[goodsid] [商品id]
     * @param [int] $[name] [<商品名称>]
     * @param [int] $[desc] [<商品描述>]
     * @param [int] $[unitprice] [<单价>]
     * @param [int] $[freighttemplateid] [运费模板id>]<第一阶段先不加>
     * @return [type] [description]
     */
    public function updateGoods($goodsid , $name , $desc , $unitprice)
    {
        $data = [ 'name'=>$name , 'desc'=>$desc , 'unitprice'=>$unitprice ];
        // print_r($data);die;
        $res = DB::table('goods')->where(['id'=>$goodsid])->update( $data );
        if( $res >= 0 )
        {
            $obj = Array(
                'result'=>$res,
                "status" => 0,
                "desc"=>"更新成功"   
            );
        }
        else
        {
            $obj = Array(
                'result'=>null,
                "status" => -1,
                "desc"=>"更新失败"   
            );   
        }
        
        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; die;
        
    }

    /**
     * 更新商品已收数量  订单状态为待收款，则数量增加  订单状态为已取消，则数量减少
     * @param  [int] $goodid [商品id]
     * @param  [int] $amount [description]
     * @return [type]         [description]
     */
    public function updateGoodSoldAmount( $goodid , $amount )
    {

        $modelObj = model('GoodsMod');
        if( $amount > 0 )
            $res = $modelObj->where([ 'id'=>$goodid ])->setInc('soldamount', $amount);
        else
            $res = $modelObj->where([ 'id'=>$goodid ])->setDec('soldamount', $amount*(-1));
        
        if( $res )
        {
            $obj = array(
                'status' => 0,
                'desc'   => '更新商品已售数量成功'
            );
        }
        else
        {
            $obj = array(
                'status' => -1,
                'desc'   => '更新商品已售数量失败'
            );   
        }

        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; die;

    }

   /**
    * 根据商品id获取对应的商品数据和photo信息
    * @param  [int] $goodid [商品id]
    * @return [array]       $list  [ 
               [0] =&gt; array(4) {
                    ["id"] =&gt; NULL
                    ["name"] =&gt; NULL
                    ["unitprice"] =&gt; NULL
                    ["urls"] =&gt; NULL
                  },

                [1] =&gt; array(4) {
                    ["id"] =&gt; NULL
                    ["name"] =&gt; NULL
                    ["unitprice"] =&gt; NULL
                    ["urls"] =&gt; NULL
                  }

         *   ]
    */
    public function getGoodsById( $gid )
    {
       
        $list = Db::table('goods')->alias('g')->join('photo p' , 'g.id=p.goodid')->where(['g.id'=>$gid])->field('g.id,g.name,g.unitprice,group_concat(p.url) urls,g.desc')->select();
        return $list;

    
    }


    /**
    * 根据多个商品id获取对应的商品数据
    * @param  [array] $goodsids [商品id]
    * @return [array]          [[gid,gname,unitprice,desc,ghsid,ghsname] , [gid,gname,unitprice,desc,ghsid,ghsname]]
    */
    public function getGoodsByIds( $goodsids )
    {
        // print_r( $goodsids );

        $list = Db::table('goods')->alias('g')->join('gonghuoshang ghs' , 'g.ghsid=ghs.id' , 'left' )->where('g.id' ,'in' , $goodsids )->field('g.id,g.name,g.desc,g.unitprice,ghs.id as ghsid,ghs.name as ghsname')->select();
        return $list ;
        // $list = Db::table('goods')->alias('g')->join('gonghuoshang ghs' , 'g.ghsid=ghs.id' , 'left' )->where('g.id' ,'in' , $goodsids )->field('group_concat(g.id) gids,group_concat(g.name) gnames,group_concat(g.desc) gdescs,group_concat(g.unitprice) gunitprices,ghs.id as ghsid,ghs.name as ghsname')->group('g.ghsid')->select();

        // foreach( $list as $key=>$val )
        // {
        //     $list[$key]['gids'] = explode("," , $val['gids']);
        //     $list[$key]['gnames'] = explode("," , $val['gnames']);
        //     $list[$key]['gunitprices'] = explode("," , $val['gunitprices']);
        //     $list[$key]['goods'] = array();
        // }

        // foreach( $list as $key=>$val )
        // {
        //     // array_push( $list[$key]['goods'] ,  ); 
        // }

        // dump( $list );die;
        // return $list;
        // die;
      
    
    }
    

     /**
     * 根据供货商id，获取商品列表
     * @param  [type] $ghsid [供货商id]
     * @param  [type] $type [1-已上架  2-已下架]
     * @param  [type] $currentpage [当前页数]
     * @param  [type] $pagesize [每页显示数量]
     * @return [Array]        [空数组-无商品数据  或者  二维数组]
     */
    public function getGoodsListByGhsId( $ghsid , $type,$currentpage,$pagesize )
    {
        $con = array("ghsid"=>$ghsid , "status"=>$type) ;
        $count  = Db::table($this->table)->where( $con )->count();
        if( 0 == $count )
        {
            return [];die;
        }

   
        $res = Db::table('goods')->alias('g')->join('photo p' , 'g.id=p.goodid', 'left')->where($con)->field('g.id,g.name,g.unitprice,group_concat(p.url) urls')->group('g.id')->page($currentpage,$pagesize)->select();

        foreach( $res as $key=>$val )
        {
            $res[$key]['urls'] = explode("," , $val['urls']);
        }
        return $res ;


    }


     /**
     * 根据供货商id，获取商品列表,包括本地的和又拍的
     * @param  [type] $ghsid [供货商id]
     * @param  [type] $source [来源  0-所有  1-本地 2-又拍 ]
     * @return [type]        [description]
     */
    // public function getGoodsListByGhsIdAndSource( $ghsid  ,  $source = 0)
    // {
    //     $modelObj = model('GoodsMod');
    //     $con = array("ghsid"=>$ghsid) ;
    //     if( 0 != $source ) $con['source'] = $source;

    //     $res = $modelObj->where( $con )->column('id,name,desc,unitprice,soldamount,source,youpaialbumid,status,uptime,downtime,ghsid,freighttemplateid');

    //     if( !$res )
    //     {
    //          $obj = array(
    //                 'result'=>null,
    //                 "status" => 0,
    //                 "desc"=>"无数据"
    //             );
    //     }
    //     else
    //     {
    //         $obj = array(
    //             'result'=>[],
    //             "status" => 0,
    //             "desc"=>"查询成功"
    //         );

    //         foreach( $res as $value )
    //         {
    //             array_push( $obj['result'] , $value );
    //         }
    //     }

    //     return json_encode( $obj , JSON_UNESCAPED_UNICODE );die;


    // }




}

?>