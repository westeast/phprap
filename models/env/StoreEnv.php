<?php
namespace app\models\env;

use app\models\projectLog\StoreLog;
use Yii;
use app\models\Env;

/**
 * This is the model class for table "doc_env".
 *
 * @property int $id
 * @property string $name 环境标识
 * @property string $title 环境名
 * @property string $domain 环境域名
 * @property int $status 启用状态 10:正常 20:删除
 * @property int $project_id 项目id
 * @property int $creater_id 创建者id
 * @property string $created_at
 * @property string $updated_at
 */
class StoreEnv extends Env
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['name', 'title', 'domain', '!project_id'], 'required', 'on' => ['create', 'update']],
            [['status', 'project_id', 'creater_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 10],
            [['title'], 'string', 'max' => 50],
            [['domain'], 'string', 'max' => 250],
            [['domain'], 'url'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],

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

        $this->domain = trim($this->domain, '/');

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
            $log->content = '创建了 环境 <code>' . $this->title . '</code>';

        }elseif($this->scenario == 'update'){

            $log->method  = 'update';

            $log->content = $this->getUpdateContent($oldAttributes, $dirtyAttributes);

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
