<?php

namespace app\controllers\home;

use app\models\account\RegisterForm;
use app\models\Config;
use Yii;
use yii\debug\models\search\Debug;
use yii\debug\Module;
use yii\debug\Panel;
use yii\helpers\Url;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\models\account\LoginForm;

class AccountController extends PublicController
{

    public $checkLogin = false;

    /**
     * 会员注册
     * @return array|string
     */
    public function actionRegister()
    {

        $request  = Yii::$app->request;

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new RegisterForm();

            if(!$model->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($model->register()) {

                return ['status' => 'success', 'message' => '注册成功', 'callback' => Url::toRoute(['project/select'])];

            } else {

                return ['status' => 'error', 'message' => $model->getErrorMessage(), 'label' => $model->getErrorLabel()];

            }

        }

        $config = Config::findOne(['type' => 'safe'])->getField();

        return $this->display('register', ['config' => $config]);

    }

    /**
     * 会员登录
     * @return array|string|Response
     */
    public function actionLogin()
    {

        $request  = Yii::$app->request;

        // 已登录用户直接挑转到项目选择页
        if(!Yii::$app->user->isGuest){

            return $this->redirect(['home/project/select']);
        }

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new LoginForm();

            if(!$model->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($model->login()) {

                $callback = $model->callback ? $model->callback : Url::toRoute(['home/project/select']);

                return ['status' => 'success', 'message' => '登录成功', 'callback' => $callback];

            } else {

                return ['status' => 'error', 'message' => $model->getErrorMessage(), 'label' => $model->getErrorLabel()];

            }

        }

        $config = Config::findOne(['type' => 'safe'])->getField();

        return $this->render('login', ['callback' => $request->get('callback', ''), 'config' => $config]);

    }

    /**
     * 退出登录
     * @return Response
     */
    public function actionLogout()
    {

        if (Yii::$app->user->isGuest || Yii::$app->user->logout()) {
            return $this->redirect(['account/login']);
        }

    }

}
