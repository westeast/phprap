<?php
namespace app\controllers\home;

use app\models\Api;
use app\models\api\DeleteApi;
use app\models\api\StoreApi;
use app\models\Field;
use app\models\field\StoreField;
use app\models\Module;
use app\models\projectLog\SearchLog;
use app\models\Template;
use app\models\template\StoreTemplate;
use Yii;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class FieldController extends PublicController
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
     * 更新字段
     * @param $id
     * @return array|string
     */
    public function actionUpdate($id)
    {

        $request = Yii::$app->request;

        $field = StoreField::findModel(['encode_id' => $id]);

        if($request->isPost){

            $field->scenario = 'update';

            if(!$field->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($field->store()) {

                return ['status' => 'success', 'message' => '编辑成功'];

            }

            return ['status' => 'error', 'model' => $field];

        }

        $this->afterAction = false;

        $project = $field->api->module->project;

        $project->current_version = $field->api->module->version;

        return $this->display('create', ['project' => $project, 'field' => $field, 'api' => $field->api]);

    }

    /**
     * 删除接口
     * @param $id
     * @return array|string
     */
    public function actionDelete($id)
    {

        $request = Yii::$app->request;

        $api = DeleteApi::findModel(['encode_id' => $id]);

        if($request->isPost){

            if(!$api->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($api->delete()) {

                $callback = url('home/project/show', ['version_id' => $api->module->version->encode_id]);

                return ['status' => 'success', 'message' => '删除成功', 'callback' => $callback];

            }

            return ['status' => 'error', 'model' => $api];

        }

        return $this->display('delete', ['api' => $api]);

    }

    /**
     * 接口详情
     * @param $id
     * @return string
     */
    public function actionShow($id, $tab = 'home')
    {

        $api = Api::findModel(['encode_id' => $id]);

        if(!$api->id || !$api->hasRule('look')){
            return $this->error('抱歉，您无权查看');
        }

        $project = $api->module->project;

        // 获取当前版本
        $project->current_version = $api->module->version;

        $params = [
            'object_name' => 'api',
            'object_id'  => $api->id
        ];

        switch ($tab) {
            case 'home':
                $view  = '/home/api/home';
                break;
            case 'field':
                $view  = '/home/field/home';
                break;
            case 'debug':
                $view  = '/home/api/debug';
                break;
            case 'log':
                $model = SearchLog::findModel()->search($params);
                $view  = '/home/log/api';
                break;
            default:
                $view  = '/home/api/home';
                break;
        }

        return $this->display($view, ['project' => $project, 'api' => $api, 'model' => $model]);

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
