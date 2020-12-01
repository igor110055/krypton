<?php

namespace app\models;

use yii\data\ActiveDataProvider;


class PortfolioTickerSearch extends PortfolioTicker
{
    /**
     * Creates data provider instance
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchAll(): ActiveDataProvider
    {
        $query = PortfolioTicker::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        return $dataProvider;
    }
}
