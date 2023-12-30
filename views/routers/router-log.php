


<div id="wrapper">
   <input style="padding: 5px; padding-left: 15px;" type="text" placeholder="<?=Yii::t('app','Search on terminal') ?>" data-search />
  <div class="sh-console sh-shadow items">
  <?php
    $i = 0;
     for($a=$countLog-1; $a > 0; $a-- ){ 
     $i++;
     echo "<div class='sh-container' data-filter-item data-filter-name='".$log[$a]["message"]." ".$log[$a][".id"]." ".$log[$a]["topics"]." ".$log[$a]["time"]."'>";
        if(isset($log[$a]["message"]) !== null){
         echo '<div class="sh sh-command">'.$i.'.'.$model['name'].'\> '.$log[$a][".id"].'  '.$log[$a]["topics"].' -  [ '.$log[$a]["time"].' ] </div>';
      
         echo '<div class="sh sh-return">'.$log[$a]["message"].'</div>';
         }
     echo "</div>";

     }
    ?>  
  </div>
</div>



 <?php
$script = <<< JS
  $('[data-search]').on('keyup', function() {
    var searchVal = $(this).val();
    console.log(searchVal)
    var filterItems = $('[data-filter-item]');

    if ( searchVal != '' ) {
      filterItems.addClass('hidden');
      $('[data-filter-item][data-filter-name*="' + searchVal.toLowerCase() + '"]').removeClass('hidden');
    } else {
      filterItems.removeClass('hidden');
    }
  });
JS;
$this->registerJs($script);
?>

<style>
.hidden {
  display: none;
}
.modal-body {
    padding:0;
}
#wrapper {
    display: flex;
    padding: 0;
    flex-direction: column;
    font-size: 18px;
}
#wrapper #section-header {
  font-weight: 700;
  text-transform: uppercase;
  margin-bottom: 2em;
}
#wrapper #sh-console {
  min-width: 300px;
  width: 50%;
}

.sh-console {
    font-family: "Monaco", "Consolas";
    -webkit-font-smoothing: antialiased;
    font-size: 0.85em;
    background: #383737;
    color: #ccc;
    box-sizing: border-box;
    padding: 0.5em;
    max-height: 600px;
    overflow-y: scroll;
}
.sh-console.sh-shadow {
  box-shadow: 5px 10px 50px #000;
}
.sh-console .sh-command {
    margin: 0 0 0 0px;
    color: #fff;
    position: relative;
}
.sh-console .sh-command:before {
  content: "#";
  color: #3a92c8;
  padding-right: 0.75em;
  position: absolute;
  left: -1.25em;
}
.sh-console .sh-return {
    margin: 0 0 0.5em 25px;
    position: relative;
}
.sh-console .sh-return:before {
  content: "$";
  padding: 0 0.75em 0 1.25em;
  position: absolute;
  left: -2.5em;
}
.sh-console .sh-return ul.sh-array {
  display: inline;
  list-style-type: none;
  padding: 0;
  margin: 0;
}
.sh-console .sh-return ul.sh-array:before {
  content: "[";
}
.sh-console .sh-return ul.sh-array:after {
  content: "]";
}
.sh-console .sh-return ul.sh-array li {
  display: inline;
  list-style-type: none;
  list-style-position: inside;
  margin: 0;
  padding: 0;
}
.sh-console .sh-return ul.sh-array li:before {
  content: '"';
}
.sh-console .sh-return ul.sh-array li:after {
  content: '"';
}
.sh-console .sh-return ul.sh-array li:not(:last-child):after {
  content: '", ';
}
.sh-console .sh:first-child {
  margin-top: 0;
}
.sh-console .sh:last-child {
  margin-bottom: 0;
}
.sh-console a {
  color: #3a92c8;
  text-decoration: none;
}
.sh-console a:hover {
  text-decoration: underline;
}
</style>