<?php
namespace app\index\controller;
Use think\Controller;
Use app\index\model\Ghs;


class GhsCon extends Controller
{
    public function index()
    {


        return 'ok'; 
    }

    public function  hello()
    {
//        return 'this is hello fun';

        $ghs  = new Ghs();//return $ghs;
        return $ghs->aa();



    }
}

?>
