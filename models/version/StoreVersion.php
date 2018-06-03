<?php

namespace app\models\version;

use app\models\projectLog\StoreLog;
use app\models\Version;
use Yii;

class StoreVersion extends Version
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['remark'], 'string', 'max' => 250],
            ['name', 'validateName'],
            [['project_id', 'creater_id', 'parent_id'], 'integer', 'on' => ['create', 'update']],

            [['!created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['!creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!encode_id'], 'default', 'value'  => $this->createEncodeId(), 'on' => 'create'],
            [['!status'], 'default', 'value'  => self::ACTIVE_STATUS, 'on' => 'create'],

            [['!encode_id', '!project_id', '!creater_id', 'name', '!status'], 'required', 'on' => ['create', 'update']],
        ];
    }

    /**
     * 验证版本号是否唯一
     * @param $attribute
     */
    public function validateName($attribute)
    {
        $query = self::find();

        $query->andFilterWhere([
            'project_id' => $this->project_id,
            'status' => self::ACTIVE_STATUS,
            'name'   => $this->name,
        ]);

        $query->andFilterWhere([
            '<>','id', $this->id,
        ]);

        if($query->exists()){
            $this->addError($attribute, '抱歉，该版本号已存在');
        }

    }

    /**
     * 保存版本
     * @return bool
     */
    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        if($this->scenario == 'create'){
            $this->parent_id = self::findModel(['encode_id' => $this->parent_id])->id;
        }

        if(!$this->validate()){
            return false;
        }

        // 判断是否有更新
        $oldAttributes   = $this->getOldAttributes();
        $dirtyAttributes = $this->getDirtyAttributes();

        if(!$dirtyAttributes){
            return true;
        }

        if(!$this->save()){
            $transaction->rollBack();
            return false;
        }

        // 记录日志
        $log = StoreLog::findModel();

        if($this->scenario == 'create'){

            $log->method  = 'create';
            $log->version_name = $this->name;

            $log->content = '创建了 版本 <code>' . $this->name . '</code>';

        }elseif($this->scenario == 'update'){
            $log->method  = 'update';

            $log->version_name = $oldAttributes['name'];

            $log->content = $this->getUpdateContent($oldAttributes, $dirtyAttributes, '版本V' . $oldAttributes['name']);

        }

        $log->project_id   = $this->project_id;
        $log->version_id   = $this->id;
        $log->object_name  = 'version';
        $log->object_id    = $this->id;

        if(!$log->store()){
            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;
    }

}
