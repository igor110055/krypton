<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Order;

/**
 * OrderSearch represents the model behind the search form of `app\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'stop_loss', 'take_profit'], 'integer'],
            [['exchange', 'market', 'condition'], 'safe'],
            [['quantity', 'price', 'current_price'], 'number'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'type' => $this->type,
            'stop_loss' => $this->stop_loss,
            'take_profit' => $this->take_profit,
            'uuid' => $this->uuid,
        ]);

        $query->andFilterWhere(['like', 'exchange', $this->exchange]);
        $query->andFilterWhere(['like', 'status', Order::STATUS_PROCESSED]);
        $query->andFilterWhere(['like', 'market', $this->market]);

        $query->orderBy(['crdate' => SORT_DESC]);

        return $dataProvider;
    }
}
