<?php

namespace app\actions;

use yii\web\Response;

class CaptchaAction extends \yii\captcha\CaptchaAction
{

    /**
     * 默认验证码刷新页面不会自动刷新
     */
    public function run()
    {
        $this->setHttpHeaders();
        \Yii::$app->response->format = Response::FORMAT_RAW;
        return $this->renderImage($this->getVerifyCode(true));
    }

}
