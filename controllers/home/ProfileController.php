<?php

namespace app\controllers\home;

use app\models\account\ProfileForm;
use app\models\account\UpdateForm;
use app\models\loginLog\SearchLog;
use Yii;

use yii\helpers\Url;
use yii\web\Response;

class ProfileController extends PublicController
{

    public function actionHome()
    {

        $user = Yii::$app->user->identity;

        return $this->display('/home/account/home', ['user' => $user]);
    }

    public function actionAccount()
    {
        $request  = Yii::$app->request;

        $user = Yii::$app->user->identity;

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new UpdateForm();

            $model->user_id = $user->id;

            if(!$model->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($model->store()) {

                // 修改密码退出登录
                if($model->getOldAttribute('PASSWORD') != $model->password){
                    Yii::$app->user->logout();
                }

                return ['status' => 'success', 'message' => '修改成功'];

            } else {

                return ['status' => 'error', 'message' => $model->getErrorMessage(), 'label' => $model->getErrorLabel()];

            }

        }

        return $this->display('/home/account/profile', ['user' => $user]);

    }

    public function actionLog()
    {

        if(Yii::$app->user->isGuest){
            return $this->redirect(['home/account/login', 'callback' => Url::current()]);
        }

        $params = Yii::$app->request->queryParams;

        $params['user_id'] = Yii::$app->user->identity->id;

        $model = SearchLog::findModel()->search($params);

        return $this->display('/home/log/login', ['model' => $model]);

    }

}
