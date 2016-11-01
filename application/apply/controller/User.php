<?php
/**
 * Created by PhpStorm.
 * User: gsy
 * Date: 2016/10/28
 * Time: 09:11
 */

namespace app\apply\controller;

use app\apply\model\UserModel;
use app\common\controller\Base;
use app\common\helper\VerifyHelper;
use think\Request;

class User extends Base
{
    public $user_model = null;

    function __construct(Request $request)
    {
        session_start();
        $this->user_model = new UserModel();
        parent::__construct($request);
    }

    public function register()
    {
        return $this->view->fetch();
    }

    public function login()
    {
        return $this->view->fetch();
    }

    public function forget()
    {
        return $this->view->fetch();
    }

    /**
     * 显示验证码图片
     */
    public function verify()
    {
        VerifyHelper::verify();
    }

    /**
     * 注册流程
     */
    public function doRegister()
    {
        $code = isset($_POST['code']) ? trim($_POST['code']) : '';
        $post['Fname'] = isset($_POST['name']) ? stripslashes(trim($_POST['name'])) : '';
        $post['Femail'] = isset($_POST['email']) ? trim($_POST['email']) : '';
        //验证密码
        if (strlen($_POST['password']) < 6) {
            $this->error('登录密码最少6个字符哟~');
        }
        $post['Fpassword'] = isset($_POST['password']) ? md5(trim($_POST['password'])) : '';
        //注册时间
        $post['Fregtime'] = time();
        //创建激活码
        $post['Ftoken'] = md5($post['Fname'] . $post['Fpassword'] . $post['Fregtime']);
        //激活码过期时间限制
        $post['FtokenExptime'] = time() + 3600 * 24;

        //验证用户名
        if (strlen($post['Fname']) < 3) {
            $this->error('用户名最少3个字符哟~');
        }
        $check_name = $this->user_model->userName($post['Fname']);
        if ($check_name) {
            $this->error('该用户名已被注册，换一个吧~');
        }
        //验证邮箱
        if (!preg_match('/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ ', $post['Femail'])) {
            $this->error('邮箱格式填写不正确！');
        }
        //验证验证码
        $check_code = VerifyHelper::check($code);
        if (!$check_code) {
            $this->error('验证码错误');
        }
        //创建注册信息
        $add_user = $this->user_model->addUser($post);
        if ($add_user) {
            require_once 'class.phpmailer.php';
            include 'class.smtp.php';
            $mail = new \PHPMailer();
            $mail->IsSMTP();
            //smtp需要鉴权 这个必须是true
            $mail->SMTPAuth = true;
            // 设置邮件的字符编码，若不指定，则为'UTF-8'
            $mail->CharSet = 'UTF-8';
            $mail->IsHTML(true);
            // 设置邮件正文
            $mail->Body = "亲爱的" . $post['Fname'] . ":<br/>欢迎您加入 LvyeCMS! 开放平台，您的账号需要邮箱认证，点击下面链接进行认证：<br/> <a href='http://" . $_SERVER["HTTP_HOST"] . "/apply/user/active/verify/" . $post['Ftoken'] . "' target='_blank'>http://" . $_SERVER["HTTP_HOST"] . "/apply/user/active/verify/" . $post['Ftoken'] . "</a><br/>如果链接无法点击，请完整拷贝到浏览器地址栏里直接访问，该链接24小时内有效。
                                                    邮件服务器自动发送邮件请勿回信!";
            // 设置发件人邮箱
            $mail->From = "gsy@alvye.cn";
            // 设置发件人名字
            $mail->FromName = 'LvyeCMS';
            // 设置邮件标题
            $mail->Subject = '用户账号激活';
            // 设置SMTP服务器。
            $mail->Host = 'smtp.exmail.qq.com';
            //设置使用ssl加密方式登录鉴权
            $mail->SMTPSecure = 'ssl';
            // SMTP服务器的端口号
            $mail->Port = 465;
            // 设置用户名和密码。
            $mail->Username = 'gsy@alvye.cn';
            $mail->Password = 'Gsy123';
            //收件人地址
            $mail->AddAddress($post['Femail']);
            if (!$mail->Send()) {
                $this->error("发送失败", "/apply/user/register");
            } else {
                $this->error("注册成功，快去邮箱验证吧~", "/apply/user/login");
            }
        }
    }

    /**
     * 邮箱激活
     */
    public function active()
    {
        $verify = Request::instance()->param("verify");
        //激活码有效期
        $check_time = $this->user_model->checkTime($verify);
        $now_time = time();
        if ($now_time > $check_time) {
            $this->error("您的激活码已过期，请登录您的账号重新发送激活邮件", "/apply/user/register");
        } else {
            $activation = $this->user_model->activation($verify);
            if ($activation) {
                $this->error("激活成功！", "/apply/user/login");
            }
        }
    }

    /**
     * 登录流程
     */
    public function doLogin()
    {
        $code = isset($_POST['code']) ? trim($_POST['code']) : '';
        $post['Fname'] = isset($_POST['name']) ? stripslashes(trim($_POST['name'])) : '';
        $post['Fpassword'] = isset($_POST['password']) ? md5(trim($_POST['password'])) : '';
        //验证验证码
        $check_code = VerifyHelper::check($code);
        if (!$check_code) {
            $this->error('验证码错误');
        }
        //匹配用户名密码
        $user_id = $this->user_model->login($post['Fname'], $post['Fpassword']);
        if ($user_id) {
            if ($this->user_model->email($user_id)) {
                $this->error('您的邮箱还未验证，快去邮箱验证吧~');
            } else {
                $_SESSION['user'] = $post['Fname'];
                $this->error('登录成功', 'apply/index/index');
            }
        } else {
            $this->error('用户信息填写错误！');
        }
    }

    public function out()
    {
        unset($_SESSION['user']);
        $this->error('退出登录', '/apply/user/login');
    }
}