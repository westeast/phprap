<?php

namespace app\controllers\home;

class HistoryController extends PublicController
{

    public function actionLogin()
    {

        return $this->display('login');

    }

}
