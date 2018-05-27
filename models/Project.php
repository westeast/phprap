<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_project".
 *
 * @property int $id
 * @property string $encode_id 加密id
 * @property string $title 项目名称
 * @property string $remark 项目描述
 * @property int $sort 项目排序
 * @property int $allow_search 搜索状态
 * @property int $status 项目状态
 * @property int $creater_id 创建者id
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Project extends Model
{

    const ALLOW_SEARCH  = 10; //允许搜索
    const FORBID_SEARCH = 20; //禁止搜索

    /**
     * 当前版本号
     * @var
     */
    public $current_version;

    /**
     * 绑定数据表
     */
    public static function tableName()
    {
        return '{{%project}}';
    }

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['sort', 'allow_search', 'status', 'creater_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['encode_id'], 'string', 'max' => 10],
            [['title', 'remark'], 'string', 'max' => 250],
            [['encode_id'], 'unique'],

            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['status'], 'default', 'value'  => self::ACTIVE_STATUS],

            [['encode_id', 'title', 'allow_search', 'status', 'creater_id'], 'required'],
        ];

    }

    /**
     * 字段字典
     */
    public function attributeLabels()
    {

        return [
            'id' => 'ID',
            'encode_id' => '加密id',
            'title' => '项目名称',
            'remark' => '项目描述',
            'sort' => '项目排序',
            'allow_search' => '搜索状态',
            'status' => '项目状态',
            'creater_id' => '创建者id',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 获取项目创建者
     * @return \yii\db\ActiveQuery
     */
    public function getCreater()
    {
        return $this->hasOne(User::className(),['id'=>'creater_id']);
    }

    /**
     * 获取项目环境
     * @return \yii\db\ActiveQuery
     */
    public function getEnvs()
    {
        $filter = ['status' => Env::ACTIVE_STATUS];
        return $this->hasMany(Env::className(), ['project_id' => 'id'])->where($filter)->orderBy(['id' => SORT_ASC]);
    }

    /**
     * 获取项目成员
     * @return \yii\db\ActiveQuery
     */
    public function getMembers()
    {
        $sort = [
            'id' => SORT_DESC
        ];

        return $this->hasMany(Member::className(), ['project_id' => 'id'])->orderBy($sort);
    }

    /**
     * 获取非项目成员
     * @return \yii\db\ActiveQuery
     */
    public function getNotMembers($param = [])
    {

        $query = User::find();

        $query->andFilterWhere(['status' => User::ACTIVE_STATUS]);

        $query->andFilterWhere(['not in', 'id', $this->getMembers()->select('user_id')->column()])
              ->andFilterWhere(['<>', 'id', $this->creater_id]);

        $query->andFilterWhere([
            'or',
            ['like','name', $param['name']],
            ['like','email', $param['name']],
        ]);

        return $query->limit(10)->all();

    }

    /**
     * 获取项目版本
     * @return \yii\db\ActiveQuery
     */
    public function getVersions()
    {

        $filter = [
            'status' => Version::ACTIVE_STATUS
        ];

        $sort = [
            'id' => SORT_DESC
        ];

        return $this->hasMany(Version::className(), ['project_id' => 'id'])->where($filter)->orderBy($sort);
    }

    /**
     * 获取我创建的项目
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getMyCreated()
    {

        $filter = [
            'status' => self::ACTIVE_STATUS,
            'creater_id' => Yii::$app->user->identity->id,
        ];

        $sort = [
            'sort' => SORT_DESC,
            'id'   => SORT_DESC
        ];

        return self::find()->where($filter)->orderBy($sort)->all();
    }

    /**
     * 获取我参与的项目
     */
    public function getMyJoined()
    {

        $filter = [
            'status' => self::ACTIVE_STATUS,
            'creater_id' => Yii::$app->user->identity->id,
        ];

        $sort = [
            'sort' => SORT_DESC,
            'id'   => SORT_DESC
        ];

        return self::find()->where($filter)->orderBy($sort)->all();
    }

    /**
     * 获取最新版本号
     */
    public function getLastVersion()
    {

        $filter = [
            'status' => Version::ACTIVE_STATUS,
            'project_id' => $this->id,
        ];

        $sort = [
            'id' => SORT_DESC
        ];

        return Version::find()->where($filter)->orderBy($sort)->one();
    }

    /**
     * 获取当前版本号
     */
    public function getCurrentVersion()
    {

        return $this->current_version;
    }

    /**
     * 获取友好的项目更新时间，如5分钟前
     * @return string
     */
    public function getFriendUpdateAt()
    {
        $time = strtotime($this->updated_at);
        return Yii::$app->formatter->asRelativeTime($time);
    }

    /**
     * 获取是否允许搜索文案
     * @return string
     */
    public function getAllowSearchText()
    {
        if($this->allow_search == self::ALLOW_SEARCH){
            return '允许';
        }
        if($this->allow_search == self::FORBID_SEARCH){
            return '禁止';
        }
    }

    /**
     * 判断是否是项目创建者
     * @return bool
     */
    public function isCreater($user_id = 0)
    {

        $user_id = $user_id ? $user_id : Yii::$app->user->identity->id;

        return $this->creater_id == $user_id ? true : false;
    }

    /**
     * 判断是否是项目参与者
     * @return bool
     */
    public function isJoiner($user_id = 0)
    {

        $user_id = $user_id ? $user_id : Yii::$app->user->identity->id;

        $query = Member::find()->where(['project_id' => $this->id, 'user_id' => $user_id]);

        return $query->exists() ? true : false;
    }

    /**
     * 检测是否有项目某项权限，如编辑、删除
     * @param $rule
     * @return bool
     */
    public function hasRule($rule, $user_id = 0)
    {

        $user_id = $user_id ? $user_id : Yii::$app->user->identity->id;

        // 项目创建者拥有该项目所有权限
        if($this->isCreater($user_id)){
            return true;
        }

        $member = Member::findOne(['project_id' => $this->id, 'user_id' => $user_id]);

        if($this->isJoiner($user_id) && $member->hasRule('project', $rule)){
            return true;
        }

        return false;
    }

}