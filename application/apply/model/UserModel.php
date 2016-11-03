<?php
/**
 * Created by PhpStorm.
 * User: gsy
 * Date: 2016/10/28
 * Time: 16:01
 */

namespace app\apply\model;

use think\Model;

class UserModel extends Model
{
    public $table = 'app_user';

    //自定义初始化
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * @param $name 用户名
     * @return array
     */
    public function userName($name)
    {
        if (empty($name)) {
            return false;
        }
        $result = db('user')->field('fname')->where('fname', $name)->select();
        if (!empty($result)) {
            return true;
        }
    }

    /**
     * @param $info array
     * @return int
     */
    public function addUser($info)
    {
        if (empty($info)) {
            return false;
        }
        $result = db('user')->insert($info);
        return $result;
    }

    /**
     * @param $token 激活码
     * @return 激活码有效期
     */
    public function checkTime($token)
    {
        $result = db('user')->field('ftokenexptime')->where('ftoken', $token)->select();
        if (!empty($result)) {
            return $result[0]['ftokenexptime'];
        }
    }

    /**
     * @param $token 激活码
     * @return int
     */
    public function activation($token)
    {
        $result = db('user')->where('ftoken', $token)->setField('Fstatus', 1);
        return $result;
    }

    /**
     * @param $name 用户名
     * @param $pwd 密码
     * @return int
     */
    public function login($name, $pwd)
    {
        $result = db('user')->field('fid')->where('fname', $name)->where('fpassword', $pwd)->select();
        if (isset($result[0]['fid'])) {
            return $result[0]['fid'];
        } else {
            return false;
        }

    }

    /**
     * @param $id 用户ID
     * @return int
     */
    public function email($id)
    {
        $result = db('user')->field('fstatus')->where('fid', $id)->select();
        if (empty($result[0]['fstatus'])) {
            return true;
        }
    }

    /**
     * @param $email 邮箱
     * @return array
     */
    public function checkEmail($email)
    {
        $result = db('user')->field('fid,fname,fpassword')->where('femail', $email)->select();
        if (!empty($result)) {
            $arr = array();
            foreach ($result as $k=>$v) {
                $arr = $v;
            }
            return $arr;
        } else {
            return false;
        }
    }

    /**
     * @param $name 用户名
     * @param $pwd 新密码
     * @return int
     */
    public function resetPassword($name,$pwd) {
        if (empty($name) || empty($pwd)) {
            return false;
        }
        $result = db('user')->where('fname', $name)->setField('Fpassword', $pwd);
        return $result;
    }
    
    

}

