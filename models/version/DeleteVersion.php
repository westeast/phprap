<?php
/**
 * 删除版本模型
 */
namespace app\models\version;

use app\models\Version;
use Yii;

class DeleteVersion extends Version
{

    public $password;

    public function rules()
    {
        return [
            ['password', 'required', 'message' => '登录密码不可以为空'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute)
    {

        if (!$this->hasErrors()) {

            $user = Yii::$app->user->identity;

            if (!$user || !$user->validatePassword($this->password)) {

                $this->addError($attribute, '登录密码验证失败');
            }

        }
    }

    public function delete()
    {

        // todo 记录日志
        $this->status = self::DELETED_STATUS;

        if(!$this->validate()){
            return false;
        }

        if($this->save(false)){
            return true;
        }

        return false;

    }

}
