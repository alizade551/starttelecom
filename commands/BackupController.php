<?php
namespace app\commands;

class BackupController extends \yii\console\Controller
{
    public function actionIndex()
    {
        $backup = \Yii::$app->backup;
        $databases = ['db',];
        foreach ($databases as $k => $db) {
            $index = (string)$k;
            $backup->fileName = 'billing-backup';
            $backup->fileName .= str_pad($index, 3, '0', STR_PAD_LEFT);
            $backup->directories = [];
            $backup->databases = [$db];
            $file = $backup->create();
            $this->stdout('Backup file created: ' . $file . PHP_EOL, \yii\helpers\Console::FG_GREEN);
        }
    }
}