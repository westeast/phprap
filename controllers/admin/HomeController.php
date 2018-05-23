<?php
namespace app\controllers\admin;

use Yii;

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

        return $this->display('index');

    }


}
