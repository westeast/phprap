<?php

namespace app\models\project;

use Yii;
use app\models\Project;

class StoreProject extends Project
{

    /**
     * 验证规则
     */
    public function rules()
    {
        return [
            [['allow_search', 'sort'], 'filter', 'filter' => 'intval'], //此规则必须，否则就算模型里该字段没有修改，也会出现在脏属性里
            [['sort', 'allow_search', 'status', 'creater_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['encode_id'], 'string', 'max' => 10],
            [['title', 'remark'], 'string', 'max' => 250],
            [['encode_id'], 'unique'],
            ['title', 'validateTitle'],

            [['!created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['!creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!encode_id'], 'default', 'value'  => $this->createEncodeId(), 'on' => 'create'],
            [['!status'], 'default', 'value'  => self::ACTIVE_STATUS, 'on' => 'create'],

            [['!encode_id', 'title', 'allow_search', '!status', '!creater_id'], 'required', 'on' => ['create', 'update']],

        ];
    }

    /**
     * 验证项目名是否唯一
     * @param $attribute
     */
    public function validateTitle($attribute)
    {
        $query = self::find();

        $query->andFilterWhere([
            'creater_id' => Yii::$app->user->identity->id,
            'status' => Project::ACTIVE_STATUS,
            'title'   => $this->title,
        ]);

        $query->andFilterWhere([
            '<>','id', $this->id,
        ]);

        if($query->exists()){
            $this->addError($attribute, '抱歉，该项目名称已存在');
        }

    }

    /**
     * 保存项目
     * @return bool
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