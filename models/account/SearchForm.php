<?php

namespace app\models\account;

use app\models\Member;
use app\models\Project;
use Yii;
use app\models\User;
use yii\data\Pagination;
use app\widgets\LinkPager;

class SearchForm extends User
{

    public $pageSize = 20;

    public function search($params = [])
    {

        $this->params = $params;

        $query = self::find();

        $query->andFilterWhere([
            'type' => self::USER_TYPE
        ]);

        $this->params['status'] && $query->andFilterWhere([
            'status' => $this->params['status'],
        ]);

        $query->andFilterWhere([
            'or',
            ['like','name', $this->params['keywords']],
            ['like','email', $this->params['keywords']],
        ]);

        if($this->params['project_id']){

            $project = Project::findModel(['encode_id' => $this->params['project_id']]);

            $user_ids = Member::find()->where(['project_id' => $project->id])->select('user_id')->column();

            if(!$user_ids){
                $user_ids = [0];
            }
            $query->andFilterWhere(['in', 'id', $user_ids]);
        }

        $pagination = new Pagination([
            'pageSizeParam' => false,
            'totalCount' => $query->count(),
            'pageSize'   => $this->pageSize,
            'validatePage' => false,
        ]);

        $this->models = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('id DESC')
            ->all();

        $this->count = $query->count();

        $this->sql = $query->createCommand()->getRawSql();

//        dump($this->sql);

        $this->pages = LinkPager::widget([
            'pagination' => $pagination,
            'nextPageLabel' => '下一页',
            'prevPageLabel' => '上一页',
            'firstPageLabel' => '首页',
            'lastPageLabel' => '尾页',
            'hideOnSinglePage' => true,
            'maxButtonCount' => 5,
        ]);

        return $this;

    }

}
