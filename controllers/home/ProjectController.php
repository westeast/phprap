<?php
namespace app\controllers\home;

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
     * 选择项目
     * @return string
     */
    public function actionSelect()
    {

        $project = Project::findModel();

        return $this->display('select', ['project' => $project]);

    }

    /**
     * 搜索项目
     * @return string
     */
    public function actionSearch()
    {

        $params = Yii::$app->request->queryParams;

        $model  = SearchProject::findModel();

        !$params['type'] && $params['type'] = [$model::PUBLIC_TYPE, $model::AUTH_TYPE];

        $params['status'] = $model::ACTIVE_STATUS;

        return $this->display('search', ['model' => $model->search($params)]);

    }

    /**
     * 转让项目
     * @return string
     */
    public function actionTransfer($id)
    {

        $request  = Yii::$app->request;

        $project  = TransferProject::findModel(['encode_id' => $id]);

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            if(!$project->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($project->transfer()) {

                return ['status' => 'success', 'message' => '转让成功'];

            }

            return ['status' => 'error', 'message' => $project->getErrorMessage(), 'label' => $project->getErrorLabel()];

        }

        return $this->display('transfer', ['project' => $project]);

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

            Yii::$app->response->format = Response::FORMAT_JSON;

            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();

            $project->scenario = 'create';

            if(!$project->load($request->post())){
                return ['status' => 'error', 'message' => '加载数据失败'];
            }

            if(!$project->store()){
                $transaction->rollBack();
                return ['status' => 'error', 'message' => $project->getErrorMessage(), 'label' => $project->getErrorLabel()];
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
                return ['status' => 'error', 'message' => $version->getErrorMessage(), 'label' => $version->getErrorLabel()];
            }

            // 添加默认模板
            $template = StoreTemplate::findModel();

            $template->scenario     = 'create';

            $template->project_id   = $project->id;
            $template->header_field  = json_encode($template->defaultAttributes['header_field'], JSON_UNESCAPED_UNICODE);
            $template->request_field = json_encode($template->defaultAttributes['request_field'], JSON_UNESCAPED_UNICODE);
            $template->response_field  = json_encode($template->defaultAttributes['response_field'], JSON_UNESCAPED_UNICODE);

            if(!$template->store()){
                $transaction->rollBack();
                return ['status' => 'error', 'message' => $template->getErrorMessage(), 'label' => $template->getErrorLabel()];
            }

            // 事务提交
            $transaction->commit();

            return ['status' => 'success', 'message' => '添加成功'];

        }

        return $this->display('create', ['project' => $project]);

    }

    /**
     * 编辑项目
     * @param $id
     * @return array|string
     */
    public function actionUpdate($id)
    {

        $request  = Yii::$app->request;

        $project  = StoreProject::findModel(['encode_id' => $id]);

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $project->scenario = 'update';

            if(!$project->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];
            }

            if ($project->store()) {

                return ['status' => 'success', 'message' => '编辑成功'];

            }

            return ['status' => 'error', 'message' => $project->getErrorMessage(), 'label' => $project->getErrorLabel()];

        }

        return $this->display('create', ['project' => $project]);

    }

    /**
     * 项目详情
     * @param $token
     * @param string $tab
     * @return string
     */
    public function actionShow($version_id, $tab = 'home')
    {

        $version = Version::findModel(['encode_id' => $version_id, 'status' => Version::ACTIVE_STATUS]);
        $project = Project::findModel($version->project_id);

        // 获取当前版本
        $project->current_version = $version;

        if(!$project->isPublic() && !$project->hasRule('project', 'look')){
            return $this->error('抱歉，您无权查看');
        }

        $params['project_id'] = $project->id;

        $data['project'] = $project;

        switch ($tab) {
            case 'home':

                $series_data = json_encode([$project->getVersions()->count(), $project->getEnvs()->count(), $project->current_version->getModules()->count(), $project->getMembers()->count()]);

                $data['series_data'] = $series_data;

                $view  = '/home/project/home';

                break;

            case 'version':

                $data['version'] = SearchVersion::findModel()->search($params);

                $view  = '/home/version/index';

                break;

            case 'template':

                $data['template'] = Template::findModel(['project_id' => $project->id]);

                $view  = '/home/template/home';

                break;

            case 'env':

                $view = '/home/env/index';

                break;

            case 'member':

                $data['member'] = SearchMember::findModel()->search($params);

                $view  = '/home/member/index';

                break;
        }

        return $this->display($view, $data);

    }

    public function actionExport($version_id)
    {
        $version = Version::findModel(['encode_id' => $version_id]);

        $file_name = $version->project->title . '接口离线文档.html';

//        header ("Content-Type: application/force-download");
//        header ("Content-Disposition: attachment;filename=$file_name");

        return $this->display('export', ['version' => $version]);

    }

    /**
     * 删除项目
     * @param $id
     * @return array|string
     */
    public function actionDelete($id)
    {

        $request  = Yii::$app->request;

        $project  = DeleteProject::findModel(['encode_id' => $id]);

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            if(!$project->hasRule( 'delete')){

                return ['status' => 'error', 'message' => '抱歉，您无权操作'];

            }

            if(!$project->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($project->delete()) {

                return ['status' => 'success', 'message' => '删除成功'];

            }

            return ['status' => 'error', 'message' => $project->getErrorMessage(), 'label' => $project->getErrorLabel()];

        }

        return $this->display('delete', ['project' => $project]);

    }

}
