<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://unpkg.com/leaflet@1.8.0/dist/leaflet.css',
        'https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap',
        'css/alertify.css',
        'css/theme-checkbox-radio.css',
        'css/jquery.fancybox.min.css',
        'css/dataTables.bootstrap4.min.css',
        'css/buttons.bootstrap4.min.css',
        '/css/loader.css',
        '/css/main.css',
        '/css/perfect-scrollbar.css',
        'css/scrollspyNav.css',
        '/css/plugins.css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.1/css/all.min.css',
        'css/tabler-icons.css',
        'css/structure_dark.css?version=1.0.9'

    ];
    public $js = [

        'js/popper.min.js',
        'js/bootstrap.min.js',
        'js/jquery.fancybox.min.js',
        'js/alertify.js',
        'js/app.js',
        'js/perfect-scrollbar.min.js',
        'js/custom.js',
        'js/perfect-scrollbar.min.js',
        'js/jquery.fancybox.min.js',
        'js/jQuery.print.min.js',
        'js/scrollspyNav.js',
        'js/modal.js',
        'js/custom.js',
        // 'https://unpkg.com/leaflet@1.8.0/dist/leaflet.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];
}
