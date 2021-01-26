<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\HodlPosition;

/**
 * HodlPositionSearch represents the model behind the search form of `app\models\HodlPosition`.
 */
class HodlPositionSearch extends HodlPosition
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['buy_date', 'sell_date', 'market', 'status', 'comment'], 'safe'],
            [['quantity', 'buy_price', 'sell_price', 'buy_value', 'sell_value', 'val_diff', 'price_diff', 'pln_value', 'pln_diff_value'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    public function search(array $params): ActiveDataProvider
    {
        $query = HodlPosition::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'buy_date' => $this->buy_date,
            'sell_date' => $this->sell_date,
            'quantity' => $this->quantity,
            'buy_price' => $this->buy_price,
            'sell_price' => $this->sell_price,
            'buy_value' => $this->buy_value,
            'sell_value' => $this->sell_value,
            'val_diff' => $this->val_diff,
            'price_diff' => $this->price_diff,
            'pln_value' => $this->pln_value,
            'pln_diff_value' => $this->pln_diff_value,
        ]);

        $query->andFilterWhere(['like', 'market', $this->market])
            ->andFilterWhere(['like', 'status', HodlPosition::STATUS_PROCESSING])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
