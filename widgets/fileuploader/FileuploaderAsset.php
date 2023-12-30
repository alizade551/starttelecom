<?php
namespace app\widgets\fileuploader;

use yii\web\AssetBundle;
/**
 * FileUploadAsset
 *
 * @author Mammad Gurbanov
 */
class FileuploaderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/fileuploader.css?v=1.8'
    ];
    public $js = [
           'js/vanilla-masker.min.js?v=1.1',
    	// 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
      'https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js',
      'https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js',
        'js/fileuploader/jquery.iframe-transport.js',
        'js/fileuploader/jquery.fileupload.js',
      'js/fileuploader/jquery.fileupload-process.js',
      'js/fileuploader/jquery.fileupload-image.js',
      'js/fileuploader/jquery.ui.touch-punch.min.js',

    ];

}

?>