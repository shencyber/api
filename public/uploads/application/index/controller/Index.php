<?php
namespace app\index\controller;
Use think\Controller;
Use app\index\model\Ghs;
Use app\index\model\User;

class Index extends Controller
{
    public function index()
    {
    	print_r( request()->get() );die();
    	return ( json_encode( request()->get() ) );
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
