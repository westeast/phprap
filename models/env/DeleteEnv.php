<?php

namespace app\models\env;

use app\models\Env;
use Yii;

class DeleteEnv extends Env
{

    public function delete()
    {

        $this->status = self::DELETED_STATUS;

        if(!$this->validate()){
            return false;
        }

        if($this->save(false)){
            return true;
        }

        return false;

    }

}
