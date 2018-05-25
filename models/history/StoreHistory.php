<?php

namespace app\models\history;

use app\models\History;
use Yii;

class StoreHistory extends History
{

    /**
     * 验证规则
     */
    public function rules()
    {
        return [
            [['res_name', 'res_id', 'user_id', 'user_name', 'user_email', 'method', 'object', 'object', 'content'], 'required'],
            [['res_id', 'user_id', 'object_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['res_name', 'method'], 'string', 'max' => 10],
            [['user_name'], 'string', 'max' => 50],
            [['user_email'], 'string', 'max' => 50],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
        ];
    }

    public function store()
    {

        $this->user_id    = Yii::$app->user->identity->id;
        $this->user_name  = Yii::$app->user->identity->name;
        $this->user_email = Yii::$app->user->identity->email;

        if(!$this->validate()){
            return false;
        }

        if($this->save(false)){
            return true;
        }

        return false;

    }
}