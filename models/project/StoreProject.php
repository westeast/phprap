<?php

namespace app\models\project;

use app\models\projectLog\StoreLog;
use Yii;
use app\models\Project;
use app\models\history\StoreHistory;

class StoreProject extends Project
{

    /**
     * 验证规则
     */
    public function rules()
    {
        return [
            [['allow_search', 'sort'], 'filter', 'filter' => 'intval', 'on' => ['create', 'update']],
            [['!creater_id', 'allow_search', 'sort', '!status'], 'integer', 'on' => ['create', 'update']],
            [['title', 'remark'], 'string', 'max' => 250, 'on' => ['create', 'update']],
            ['title', 'validateTitle', 'on' => ['create', 'update']],
            [['!created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['!creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!creater_id', 'title', 'allow_search', '!status'], 'required', 'on' => ['create', 'update']],
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
            'creater_id' => $this->creater_id,
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

        $this->status = self::ACTIVE_STATUS;

        if(!$this->validate()){
            return false;
        }

        // 判断是否有更新
        $oldAttributes   = $this->getOldAttributes();
        $dirtyAttributes = $this->getDirtyAttributes();

        if(!$dirtyAttributes){
            return true;
        }

        if(!$this->save(false)){
            $transaction->rollBack();
            return false;
        }

        // 记录日志
        $log = StoreLog::findModel();

        if($this->scenario == 'create'){
            $log->method  = 'create';
            $log->content = '创建了 项目 <code>' . $this->title . '</code>';

        }elseif($this->scenario == 'update'){

            $log->method  = 'update';

            $find = [self::ALLOW_SEARCH, self::FORBID_SEARCH];

            $replace = ['允许','禁止'];

            $oldAttributes['allow_search']   = str_replace($find, $replace, $oldAttributes['allow_search']);
            $dirtyAttributes['allow_search'] = str_replace($find, $replace, $dirtyAttributes['allow_search']);

            $log->content = $this->getUpdateContent($oldAttributes, $dirtyAttributes);

        }

        $log->project_id  = $this->id;
        $log->object_name = 'project';
        $log->object_id   = $this->id;

        if(!$log->store()){
            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;
    }

}