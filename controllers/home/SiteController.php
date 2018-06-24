<?php
namespace app\controllers\home;

use app\models\Config;
use app\models\Project;
use app\models\Version;
use Yii;

/**
 * Site controller
 */
class SiteController extends PublicController
{

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {

        return $this->redirect(['project/search']);

    }

}
