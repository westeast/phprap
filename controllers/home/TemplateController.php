<?php
namespace app\controllers\home;

use app\models\Field;
use app\models\Project;
use app\models\template\StoreTemplate;
use app\models\Version;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

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
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],

        ];
    }

    /**
     * 更新模板
     * @param $id
     * @return array|string
     */
    public function actionUpdate($version_id)
    {

        $request = Yii::$app->request;

        $version = Version::findModel(['encode_id' => $version_id]);

        $project = $version->project;

        // 获取当前版本
        $project->current_version = $version;

        $template = StoreTemplate::findModel(['project_id' => $project->id]);

        if(!$request->isAjax){
//            return $this->error('非法请求');
        }

        if($request->isPost){

            $template->scenario = 'update';

            if(!$template->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            $template->project_id = $project->id;

//            dump($template->attributes);exit;

            if ($template->store()) {

                return ['status' => 'success', 'message' => '保存成功'];

            }

            return ['status' => 'error', 'model' => $template];

        }

        $this->afterAction = false;

        return $this->display('create', ['project' => $project, 'model' => $template]);

    }

    /**
     * 将表单传递过来的二维数组键值互换后转成json
     * @param $array
     * @return string
     */
    private function array2json($array)
    {
        if(!$array){
            return '';
        }
        $data = [];

        foreach ($array as $k => $v) {

            foreach ($v as $k1 => $v1) {
                $data[$k1][$k] = $v1;
            }
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}
