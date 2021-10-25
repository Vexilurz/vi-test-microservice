<?php

namespace App\Tests\Utils;

class TestUtils
{
    public static function getRandomStr(): string {
        return md5(microtime());
    }
}