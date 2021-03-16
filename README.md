# easyGeo

[中文说明](README_zh-CN.md)


# features
1. Update ip db file 
2. Wrapper of geolite city & asn , it will return an array include city & asn info.


# Update ip db file 

Get your licenser_key from your maxmind account.and then run the code as bellow

```
<?php
use mallka\easygeo\Geo;
\mallka\easygeo\Geo::update('Your key','/path/to/your_db_floder');

```

# Get extra info of ip

```
<?php
use mallka\easygeo\Geo;
$res = \mallka\easygeo\Geo::getInfo('113.110.215.242','./store/GeoLite2-City.mmdb','./store/GeoLite2-ASN.mmdb',$lang='zh-CN');
$res = json_encode($res);

/**
$res will display as bellow:
{
    "city":"深圳市",
    "continent":"亚洲",
    "country":"中国",
    "registered_country":"中国",
    "province":"广东",
    "country_code":"CN",
    "province_code":"GD",
    "location"":{
        "accuracy_radius":5,
        "latitude":22.5333,
        "longitude":114.1333,
        "time_zone":"Asia/Shanghai"
    },
    "autonomous_system_number""":"AS4134",
    "organization"":"ASChinanet"
}
**/
```


# How to install

## via composer 
```shell
composer require mallka/easygeo
```

## via direct download
1. download the file (include file)
2. set floder permissions to 777 for store floder when you on linux ,like chmod -Rf 777  store
3. use it or learn it from *test.php*



# thanks :
1. tronovav/geoip2-update
2. maxmind-db/reader   


 
