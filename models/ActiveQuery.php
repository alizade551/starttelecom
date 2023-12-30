<?php
namespace app\models;
use Yii;


class ActiveQuery extends \yii\db\ActiveQuery{
    public function withByLocation(){
        if (Yii::$app->user->IsSuperadmin){
            return $this;
        }
        $modelClass = $this->modelClass;
        $memberLocation = \app\models\MemberLocation::find()
        ->where(['member_id'=>Yii::$app->user->id])
        ->asArray()
        ->one();

        $cities = [];
        $districts = [];
        $locations = [];

        if ($memberLocation !== null) {
            $cities = explode(",",$memberLocation['city_id']);
            $districts =  explode(",",$memberLocation['district_id']);
            if ($memberLocation['location_id'] != null) {
                 $locations =  explode(",",$memberLocation['location_id']);
            }
        }
  

        if ($districts != null) {
            $this->andWhere(['users.district_id'=>$districts]);
            return $this;
        }

        if ($locations != null) {
            $this->andWhere(['users.location_id'=>$locations]);
        }

        return $this;
    }


    public function withByCityId(){
        if (Yii::$app->user->IsSuperadmin){
            return $this;
        }
        $modelClass = $this->modelClass;
        $memberLocation = \app\models\MemberLocation::find()
        ->where(['member_id'=>Yii::$app->user->id])
        ->asArray()
        ->one();

        $cities = [];
        if ($memberLocation !== null) {
            $cities = explode(",",$memberLocation['city_id']);
        }

        if ($cities != null) {
            $this->andWhere(['address_cities.id'=>$cities]);
            return $this;
        }
        return $this;
    }



    public function withByDistrictId(){
        if (Yii::$app->user->IsSuperadmin){
            return $this;
        }
        $modelClass = $this->modelClass;
        $memberLocation = \app\models\MemberLocation::find()
        ->where(['member_id'=>Yii::$app->user->id])
        ->asArray()
        ->one();

        $districts = [];
        if ($memberLocation !== null) {
            $districts =  explode(",",$memberLocation['district_id']);
        }
        if ($districts != null) {
            $this->andWhere(['address_district.id'=>$districts]);
            return $this;
        }
        
        return $this;
    }

    public function withByLocationId(){
        if (Yii::$app->user->IsSuperadmin){
            return $this;
        }
        $modelClass = $this->modelClass;
        $memberLocation = \app\models\MemberLocation::find()
        ->where(['member_id'=>Yii::$app->user->id])
        ->asArray()
        ->one();
    
        $locations = [];
        if ($memberLocation['location_id'] != null) {
            $locations =  explode(",",$memberLocation['location_id']);
        }
        if (count($locations) != 0) {
            $this->andWhere(['address_locations.id'=>$locations]);
            return $this;
        }
        
        return $this;
    }

}
?>
