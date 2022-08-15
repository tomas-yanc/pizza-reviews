<?php

namespace app\modules\rest\interfaces;

interface CreateAuthDataInterface
{
    public function createJwt();

    public static function test();
}

