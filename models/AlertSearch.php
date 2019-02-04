<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Alert;

/**
 * AlertSearch represents the model behind the search form of `app\models\Alert`.
 */
class AlertSearch extends Alert
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['market', 'condition', 'message', 'modified', 'crdate'], 'safe'],
            [['price'], 'number'],
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
        $query = Alert::find();

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
            'price' => $this->price,
            'modified' => $this->modified,
            'crdate' => $this->crdate,
        ]);

        $query->andFilterWhere(['like', 'market', $this->market])
            ->andFilterWhere(['like', 'condition', $this->condition])
            ->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
