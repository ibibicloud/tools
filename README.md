
## ibibicloud tools 常用工具类

### 安装
~~~
composer require ibibicloud/tools
~~~

### HttpClient
HTTP客户端类，用于发送各种HTTP请求

封装了cURL功能，支持GET、POST、PUT、DELETE等请求方法
~~~
use ibibicloud\facade\HttpClient;

HttpClient::get($url, $queryParams = [], $headers = [], $includeResponseHeaders = false, $followLocation = false, $maxRedirs = 5);
~~~
~~~
HttpClient::post($url, $data = null, $headers = [], $includeResponseHeaders = false, $followLocation = false, $maxRedirs = 5);
~~~

### FormatUnit
各种单位格式化
~~~
use ibibicloud\facade\FormatUnit;

// 数字 123456789 转换为 1亿2345.67万
// 数字 12345678 转换为 1234.56万
FormatUnit::number2CN($number, $decimals = 2);
~~~

~~~
// 文件大小转换为 1.2KB 1.2MB 1.2GB 1.2TB
FormatUnit::fileSize($bytes, $decimals = 2);
~~~

~~~
// 时长转换为 12秒 12:34 12：34：56
FormatUnit::duration($duration, $isMsSecond = true);
~~~