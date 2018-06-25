<?php

namespace app\models\apply;

use app\models\Apply;
use Yii;
use yii\data\Pagination;
use app\widgets\LinkPager;

class SearchApply extends Apply
{

    public $pageSize = 20;

    public function search($params = [])
    {

        $this->params = $params;

        $query = self::find()->joinWith('project');

        $query->andFilterWhere([
            '{{%project}}.status' => self::ACTIVE_STATUS,
        ]);

        $query->andFilterWhere(['in', '{{%project}}.id', $this->params['project_ids']]);

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
            ->orderBy('{{%apply}}.id DESC')
            ->all();

        $this->count = $query->count();

        $this->sql = $query->createCommand()->getRawSql();

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
