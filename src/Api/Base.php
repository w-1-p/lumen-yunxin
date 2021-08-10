<?php

namespace W1p\LumenYunxin\Api;

use App\Jobs\YunxinQueue;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use W1p\LumenYunxin\Exception\YunXinBusinessException;
use W1p\LumenYunxin\Exception\YunXinInnerException;
use W1p\LumenYunxin\Exception\YunXinNetworkException;

class Base
{
    private $baseUrl = 'https://api.netease.im/nimserver/';

    const HEX_DIGITS = "0123456789abcdefghijklmn";

    const BUSINESS_SUCCESS_CODE = 200;

    /**
     * 网易云信分配的账号
     *
     * @var string $appKey
     */
    private $appKey;

    /**
     * 网易云信分配的密钥
     *
     * @var string $appSecrt
     */
    private $appSecrt;

    /**
     * 随机数（最大长度128个字符）
     *
     * @var string $nonce
     */
    public $nonceStr;

    /**
     * 当前UTC时间戳，从1970年1月1日0点0 分0 秒开始到现在的秒数(String)
     *
     * @var string $curTime
     */
    public $curTime;

    /**
     * 校验码
     * SHA1(AppSecret + Nonce + CurTime)
     * 三个参数拼接的字符串，进行SHA1哈希计算，转化成16进制字符(String，小写)
     *
     * @var string
     */
    public $checkSum;

    /**
     * http 超时时间
     *
     * @var int $timeout
     */
    private $timeout = 5;

    /**
     * 此刻要发送的队列，空标识不发送队列
     *
     * @var string
     */
    protected $queue;

    public function __construct($appKey, $appSecrt)
    {
        $this->appKey = $appKey;
        $this->appSecrt = $appSecrt;
    }

    /**
     * 设置本次消息发送为异步消息，消息将被推送到指定组件的队列中
     * 异步发送时，所有接口都返回空值
     *
     * @param string $queue
     *
     * @return $this
     */
    public function async(string $queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * API checksum校验生成
     *
     * @return void $CheckSum(对象私有属性)
     */
    public function checkSumBuilder()
    {
        //此部分生成随机字符串
        $hexDigits = self::HEX_DIGITS;
        $digitsLen = strlen($hexDigits);
        $this->nonceStr;
        for ($i = 0; $i < 128; $i++) {
            $this->nonceStr .= $hexDigits[rand(0, $digitsLen - 1)];
        }
        $this->curTime = (string)(time());    //当前时间戳，以秒为单位

        $joinString = $this->appSecrt.$this->nonceStr.$this->curTime;
        $this->checkSum = sha1($joinString);
    }

    /**
     * 设置超时时间
     *
     * @param $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * 发送请求
     *
     * @param string $uri
     * @param array $data
     *
     * @return mixed
     * @throws YunXinBusinessException
     * @throws YunXinInnerException
     * @throws YunXinNetworkException|GuzzleException
     */
    protected function sendRequest($uri, array $data)
    {
        $this->checkSumBuilder();

        if ($this->queue) {
            dispatch((new YunxinQueue(['method' => $uri, 'data' => $data]))->onQueue($this->queue));
            $this->queue = '';
            return [];
        }

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->baseUrl,
            // You can set any number of default request options.
            'timeout' => $this->timeout,
        ]);
        $response = $client->request('POST', $uri, [
            'headers' => [
                'User-Agent' => 'WebWorker/2.0',
                'AppKey' => $this->appKey,
                'Nonce' => $this->nonceStr,
                'CurTime' => $this->curTime,
                'CheckSum' => $this->checkSum,
            ],
            'form_params' => $data,
        ]);
        $code = $response->getStatusCode();
        $body = $response->getBody();
        if ($code != 200) {
            throw new YunXinNetworkException('NetEase Network Error: '.$body, $code);
        }
        $jsonRes = json_decode((string)$body, true);
        if ($jsonRes && is_array($jsonRes) && $jsonRes['code'] == self::BUSINESS_SUCCESS_CODE) {
            return $jsonRes;
        } elseif ($jsonRes && is_array($jsonRes)) {
            throw new YunXinBusinessException($jsonRes['desc'], $jsonRes['code']);
        } else {
            throw new YunXinInnerException('NetEase inner error: '.$body);
        }
    }

    /**
     * @param $var
     *
     * @return string
     */
    protected function bool2String($var)
    {
        return $var ? 'true' : 'false';
    }
}
