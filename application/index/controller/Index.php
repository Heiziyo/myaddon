<?php
namespace app\index\controller;
use app\common\controller\Base;
use think\Request;

class Index  extends Base
{

    function __construct(Request $request)
    {
        session_start();
        parent::__construct($request);
    }

    public function index()
    {


        
        return $this->view->fetch('index');

    }
}
