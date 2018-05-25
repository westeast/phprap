<?php

namespace app\controllers\home;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

class PublicController extends Controller
{

    public $layout = false;



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

        if(Yii::$app->request->isAjax){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $rs = parent::afterAction($action, $result);

            if(isset($rs['message'])){

                $rs['message'] = $rs['message'] ? $rs['message'] : '操作成功';

            }else{

                $rs['message'] = isset($rs['model']) ? $rs['model']->error : '操作成功';

            }

            $rs['label'] = isset($rs['model']) ? $rs['model']->label : '';

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
        $view = $view . '.html';
        return $this->render($view, $params);
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

}
