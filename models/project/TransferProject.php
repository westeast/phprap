<?php

namespace app\models\project;

use app\models\Project;
use Yii;

class TransferProject extends Project
{

    public $user_id;
    public $password;

    /**
     * 验证规则
     * @return array
     */
    public function rules()
    {
        return [
            ['user_id', 'required', 'message'  => '请选择成员'],
            ['password', 'required', 'message' => '登录密码不可以为空'],
            ['password', 'validatePassword'],
            ['user_id', 'validateJoiner'],
        ];
    }

    /**
     * 验证密码是否正确
     * @param $attribute
     */
    public function validatePassword($attribute)
    {

        if (!$this->hasErrors()) {

            $user = Yii::$app->user->identity;

            if (!$user || !$user->validatePassword($this->password)) {

                $this->addError($attribute, '登录密码验证失败');
            }

        }
    }

    /**
     * 验证选择用户是不是项目成员
     * @param $attribute
     */
    public function validateJoiner($attribute)
    {

        if (!$this->hasErrors()) {

            if(!$this->isJoiner($this->user_id)){
                $this->addError($attribute, '抱歉，该用户不是该项目成员，无法转让');
            }

        }
    }

    /**
     * 转让项目
     * @return bool|mixed
     */
    public function transfer()
    {

        $this->creater_id = $this->user_id;

        //todo 记录日志

        if(!$this->validate()){
            return false;
        }

        if($this->save(false)){
            return true;
        }

        return $this->getError();

    }

}
