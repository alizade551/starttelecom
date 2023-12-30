<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ItemStock;
use kartik\daterange\DateRangeBehavior;

/**
 * ItemStockSearch represents the model behind the search form of `\app\models\ItemStock`.
 */
class ItemStockSearch extends ItemStock
{

    public $item;
    public $warehouse;

    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'item_id', 'warehouse_id', 'updated_at', 'created_at'], 'integer'],
            [['quantity', 'price'], 'number'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['sku','item','warehouse'], 'safe'],
        ];
    }


   public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'createTimeRange',
                'dateFormat'=>'Y-m-d',
                'dateStartFormat'=>'Y-m-d',
                'dateEndFormat'=>'Y-m-d',
                'dateStartAttribute' => 'createTimeStart',
                'dateEndAttribute' => 'createTimeEnd',
            ]
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
        $query = ItemStock::find()
        ->select('item_stock.*,items.name as item_name,warehouses.name as warehouse_name')
        ->leftJoin('items','items.id=item_stock.item_id')
        ->leftJoin('warehouses','warehouses.id=item_stock.warehouse_id')
        ->asArray();

        // add conditions that should always apply here

       $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size_item_stock', 20),
            ],
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
            'item_id' => $this->item_id,
            'warehouse_id' => $this->warehouse_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        if ($this->createTimeRange) {
            $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(item_stock.created_at), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(item_stock.created_at), '%Y-%m-%d')", $this->createTimeEnd]]);
         }  


        $query->andFilterWhere(['like', 'sku', $this->sku])
        ->andFilterWhere(['like', 'warehouses.name', $this->warehouse])
        ->andFilterWhere(['like', 'items.name', $this->item]);

        return $dataProvider;
    }
}
