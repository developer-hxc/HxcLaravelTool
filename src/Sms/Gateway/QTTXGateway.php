<?php

namespace Hxc\HxcLaravelTool\Sms\Gateway;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Gateways\Gateway;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;


class QTTXGateway extends Gateway
{
    use HasHttpRequest;

    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $params = [
            'account' => config('hxc.sm.account'),
            'password' => $config->get('password'),
            'content' => $message->getContent(),
            'mobile' => $to->getNumber()
        ];
        return file_get_contents($this->getUrl($params));
    }

    /**
     * @param $params
     * @return string
     */
    protected function getUrl($params)
    {
        $content = rawurlencode(mb_convert_encoding($params['content'], "gb2312", "utf-8"));//短信内容做GB2312转码处理
        return "https://sdk2.028lk.com/sdk2/LinkWS.asmx/BatchSend2?CorpID={$params['account']}&Pwd={$params['password']}&Mobile={$params['mobile']}&Content={$content}&Cell=&SendTime=";
    }

}
