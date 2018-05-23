<?php

namespace app\models\version;

use app\widgets\LinkPager;
use Yii;
use yii\data\Pagination;
use app\models\Version;

class SearchVersion extends Version
{

    public $pageSize = 20;

    public function search($params = [])
    {

        $this->params = array_map(function ($value) {

            return trim($value);

        }, $params);

        $query = static::find();

        $query->andFilterWhere([
            'status' => 10,
            'project_id' => $this->params['project_id'],
        ]);

        $query->andFilterWhere(['like', 'name', $this->params['name']]);

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
