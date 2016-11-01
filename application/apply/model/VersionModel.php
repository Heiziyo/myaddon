<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/13
 * Time: 17:52
 */

namespace app\apply\model;

use think\Model;

class VersionModel extends Model{
    protected $table = "app_versionmanagement";
    protected $createTime = 'dateline';
    //自定义初始化
    protected function initialize()
    {

        parent::initialize();

    }
    public function addData($data){
        if (empty($data)){
            return false;
        }
        $result = $this->data($data)->save();
        if (!empty($result)){
            return $this->id;
        }
    }

}