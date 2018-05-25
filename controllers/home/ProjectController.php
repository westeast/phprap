<?php
namespace app\controllers\home;

use app\models\member\SearchMember;
use app\models\project\SearchProject;
use app\models\project\TransferProject;
use Yii;
use yii\web\Response;
use yii\db\Exception;
use yii\filters\AccessControl;

use app\models\Project;
use app\models\Version;

use app\models\history\SearchHistory;
use app\models\version\SearchVersion;
use app\models\version\StoreVersion;
use app\models\project\DeleteProject;
use app\models\project\StoreProject;

/**
 * Project controller
 */
class ProjectController extends PublicController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['select','search','create', 'update', 'transfer','delete', 'show'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],

        ];
    }

    /**
     * 选择项目
     * @return string
     */
    public function actionSelect()
    {

        $model = Project::findModel();

        return $this->display('select', ['model' => $model]);

    }

    /**
     * 搜索项目
     * @return string
     */
    public function actionSearch()
    {

        $model = SearchProject::findModel()->search(Yii::$app->request->queryParams);

        return $this->display('search', ['model' => $model]);

    }

    /**
     * 转让项目
     * @return string
     */
    public function actionTransfer($id)
    {

        $request  = Yii::$app->request;

        $project  = TransferProject::findModel($id);

        if($request->isPost){

            if(!$project->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($project->transfer()) {

                return ['status' => 'success', 'message' => '转让成功'];

            }

            return ['status' => 'error', 'model' => $project];

        }

        return $this->display('transfer', ['model' => $project]);

    }

    /**
     * 添加项目
     * @return string
     */
    public function actionCreate()
    {

        $request = Yii::$app->request;

        $project = StoreProject::findModel();

        if($request->isPost){

            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();

            $project->scenario = 'create';

            if(!$project->load($request->post())){
                return ['status' => 'error', 'message' => '加载数据失败'];
            }

            if(!$project->store()){
                $transaction->rollBack();
                return ['status' => 'error', 'model' => $project];
            }

            // 添加默认版本
            $version = StoreVersion::findModel();

            $version->scenario   = 'create';
            $version->project_id = $project->id;
            $version->parent_id  = 0;
            $version->remark     = '初始版本';

            if(!$version->load($request->post())){
                return ['status' => 'error', 'message' => '加载数据失败'];
            }

            if(!$version->store()){
                $transaction->rollBack();
                return ['status' => 'error', 'model' => $version];

            }

            // 事务提交
            $transaction->commit();

            return ['status' => 'success', 'message' => '添加成功'];

        }

        return $this->display('create', ['model' => $project]);

    }

    /**
     * 编辑项目
     * @param $id
     * @return array|string
     */
    public function actionUpdate($id)
    {

        $request  = Yii::$app->request;

        $project  = StoreProject::findModel($id);

        if($request->isPost){

            $project->scenario = 'update';

            if(!$project->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];
            }

            if ($project->store()) {

                return ['status' => 'success', 'message' => '编辑成功'];

            }

            return ['status' => 'error', 'model' => $project];

        }

        return $this->display('create', ['model' => $project]);

    }

    /**
     * 项目详情
     * @param $token
     * @param string $tab
     * @return string
     */
    public function actionShow($token, $tab = 'home')
    {

        $version = Version::findModel(['token' => $token, 'status' => Version::ACTIVE_STATUS]);
        $project = Project::findModel($version->project_id);

        // 获取当前版本
        $project->current_version = $version;

        if(!$project->id || !$project->hasRule('look')){
            return $this->error('抱歉，您无权查看');
        }

        $params = [
            'project_id' => $project->id
        ];

        switch ($tab) {
            case 'home':
                $view  = '/home/project/home';
                break;
            case 'version':
                $model = SearchVersion::findModel()->search($params);
                $view  = '/home/version/index';
                break;
            case 'env':
                $view = '/home/env/index';
                break;
            case 'member':
                $model = SearchMember::findModel()->search($params);
                $view  = '/home/member/index';
                break;
            case 'history':
                $model = SearchHistory::findModel()->search($params);
                $view  = '/home/history/project';
                break;
            default:
                $view  = '/home/project/home';
                break;
        }

        return $this->display($view, ['project' => $project, 'model' => $model]);

    }

    /**
     * 删除项目
     * @param $id
     * @return array|string
     */
    public function actionDelete($id)
    {

        $request  = Yii::$app->request;

        $project  = DeleteProject::findModel($id);

        if($request->isPost){

            if(!$project->hasRule( 'delete')){

                return ['status' => 'error', 'message' => '抱歉，您无权操作'];

            }

            if(!$project->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($project->delete()) {

                return ['status' => 'success', 'message' => '删除成功'];

            }

            return ['status' => 'error', 'model' => $project];

        }

        return $this->display('delete', ['project' => $project]);

    }

}
