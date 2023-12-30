<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MessageTemplate;

/**
 * MessageTemplateSearch represents the model behind the search form of `app\models\MessageTemplate`.
 */
class MessageTemplateSearch extends MessageTemplate
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'lang', 'sms_text','whatsapp_header_text','whatsapp_body_text','whatsapp_footer_text'], 'safe'],
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
        $query = MessageTemplate::find();

        // add conditions that should always apply here

        $cookieName = '_grid_page_size_message_template';

        $dataProvider = new ActiveDataProvider([
            'query' => $query, 
            'pagination' => [
                'pageSize' => \Yii::$app->request->cookies->getValue( $cookieName, 20),
            ]
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
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'lang', $this->lang])
            ->andFilterWhere(['like', 'whatsapp_header_text', $this->whatsapp_header_text])
            ->andFilterWhere(['like', 'whatsapp_body_text', $this->whatsapp_body_text])
            ->andFilterWhere(['like', 'whatsapp_footer_text', $this->whatsapp_footer_text])
            ->andFilterWhere(['like', 'sms_text', $this->sms_text]);

        return $dataProvider;
    }
}
