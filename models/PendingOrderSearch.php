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
            [['id', 'stop_loss', 'take_profit'], 'integer'],
            [['exchange', 'market', 'condition', 'type'], 'safe'],
            [['quantity', 'price'], 'number'],
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
        $query = PendingOrder::find()->orderBy(['market' => SORT_ASC, 'crdate' => SORT_DESC]);

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
            'take_profit' => $this->take_profit,
            'uuid' => $this->uuid,
        ]);

        $query->andFilterWhere(['like', 'exchange', $this->exchange]);
        $query->andFilterWhere(['like', 'market', $this->market]);

        return $dataProvider;
    }
}
