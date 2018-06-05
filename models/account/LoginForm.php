<?php

namespace app\models\account;

use app\models\Config;
use app\models\LoginLog;
use app\models\loginLog\StoreLog;
use wsl\ip2location\Ip2Location;
use Yii;
use app\models\User;

class LoginForm extends User
{

    public $email;
    public $password;
    public $verifyCode;
    public $rememberMe = 1;

    public function rules()
    {
        return [
            [['email', 'verifyCode'], 'filter', 'filter' => 'trim'],
            ['email', 'required', 'message' => '登录邮箱不能为空'],
            ['email', 'email','message' => '邮箱格式不合法'],
            ['rememberMe', 'boolean'],
            ['password', 'required', 'message' => '密码不可以为空'],
            ['password', 'validatePassword'],
            ['verifyCode', 'required', 'message' => '验证码不能为空'],
            ['verifyCode', 'captcha', 'captchaAction' => 'home/captcha/login'],
        ];
    }

    public function validatePassword($attribute)
    {

        if (!$this->hasErrors()) {
            $user = User::findByEmail($this->email);

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '登录邮箱或密码错误');
            }
        }
    }

    public function login()
    {

        if(!$this->validate()){
            return false;
        }

        $user = User::findByEmail($this->email);

        // 记录日志
        $loginLog = new StoreLog();

        $loginLog->user_id = $user->id;
        $loginLog->user_name = $user->name;
        $loginLog->user_email = $user->email;

        if(!$loginLog->store()){
            return false;
        }

        $login_keep = config('login_keep', 'safe');

        return Yii::$app->user->login($user, 60*60*$login_keep);

    }

}
