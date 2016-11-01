<?php
namespace app\common\controller;
use think\View;
use think\Controller;

class Base extends Controller
{
    public $view;
    public function _initialize(){
        $this->view = new View();
    }
}
