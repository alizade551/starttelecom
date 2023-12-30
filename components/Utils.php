<?php 
/**
 * Author: Hafiz Alizade
   Company: netbox.az
 * 
 */
namespace app\components;

use yii\httpclient\Client;
use Yii;

//Helpers
class Utils {


  public static function getSiteConfig (){
      $siteConfig = \app\models\SiteConfig::find()
      ->asArray()
      ->one();

      return $siteConfig;
  }

  public static function nextUpdateBonus ( $paidDay, $updated_at  ){

    $paidDay = $paidDay;
    $currentMonth  = date('m', $updated_at);
    $currentYear   = date('Y', $updated_at);
    $currentHour   = date('H', $updated_at);
    $currentMinute = date('i', $updated_at);
    $currentSecond = date('s', $updated_at);


    $currentHour   = date('H');
    $currentMinute = date('i');
    $currentSecond = date('s');


    $monthCount = 1;

    $yearCount = floor( $monthCount / 12 );
    $remnantMonth = $monthCount % 12;

    if ( $currentMonth + $remnantMonth > 12 ) {
        $nextMonth = ( $currentMonth + $remnantMonth ) % 12;
        $nextYear = $currentYear + ceil( $remnantMonth  / 12) + $yearCount;
    } else {
        $nextYear = $currentYear + $yearCount;
        $nextMonth = $currentMonth + $remnantMonth;
    }


    $monthCountLaterTimestamp = strtotime( $nextMonth . "/$paidDay/" . $nextYear . " $currentHour:$currentMinute:$currentSecond" );

      if ( $paidDay == 31 ) {
          $monthCountLaterDay = date('d',$monthCountLaterTimestamp);
          if (  $monthCountLaterDay != $paidDay ) {
                $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                if ( date("d", $monthCountLaterTimestamp ) != 30 ) {
                    $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                    if ( date("d", $monthCountLaterTimestamp ) != 29 ){
                      $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                    }
                }
          }
      }
   
      if ( $paidDay == 30 ) {
          $monthCountLaterDay = date('d',$monthCountLaterTimestamp);
          if (  $monthCountLaterDay != $paidDay ) {
                $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                if ( date("d", $monthCountLaterTimestamp ) != 29 ) {
                    $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                }
          }
      }

      if ( $paidDay == 29 ) {
          $monthCountLaterDay = date('d',$monthCountLaterTimestamp);
          if (  $monthCountLaterDay != $paidDay ) {
                $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
          }
      }

      return ['updateAt'=>$monthCountLaterTimestamp,'paidDay'=>$paidDay];

    }

  public static function calculateNextPaymentTimestamp( $monthCount, $status, $paidTimeType, $paidDay, $updatedAt ) {

        $siteConfig = \app\models\SiteConfig::find()->one();

      if ( $status == 2 || $status == 3  ) {

        if ( $paidTimeType == "0" ) {
          $paidDay = "01";
          $currentMonth  = date('m');
          $currentYear   = date('Y');
          $currentHour   = date('H');
          $currentMinute = date('i');
          $currentSecond = date('s');
        }

        if ( $paidTimeType == "1" ) {
          if ( $siteConfig['paid_day_refresh'] == "0" ) {
             $paidDay = $paidDay;
             if ( date('m') == date('m', $updatedAt) && date('Y') == date('Y', $updatedAt)   ) {
               $monthCount = $monthCount;
             }else{
               if ( date("d") >= $paidDay ) {
                 $monthCount =  $monthCount ;
               }else{
                $monthCount =  $monthCount - 1;
               }
             }
          }else{
             $paidDay = date('d');
          }
          $currentMonth  = date('m');
          $currentYear   = date('Y');
          $currentHour   = date('H');
          $currentMinute = date('i');
          $currentSecond = date('s');
        }

      }

      if ( $status == 0 || $status == 1   ) {

         if ( $paidTimeType == "1" ) {
            $paidDay       = $paidDay;
            $currentMonth  = date('m', $updatedAt);
            $currentYear   = date('Y', $updatedAt);
            $currentHour   = date('H', $updatedAt);
            $currentMinute = date('i', $updatedAt);
            $currentSecond = date('s', $updatedAt);
          }

          if ( $paidTimeType == "0" ) {
            $paidDay = "01";
            $currentMonth  = date('m', $updatedAt);
            $currentYear   = date('Y', $updatedAt);
            $currentHour   = date('H', $updatedAt);
            $currentMinute = date('i', $updatedAt);
            $currentSecond = date('s', $updatedAt);
          }
        
      }


      $yearCount = floor( $monthCount / 12 );
      $remnantMonth = $monthCount % 12;

      if ( $currentMonth + $remnantMonth > 12 ) {
          $nextMonth = ( $currentMonth + $remnantMonth ) % 12;
          $nextYear = $currentYear + ceil( $remnantMonth  / 12) + $yearCount;
      } else {
          $nextYear = $currentYear + $yearCount;
          $nextMonth = $currentMonth + $remnantMonth;
      }
      
      $monthCountLaterTimestamp = strtotime( $nextMonth . "/$paidDay/" . $nextYear . " $currentHour:$currentMinute:$currentSecond" );


       return  $monthCountLaterTimestamp ;
  }

  public static function monthCountAndRemaind( $dividend, $divisor ) {
        $month = floor($dividend / $divisor); // Integer part
        $remaind = $dividend - ( $month * $divisor); // Remainder

        return ['month'=>$month,'remaind'=>$remaind];
  }

  public static function nextUpdateAtWhenRequested ( $userId, $balanceIn = 0, $requestType = false, $temporary = false ) {
      $model = \app\models\Users::find()->where(['id'=>$userId])->one();
      if ( $model->status == 0 || $model->status == 3 || $model->second_status == 4  ) {
            $daily_calc =  true;
            $half_month =  false;
       

            $tariffAndServiceArray = \app\models\UserBalance::CalcUserTariffDaily(
                $model->id, 
                $daily_calc, 
                $half_month
            );

            if ( $model->paid_time_type == "0" ) {
                if( $requestType == "1" ){
                  $caclNextUpdateAtForUser = \app\models\Users::caclNextUpdateAtForUser( 
                      $model->id,
                      $tariffAndServiceArray['services_total_tariff'] + $tariffAndServiceArray['credit_tariff'] , 
                      $model->tariff ,
                      [
                        'untilToMonthTariff'=>$tariffAndServiceArray['services_total_tariff'],
                        'credit_tariff'=>$tariffAndServiceArray['credit_tariff'],
                        'total_tariff'=>$model->tariff
                      ]
                  );
                  $date = date("d-m-Y H:i:s",$caclNextUpdateAtForUser['updateAt']);
                  $paidDay = date( "d",$caclNextUpdateAtForUser['updateAt'] );
                  $updatedAt = $caclNextUpdateAtForUser['updateAt'];
                  $removalAmount = 0;

                }elseif( $requestType == "2" ){
                  $date = Yii::t("app","Unlimted");
                  $paidDay = $model->paid_day;
                  $updatedAt =  Yii::t("app","Unlimted");
                  $removalAmount = 0;
                }elseif( $requestType == "3" ){
                  $temoraryTime = time() + ( $temporary * 3600);
                  $date = date( "d-m-Y H:i:s", $temoraryTime );
                  $paidDay = $model->paid_day;
                  $updatedAt =  $temoraryTime;
                  $removalAmount = 0;
                }else{
                  $caclNextUpdateAtForUser = \app\models\Users::caclNextUpdateAtForUser( 
                      $model->id,
                      $tariffAndServiceArray['services_total_tariff'] + $tariffAndServiceArray['credit_tariff'] , 
                      $balanceIn ,
                      [
                        'untilToMonthTariff'=>$tariffAndServiceArray['services_total_tariff'],
                        'credit_tariff'=>$tariffAndServiceArray['credit_tariff'],
                        'total_tariff'=>$model->tariff
                      ]
                  );

                  $date = date("d-m-Y H:i:s",$caclNextUpdateAtForUser['updateAt']);
                  $paidDay = date( "d",$caclNextUpdateAtForUser['updateAt'] );
                  $updatedAt = $caclNextUpdateAtForUser['updateAt'];
                  $removalAmount = round($caclNextUpdateAtForUser['removalAmount'],2);
                }
            }

            if ( $model->paid_time_type == "1" ) {

                if( $requestType == "1" ){
                  $caclNextUpdateAtForUser = \app\models\Users::caclNextUpdateAtForUser( 
                      $model->id,
                      $tariffAndServiceArray['services_total_tariff'] + $tariffAndServiceArray['credit_tariff'] , 
                      $model->tariff,
                  );
                  $date = date("d-m-Y H:i:s",$caclNextUpdateAtForUser['updateAt']);
                  $paidDay = date( "d",$caclNextUpdateAtForUser['updateAt'] );
                  $updatedAt = $caclNextUpdateAtForUser['updateAt'];
                  $removalAmount = 0;
                }elseif( $requestType == "2" ){
                  $date = Yii::t("app","Unlimted");
                  $paidDay = $model->paid_day;
                  $updatedAt =  Yii::t("app","Unlimted");
                  $removalAmount = 0;
                }elseif( $requestType == "3" ){
                  $temoraryTime = $model->created_at + ( $temporary * 3600);
                  $date = date( "d-m-Y H:i:s", $temoraryTime );
                  $paidDay = $model->paid_day;
                  $updatedAt =  $temoraryTime;
                  $removalAmount = 0;
                }else{
                  $caclNextUpdateAtForUser = \app\models\Users::caclNextUpdateAtForUser( 
                      $model->id,
                      $tariffAndServiceArray['services_total_tariff'] + $tariffAndServiceArray['credit_tariff'] , 
                      $balanceIn,
                  );

                  $date = date("d-m-Y H:i:s",$caclNextUpdateAtForUser['updateAt']);
                  $paidDay = date( "d",$caclNextUpdateAtForUser['updateAt'] );
                  $updatedAt = $caclNextUpdateAtForUser['updateAt'];
                  $removalAmount = $caclNextUpdateAtForUser['removalAmount'];

                }
            }

        return [
           'date'=>$date,
           'paidDay'=>$paidDay,
           'updatedAt'=>$updatedAt,
           'removalAmount'=>$removalAmount,
           'status'=>$model->status
        ];
      }
      if ( $model->status == 1 || $model->second_status  == 5 ) {
        $userServicesPacketsModel = \app\models\UsersServicesPackets::find()
        ->where(['user_id'=>$model->id])
        ->andWhere(['status'=>0])
        ->all();

        $current_day = date("d");
        $month_day = date("t");
        $diff = ( $month_day - date("d") ) + 1;
        $service_tariff = 0;

        foreach ($userServicesPacketsModel as $key => $packet_one) {
          if ( $model->paid_time_type == "0" ) {
            if ( $packet_one->price != null || $packet_one->price != 0 ) {
                $service_tariff += round( ( $packet_one->price   / $month_day ) * $diff , 1);
            }else{
                $service_tariff += round( ( ( $packet_one->packet->packet_price  / $month_day ) * $diff ), 1);
            }
          }

          if ( $model->paid_time_type == "1" ) {
            if ( $packet_one->price != null || $packet_one->price != 0 ) {
                $service_tariff += round( $packet_one->price , 1 );
            }else{
                $service_tariff += round( $packet_one->packet->packet_price , 1 );                       
            }
          }
        }

        $date = date("d-m-Y H:i:s",$model->updated_at);
        $paidDay = $model->paid_day;
        $updatedAt = $model->updated_at;

        if ( $requestType == "1" || $requestType == "2" ) {
          $removalAmount = 0;
        }else{
          $removalAmount = $service_tariff;
        }

        return [
           'date'=>$date,
           'paidDay'=>$paidDay,
           'updatedAt'=>$updatedAt,
           'removalAmount'=>$removalAmount,
           'status'=>$model->status
        ];
      }
    }

  public static function changeNextUpdate ( $paidTimeType, $paidDay = null ){
      $currentYear = date( 'Y');
      $currentMonth = date("m");
      $currentDay = date("d");

      if ( $paidTimeType == "1" ) {
        $date = new \DateTime('now');
        $currentMonthLastDay = date("t");

        if ( $paidDay == null ) {
           $paidDay = date('d');
        }

        if ( $paidDay > $currentMonthLastDay ) {
            $nextPaidDay = $currentMonthLastDay;
        }elseif( $paidDay < $currentMonthLastDay ){
            $nextPaidDay = $paidDay;
        }elseif( $paidDay == $currentMonthLastDay ){
            $nextPaidDay = $paidDay;
        }
         $updateAt = strtotime( $currentMonth . '/'.$nextPaidDay.'/' . $currentYear . ' 00:01:00');

      }else{
         $updateAt = strtotime( $currentMonth . '/01/' . $currentYear . ' 00:01:00');
         $paidDay = 1;
      }


      return ['updateAt'=>$updateAt,'paidDay'=>$paidDay];
    }

  public static function arrayDifference(array $array1, array $array2, array $keysToCompare = null) {
      $serialize = function (&$item, $idx, $keysToCompare) {
          if (is_array($item) && $keysToCompare) {
              $a = array();
              foreach ($keysToCompare as $k) {
                  if (array_key_exists($k, $item)) {
                      $a[$k] = $item[$k];
                  }
              }
              $item = $a;
          }
          $item = serialize($item);
      };

      $deserialize = function (&$item) {
          $item = unserialize($item);
      };

      array_walk($array1, $serialize, $keysToCompare);
      array_walk($array2, $serialize, $keysToCompare);

      // Items that are in the original array but not the new one
      $deletions = array_diff($array1, $array2);
      $insertions = array_diff($array2, $array1);

      array_walk($insertions, $deserialize);
      array_walk($deletions, $deserialize);

      return array('insertions' => $insertions, 'deletions' => $deletions);
  }

  public static function ipPortSplitting( int $splitCount ,$startCount = 1000 ,int $endCount = 65535 ){

      $inputArray = range( $startCount, $endCount, 1);
      $chunks = array_chunk( $inputArray, round( ( $endCount - $startCount ) / $splitCount ) );

      $ipInterval = [];
      foreach ($chunks as $chunkKey => $chunkValue) {
         $ipInterval[$chunkKey] = min( $chunkValue )."-".max( $chunkValue );
      }
      return $ipInterval;
  }

  public static function createText( $str ,array $variables = [] ){
        if ( count( $variables ) != 0 ) {
          $pattern = array_keys( $variables );
          $replacement = array_values( $variables );
          $new_str =   str_replace($pattern ,$replacement,$str);
        }else{
          $new_str = $str;
        }
        return $new_str;
  }

  public static function sendWhatsappMessage( array $template, $user_id, $member_name, $user_phone, $text, $params, $messageId = false ){
      $siteConfig = \app\models\SiteConfig::find()->one();
      $token = $siteConfig->whatsapp_token;
      $client = new Client([
        'transport' => 'yii\httpclient\CurlTransport'
      ]);

      $response = $client->createRequest()
      ->addHeaders([
          'content-type' => 'application/json',
          'Authorization' => 'Bearer '.$token,
      ])
      ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
      ->setUrl('https://graph.facebook.com/v15.0/'.$siteConfig->whatsapp_number_id.'/messages')
      ->setMethod('POST')
      ->setData( 
        [
        'messaging_product'=>'whatsapp',
        'to'=> $user_phone,
        'type'=>'template',
        'template'=> $template,
        ] 
      )
      ->setOptions([
        CURLOPT_CONNECTTIMEOUT => 5, // connection timeout
        CURLOPT_TIMEOUT => 10, // data receiving timeout
      ])
      ->send();
      

      if ( isset( $response->getData()['error'] ) ) {
        $status = "0";
      }else{
        $status = "1";
      }


      if ( $messageId == false) {
        return \app\models\UsersMessage::saveMessage( 
          $user_id, 
          $member_name, 
          $user_phone, 
          $text, 
          "whatsapp", 
          $status, 
          $params,
          json_encode( $response->getData() )
        );
      }else{


        $messageModel = \app\models\UsersMessage::find()
        ->where([ 'id'=>$messageId ])
        ->one();

        if ( $status == "1" ) {
          $messageModel->status = "1";
          $messageModel->save(false);
          return ['status'=>'success','message'=>Yii::t("app","Message was sent")];
        }else{

          return ['status'=>'error','message'=>$response->getData()['error']['message']];
        }


      }
  }

  public static function getClosest( $search, $arr ) {
       $closest = null;
       foreach ($arr as $item) {
          if ($closest === null || abs($search - $closest) >= abs($item - $search)) {
             $closest = $item;
          }
       }
       return $closest;
  }

  private static function getBonusFactor( $monthCount, $bonusModel ) {
    $bonusArray = [];
    foreach ( $bonusModel as $key => $bonus ) {
      $bonusArray[$bonus['month_count']] = $bonus['factor'];
    }

    return $bonusArray[$monthCount];
  }

  public static function getUserFirstCharacter( string $fullName ){
        $arryName = explode(" ", $fullName);
        $shortName = '';
        foreach ($arryName as $key => $value) {
          $shortName .= mb_substr($value, 0, 1, "UTF-8");
          if ($key === 2) {break;}
        }
        return $shortName;
  }
                           
  public static  function formatBytes($bytes, $to, $decimal_places = 1) { 
      $formulas = array(
          'K' => number_format($bytes / 1024, $decimal_places),
          'M' => number_format($bytes / 1048576, $decimal_places),
          'G' => number_format($bytes / 1073741824, $decimal_places)
      );
      return isset($formulas[$to]) ? $formulas[$to] : 0;
  } 

  public static function calcUserBonusPayment( $balanceIn, $userId ) {

    $userModel = \app\models\Users::find()
    ->where(['id'=>$userId])
    ->asArray()
    ->one();

    $bonusModel = \app\models\Bonus::find()
    ->orderBy(['month_count'=>SORT_ASC])
    ->where(['published'=>'1'])
    ->asArray()
    ->all();

    $bonusExceptModel = \app\models\BonusExceptPackets::find()
    ->select('bonus_except_packets.*,bonus.published as published,bonus.month_count as month_count,bonus.factor as factor')
    ->leftJoin('bonus','bonus.id=bonus_except_packets.bonus_id')
    ->where(['published'=>'1'])
    ->all();

    $userServicePacketsModel = \app\models\UsersServicesPackets::find()
    ->where(['user_id'=>$userId])
    ->asArray()
    ->all();

    $bonusMonthCount = []; 
    foreach ($bonusModel as $key => $b) {
      array_push(
        $bonusMonthCount,
        $b['month_count']
      );
    }   
    $exceptPackets = []; 
    foreach ($bonusExceptModel as $key => $bonus) {
      array_push(
        $exceptPackets,
        $bonus['packet_id']
      );
    }

    $userPacketId = [];
    foreach ($userServicePacketsModel as $key => $userPacket) {
      array_push(
        $userPacketId,
        $userPacket['packet_id']
      );
    }

    $userTariff = \app\models\Users::CalcUserTariff( $userModel['id'] );
    $howManyMonthCountPaid = $balanceIn / $userTariff ;
    $checkPacketBonus = !empty(array_intersect($userPacketId, $exceptPackets));
      if ( $bonusModel != null ) {
        if ( $checkPacketBonus == false  ) {
          $hasBonusPayment = ( min( $bonusMonthCount ) <= $howManyMonthCountPaid ) ? true : false;
          if ( $hasBonusPayment == true ) {
            if ( $howManyMonthCountPaid  >= self::getClosest( $howManyMonthCountPaid,$bonusMonthCount ) ) {
              $selectBonus = self::getClosest( $howManyMonthCountPaid, $bonusMonthCount );

              $factorPaid  = round( $balanceIn / $userTariff / $selectBonus , 2);
              $factorBonus = self::getBonusFactor( $selectBonus, $bonusModel );

              return $factorPaid * $factorBonus * $userTariff;
            }else{
              $selectBonus = $bonusMonthCount[ array_search( self::getClosest( $howManyMonthCountPaid, $bonusMonthCount), $bonusMonthCount ) -1 ];
              $factorPaid  = round( $balanceIn / $userTariff / $selectBonus , 2);
              $factorBonus = self::getBonusFactor( $selectBonus, $bonusModel );

              return $factorPaid * $factorBonus * $userTariff;
            }
          }else{
            return 0;
          }

        }else{
          return 0;
        }
      }else{
          return 0;
      }
  }


  public static function failProcessText ( $params ){
      $paramText = '';
      foreach ($params as $pK => $pv) {
          if ( $pK == "login" ) {
              $paramText .= 'login :'.$pv."</br>";
          }
      
          if ( $pK == "nas" ) {
              $paramText .= 'nas :'.$pv."</br>";
          }

          if ( $pK == "name" ) {
              $paramText .= 'name :'.$pv."</br>";
          }

          if ( $pK == "download" ) {
              $paramText .= 'download :'.$pv."</br>";
          }

          if ( $pK == "upload" ) {
              $paramText .= 'upload :'.$pv."</br>";
          }

          if ( $pK == "change_tcp_mss" ) {
              $paramText .= 'change_tcp_mss :'.$pv."</br>";
          }


          if ( $pK == "use_upnp" ) {
              $paramText .= 'use_upnp :'.$pv."</br>";
          }
          if ( $pK == "use_mpls" ) {
              $paramText .= 'use_mpls :'.$pv."</br>";
          }

          if ( $pK == "use_compression" ) {
              $paramText .= 'use_compression :'.$pv."</br>";
          }

          if ( $pK == "use_encryption" ) {
              $paramText .= 'use_encryption :'.$pv."</br>";
          }

          if ( $pK == "only_one" ) {
              $paramText .= 'only_one :'.$pv."</br>";
          }

          if ( $pK == "remote_address" ) {
              $paramText .= 'remote_address :'.$pv."</br>";
          }

          if ( $pK == "local_address" ) {
              $paramText .= 'local_address :'.$pv."</br>";
          }

          if ( $pK == "oldName" ) {
              $paramText .= 'oldName :'.$pv."</br>";
          }

          if ( $pK == "newName" ) {
              $paramText .= 'newName :'.$pv."</br>";
          }

          if ( $pK == "packet_name" ) {
              $paramText .= 'packet_name :'.$pv."</br>";
          }

          if ( $pK == "profile" ) {
              $paramText .= 'profile :'.$pv."</br>";
          }
          if ( $pK == "service" ) {
              $paramText .= 'service :'.$pv."</br>";
          }

          if ( $pK == "public_ip" ) {
              $paramText .= 'public_ip :'.$pv."</br>";
          }

          if ( $pK == "out_interface" ) {
              $paramText .= 'out_interface :'.$pv."</br>";
          }

          if ( $pK == "internal_ip" ) {
              $paramText .= 'internal_ip :'.$pv."</br>";
          }
          
          if ( $pK == "port_range" ) {
              $paramText .= 'port_range :'.$pv."</br>";
          }


      }

      return $paramText;
  }

  public static function transliterate($str){
      $dictionary = [
          'Ə' =>'E', 'ə'  =>'e', 'Ç'  =>'C',
          'ç' =>'c', 'Ş'  =>'S', 'ş'  =>'s',
          'Ö' =>'O', 'ö'  =>'o', 'Ü'  =>'U',
          'ü' =>'u', 'Ğ'  =>'G', 'ğ'  =>'g',
          'i' =>'i', 'İ'  =>'I',
          'ı' =>'i', 'I'  =>'I',

          'а' =>'a', 'б'  =>'b', 'в'  =>'v',
          'г' =>'g', 'д'  =>'d', 'е'  =>'e',
          'ё' =>'e', 'ж'  =>'zh', 'з' =>'z',
          'и' =>'i', 'й'  =>'y', 'к'  =>'k',
          'л' =>'l', 'м'  =>'m', 'н'  =>'n',
          'о' =>'o', 'п'  =>'p', 'р'  =>'r',
          'с' =>'s', 'т'  =>'t', 'у'  =>'u',
          'ф' =>'f', 'х'  =>'h', 'ц'  =>'c',
          'ч' =>'ch', 'ш' =>'sh', 'щ' =>'sch',
          'ь' =>'', 'ы'   =>'y', 'ъ'  =>'',
          'э' =>'e', 'ю'  =>'yu', 'я' =>'ya',
          'А' =>'A', 'Б'  =>'B', 'В'  =>'V',
          'Г' =>'G', 'Д'  =>'D', 'Е'  =>'E',
          'Ё' =>'E', 'Ж'  =>'Zh', 'З' =>'Z',
          'И' =>'I', 'Й'  =>'Y', 'К'  =>'K',
          'Л' =>'L', 'М'  =>'M', 'Н'  =>'N',
          'О' =>'O', 'П'  =>'P', 'Р'  =>'R',
          'С' =>'S', 'Т'  =>'T', 'У'  =>'U',
          'Ф' =>'F', 'Х'  =>'H', 'Ц'  =>'C',
          'Ч' =>'Ch', 'Ш' =>'Sh', 'Щ' =>'Sch',
          'Ь' =>'', 'Ы'   =>'Y', 'Ъ'  =>'',
          'Э' =>'E', 'Ю'  =>'Yu', 'Я' =>'Ya',
      ];
      return strtr($str, $dictionary);
  }

  public static function sendExperiedDate( $userId, $contractNumber, $phone, $lang, $updated_at ){

    $messageModel = \app\models\MessageTemplate::find()
    ->where(['name'=>'expired'])
    ->andWhere(['lang'=>$lang])
    ->asArray()
    ->one();


    if ( Utils::getSiteConfig()['expired_service'] == "1" ) {

        $params = json_encode(
            [
                '{contract_number}'=>$contractNumber,
                '{date}'=>date( "d.m.Y H:i", $updated_at ),
            ]
        );

        $templateSmsAsText = \app\components\Utils::createText( 
            $messageModel['sms_text'],
            [
                '{contract_number}'=>$contractNumber,
                '{date}'=>date("d.m.Y  H:i", $updated_at ),
            ] 
        );



        $checkSmsWasSent = \app\models\UsersMessage::sendSms( 
            $userId , 
            "expired", 
            substr( $phone , 1 ), 
            $templateSmsAsText,
            $params
        );

    }
    
      // whatsapp selected on setting
      if (  Utils::getSiteConfig()['expired_service'] == "2" ) {

          $params = json_encode(
              [
                  '{{1}}'=>$contractNumber,
                  '{{2}}'=>date("d.m.Y H:i",$updated_at ),
              ]
          );

          $template = [
            'name'=>$messageModel['name'],
            'language'=>['code'=>$messageModel['lang']],
            "components"=>[
              ['type'=>'header'],
              [
                  'type'=>'body',
                  'parameters'=>[
                    [ 'type'=>'text','text'=>$contractNumber ],
                    [ 'type'=>'text','text'=> date("d.m.Y H:i", $updated_at ) ],
                  ]
              ],
            ]
          ];

          $templateWhatsappAsText = \app\components\Utils::createText( 
              $messageModel['whatsapp_body_text'],
              [
                  '{{1}}'=>$contractNumber,
                  '{{2}}'=>date("d.m.Y", $updated_at ),
              ],

          );

          $checkWhatsappMessage = \app\components\Utils::sendWhatsappMessage( 
              $template, 
              $userId, 
              "expired", 
              substr( $phone , 1 ),
              $templateWhatsappAsText,
              $params
          );

      }

  }

}







 ?>