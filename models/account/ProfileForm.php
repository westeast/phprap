<?php

namespace app\models\account;

use Yii;
use app\models\User;

class ProfileForm extends User
{

    public $name;
    public $email;
    public $password;

    public function rules()
    {

        return [
            [['name', 'email'], 'filter', 'filter' => 'trim'],
            ['name', 'required', 'message' => '用户昵称不可以为空'],
            ['name', 'string', 'min' => 2, 'max' => 50, 'message' => '用户昵称至少包含2个字符，最多50个字符'],
            ['email', 'required', 'message' => '登录邮箱不能为空'],
            ['email', 'email','message' => '邮箱格式不合法'],
            ['email', 'validateEmail'],
        ];
    }

    /**
     * 验证登录邮箱是否唯一
     * @param $attribute
     */
    public function validateEmail($attribute)
    {

        $query = self::find();

        $query->andFilterWhere([
            'email' => $this->email,
        ]);

        $query->andFilterWhere([
            '<>','id', Yii::$app->user->identity->id
        ]);

        if($query->exists()){
            $this->addError($attribute, '抱歉，该邮箱已被注册');
        }

    }

    public function store()
    {

        if (!$this->validate()) {
            return false;
        }

        $user = Yii::$app->user->identity;

        $user->name   = $this->name;
        $user->email  = $this->email;

        $this->password && $user->setPassword($this->password);
        $user->generateAuthKey();

//        return true;

        if($user->save()){

            return true;

        }

        return false;
    }

}
