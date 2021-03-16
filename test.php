<?php
require_once "vendor/autoload.php";
use mallka\easygeo\Geo;

//method 1: instance  for get ip info
//$obj = new Geo("./Lite2-City.mmdb",'GeoLite2-ASN.mmdb');
//$obj->get('113.110.215.242');


//method 2: static function call for get ip info
$res = \mallka\easygeo\Geo::getInfo('113.110.215.242','./store/GeoLite2-City.mmdb','./store/GeoLite2-ASN.mmdb');
var_dump($res);

//update your db file
//\mallka\easygeo\Geo::update('Gw3SXxelyhRKobeQbcde','/Users/Eric/Desktop/easyGeo/store');
