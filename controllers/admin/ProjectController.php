<?php
namespace app\controllers\admin;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\models\Project;
use app\models\Version;
use app\models\Template;

use app\models\member\SearchMember;
use app\models\template\StoreTemplate;
use app\models\version\SearchVersion;
use app\models\version\StoreVersion;
use app\models\project\DeleteProject;
use app\models\project\StoreProject;
use app\models\project\SearchProject;
use app\models\project\TransferProject;
use yii\web\Response;

/**
 * Project controller
 */
class ProjectController extends PublicController
{

    /**
     * 搜索项目
     * @return string
     */
    public function actionIndex()
    {

        $params = Yii::$app->request->queryParams;

        $model  = SearchProject::findModel();

        return $this->display('index', ['model' => $model->search($params)]);

    }

    /**
     * 更新项目
     * @param $id
     * @return array|string
     */
    public function actionUpdate($id, $status)
    {

        $request = Yii::$app->request;

        $project = Project::findModel(['encode_id' => $id]);

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $project->status = $status;

            if ($project->save()) {

                return ['status' => 'success', 'message' => '操作成功'];

            }

            return ['status' => 'error', 'message' => $project->getErrorMessage(), 'label' => $project->getErrorLabel()];

        }

    }

}
