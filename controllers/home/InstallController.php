<?php

namespace app\controllers\home;

use Yii;


class InstallController extends PublicController
{

    public function actionStep1()
    {

        $user = Yii::$app->user->identity;

        return $this->display('/install/step1', ['user' => $user]);
    }

    public function actionStep2()
    {
        $request  = Yii::$app->request;

        $user = Yii::$app->user->identity;

        return $this->display('/install/step2', ['user' => $user]);

    }

    public function actionStep3()
    {

        return $this->display('/install/step3');

    }

    public function actionStep4()
    {

        return $this->display('/install/step4');

    }

}
