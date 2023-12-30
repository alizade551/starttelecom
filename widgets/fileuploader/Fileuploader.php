<?php
namespace app\widgets\fileuploader;


use yii\base\Widget;


use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class Fileuploader extends Widget {

    public $url;
    public $photos = null;
    public $name = null;

    public function init()
    {
        parent::init();
    }
    /**
     * @inheritdoc
     */
    public function run()
    {
       return $this->render("index",['url'=>$this->url,'photos'=>$this->photos,'name'=>$this->name]);
    }

}
?>