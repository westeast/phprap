<?php
namespace app\controllers\admin;

use Yii;
use yii\helpers\Url;

/**
 * Site controller
 */
class HomeController extends PublicController
{


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {

        if(Yii::$app->user->isGuest){
            return $this->redirect(['home/account/login', 'callback' => Url::current()]);
        }

        if(!Yii::$app->user->identity->isAdmin){
            return $this->error('抱歉，您无权访问');
        }

        return $this->display('index');

    }


}
