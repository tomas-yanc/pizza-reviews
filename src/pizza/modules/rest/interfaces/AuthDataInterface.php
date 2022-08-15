<?php

namespace app\modules\rest\interfaces;

interface AuthDataInterface
{
    public function createJwt($userId, $clientSecret, $secretKey);
}

