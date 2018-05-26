<?php

namespace app\models\projectLog;

use app\models\ProjectLog;
use Yii;

class StoreLog extends ProjectLog
{

    public function store()
    {

        $this->user_id    = Yii::$app->user->identity->id;
        $this->user_name  = Yii::$app->user->identity->name;
        $this->user_email = Yii::$app->user->identity->email;

        if(!$this->validate()){
            return false;
        }

        if($this->save(false)){
            return true;
        }

        return false;

    }
}