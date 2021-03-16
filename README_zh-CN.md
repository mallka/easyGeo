# easyGeo

# features
1. 带升级功能，可每月执行一次 
2. 查询IP的信息，国家、省、市、区、地址，经纬度，时区、AS号，机构名字


# 更新IP数据库 

先从maxmind.com注册一个账号，然后从左侧菜单生成一个license key，然后执行以下代码

```
<?php
use mallka\easygeo\Geo;
\mallka\easygeo\Geo::update('你的KEY','存储IPdb的目录。本例使用的是store目录');

```

# 获取iP信息

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


# 如何安装

##  composer 
```shell
composer require mallka/easygeo
```

## 直接下载
1. 下载整个文件
2. 如果系统是linux系列，注意下存储目录的权限必须是777
3. 打开test.php看以下就明白





 
