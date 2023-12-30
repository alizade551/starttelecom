<?php 

namespace app\constants;

class RadiusAttributes
{

    // FreeRADIUS Attr
    public const CLEARTEXT_PASSWORD = "Cleartext-Password";
    public const SIMULTANEOUS_USE = "Simultaneous-Use";
    public const FRAMED_MTU = "Framed-MTU";
    public const FILTER_ID = "Filter-Id";
    public const CHAP_PASSWORD = "CHAP-Password";


    // FreeRADIUS operation consts
    public const OPERATION_EQUALS = ":=";
    public const OPERATION_ADDTION = "+=";
    public const OPERATION_ASSIGNMENT_WITH_PLUS = "=+";
    public const OPERATION_PATTERN_MATCH = ":=~";
    public const OPERATION_ADDTION_WITH_PATTERN_MATCH = "+=~";
    public const OPERATION_PATTERN_TEST = "=~";
    public const OPERATION_NEGATION_OF_PATTERN_TEST = "!~";

    // MikroTik RouterOS Attr
    public const MIKROTIK_RATE_LIMIT = "Mikrotik-Rate-Limit";
    public const MIKROTIK_RECV_LIMIT = "MikroTik-Recv-Limit";
    public const MIKROTIK_XMIT_LIMIT = "MikroTik-Xmit-Limit";
    public const MIKROTIK_GROUP = "MikroTik-Group";
    public const MIKROTIK_WIRELESS_FORWARD = "MikroTik-Wireless-Forward";



    // Cisco  Attr
    public const CISCO_AVPAIR = "Cisco-AVPair";
    public const CISCO_TUNNEL_ASSIGNMENT_ID = "Cisco-Tunnel-Assignment-ID";
    public const CISCO_DEVICE_TYPE = "Cisco-Device-Type";
    public const CISCO_NAS_PORT_TYPE = "Cisco-NAS-Port-Type";

}

 ?>