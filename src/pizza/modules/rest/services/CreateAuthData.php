<?php

namespace app\modules\rest\services;

class CreateAuthData
{
    public static function createJwt($userId, $clientSecret, $secretKey)
    {
        $headerArr = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payloadArr = ['userId' => $userId, 'clientSecret' => $clientSecret];

        if($payloadArr == null) {
            return $this->modelClass::setGenericServerErrors();
        }

        $header = implode($headerArr);
        $payload = implode($payloadArr);

        $unsignedToken = base64_encode($header) . '.' . base64_encode($payload);
        $signature = hash_hmac('sha256', $unsignedToken, $secretKey);

        return $signature;
    }
}