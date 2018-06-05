<?php

namespace app\models\loginLog;

use app\models\LoginLog;
use Yii;

class StoreLog extends LoginLog
{

    public function store()
    {

        $this->ip = Yii::$app->request->userIP;
        $this->location = $this->getLocation();

        if(!$this->validate()){
            return false;
        }

        if($this->save(false)){
            return true;
        }

        return false;

    }
}