<?php

namespace app\models;

use Yii;

class Model extends \yii\db\ActiveRecord
{

    public $model;
    public $models;
    public $pages;
    public $params;
    public $count;
    public $sql;
    public $pageSize = 20;

    const ACTIVE_STATUS  = 10; //启用状态
    const DISABLE_STATUS = 20; //禁用状态
    const DELETED_STATUS = 30; //删除状态

    public static function findModel($condition = null)
    {

        if(!$condition){
            return new static();
        }

        if (($model = static::findOne($condition)) !== null) {

            return $model;

        } else {

            return new static();
        }
    }

    /**
     * 获取错误字段
     * @return int|null|string
     */
    public function getErrorLabel()
    {
        return key($this->getFirstErrors());
    }

    /**
     * 获取错误信息
     * @return mixed
     */
    public function getErrorMessage()
    {

        return current($this->getFirstErrors());

    }

    /**
     * 创建加密id
     * @return string
     */
    public function createEncodeId()
    {
        return mt_rand(1000, 9999) . date('His');
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
     * 获取友好的时间，如5分钟前
     * @return string
     */
    public function getFriendTime($time = null)
    {
        $time = $time ? strtotime($time) : time();
        return Yii::$app->formatter->asRelativeTime($time);
    }

    /**
     * 获取模型更新内容
     * @param $oldAttributes 原始属性
     * @param $dirtyAttributes 更新属性
     * @param string $preText 前缀文案
     * @return string
     */
    public function getUpdateContent($oldAttributes, $dirtyAttributes, $preText = '')
    {

        $content = '';

        foreach ($dirtyAttributes as $name => $value) {

            $label = '<strong>' . $this->getAttributeLabel($name) . '</strong>';

            if(isset($oldAttributes[$name])){
                $oldValue = '<code>' . $oldAttributes[$name] . '</code>';
                $newValue = '<code>' . $value . '</code>';

                $content .= $preText . ' ' . $label . ' 从' . $oldValue . '更新为' . $newValue . ',';
            }

        }

        return trim($content, ',');
    }

    /**
     * 获取ip地理位置
     * @param null $ip
     * @return string
     */
    public function getLocation($ip = null)
    {
        if(!$ip){

            $ip = Yii::$app->request->userIP;
        }

        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        //调用淘宝接口获取信息
        $json = file_get_contents($url);

        $data = json_decode($json, true);

        if ($data['code']) {

            return $data['data'];

        } else {

            $country = $data['data']['country'];
            $province = $data['data']['region'];
            $city = $data['data']['city'];
            $area = $data['data']['area'];

            return $country . ' ' . $province . ' ' . $city . ' ' . $area;

        }
    }

}
