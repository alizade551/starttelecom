<div class="row">
    <h2><?=Yii::t('app','Cron job\'s  action logs') ?></h2>
    <div  style="max-width: 600px; height: 300px; overflow-y:scroll;">
        <?php 
        if(file_exists($file)){
            $handle = fopen($file, "r");
            if ($handle) {
                $c = 1;
                while (($line = fgets($handle)) !== false) {
                    if (strlen($line) > 10 && $line != "" ) {
                        echo $c.". ". $line."</br>";
                        $c++;
                    }
                }
                fclose($handle);
            } else {
                // error opening the file.
            } 
        }
        ?>
    </div>
</div>