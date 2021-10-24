<?php

namespace App\Utils;

class TokenGenerator
{
    public function getNewApiToken(): string {
        return md5(microtime());
    }
}