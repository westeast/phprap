<?php

namespace app\models\version;

use app\models\history\StoreHistory;
use app\models\Version;
use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "doc_version".
 *
 * @property int $id
 * @property int $project_id 项目id
 * @property int $parent_id 父级项目id
 * @property string $name 版本号
 * @property string $token 版本token
 * @property string $remark 备注信息
 * @property int $status 版本状态
 * @property int $creater_id 版本创建者id
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class StoreVersion extends Version
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['project_id', 'parent_id', 'name'], 'required', 'on' => ['create']],
            [['project_id', 'name'], 'required', 'on' => ['update']],
            [['remark'], 'string', 'max' => 250],
            ['name', 'validateName'],
            [['project_id', 'creater_id', 'parent_id'], 'integer', 'on' => ['create', 'update']],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['token'], 'default', 'value' => mt_rand(1000, 9999) . date('His'), 'on' => 'create'],
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
            'status' => Version::ACTIVE_STATUS,
            'name'   => $this->name,
        ]);

        $query->andFilterWhere([
            '<>','id', $this->id,
        ]);

        if($query->exists()){
            $this->addError($attribute, '抱歉，该版本号已存在');
        }

    }

    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        if(!$this->save()){
            $transaction->rollBack();
            return false;
        }

        // 记录日志
        $log = StoreHistory::findModel();

        if($this->scenario == 'create'){
            $log->method  = 'create';
            $log->content = '创建了版本<code>' . $this->name . '</code>';

        }elseif($this->scenario == 'update'){
            $log->method  = 'update';
            $log->content = '更新了版本<code>' . $this->name . '</code>';

        }

        $log->res_name  = 'project';
        $log->res_id    = $this->project_id;
        $log->object    = 'version';
        $log->object_id = $this->id;

        if(!$log->store()){
            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;
    }

}
