<?php

namespace Hxc\HxcLaravelTool\Sms\Functions;

use Hxc\HxcLaravelTool\Models\Sms;
use Hxc\HxcLaravelTool\Sms\Gateway\QTTXGateway;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;


class QTTXSms
{
    protected $config;
    protected $sms;

    public function __construct()
    {
        $this->config = config('hxc.sms');
        $this->sms = new EasySms($this->config['gateway']);
        $this->sms->extend('QTTXGateway',function(){
            return new QTTXGateway($this->config['gateway']['gateways']['QTTXGateway']);
        });
    }

    /**
     * @return QTTXSms
     */
    static public function init()
    {
        return new self();
    }

    /**
     * @param $to
     * @param array $params
     * @return array
     */
    public function send($to,array $params)
    {
        $patt = '/^1[3456789][0-9]{9}$/';
        if (!preg_match($patt, $to)) {
            return returnFail('手机号码格式有误');
        }
        $now = now()->format('Y-m-d H:i:s');
        $term = $this->config['exp'];
        $sms = Sms::where([
            'phone' => $to,
            'scene' => $params['scene'],
            'status' => 1,
        ])->where('end_time','>=',$now)->orderBy('id','desc')->first();
        if ($sms) {
            $time = ($term) - (strtotime($now) - strtotime($sms['created_at']));
            return returnFail("{$time}秒后重新发送");
        }
        try{
            $res = $this->sms->send($to,[
                'content' => $params['content']
            ]);
            $result = $res['QTTXGateway']['result'];
            $re = simplexml_load_string($result);
            if ($re[0] > 0) {
                if(!$sms){
                    Sms::where([
                        'phone' => $to,
                        'scene' => $params['scene'],
                        'status' => 1
                    ])->update(['status' => 2]);
                    $create_data = [
                        'phone' => $to,
                        'content' => $params['content'],
                        'end_time' => now()->addSeconds($term),
                        'scene' => $params['scene'],
                        'ip' => $_SERVER["REMOTE_ADDR"]
                    ];
                    if($params['code']){
                        $create_data['code'] = $params['code'];
                    }
                    Sms::create($create_data);
                }
                if($res !== false){
                    return returnSuccess('发送成功');
                }else{
                    return returnFail('验证码异常');
                }
            } elseif ($re == 0) {
                return returnFail('网络访问超时，请稍后再试！');
            } elseif ($re == -9) {
                return returnFail('发送号码为空');
            } elseif ($re == -101) {
                return returnFail('调用接口速度太快');
            } else {
                return returnFail('发送失败');
            }
        }catch (NoGatewayAvailableException $exception){
            return returnFail($exception->getExceptions()['QTTXGateway']->getMessage());
        }
    }
}