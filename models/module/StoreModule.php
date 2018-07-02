<?php

namespace app\models\module;

use Yii;
use app\models\Module;

class StoreModule extends Module
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['sort'], 'filter', 'filter' => 'intval'], //此规则必须，否则就算模型里该字段没有修改，也会出现在脏属性里
            [['project_id', 'version_id', 'status', 'sort', 'creater_id'], 'integer'],
            [['encode_id'], 'string', 'max' => 10],
            [['title'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 250],
            [['encode_id'], 'unique'],
            ['title', 'validateTitle'],

            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['encode_id'], 'default', 'value'  => $this->createEncodeId(), 'on' => 'create'],
            [['status'], 'default', 'value'  => self::ACTIVE_STATUS, 'on' => 'create'],

            [['encode_id', 'project_id', 'version_id', 'title', 'status', 'creater_id'], 'required', 'on' => ['create', 'update']],

        ];

    }

    /**
     * 验证模块名是否唯一
     * @param $attribute
     */
    public function validateTitle($attribute)
    {
        $query = self::find();

        $query->andFilterWhere([
            'version_id' => $this->version_id,
            'status' => self::ACTIVE_STATUS,
            'title'  => $this->title,
        ]);

        $query->andFilterWhere([
            '<>','id', $this->id,
        ]);

        if($query->exists()){
            $this->addError($attribute, '抱歉，该模块名称已存在');
        }

    }

    /**
     * @return bool保存模块
     */
    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        if(!$this->validate()){
            return false;
        }

        if(!$this->save(false)){

            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;

    }

}
