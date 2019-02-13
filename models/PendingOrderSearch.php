<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PendingOrder;

/**
 * PendingOrderSearch represents the model behind the search form of `app\models\PendingOrder`.
 */
class PendingOrderSearch extends PendingOrder
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'stop_loss', 'start_earn'], 'integer'],
            [['market', 'condition'], 'safe'],
            [['quantity', 'price', 'last_bid'], 'number'],
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
        $query = PendingOrder::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'type' => $this->type,
            'stop_loss' => $this->stop_loss,
            'start_earn' => $this->start_earn,
            'last_bid' => $this->last_bid,
        ]);

        $query->andFilterWhere(['like', 'market', $this->market]);

        return $dataProvider;
    }
}
