<?php

/**
 * 照片数据库
 */

namespace app\index\model;
Use think\Model;
Use think\Db;
Use think\Config;
class PhotoMod extends Model
{

    // 设置当前模型对应的完整数据表名称
    protected $table = 'photo';


   
    /**
     * 添加本地图片
     * @param [int] $[goodid] [<商品id>]
     * @param [int] $[imgurls] [<图片名数组>]
     * @return [type] [description]
     */
    public function addLocalImage( $goodid ,  $imgurls)
    {
       $modelObj = model('PhotoMod');

       $list = [] ;
       foreach( $imgurls as $imgurl )
       {    
            array_push( $list , [ 
                'goodid'=>$goodid , 
                'code'=>$this->getHashValue( Config::get('ImageBaseURL').$imgurl ) , 
                'url'=>$imgurl, 
                'uploadtime'=>date('Y-m-d H:i:s')] 
            );
       } 



        $res = $modelObj->saveAll( $list );

        if( $res !== false )
        {
            $obj = array( 
                "status" => 0 , 
                "result" => $res ,  //插入的记录数组
                "desc" => "添加成功"
            );
        }
        else
        {
            $obj = array( 
                "status" => -1 ,  //插入数据错误
                "desc" => "添加失败"
            );


            //生成code
            
        }
        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ;die;   

    }

    /**
     * 根据图片的hash值，获取图片数据
     * @param  [String] $hash [图片hash值]
     * @return [Array]       [图片对应的id数组]
     */
    public function getImageByHash( $hash )
    {
        $modelObj = model('PhotoMod');

         $res = $modelObj->where(["code"=>$hash])->column('id');

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
     * 根据图片的hash值，获取对应的商品id数组
     * @param  [String] $hash [图片hash值]
     * @return [Array]       [商品对应的id数组]
     */
    public function getGoodsIdByImgHash( $hash )
    {
        $modelObj = model('PhotoMod');

         $res = $modelObj->where(["code"=>$hash])->group('goodid')->column('goodid');

        

        if( !$res )
        {
             $obj = array(
                    'result'=>null,
                    "status" => 0,
                    "desc"=>"图片未找到"
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
     * 根据商品id查找对应的图片
     * @param  [int] $goodid [商品id数组]
     * @return [type]          [description]
     */
    public function getImagesByGoodId( $goodid )
    {
        // $modelObj = model('PhotoMod');

        // $list = $modelObj->where( [ "goodid"=> $goodid ] )->column('url');
        $list = Db::table('photo')->where( [ "goodid"=> $goodid ] )->field('url')->select();


        // print_r( $list[0]['url'] );
        $res = [];
        foreach( $list as $value )
        {
            array_push( $res , $value['url'] );
        }

        
        // print_r("res");
        // print_r($res);
        //die;

        if( !$list )
        {
            $obj = array(
                'result'=>[],
                "status" => 0,
                "desc"=>"没找到商品图片" 
            );
        }
        else
        {
            $obj = array(
                'result'=>$res,
                "status" => 0,
                "desc"=>"找到了" 
            );

            // foreach($list as $url)
            // {
            //     array_push( $obj['result'] , $url );
            // }

        }
        // print_r("db d ata");
        // print_r( $obj );die;
        return json_encode( $obj , JSON_UNESCAPED_UNICODE ) ; die;
    }

    /*图片处理 begin  */

    public static function run($src1, $src2) 
    {
       
        static $self;
        if(!$self) $self = new static;
        
        // if(!is_file($src1) || !is_file($src2)) exit(self::FILE_NOT_FOUND);
        if(!is_file($src1) || !is_file($src2)) exit("FILE_NOT_FOUND");
 
        $hash1 = $self->getHashValue($src1);
        $hash2 = $self->getHashValue($src2);
        
        //原版本
        // if(strlen($hash1) !== strlen($hash2)) return false;
        
        // $count = 0;
        // $len = strlen($hash1);
        // for($i = 0; $i < $len; $i++) if($hash1[$i] !== $hash2[$i]) $count++;
        // return $count <= 10 ? true : false;
        // 
        
        //修改为绝对相同
        $len = strlen($hash1);
        if(strlen($hash1) !== strlen($hash2)) return false;
        for($i = 0; $i < $len; $i++) if($hash1[$i] !== $hash2[$i]) return false;
        return true ;

    }
    
    public function getImage($file) 
    {
        
        $extname = pathinfo($file, PATHINFO_EXTENSION);
        if(!in_array($extname, ['jpg','jpeg','png','gif'])) exit(self::FILE_EXTNAME_ILLEGAL);
        $img = call_user_func('imagecreatefrom'. ( $extname == 'jpg' ? 'jpeg' : $extname ) , $file);
        return $img;
        
    }
    
    /**
     * 获取图片hash值
     * @param  [type] $file [图片名字]
     * @return [type]       [description]
     */
    public function getHashValue($file) 
    {
        
        $w = 8;
        $h = 8;
        $img = imagecreatetruecolor($w, $h);
        list($src_w, $src_h) = getimagesize($file);
        $src = $this->getImage($file);
        imagecopyresampled($img, $src, 0, 0, 0, 0, $w, $h, $src_w, $src_h);
        imagedestroy($src);
        
        $total = 0;
        $array = array();
        for( $y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $gray = (imagecolorat($img, $x, $y) >> 8) & 0xFF;
                if(!isset($array[$y])) $array[$y] = array();
                $array[$y][$x] = $gray;
                $total += $gray;
            }
        }
        
        imagedestroy($img);
        
        $average = intval($total / ($w * $h * 2));
        $hash = '';
        for($y = 0; $y < $h; $y++) {
            for($x = 0; $x < $w; $x++) {
                $hash .= ($array[$y][$x] >= $average) ? '1' : '0';
            }
        }
        
        // var_dump($hash);
        return $hash;
        
    }

    /*图片处理 end  */
  

}

?>