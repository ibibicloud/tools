<?php

declare(strict_types=1);

namespace ibibicloud;

/**
 * HTTP客户端类，用于发送各种HTTP请求
 * 封装了cURL功能，支持GET、POST、PUT、DELETE等请求方法
 */
class HttpClient
{
    /**
     * TCP连接超时时间（秒）
     * 用于检测目标服务器是否过载、下线或崩溃
     * @var int
     */
    private int $connectTimeout = 5;

    /**
     * 响应超时时间（秒）
     * 接收缓冲完成前需要等待的时间，大文件请求应适当调高
     * @var int
     */
    private int $timeout = 30;

    /**
     * 发送HTTP请求的核心方法
     * 
     * @param string $method HTTP请求方法（GET/POST/PUT/DELETE等）
     * @param string $url 请求的URL地址
     * @param mixed $data 请求数据，GET请求时忽略，其他请求作为请求体
     * @param array $header 请求头信息，键值对数组
     * @param bool $includeResponseHeaders 是否包含响应头信息
     * @param bool $followLocation 是否跟随重定向
     * @param int $maxRedirs 最大重定向次数
     * 
     * @return array 返回包含响应信息的数组
     *               - body: 响应主体内容
     *               - statusCode: HTTP状态码
     *               - headers: 响应头信息(当includeResponseHeaders为true时)
     * 
     * @throws \InvalidArgumentException 当使用不支持的HTTP方法时抛出
     * @throws \RuntimeException 当cURL请求失败时抛出
     */
    public function sendRequest(
        string $method,
        string $url,
        $data = null,
        array $header = [],
        bool $includeResponseHeaders = false,
        bool $followLocation = false,
        int $maxRedirs = 5): array
    {
        // 初始化CURL会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $this->setRequestMethod($ch, $method, $data);

        // 设置超时参数
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        
        // 设置返回数据格式
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // 设置User-Agent，模拟浏览器请求
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
        
        // HTTPS请求设置
        if ( strpos($url, 'https://') !== false ) {
            // 禁用SSL验证（生产环境建议配置证书）
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        
        // 设置请求头
        if ( !empty($header) ) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        
        // 设置是否返回响应头
        curl_setopt($ch, CURLOPT_HEADER, $includeResponseHeaders);
        
        // 设置重定向选项
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followLocation);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $maxRedirs);

        // 执行请求并获取响应
        $responseData = curl_exec($ch);
        
        // 检查请求是否成功
        if ( $responseData === false ) {
            $error = curl_error($ch);
            $errorCode = curl_errno($ch);
            curl_close($ch);
            throw new \RuntimeException("cURL请求失败 (错误码: {$errorCode}): {$error}");
        }
        
        // 解析响应数据
        $response = [];
        if ( $includeResponseHeaders ) {
            // 分离响应头和响应体
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $responseHeader = substr($responseData, 0, $headerSize);
            $responseBody = substr($responseData, $headerSize);
            
            $response['body'] = $responseBody;
            
            // 解析响应头信息
            $headers = [];
            $headerLines = explode("\r\n", $responseHeader);
            foreach ($headerLines as $line) {
                if ( empty($line) ) continue;
                if ( strpos($line, ': ') !== false ) {
                    [$key, $value] = explode(': ', $line, 2);
                    $headers[$key] = $value;
                } else {
                    // 状态行
                    $headers['status'] = $line;
                }
            }
            $response['headers'] = $headers;
        } else {
            $response['body'] = $responseData;
        }
        
        // 获取HTTP状态码并关闭连接
        $response['statusCode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $response;
    }

    public function get(
        string $url,
        array $queryParams = [],
        array $headers = [],
        bool $includeResponseHeaders = false,
        bool $followLocation = false,
        int $maxRedirs = 5
    ): array
    {
        // 构建URL查询参数
        if ( !empty($queryParams) ) {
            $url .= ( strpos($url, '?') !== false ? '&' : '?' ) . http_build_query($queryParams);
        }

        return $this->sendRequest('GET', $url, null, $headers, $includeResponseHeaders, $followLocation, $maxRedirs);
    }

    public function post(
        string $url,
        $data = null,
        array $headers = [],
        bool $includeResponseHeaders = false,
        bool $followLocation = false,
        int $maxRedirs = 5
    ) : array
    {
        return $this->sendRequest('POST', $url, $data, $headers, $includeResponseHeaders, $followLocation, $maxRedirs);
    }

    public function put(
        string $url,
        $data = null,
        array $headers = [],
        bool $includeResponseHeaders = false,
        bool $followLocation = false,
        int $maxRedirs = 5
    ): array
    {
        return $this->sendRequest('PUT', $url, $data, $headers, $includeResponseHeaders, $followLocation, $maxRedirs);
    }

    public function delete(
        string $url,
        $data = null,
        array $headers = [],
        bool $includeResponseHeaders = false,
        bool $followLocation = false,
        int $maxRedirs = 5
    ): array
    {
        return $this->sendRequest('DELETE', $url, $data, $headers, $includeResponseHeaders, $followLocation, $maxRedirs);
    }

    private function setRequestMethod($ch, string $method, $data): void
    {
        $method = strtoupper($method);
        switch ( $method ) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'GET':
                // 默认是GET，不需要额外设置
                break;
            case 'PUT':
            case 'DELETE':
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                if ( $data !== null ) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                }
                break;
            default:
                throw new \InvalidArgumentException("不支持的HTTP方法: {$method}");
        }
    }
    
}