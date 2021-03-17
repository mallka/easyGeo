### ip-location-zh project
ipip.net resource, Fetch provincial information via IP

## Install

> composer require "smartJson/ips-location-zh"

## USAGE

#### 对于普通项目：
```markdown
require 'vendor/autoload.php';  
use smartJson\IpLocationZh\Ip;  
var_dump(Ip::find('ip address'));`
````

#### Return info
```markdown
array:4 [
   0 => "中国"
   1 => "上海"
   2 => "上海"
 ]
```

#### For Laravel:
```markdown
# General Usage:
use SmartJson/IpLocationZh;

Ip::find('ip address') OR Ip::find(Request::getClientIp());
Ip::findInfo('ip address') OR Ip::find(Request::getClientIp());
Ip::findMap('ip address') OR Ip::find(Request::getClientIp());

```
##License MIT