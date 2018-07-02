<?php

namespace app\models\project;

use Yii;
use app\models\Member;
use app\models\Project;
use yii\data\Pagination;
use app\widgets\LinkPager;

class SearchProject extends Project
{

    public $pageSize = 20;

    public function search($params = [])
    {

        $this->params = $params;

        $query = self::find()->joinWith('creater');

        $query->andFilterWhere([
            '{{%project}}.creater_id' => $this->params['creater_id'],
        ]);

        $this->params['status'] && $query->andFilterWhere([
            '{{%project}}.status' => $this->params['status'],
        ]);

        if($this->params['joiner_id']){

            $project_ids = Member::find()->where(['user_id' => $this->params['joiner_id']])->select('project_id')->column();

            if(!$project_ids){
                $project_ids = [0];
            }

            $query->andFilterWhere(['in', '{{%project}}.id', $project_ids]);
        }

        $this->params['type'] && $query->andFilterWhere(['in', '{{%project}}.type', $this->params['type']]);

        $query->andFilterWhere(['like', '{{%project}}.title', $this->params['title']]);

        $query->andFilterWhere(['like', '{{%user}}.name', $this->params['name']]);

        $pagination = new Pagination([
            'pageSizeParam' => false,
            'totalCount' => $query->count(),
            'pageSize'   => $this->pageSize,
            'validatePage' => false,
        ]);

        $this->models = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('{{%project}}.id DESC')
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
