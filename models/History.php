<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_history".
 *
 * @property int $id
 * @property string $res_name 资源名
 * @property int $res_id 资源id
 * @property int $user_id 操作人id
 * @property string $user_name 操作人昵称
 * @property string $user_email 操作人邮箱
 * @property string $method 操作方式
 * @property string $object 操作对象
 * @property string $content 操作内容
 * @property string $created_at
 * @property string $updated_at
 */
class History extends Model
{

    /**
     * 绑定数据表
     */
    public static function tableName()
    {
        return '{{%history}}';
    }

    /**
     * 获取操作方式文案
     * @return mixed
     */
    public function getMethodText()
    {

        $find = ['look','create','update', 'transfer', 'export', 'delete', 'remove'];

        $replace = ['查看','添加', '更新', '转让', '导出', '删除', '移除'];

        return str_replace($find, $replace, $this->method);
    }

    /**
     * 获取操作对象文案
     * @return mixed
     */
    public function getObjectText()
    {

        $find = ['project','version','nev', 'module', 'api', 'field', 'member'];

        $replace = ['项目','版本', '环境', '模块', '接口', '字段', '成员'];

        return str_replace($find, $replace, $this->object);
    }

}
