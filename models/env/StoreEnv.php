<?php
namespace app\models\env;

use app\models\projectLog\StoreLog;
use Yii;
use app\models\Env;


class StoreEnv extends Env
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['sort'], 'filter', 'filter' => 'intval'], //此规则必须，否则就算模型里该字段没有修改，也会出现在脏属性里
            [['status', 'project_id', 'creater_id'], 'integer'],
            [['encode_id', 'name'], 'string', 'max' => 10],
            [['title'], 'string', 'max' => 50],
            [['base_url'], 'string', 'max' => 250],
            [['encode_id'], 'unique'],

            [['!created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['!creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!encode_id'], 'default', 'value'  => $this->getEncodeId(), 'on' => 'create'],
            [['!status'], 'default', 'value'  => self::ACTIVE_STATUS, 'on' => 'create'],

            [['!encode_id', 'title', 'name', 'base_url', '!project_id', '!creater_id'], 'required', 'on' => ['create', 'update']],

        ];
    }

    /**
     * 保存环境
     * @return bool
     */
    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        $this->base_url = trim($this->base_url, '/');

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
            $log->content = '创建了 环境 <code>' . $this->title . '</code>';

        }elseif($this->scenario == 'update'){

            $log->method  = 'update';

            $log->content = $this->getUpdateContent($oldAttributes, $dirtyAttributes, $oldAttributes['title']);

        }

        $log->project_id  = $this->project_id;
        $log->object_name = 'env';
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
