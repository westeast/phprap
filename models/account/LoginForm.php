<?php

namespace app\models\account;

use Yii;
use app\models\Model;
use app\models\User;

class LoginForm extends Model
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
            ['verifyCode', 'captcha', 'captchaAction' => 'home/account/captcha'],
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

        // 调用validate方法 进行rule的校验，其中包括用户是否存在和密码是否正确的校验
        if ($this->validate()) {

            $user = User::findByEmail($this->email);


            // 校验成功后，session保存用户信息
            return Yii::$app->user->login($user, $this->rememberMe ? 60*60*24 : 0);

        } else {


            return false;
        }
    }

}
