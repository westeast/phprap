<?php
namespace app\controllers\home;

use Yii;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class TemplateController extends PublicController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'select','delete', 'show', 'debug'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],

        ];
    }

    /**
     * 编辑接口
     * @param $id
     * @return array|string
     */
    public function actionUpdate()
    {


        return $this->display('create', ['template' => $template]);

    }

}
