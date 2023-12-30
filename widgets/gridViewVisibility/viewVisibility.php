<?php
namespace app\widgets\gridViewVisibility;


use yii\base\Widget;


use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class viewVisibility extends Widget {

    public $url;
    public $params;
    public $pjaxContainer;

    public function init()
    {
        parent::init();
    }
    /**
     * @inheritdoc
     */
    public function run()
    {
       return $this->render("index",['params'=>$this->params,'url'=>$this->url,'pjaxContainer'=>$this->pjaxContainer]);
    }

}
?>