<?php

namespace app\controllers\home;

use Yii;
use yii\debug\Module;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

class PublicController extends Controller
{

    /**
     * 是否启用布局
     * @var bool
     */
    public $layout = false;

    /**
     * 是否启用后置动作
     * @var bool
     */
    public $afterAction = true;

    public $debugTags;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function afterAction($action, $result)
    {

        if($this->afterAction === false){
            return $result;
        }

        if(Yii::$app->request->isAjax){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $rs = parent::afterAction($action, $result);

            if($rs['status'] == 'success'){
                $defaultMessage= '操作成功';
            }elseif ($rs['status'] == 'error') {
                $defaultMessage= '操作失败';
            }

            if(isset($rs['message'])){

                $rs['message'] = $rs['message'] ? $rs['message'] : $defaultMessage;

            }else{

                $rs['message'] = $rs['model']->error ? $rs['model']->error : $defaultMessage;

            }

            $rs['label'] = $rs['model']->label ? $rs['model']->label : '';

            return array_filter($rs);

        }else{

            return $result;
        }

    }

    /** 展示模板
     * @param $view
     * @param array $params
     * @return string
     */
    public function display($view, $params = [])
    {

        if(YII_DEBUG === true){
            $tags = array_keys($this->getDebugTags());
            $tag  = reset($tags);

            $params['toolbarTag'] = $tag;
        }

        return $this->render($view . '.html', $params);
    }

    /**
     * 成功消息提示
     * @param $message 提示信息
     * @param int $jumpSeconds 延迟时间
     * @param string $jumpUrl 跳转链接
     * @param null $model
     * @return string
     */
    public function success($message, $jumpSeconds = 1, $jumpUrl = '', $model = null)
    {

        $jumpUrl = $jumpUrl ? Url::toRoute($jumpUrl) : \Yii::$app->request->referrer;

        return $this->display('../public/message', ['flag' => 'success', 'message' => $message, 'time' => $jumpSeconds, 'url' => $jumpUrl, 'model' => $model]);

    }

    /**
     * 错误消息提示
     * @param $message
     * @param int $jumpSeconds
     * @param string $jumpUrl
     * @return string
     */
    public function error($message, $jumpSeconds = 3, $jumpUrl = '')
    {

        $jumpUrl = $jumpUrl ? Url::toRoute($jumpUrl) : \Yii::$app->request->referrer;

        return $this->display('/home/public/message', ['flag' => 'error', 'message' => $message, 'time' => $jumpSeconds, 'url' => $jumpUrl]);

    }

    public function getDebugTags($forceReload = false)
    {
        if ($this->debugTags === null || $forceReload) {
            if ($forceReload) {
                clearstatcache();
            }

            $indexFile = Module::getInstance()->dataPath . '/index.data';

            $content = '';
            $fp = @fopen($indexFile, 'r');
            if ($fp !== false) {
                @flock($fp, LOCK_SH);
                $content = fread($fp, filesize($indexFile));
                @flock($fp, LOCK_UN);
                fclose($fp);
            }

            if ($content !== '') {
                $this->debugTags = array_reverse(unserialize($content), true);
            } else {
                $this->debugTags = [];
            }
        }

        return $this->debugTags;

    }

}
