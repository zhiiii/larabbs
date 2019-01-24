<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $sms)
    {
        $phone = $request->phone;
//        生成验证码
        $code = str_pad(random_int(0, 9999), 4, 0, STR_PAD_LEFT);
        dd($code);
        // 验证生产环境
        if (!app()->environment('production')) {
            $code = '1234';
        } else {
            try {
                $sms->send($phone, [
                    'content' => "【ZHIIII】您的验证码是{$code}。如非本人操作，请忽略本短信"
                ]);
            } catch (NoGatewayAvailableException $exception) {
                $message = $exception->getException('yunpian')->getMessage();
                return $this->response->errorInternal($message, '短信发送异常');
            }
        }

        $key = 'verificationCode_' . str_random(15);
        $expiredAt = now()->addMinute(10);

        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
