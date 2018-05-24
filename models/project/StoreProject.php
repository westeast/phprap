<?php

namespace app\models\project;

use app\models\history\StoreHistory;
use Yii;
use yii\db\Exception;
use app\models\Project;

/**
 * This is the model class for table "doc_project".
 *
 * @property int $id
 * @property int $creater_id 创建者id
 * @property string $title 项目标题
 * @property string $remark 项目备注
 * @property int $allow_search 是否允许搜索
 * @property int $status 启用状态
 */
class StoreProject extends Project
{

    /**
     * 验证规则
     */
    public function rules()
    {
        return [
            [['creater_id', 'allow_search', 'sort'], 'integer', 'on' => ['create', 'update']],
            [['title', 'remark'], 'required', 'on' => ['create', 'update']],
            ['title', 'validateTitle'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
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

    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        try {

            if(!$this->save()){

                throw new Exception($this->getError());
            }

            // 记录日志
            $log = StoreHistory::findModel();

            if($this->scenario == 'create'){
                $log->method = 'create';
                $log->content = '创建了项目<code>' . $this->title . '</code>';

            }elseif($this->scenario == 'update'){
                $log->method = 'update';
                $log->content = '更新了项目<code>' . $this->title . '</code>';

            }

            $log->res_name = 'project';
            $log->res_id   = $this->id;
            $log->object = 'project';

            if(!$log->store()){

                throw new Exception($log->getError());
            }

            // 事务提交
            $transaction->commit();

            return true;

        } catch (Exception $e) {

            $this->addError('project', $e->getMessage());

            // 事务回滚
            $transaction->rollBack();

            return false;

        }

    }
}