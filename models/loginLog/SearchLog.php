<?php

namespace app\models\loginLog;

use Yii;
use app\models\LoginLog;
use yii\data\Pagination;
use app\widgets\LinkPager;

class SearchLog extends LoginLog
{

    public $pageSize = 20;

    public function search($params = [])
    {

        $this->params = array_map(function ($value) {

            return trim($value);

        }, $params);

        $query = static::find();

        $query->andFilterWhere([
            'user_id'  => $this->params['user_id'],
        ]);

        $query->andFilterWhere(['like', 'ip', $this->params['ip']]);
        $query->andFilterWhere(['like', 'location', $this->params['location']]);
        $query->andFilterWhere(['like', 'user_name', $this->params['user_name']]);
        $query->andFilterWhere(['like', 'user_email', $this->params['user_email']]);

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
