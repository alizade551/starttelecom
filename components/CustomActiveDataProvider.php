<?php 
namespace app\components;

use yii\data\ActiveDataProvider;

class CustomActiveDataProvider extends ActiveDataProvider
{
    public $activeUsers;

    public function prepareModels()
    {
        if ($this->activeUsers) {
            // Burada ekstra işlemleri yapabilirsiniz
            // activeUsers parametresini kullanabilirsiniz
        }
        return parent::prepareModels();
    }
}
 ?>