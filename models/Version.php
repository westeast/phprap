<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_version".
 *
 * @property int $id
 * @property int $project_id 项目id
 * @property int $parent_id 父级版本id
 * @property int $creater_id 版本创建者id
 * @property string $token 版本token
 * @property string $name 版本号
 * @property string $remark 备注信息
 * @property int $status 版本状态
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Version extends Model
{

    const ACTIVE_STATUS  = 10; //启用状态
    const DISABLE_STATUS = 20; //禁用状态
    const DELETED_STATUS = 30; //删除状态

    /**
     * 绑定数据表
     */
    public static function tableName()
    {
        return '{{%version}}';
    }

    /**
     * 默认验证规则
     * @return array
     */
    public function rules()
    {
        return [
            [['project_id', 'parent_id', 'creater_id', 'token', 'name'], 'required'],
            [['project_id', 'parent_id', 'creater_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['token'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 10],
            [['remark'], 'string', 'max' => 250],
        ];
    }

    /**
     * 字段字典
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => '项目',
            'parent_id' => '父级版本',
            'creater_id' => '创建者',
            'token' => 'Token',
            'name'  => '版本号',
            'remark' => '版本描述',
            'status' => '版本状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 获取创建者
     * @return \yii\db\ActiveQuery
     */
    public function getCreater()
    {
        return $this->hasOne(User::className(),['id'=>'creater_id']);
    }

    /**
     * 获取项目
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(),['id'=>'project_id']);
    }

    /**
     * 获取父级版本
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Version::className(),['id'=>'parent_id']);
    }

    /**
     * 获取项目模块
     * @return \yii\db\ActiveQuery
     */
    public function getModules()
    {
        $filter = [
            'status' => Module::ACTIVE_STATUS
        ];

        $sort = [
            'sort' => SORT_DESC,
            'id'   => SORT_DESC
        ];

        return $this->hasMany(Module::className(), ['version_id' => 'id'])->where($filter)->orderBy($sort);
    }

    /**
     * 创建还不存在的下个版本号
     * @return string
     */
    public function getNextName()
    {
        // 获取项目最新的版本
        $version = $this->project->lastVersion;

        $data = explode('.', $version->name);

        $key  = count($data)-1;

        $data[$key] = $data[$key] + 1;

        return implode('.', $data);
    }

    /**
     * 检测是否有项目某项权限，如编辑、删除
     * @param $rule
     * @return bool
     */
    public function hasRule($rule)
    {
        return false;
    }

}
