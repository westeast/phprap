<?php
namespace app\models\env;

use Yii;
use yii\db\Exception;
use app\models\Env;
use app\models\history\StoreHistory;

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
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],

        ];
    }

    /**
     * 验证规则
     * @return bool
     */
    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        try {

            $this->domain = trim($this->domain, '/');

            if(!$this->save()){
                throw new Exception($this->getError());
            }

            // 记录日志
            $log = StoreHistory::findModel();

            if($this->scenario == 'create'){
                $log->method = '创建';

            }elseif($this->scenario == 'update'){
                $log->method = '更新';

            }

            $log->res_name = 'version';
            $log->res_id   = $this->id;
            $log->object   = 'env';
            $log->content  = $log->method . '了环境<code>' . $this->name . '</code>';

            if(!$log->store()){

                throw new Exception($log->getError());
            }

            // 事务提交
            $transaction->commit();

            return true;

        } catch (Exception $e) {

            $this->addError('env', $e->getMessage());

            // 事务回滚
            $transaction->rollBack();

            return false;

        }

    }

}
