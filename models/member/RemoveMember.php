<?php

namespace app\models\member;

use Yii;
use app\models\Member;

class RemoveMember extends Member
{

    public function remover()
    {

        if($this->delete()){
            return true;
        }

        return false;

    }

}
