<?php 
namespace app\components;
 
use Yii;
use app\components\RouterosApi;


class MikrotikQueries{




    public static function queueSimplePrint(  $nas, $router_username, $router_password ){

            $API = new RouterosAPI();

            if ( $API->connect($nas , $router_username, $router_password) ) {
          
                $data = $API->comm ( '/queue/simple/print',array ("?target" => '10.10.4.7/32') );
            }
            $API->disconnect();

   


            if ( $data != null ) {
                return $data;
            }
            return [];
    }


    public static function dhcpGetFakeUser(  $nas, $router_username, $router_password ){
   
            $API = new RouterosAPI();
            $API->debug = false;
            if ( $API->connect($nas , $router_username, $router_password) ) {
             $data = $API->comm ( '/ip/dhcp-server/lease/print', array ("?address-lists" => 'fake_user') );
            }else{
               $data = null;
            }

            $API->disconnect();
            $mac = [];
            if ( is_array($data) ) {
                foreach ($data as $key => $value) {
                    if ( isset( $value['host-name'] ) ) {
                        $mac[$value['mac-address']] = $value['address']."- ".$value['host-name']. " - ".$value['mac-address'];
                    }else{
                        $mac[$value['mac-address']] = $value['address']." - ".$value['mac-address'];
                    }
                }
            }
            if ( $data != null ) {
                return $mac;
            }else{
                Yii::$app->getResponse()->redirect(['request-order/index'])->send();
                Yii::$app->end();
            }
    }



    public static  function dhcpPrint( $login,$nas,$router_username,$router_password ){
       $API = new RouterosAPI;
       $API->debug = false;
          if ( $API->connect( $nas,$router_username,$router_password ) ){
            $data =  $API->comm("/ip/dhcp-server/lease/print
            ?mac-address=".$login);
            $API->disconnect();
            return $data;
      }else{
        return ['error'=>'Router connection error'];
      }
    }




    public static function dhcpAddMac( 
        $login, 
        $rateLimit, 
        $ipAddress,
        $addressList, 
        $nas, 
        $router_username, 
        $router_password,
        $action,
        $params,
        $save = true
    ){
    // ip dhcp-server lease set [find mac-address=tapılan mak adres] address=ip_adrsi_siyahidan rate-limit=20M address-lists=iNet_yes

            $API = new RouterosAPI;
            if ( $API->connect( $nas,  $router_username,  $router_password ) ) {

            $data =  $API->comm("/ip/dhcp-server/lease/print
            ?mac-address=".$login);
         
            foreach ($data as $key => $p) {

                $API->comm("/ip/dhcp-server/lease/set",[
                    '.id'=>$p['.id'],
                    "rate-limit"=>$rateLimit
                ]);

                $API->comm("/ip/dhcp-server/lease/set",[
                    '.id'=>$p['.id'],
                    "address"=>$ipAddress
                ]);

                $API->comm("/ip/dhcp-server/lease/set",[
                    '.id'=>$p['.id'],
                    "address-lists"=>$addressList
                ]);


            }
            $API->disconnect();
            return true;

          }else{
            self::saveFailProcess( $action , $params, $save );
        }
    
    }



    public static function dhcpAddMacFromArchive( 
        $login, 
        $rateLimit, 
        $ipAddress,
        $addressList, 
        $nas, 
        $router_username, 
        $router_password,
        $action,
        $params,
        $save = true
    ){
    // ip dhcp-server lease set [find mac-address=tapılan mak adres] address=ip_adrsi_siyahidan rate-limit=20M address-lists=iNet_yes
    
            $API = new RouterosAPI;
        if ( $API->connect( $nas,  $router_username,  $router_password ) ) {     
                $API->comm("/ip/dhcp-server/lease/add",[
                    'mac-address'=>$login,
                    "address"=>$ipAddress,
                    "rate-limit"=>$rateLimit,
                    "address-lists"=>$addressList,
                    "server"=>"dhcp_server"
                ]);
            
            $API->disconnect();
            return true;

          }else{
            self::saveFailProcess( $action , $params, $save );
        }
    
    }




    public static function dhcpRemoveMac( 
        $login, 
        $nas, 
        $router_username, 
        $router_password,
        $params,
        $save = true
    ){
   
        $API = new RouterosAPI;
        if ( $API->connect( $nas,  $router_username,  $router_password ) ) {

            $data =  $API->comm("/ip/dhcp-server/lease/print
            ?mac-address=".$login);

            foreach ($data as $key => $p) {
                $API->comm("/ip/dhcp-server/lease/remove",[
                    '.id'=>$p['.id']
                ]);
            }
            $API->disconnect();
            return true;
          }
    
    }


    public static function dhcpBlockMac( 
        $login, 
        $nas, 
        $router_username, 
        $router_password,
        $action,
        $params,
        $save = true
    ){
        $API = new RouterosAPI;
        if ( $API->connect( $nas,  $router_username,  $router_password ) ) {

            $data =  $API->comm("/ip/dhcp-server/lease/print
            ?mac-address=".$login);
            
            foreach ($data as $key => $p) {
                $API->comm("/ip/dhcp-server/lease/set",[
                    '.id'=>$p['.id'],
                    "address-lists"=>"iNet_no"
                ]);
            }
            $API->disconnect();
            return true;

          }else{
            self::saveFailProcess( $action , $params, $save );
        }
    
    }



    public static function dhcpUnBlockMac( 
        $login, 
        $nas, 
        $router_username, 
        $router_password,
        $action,
        $params,
        $save = true
    ){    
        $API = new RouterosAPI;
        if ( $API->connect( $nas,  $router_username,  $router_password ) ) {

            $data =  $API->comm("/ip/dhcp-server/lease/print
            ?mac-address=".$login);
            
            foreach ($data as $key => $p) {
                $API->comm("/ip/dhcp-server/lease/set",[
                    '.id'=>$p['.id'],
                    "address-lists"=>"iNet_yes"
                ]);
            }
            $API->disconnect();
            return true;

          }else{
            self::saveFailProcess( $action , $params, $save );
        }
    
    }





    public static function dhcpSetMac( 
        $mac, 
        $rateLimit, 
        $ipAddress,
        $nas, 
        $router_username, 
        $router_password,
        $action,
        $params,
        $save = true
    ){
    // ip dhcp-server lease set [find mac-address=tapılan mak adres] address=ip_adrsi_siyahidan rate-limit=20M address-lists=iNet_yes
    
            $API = new RouterosAPI;
        if ( $API->connect( $nas,  $router_username,  $router_password ) ) {

            $data =  $API->comm("/ip/dhcp-server/lease/print
            ?mac-address=".$mac);
            
            foreach ($data as $key => $p) {
                $API->comm("/ip/dhcp-server/lease/set",[
                    '.id'=>$p['.id'],
                    "address"=>$ipAddress,
                    "rate-limit"=>$rateLimit,
                ]);
            }
            $API->disconnect();
            return true;

          }else{
            self::saveFailProcess( $action , $params, $save );
        }
    
    }





    private static function saveFailProcess( $action, $params,$save = true ){

        if ( $save == true ) {
            $model = new \app\models\FailProcess;
            $model->action = $action;
            $model->params = serialize($params);
            $model->status = '0';
            $model->created_at = time();
            $model->save(false);
        }
    }


    public static function interfacePrintName( $login, $nas, $router_username, $router_password ){
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $API = new RouterosAPI();
            $API->debug = false;
            if ( $API->connect($nas , $router_username, $router_password) ) {
                $data = $API->comm(
                    "/interface/print
                    ?name=".$login);
            }
            $API->disconnect();
            if ( $data != null ) {
                return json_decode(json_encode($data[0]),true);
            }
            return [];
        }
    }



    public static function pppActivesPrint(  $nas, $router_username, $router_password ){
    
         
            $API = new RouterosAPI();
            $API->debug = false;
            if ( $API->connect($nas , $router_username, $router_password) ) {
                $data = $API->comm("/ppp/active/print");
            }
            $API->disconnect();
// echo "<pre>";
// print_r($data);
// echo "</pre>";
// die;

            if ( $data != null ) {
                return $data;
            }
            return [];
        
    }




    public static function pppProfilePrint( $nas, $router_username, $router_password ){
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $API = new RouterosAPI();
            $API->debug = false;
            if ( $API->connect($nas , $router_username, $router_password) ) {
                $data = $API->comm(
                    "/ppp/profile/print"
                );
            }
            $API->disconnect();
            if ( $data != null ) {
                return json_decode(json_encode($data),true);
            }
            return [];
        }
    }

    public static function pppSecretPrint( $nas, $router_username, $router_password ){
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $API = new RouterosAPI();
            $API->debug = false;
            if ( $API->connect($nas , $router_username, $router_password) ) {
                $data = $API->comm(
                    "/ppp/secret/print"
                );
            }
            $API->disconnect();
            if ( $data != null ) {
                return json_decode(json_encode($data),true);
            }
            return [];
        }
    }




    public static function checkRouterAccess( $nas,$router_username,$router_password ){
       $API = new RouterosAPI;
       if ($API->connect( $nas,  $router_username,  $router_password)){
            return true;
        }else{
            return false;
        }
    }

    public static function export(
        $nas,
        $router_username,
        $router_password
    ){
       $API = new RouterosAPI;
       if ($API->connect($nas,  $router_username,  $router_password)){
            $API->write('/export', false); 
            $API->write('=file='.date("d-m-Y").'.rsc', true);
            $read = $API->read();
            $API->disconnect();
            return true;
        }
    }



    public static function reboot(
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true,
    ){
       $API = new RouterosAPI;
       if ($API->connect($nas,  $router_username,  $router_password)){
            $API->write('/system/reboot');
            $API->read();
            $API->disconnect();
            return true;
        }else{
            self::saveFailProcess( $action, $params, $save );
        }
    }




    public static function addCgnIp(
        $public_ip,
        $out_interface,
        $internal_ip,
        $port_range,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true,
    ){
       $API = new RouterosAPI;
       if ($API->connect($nas,  $router_username,  $router_password)){
            $API->comm("/ip/firewall/nat/add\n=chain=srcnat\n=src-address=".$internal_ip."\n=protocol=icmp\n=out-interface=".$out_interface."\n=action=src-nat\n=to-addresses=".$public_ip."");
            $API->comm("/ip/firewall/nat/add\n=chain=srcnat\n=src-address=".$internal_ip."\n=protocol=tcp\n=out-interface=".$out_interface."\n=action=src-nat\n=dst-port=0-65535\n=to-addresses=".$public_ip."\n=to-ports=".$port_range."");
            $API->comm("/ip/firewall/nat/add\n=chain=srcnat\n=src-address=".$internal_ip."\n=protocol=udp\n=out-interface=".$out_interface."\n=action=src-nat\n=dst-port=0-65535\n=to-addresses=".$public_ip."\n=to-ports=".$port_range."");
            $API->disconnect();
            return true;
        }else{
            self::saveFailProcess( $action , $params, $save );
        }

    }

    public static function removeCgnIp(
        $internal_ip,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
       $API = new RouterosAPI;
       if ($API->connect($nas,  $router_username,  $router_password)){
            $API->write('/ip/firewall/nat/getall',false);
            $API->write('?src-address='.$internal_ip.'',false);
            $API->write('=.proplist=.id');
            $READ = $API->read(true);  

            foreach($READ as $item){
            $API->write('/ip/firewall/nat/remove',false);
            $API->write('=.id=' . $item['.id']);
            $READ = $API->read(true);
                                                
            }
            $API->disconnect();   
            return true;
        }else{
            self::saveFailProcess( $action , $params, $save );
        }

    }


    public static function pppSecretEnable(
        $login,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
       $API = new RouterosAPI;
       if ($API->connect($nas,  $router_username,  $router_password)){
           $API->comm("/ppp/secret/enable",
            [
                "numbers"=> $login
            ]
            );
           $API->disconnect();
           return true;
        }else{
            self::saveFailProcess( $action , $params, $save );
        }
    }

 


    // static  ppp profile
    public static  function pppProfileAddStatic(
        $name,
        $download,
        $upload,
        $change_tcp_mss,
        $use_upnp,
        $use_mpls,
        $use_compression,
        $use_encryption,
        $only_one,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
        $API = new RouterosAPI;
        if ( $API->connect( $nas,  $router_username,  $router_password ) ) {
            $API->comm("/ppp/profile/add", array(
                "name"=> $name,
                "change-tcp-mss"=>$change_tcp_mss,
                "use-upnp"=>$use_upnp,
                "use-mpls"=>$use_mpls,
                "use-compression"=>$use_compression,
                "use-encryption"=>$use_encryption,
                "only-one"=>$only_one,
                "rate-limit"=>$download."k"."/".$upload."k"
             ));
            $API->disconnect();
            return true;
          }else{
            self::saveFailProcess( $action , $params, $save );
          }
    }


    //  ordinary ppp profile
    public static  function pppProfileAdd(
        $name,
        $remote_address,
        $local_address,
        $download,
        $upload,
        $change_tcp_mss,
        $use_upnp,
        $use_mpls,
        $use_compression,
        $use_encryption,
        $only_one,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
        $API = new RouterosAPI;
        if ( $API->connect( $nas,  $router_username,  $router_password ) ) {
            $API->comm("/ppp/profile/add", array(
                "name"=> $name,
                "remote-address"=> $remote_address,
                "local-address"=> $local_address,
                "change-tcp-mss"=>$change_tcp_mss,
                "use-upnp"=>$use_upnp,
                "use-mpls"=>$use_mpls,
                "use-compression"=>$use_compression,
                "use-encryption"=>$use_encryption,
                "only-one"=>$only_one,
                "rate-limit"=>$download."k"."/".$upload."k"
             ));
             $API->disconnect();
             return true;
          }else{
            self::saveFailProcess( $action , $params, $save );
          }
    }

    public static  function pppProfileRemove(
        $name,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
        $API = new RouterosAPI;
        if ( $API->connect( $nas,  $router_username,  $router_password ) ) {
            $API->comm("/ppp/profile/remove",
                 ["numbers"=> $name]
             );
            $API->disconnect();
            return true;
          }else{
            self::saveFailProcess( $action , $params, $save );
          }
    }



    public static  function pppProfileSet(
        $oldName,
        $newName,
        $download,
        $upload,
        $change_tcp_mss,
        $use_upnp,
        $use_mpls,
        $use_compression,
        $use_encryption,
        $only_one,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
        $API = new RouterosAPI;
        if ( $API->connect( $nas,  $router_username,  $router_password ) ) {

           $data =  $API->comm("/ppp/profile/print
            ?name=".$oldName);
            foreach ($data as $key => $p) {
                $API->comm("/ppp/profile/set",[
                    '.id'=>$p['.id'],
                    "name"=>$newName,
                    "change-tcp-mss"=> $change_tcp_mss,
                    "use-upnp"=> $use_upnp,
                    "use-mpls"=> $use_mpls,
                    "use-compression"=> $use_compression,
                    "use-encryption"=> $use_encryption,
                    "only-one"=> $only_one,
                    "rate-limit"=>$download."k"."/".$upload."k"
                ]);
            }
            $API->disconnect();
            return true;
          }else{
            self::saveFailProcess( $action, $params, $save );
          }
    }





    public static  function pppSecretDisable(
        $login,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
      $API = new RouterosAPI;
       if ($API->connect($nas,  $router_username,  $router_password)){
         $API->comm("/ppp/secret/disable",[ "numbers"=> $login, ]);
         $API->disconnect();
         return true;
        }else{
            self::saveFailProcess( $action , $params, $save );
        }
    }

    public static  function interfacepPpoeServerRemove(
        $login,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
      $API = new RouterosAPI;
       if ($API->connect($nas,  $router_username,  $router_password)){
           $API->comm(
                "/interface/pppoe-server/remove",
                [ 
                    "numbers"=> "<pppoe-".$login.">"
                ]
            );
         $API->disconnect();
         return true;
        }else{
           self::saveFailProcess( $action , $params, $save );
        }
    }



    public static  function pppSecretDisableAndInterfacepPpoeServerRemove(
        $login,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
      $API = new RouterosAPI;
       if ( $API->connect( $nas, $router_username, $router_password ) ){

            $API->comm(
                "/ppp/secret/disable",
                [
                    "numbers"=>$login
                ]
            );

           $API->comm(
                "/interface/pppoe-server/remove",
                [ 
                    "numbers"=> "<pppoe-".$login.">"
                ]
            );
         $API->disconnect();
         return true;
        }else{
            self::saveFailProcess( $action, $params, $save );
        }
    }


    public static  function pppSecretUnset(
        $login,
        $packet_name,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
        $API = new RouterosAPI;
         if ($API->connect($nas,  $router_username,  $router_password)){
            $API->comm("/ppp/secret/set",[
              "numbers"=> $login,
                "profile"=>$packet_name
             ]);
             $API->comm("/ppp/secret/unset", array(
                "numbers"=> $login,
                 "value-name"=> "local-address",
             ));
             $API->comm("/ppp/secret/unset", array(
                "numbers"=> $login,
                 "value-name"=> "remote-address",
             ));
             $API->disconnect();
             return true;
        }else{
            self::saveFailProcess( $action , $params,  $save );
        }
    }

    public static  function pppSecretSet(
        $login,
        $packet_name,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
        $API = new RouterosAPI;
          if ($API->connect($nas,  $router_username,  $router_password)){
            $API->comm(
                "/ppp/secret/set",
                [
                "numbers"=> $login,
                "profile"=>$packet_name
                ]
           );
           $API->disconnect();
           return true;
        }else{
           self::saveFailProcess( $action, $params,  $save );
        }
    }

    public static  function pppSecretSetStatic(
        $login,
        $packet_name,
        $remote_addres,
        $local_addres,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
      $API = new RouterosAPI;
       if ($API->connect($nas,  $router_username,  $router_password)){
         $API->comm("/ppp/secret/set", array(
            "numbers"=> $login,
            "profile"  => $packet_name,
            "local-address"=> $local_addres,
            "remote-address"=> $remote_addres,
         ));
         $API->disconnect();
         return true;
      }else{
        self::saveFailProcess( $action , $params, $save );
      }
    }

    public static  function pppSecretAddStatic(
        $name,
        $password,
        $profile,
        $service,
        $remote_address,
        $local_address,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
      $API = new RouterosAPI;
      if ($API->connect($nas,  $router_username,  $router_password)){
        $API->comm("/ppp/secret/add", array(
            "name"     => $name,
            "password" => $password,
            "profile"  => $profile,
            "service"  => $service,
            "remote-address"=> $remote_address,
            "local-address"=> $local_address
         ));
         $API->disconnect();
         return true;
      }else{
        self::saveFailProcess( $action , $params, $save );
      }
    }


    public static  function pppSecretAddStaticAsDisable(
        $name,
        $password,
        $profile,
        $service,
        $remote_address,
        $local_address,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
      $API = new RouterosAPI;
      if ($API->connect($nas,  $router_username,  $router_password)){
        $API->comm("/ppp/secret/add", array(
            "name"     => $name,
            "password" => $password,
            "profile"  => $profile,
            "service"  => $service,
            "disabled" => "yes",
            "local-address"=> $local_address,
            "remote-address"=> $remote_address,
         ));

        $API->disconnect();
        return true;
        
      }else{
        self::saveFailProcess( $action , $params,  $save );
      }
    }



    public static  function pppSecretAdd(
        $name,
        $password,
        $profile,
        $service,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
      $API = new RouterosAPI;
        if ( $API->connect( $nas,  $router_username,  $router_password ) ){
          $API->comm("/ppp/secret/add", array(
             "name"     => $name,
             "password" => $password,
             "profile"  => $profile,
             "service"  => $service,
          ));
          $API->disconnect();
          return true;
      }else{
        self::saveFailProcess( $action , $params, $save );
      }
    }


    public static  function pppSecretAddAsDisable(
        $name,
        $password,
        $profile,
        $service,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true
    ){
      $API = new RouterosAPI;
        if ( $API->connect( $nas,  $router_username,  $router_password ) ){
            $API->comm("/ppp/secret/add", array(
             "name"     => $name,
             "password" => $password,
             "profile"  => $profile,
             "service"  => $service,
             "disabled" => "yes",
            ));

          $API->disconnect();
          return true;
      }else{
        self::saveFailProcess( $action , $params, $save );
      }
    }




    public static function pppSecretRemove(
        $login,
        $nas,
        $router_username,
        $router_password,
        $action,
        $params,
        $save = true

    ){
       $API = new RouterosAPI;
       if ( $API->connect( $nas,$router_username,$router_password ) ){
            $API->comm(
                "/ppp/secret/remove",
                [
                "numbers"=> $login
                ]
            );
            $API->comm("/interface/pppoe-server/remove",
                [
                "numbers"=> "<pppoe-".$login.">"
                ]
            );

        $API->disconnect();
        return true;
      }else{
        self::saveFailProcess( $action, $params, $save );
      }
    }

    public static  function pppActivePrint( $login,$nas,$router_username,$router_password ){
       $API = new RouterosAPI;
       $API->debug = false;
          if ( $API->connect( $nas,$router_username,$router_password ) ){
           $API->write(
           "/ppp/active/print
           ?name=".$login.""
           );
            $READ = $API->read();
            $API->disconnect();
            return $READ;
      }else{
        return ['error'=>'Router connection error'];
      }
    }


    public static  function checkRxTxDHCP( $login,$nas,$router_username,$router_password ){
        $API = new RouterosAPI();
        $API->debug = false;

        if ( $API->connect( $nas , $router_username , $router_password ) ) {

            $data =  $API->comm("/queue/simple/print
                    =stats=detail
                    ?name=dhcp-ds<".$login.">
                    ");

               $rxTxString = $data[0]['rate'];
               $rxTx = explode("/",$data[0]['rate']);
               $rows = array(); 
               $rows2 = array();  


                if( count($data) > 0 ){  
                    $rx = $rxTx[0] ;
                    $tx = $rxTx[1];

                    $rows['name'] = 'Tx';
                    $rows['data'][] = $tx;
                    $rows['time'][] = time();

                    $rows2['name'] = 'Rx';
                    $rows2['data'][] = $rx;
                    $rows2['time'][] = $rx;

                }

            $API->disconnect();
        }else{
            echo "Something went wrong!";
           die;
        }

        $result = array();
        array_push($result,$rows);
        array_push($result,$rows2);
        return json_encode($result, JSON_NUMERIC_CHECK);
    }

    public static  function checkRxTx( $login,$nas,$router_username,$router_password ){
        $API = new RouterosAPI();
        $API->debug = false;

        if ( $API->connect( $nas , $router_username , $router_password ) ) {
            $rows = array(); $rows2 = array();  
               $API->write("/interface/monitor-traffic",false);
               $API->write("=interface=<pppoe-".$login.">",false); 
               $API->write("=once=",true);
               $READ = $API->read(false);
               $ARRAY = $API->ParseResponse($READ);
                if( count($ARRAY) > 0 ){  
                    $rx = $ARRAY[0]["rx-bits-per-second"];
                    $tx = $ARRAY[0]["tx-bits-per-second"];
                    $rows['name'] = 'Tx';
                    $rows['data'][] = $tx;
                    $rows['time'][] = time();
                    $rows2['name'] = 'Rx';
                    $rows2['data'][] = $rx;
                    $rows2['time'][] = $rx;

                }else{  
                    echo $ARRAY['!trap'][0]['message'];  
                } 
            $API->disconnect();
        }else{
            echo "Something went wrong!";
           die;
        }

        $result = array();
        array_push($result,$rows);
        array_push($result,$rows2);
        return json_encode($result, JSON_NUMERIC_CHECK);
    }

    public static  function checkRxTxRouter( $nas, $router_username, $router_password, $interface ){
        $API = new RouterosAPI(); 
        $API->debug = false;
        if ( $API->connect( $nas,$router_username,$router_password ) ) {
            $rows = array(); $rows2 = array();  
               $API->write("/interface/monitor-traffic",false);
               $API->write("=interface=".$interface."",false);
               $API->write("=once=",true);
               $READ = $API->read(false);
               $ARRAY = $API->ParseResponse($READ);
                if( count($ARRAY) > 0 ){  
                    $rx = $ARRAY[0]["rx-bits-per-second"];
                    $tx = $ARRAY[0]["tx-bits-per-second"];
                    $rows['name'] = 'Tx';
                    $rows['data'][] = $tx;
                    $rows['time'][] = time();
                    $rows2['name'] = 'Rx';
                    $rows2['data'][] = $rx;
                    $rows2['time'][] = $rx;
                }
        }
        $API->disconnect();
        $result = array();
        array_push($result,$rows);
        array_push($result,$rows2);
        return json_encode($result, JSON_NUMERIC_CHECK);
    }


    public static function getRouterNameWithMacAddress( $mac_address ){
      $url = "https://api.macvendors.com/" . urlencode($mac_address);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $response = curl_exec($ch);
      if($response) {
        echo $response;
      } else {
        echo "Not Found";
      }
    }   	

}

 ?>