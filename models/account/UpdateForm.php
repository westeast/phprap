<?php

namespace app\models\account;

use app\models\Config;
use Yii;
use app\models\User;

class UpdateForm extends User
{

    public $user_id;
    public $name;
    public $email;
    public $password;

    public function rules()
    {
        return [
            [['user_id'], 'filter', 'filter' => 'intval'],
            [['name', 'email'], 'filter', 'filter' => 'trim'],
            ['name', 'required', 'message' => '用户昵称不可以为空'],
            ['name', 'string', 'min' => 2, 'max' => 50, 'message' => '用户昵称至少包含2个字符，最多50个字符'],
            ['email', 'required', 'message' => '登录邮箱不能为空'],
            ['email', 'email','message' => '邮箱格式不合法'],
            ['email', 'validateEmail'],
            ['password', 'required', 'message' => '密码不可以为空', 'on' => 'password'],
        ];
    }

    public function validateEmail($attribute)
    {

        if (!$this->hasErrors()) {

            $query = self::find();

            $query->andFilterWhere([
                'status' => User::ACTIVE_STATUS,
                'email'  => $this->email,
            ]);

            $query->andFilterWhere([
                '<>','id', $this->user_id,
            ]);

            if($query->exists()){
                $this->addError($attribute, '抱歉，该邮箱已被注册');
            }

        }
    }

    public function store()
    {

        $user = User::findModel($this->user_id);

        if(!$user){
            return false;
        }

        $user->name = $this->name;
        $user->email = $this->email;

        if($this->password){
            $this->scenario = 'password';

            $user->setPassword($this->password);
            $user->generateAuthKey();
        }

        if (!$user->validate()) {
            return false;
        }

        if(!$user->save(false)){

            return false;
        }

        return true;
    }

}
